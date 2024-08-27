<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdLimit;
use App\Models\Advertisement;
use App\Models\CryptoCurrency;
use App\Models\FiatCurrency;
use App\Models\FiatGateway;
use App\Models\PaymentWindow;
use App\Models\Review;
use App\Models\Wallet;
use Illuminate\Http\Request;

class AdvertisementController extends Controller
{
    public function index()
    {
        $pageTitle      = 'My Advertisements';
        $advertisements = Advertisement::withoutGlobalScope('ignoreDraft')->where('user_id', auth()->id())->latest()->with(['crypto', 'fiatGateway', 'fiat', 'user.wallets'])->paginate(getPaginate());
        $wallets        = Wallet::where('user_id', auth()->id())->first();

        return view('Template::user.advertisement.index', compact('pageTitle', 'advertisements', 'wallets'));
    }

    public function create()
    {
        $pageTitle      = 'New Advertisement';
        $isPermitted    = $this->checkAdLimit();
        $cryptos        = CryptoCurrency::active()->orderBy('name')->get();
        $paymentWindows = PaymentWindow::orderBy('minute')->get();
        $fiatCurrencies = FiatCurrency::active()->latest()->get();

        $step          = 'one';
        $advertisement = null;
        $fiatGateways  = null;

        if (request()->step) {

            $url = route('user.advertisement.new');
            if (!in_array(request()->step, ['one', 'two', 'three'])) {
                $notify[] = ['error', "Invalid URL"];
                return redirect($url)->withNotify($notify);
            }
            $advertisement = Advertisement::withoutGlobalScope('ignoreDraft')->where('user_id', auth()->id())->orderBy('id', 'desc')->first();
            if (!$advertisement || @$advertisement->status != Status::ADVERTISEMENT_DRAFT) {
                $notify[] = ['error', "Advertisement not found"];
                return redirect($url)->withNotify($notify);
            }
            $fiatGateways = FiatGateway::active()->whereJsonContains('code', "$advertisement->fiat_currency_id")->get();
            $step         = request()->step;
        }
        return view('Template::user.advertisement.create', compact('pageTitle', 'cryptos', 'fiatGateways', 'paymentWindows', 'isPermitted', 'fiatCurrencies', 'step', 'advertisement'));
    }

    public function edit($id)
    {
        $advertisement  = Advertisement::withoutGlobalScope('ignoreDraft')->where('user_id', auth()->id())->findOrFail($id);
        $pageTitle      = 'Update Advertisement';
        $cryptos        = CryptoCurrency::orderBy('name')->get();
        $paymentWindows = PaymentWindow::orderBy('minute')->get();
        $fiatCurrencies = FiatCurrency::active()->latest()->get();

        $fiatGateways = null;
        $step         = 'one';

        if (request()->step) {
            if (!in_array(request()->step, ['two', 'three', 'one'])) {
                $notify[] = ['error', "Invalid URL"];
                return to_route('user.home')->withNotify();
            }
            $step         = request()->step;
            $fiatGateways = FiatGateway::active()->whereJsonContains('code', "$advertisement->fiat_currency_id")->get();
        }
        return view('Template::user.advertisement.edit', compact('pageTitle', 'advertisement', 'cryptos', 'fiatGateways', 'paymentWindows', 'step', 'fiatCurrencies'));
    }

    public function store(Request $request, $id = 0)
    {
        $marginIsRequired     = @$request->step == 2 && @$request->price_type == 1 ? 'required|numeric|gte:0' : 'nullable';
        $fixedPriceIsRequired = @$request->step == 2 && @$request->price_type == 2 ? 'required|numeric|gt:0' : 'nullable';

        $request->validate([
            'step'            => 'required|integer|in:1,2,3',
            'type'            => 'required_if:step,1|in:1,2',
            'crypto_id'       => 'required_if:step,1|integer:gt:0',
            'fiat_id'         => 'required_if:step,1|integer:gt:0',
            'fiat_gateway_id' => 'required_if:step,2|integer:gt:0',
            'price_type'      => 'required_if:step,2|in:1,2',
            'margin'          => $marginIsRequired,
            'fixed_price'     => $fixedPriceIsRequired,
            'window'          => 'required_if:step,2|integer|gt:0',
            'min'             => 'required_if:step,2|numeric|gt:0',
            'max'             => 'required_if:step,2|numeric|gt:min',
            'details'         => 'required_if:step,2',
            'terms'           => 'required_if:step,3',
            'mode'            => 'required|in:create,edit',
        ]);


        if ($request->step == 1) {
            $check = $this->checkData($request, $id, false);
            if (@$check[0] == 'error') {
                $notify[] = ['error', @$check[1]];
                return back()->withNotify($notify);
            }
        }

        if ($id) {
            $advertisement = Advertisement::withoutGlobalScope('ignoreDraft')->where('user_id', auth()->id())->find($id);
        } else {
            if ($request->step == 1) {
                $advertisement          = new Advertisement();
                $advertisement->user_id = auth()->id();
            } else {
                $advertisement = Advertisement::withoutGlobalScope('ignoreDraft')->where('user_id', auth()->id())->orderBy('id', 'desc')->where('status', Status::ADVERTISEMENT_DRAFT)->firstOrFail();
            }
        }

        if ($request->mode == 'create') {
            $url     = route('user.advertisement.new');
            $message = 'Your advertisement added successfully';
        } else {
            $url     = route('user.advertisement.edit', $id);
            $message = 'Your advertisement updated successfully';
        }

        if ($request->step == 1) {
            $advertisement->type               = $request->type;
            $advertisement->crypto_currency_id = $request->crypto_id;
            $advertisement->fiat_currency_id   = $request->fiat_id;
            $advertisement->status             = $id ? $advertisement->status : Status::ADVERTISEMENT_DRAFT;
            $advertisement->save();

            $url .= "?step=two";
            return redirect($url);

        } elseif ($request->step == 2) {
            $advertisement->fiat_gateway_id = $request->fiat_gateway_id;
            $advertisement->margin          = $request->margin ?? 0;
            $advertisement->fixed_price     = $request->fixed_price ?? 0;
            $advertisement->window          = $request->window;
            $advertisement->min             = $request->min;
            $advertisement->max             = $request->max;
            $advertisement->details         = $request->details;
            $advertisement->save();

            $url .= "?step=three";
            return redirect($url);

        } elseif ($request->step == 3) {
            $advertisement->terms  = $request->terms;
            $advertisement->status = $advertisement->status == Status::ADVERTISEMENT_DRAFT ? Status::ENABLE : $advertisement->status;
            $advertisement->save();
        }

        $advertisement->save();
        $notify[] = ['success', $message];
        return to_route('user.advertisement.index')->withNotify($notify);
    }

    public function updateStatus($id)
    {
        $advertisement = Advertisement::where('user_id', auth()->id())->findOrFail($id);

        if ($advertisement->status == Status::ENABLE) {
            $advertisement->status = Status::DISABLE;
            $notify[]              = ['success', 'Advertisement deactivated successfully'];
        } else {
            $advertisement->status = Status::ENABLE;
            $notify[]              = ['success', 'Advertisement activated successfully'];
        }

        $advertisement->save();
        return back()->withNotify($notify);
    }

    public function reviews($id)
    {
        $pageTitle = 'Feedbacks';
        $reviews   = Review::where('advertisement_id', $id)->where('to_id', auth()->id())->with(['user'])->paginate(getPaginate());
        return view('Template::user.advertisement.reviews', compact('pageTitle', 'reviews'));
    }

    protected function checkData($request, $id)
    {
        if (!$id) {
            $isPermitted = $this->checkAdLimit();
            if (!$isPermitted) {
                return ['error', 'You have reached the maximum limit of creating advertisement'];
            }
        }
        $crypto = CryptoCurrency::active()->where('id', $request->crypto_id)->first();
        $fiat   = FiatCurrency::active()->where('id', $request->fiat_id)->first();

        if (!$crypto) {
            return ['error', 'Crypto currency not found or disabled'];
        }

        if (!$fiat) {
            return ['error', 'Fiat currency not found or disabled'];
        }

        $request->merge([
            'crypto' => $crypto,
            'fiat'   => $fiat,
        ]);

        if (getRate($request) <= 0) {
            return ['error', 'Price Equation must be positive greater than zero'];
        }

        return ['success'];
    }

    protected function checkAdLimit()
    {
        $user           = auth()->user();
        $isPermitted    = true;
        $completedTrade = $user->completed_trade;
        $createdAd      = Advertisement::where('user_id', $user->id)->count();
        $limitCount     = AdLimit::count();

        if ($limitCount == 1) {
            $limit = AdLimit::first();
        } elseif ($limitCount > 1) {
            $limit = AdLimit::where('completed_trade', '<=', $completedTrade)->orderBy('completed_trade', 'DESC')->first();
        } else {
            $limit = null;
        }

        if ($limit && $completedTrade < $limit->completed_trade) {
            $isPermitted = false;
        }

        if ($limit && $createdAd >= $limit->ad_limit) {
            $isPermitted = false;
        }

        return $isPermitted;
    }

    public function delete($id)
    {
        $advertisement = Advertisement::withoutGlobalScope('ignoreDraft')->where('status', Status::ADVERTISEMENT_DRAFT)->where('user_id', auth()->id())->findOrFail($id);
        $advertisement->delete();

        $notify[] = ['success', 'Advertisement deleted successfully'];
        return back()->withNotify($notify);
    }
}
