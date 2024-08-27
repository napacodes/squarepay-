<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Page;
use App\Models\User;
use App\Models\Frontend;
use App\Models\Language;
use App\Constants\Status;
use App\Models\Subscriber;
use App\Models\FiatGateway;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\CryptoCurrency;
use App\Models\SupportMessage;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;


class SiteController extends Controller
{
    public function index(){
        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        $pageTitle = 'Home';
        $sections = Page::where('tempname',activeTemplate())->where('slug','/')->first();
        $cryptos      = CryptoCurrency::active()->orderBy('name')->get();
        $fiatGateways = FiatGateway::getGateways();
        $info         = json_decode(json_encode(getIpInfo()), true);
        $mobileCode   = @implode(',', $info['code']);
        $countries    = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $user         = auth()->user();
        $seoContents = $sections->seo_content;
        $seoImage = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::home', compact('pageTitle', 'sections', 'cryptos', 'fiatGateways', 'countries', 'mobileCode', 'user'));
    }

    public function publicProfile($username)
    {
        $request        = request();
        $user           = User::where('username', $username)->firstOrFail();
        $pageTitle      = "Profile of $user->username";

        $advertisements = $user->advertisements()->active()
            ->whereHas('fiat', function ($q) {
                $q->active();
            })->whereHas('crypto', function ($q) {
                $q->active();
            })
            ->whereHas('fiatGateway',  function ($q) {
                $q->active();
            });

        $buyAds         = clone $advertisements;
        $sellAds        = clone $advertisements;

        $latestBuyAds   = $buyAds->where('type', 2);
        $latestSellAds  = $sellAds->where('type', 1);

        if ($request->crypto) {
            $latestBuyAds  = $latestBuyAds->where('crypto_currency_id', $request->crypto);
            $latestSellAds = $latestSellAds->where('crypto_currency_id', $request->crypto);
        }

        if ($request->fiat_gateway) {
            $latestBuyAds  = $latestBuyAds->where('fiat_gateway_id', $request->fiat_gateway);
            $latestSellAds = $latestSellAds->where('fiat_gateway_id', $request->fiat_gateway);
        }

        if ($request->amount) {
            $latestBuyAds  = $latestBuyAds->where('min', '<=', $request->amount)->where('max', '>=', $request->amount);
            $latestSellAds = $latestSellAds->where('min', '<=', $request->amount)->where('max', '>=', $request->amount);
        }

        $latestBuyAds      = $latestBuyAds->latest()->with(['crypto', 'user.wallets', 'fiatGateway', 'fiat'])->get();
        $latestSellAds     = $latestSellAds->latest()->with(['crypto', 'user.wallets', 'fiatGateway', 'fiat'])->get();
        $cryptos           = CryptoCurrency::active()->orderBy('name')->get();
        $fiatGateways      = FiatGateway::active()->orderBy('name')->get();
        $positiveFeedbacks = $user->positiveFeedBacks->count();
        $negativeFeedBacks = $user->negativeFeedBacks->count();

        return view('Template::public_profile', compact('user', 'pageTitle', 'latestBuyAds', 'latestSellAds', 'cryptos', 'fiatGateways', 'positiveFeedbacks', 'negativeFeedBacks'));
    }

    public function pages($slug)
    {
        $page = Page::where('tempname',activeTemplate())->where('slug',$slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections = $page->secs;
        $seoContents = $page->seo_content;
        $seoImage = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::pages', compact('pageTitle','sections','seoContents','seoImage'));
    }


    public function contact()
    {
        $pageTitle = "Contact Us";
        $user = auth()->user();
        $sections = Page::where('tempname',activeTemplate())->where('slug','contact')->first();
        $seoContents = $sections->seo_content;
        $seoImage = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::contact',compact('pageTitle','user','sections','seoContents','seoImage'));
    }


    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        $request->session()->regenerateToken();

        if(!verifyCaptcha()){
            $notify[] = ['error','Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;


        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view',$ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug)
    {
        $policy = Frontend::where('slug',$slug)->where('data_keys','policy_pages.element')->firstOrFail();
        $pageTitle = $policy->data_values->title;
        $seoContents = $policy->seo_content;
        $seoImage = @$seoContents->image ? frontendImage('policy_pages',$seoContents->image,getFileSize('seo'),true) : null;
        return view('Template::policy',compact('policy','pageTitle','seoContents','seoImage'));
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return back();
    }

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers,email',
        ], [
            'email.unique' => "You have already subscribed"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'   => $validator->errors()->all(),
                'success' => false,
            ]);
        }

        $subscribe        = new Subscriber();
        $subscribe->email = $request->email;
        $subscribe->save();

        return response()->json([
            'message' => "Thank you for subscribing us",
            'success' => true
        ]);
    }


    public function cookieAccept(){
        Cookie::queue('gdpr_cookie',gs('site_name') , 43200);
    }

    public function cookiePolicy(){
        $cookieContent = Frontend::where('data_keys','cookie.data')->first();
        abort_if($cookieContent->data_values->status != Status::ENABLE,404);
        $pageTitle = 'Cookie Policy';
        $cookie = Frontend::where('data_keys','cookie.data')->first();
        return view('Template::cookie',compact('pageTitle','cookie'));
    }

    public function placeholderImage($size = null){
        $imgWidth = explode('x',$size)[0];
        $imgHeight = explode('x',$size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font/solaimanLipi_bold.ttf');
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if($imgHeight < 100 && $fontSize > 30){
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        if(gs('maintenance_mode') == Status::DISABLE){
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys','maintenance.data')->first();
        return view('Template::maintenance',compact('pageTitle','maintenance'));
    }

}
