<?php

use App\Constants\Status;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Lib\GoogleAuthenticator;
use App\Models\Advertisement;
use App\Models\CommissionLog;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Notify\Notify;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laramin\Utility\VugiChugi;

function systemDetails()
{
    $system['name']          = 'SquarePay';
    $system['version']       = '3.0';
    $system['build_version'] = '5.0.3';
    return $system;
}

function slug($string)
{
    return Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0) {
        return 0;
    }

    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters       = '1234567890';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function activeTemplate($asset = false)
{
    $template = session('template') ?? gs('active_template');
    if ($asset) {
        return 'assets/templates/' . $template . '/';
    }

    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $template = session('template') ?? gs('active_template');
    return $template;
}

function siteLogo($type = null)
{
    $name = $type ? "/logo_$type.png" : '/logo.png';
    return getImage(getFilePath('logo_icon') . $name);
}
function siteFavicon()
{
    return getImage(getFilePath('logo_icon') . '/favicon.png');
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }

    return $printAmount;
}

function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://api.qrserver.com/v1/create-qr-code/?data=$wallet&size=300x300&ecc=m";
}

function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}

function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}

function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}

function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}

function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}

function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website']      = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url                   = VugiChugi::gttmp() . systemDetails()['name'];
    $response              = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}

function getPageSections($arr = false)
{
    $jsonUrl  = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}

function getImage($image, $size = null, $avatar = false)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    if ($avatar) {
        return asset('assets/images/avatar.png');
    }
    return asset('assets/images/default.png');
}

function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true, $pushImage = null)
{
    $globalShortCodes = [
        'site_name'       => gs('site_name'),
        'site_currency'   => gs('cur_text'),
        'currency_symbol' => gs('cur_sym'),
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify               = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes   = $shortCodes;
    $notify->user         = $user;
    $notify->createLog    = $createLog;
    $notify->pushImage    = $pushImage;
    $notify->userColumn   = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = null)
{
    if (!$paginate) {
        $paginate = gs('paginate_number');
    }
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}

function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3) {
        $class = 'side-menu--open';
    } elseif ($type == 2) {
        $class = 'sidebar-submenu__open';
    } else {
        $class = 'active';
    }

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }

        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param)) {
                return $class;
            } else {
                return;
            }

        }
        return $class;
    }
}

function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null)
{
    $fileManager           = new FileManager($file);
    $fileManager->path     = $location;
    $fileManager->size     = $size;
    $fileManager->old      = $old;
    $fileManager->thumb    = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A')
{
    if (!$date) {
        return '-';
    }
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{

    $templateName = activeTemplateName();
    if ($singleQuery) {
        $content = Frontend::where('tempname', $templateName)->where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::where('tempname', $templateName);
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode  = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = Status::YES;
        $user->save();
        return true;
    } else {
        return false;
    }
}

function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path     = str_replace($basePath, '', $url);
    return $path;
}

function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}

function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}

function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function gs($key = null)
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key) {
        return @$general->$key;
    }

    return $general;
}
function isImage($string)
{
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    $fileExtension     = pathinfo($string, PATHINFO_EXTENSION);
    if (in_array($fileExtension, $allowedExtensions)) {
        return true;
    } else {
        return false;
    }
}

function isHtml($string)
{
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}

function convertToReadableSize($size)
{
    preg_match('/^(\d+)([KMG])$/', $size, $matches);
    $size = (int) $matches[1];
    $unit = $matches[2];

    if ($unit == 'G') {
        return $size . 'GB';
    }

    if ($unit == 'M') {
        return $size . 'MB';
    }

    if ($unit == 'K') {
        return $size . 'KB';
    }

    return $size . $unit;
}

function frontendImage($sectionName, $image, $size = null, $seo = false)
{
    if ($seo) {
        return getImage('assets/images/frontend/' . $sectionName . '/seo/' . $image, $size);
    }
    return getImage('assets/images/frontend/' . $sectionName . '/' . $image, $size);
}

function ordinal($number)
{
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
        return $number . 'th';
    } else {
        return $number . $ends[$number % 10];
    }

}

function getRateAttributeForApp($data)
{
    return ($data->fiat->code . '/' . $data->crypto->code);
}

function levelCommission($user, $amount, $cryptoId, $trx, $commissionType = '')
{
    $tempUser = $user;
    $i        = 1;
    $level    = Referral::where('commission_type', $commissionType)->count();

    while ($i <= $level) {
        $referer = $tempUser->refBy;

        if (!$referer) {
            break;
        }

        $userWallet = Wallet::where('user_id', $referer->id)->where('crypto_currency_id', $cryptoId)->first();
        $commission = Referral::where('commission_type', $commissionType)->where('level', $i)->first();

        if (!$userWallet || !$commission) {
            break;
        }

        $commissionAmount = ($amount * $commission->percent) / 100;

        $userWallet->balance += $commissionAmount;
        $userWallet->save();

        $transactions[] = [
            'user_id'            => $referer->id,
            'crypto_currency_id' => $cryptoId,
            'amount'             => getAmount($commissionAmount, 8),
            'post_balance'       => $userWallet->balance,
            'charge'             => 0,
            'trx_type'           => '+',
            'details'            => 'Level ' . $i . ' referral commission From ' . $user->username,
            'remark'             => 'referral',
            'trx'                => $trx,
            'created_at'         => now(),
        ];

        $commissionLog[] = [
            'to_id'              => $referer->id,
            'from_id'            => $user->id,
            'crypto_currency_id' => $cryptoId,
            'level'              => $i,
            'post_balance'       => $userWallet->balance,
            'commission_amount'  => $commissionAmount,
            'trx_amo'            => $amount,
            'title'              => 'Level ' . $i . ' referral commission from ' . $user->username . ' for ' . $userWallet->crypto->code . ' Wallet',
            'type'               => $commissionType,
            'percent'            => $commission->percent,
            'trx'                => $trx,
            'created_at'         => now(),
        ];

        $tempUser = $referer;
        $i++;
    }

    if (isset($transactions)) {
        Transaction::insert($transactions);
    }

    if (isset($commissionLog)) {
        CommissionLog::insert($commissionLog);
    }
}

function getPublishStatus($ad, $maxLimit)
{
    if (!$ad->crypto->status || !$ad->fiatGateway->status || !$ad->fiat->status || !$ad->status) {
        return 0;
    }

    if ($ad->type == 1) {
        return 1;
    }

    if ($maxLimit >= $ad->min) {
        return 1;
    }

    return 0;
}

function getAdUnpublishReason($ad, $maxLimit, $admin = false)
{
    $message = [];

    if (!$ad->status) {
        $message['status'] = "This ad status is currently disabled";
    }

    if (!$ad->crypto->status) {
        $message['crypto'] = $ad->crypto->code . " crypto currency is currently disabled";
    }

    if (!$ad->fiat->status) {
        $message['fiat'] = $ad->fiat->code . " currency is currently disabled";
    }

    if (!$ad->fiatGateway->status) {
        $message['fiat_gateway'] = $ad->fiatGateway->name . " currency is currently disabled";
    }

    if ($maxLimit < $ad->min) {
        $message['limit'] = "You do not have the exact amount of maximum limit on your wallet";

        if ($admin) {
            $message['limit'] = "Advertiser does not have the exact amount of maximum limit on his wallet";
        }
    }

    return $message;
}

function getMaxLimit($wallets, $ad)
{
    $maxLimit = $ad->max;

    if ($ad->type == 2) {
        $userWallet = $wallets->where('crypto_currency_id', $ad->crypto_currency_id)->first();
        $rate       = getRate($ad);
        $userMax    = $userWallet->balance * $rate;
        $maxLimit   = $ad->max < $userMax ? $ad->max : $userMax;
    }

    return $maxLimit;
}

function getRate($data)
{
    $type       = $data->type;
    $cryptoRate = $data->crypto->rate;
    $fiatRate   = $data->fiat->rate;
    $margin     = $data->margin;

    $fixed  = $data->fixed_price ?? 0;
    $amount = $cryptoRate * $fiatRate;

    if ($fixed > 0) {
        $rate = $fixed;
    } else {
        $percentValue = $amount * $margin / 100;
        $rate         = $type == 1 ? $amount - $percentValue : $amount + $percentValue;
    }

    if ($rate > 0) {
        return round($rate, 2);
    } else {
        return floatval($rate);
    }

}

function avgTradeSpeed($ad)
{
    if ($ad->completed_trade) {
        return round($ad->total_min / $ad->completed_trade) . ' ' . trans('Minutes');
    }
    return trans('No trades yet');
}

function getReferees($user, $maxLevel, $data = [], $depth = 1, $layer = 0)
{
    if ($user->allReferrals->count() > 0 && $maxLevel > 0) {
        foreach ($user->allReferrals as $under) {
            $i = 0;
            if ($i == 0) {
                $layer++;
            }
            $i++;

            $userData['id']       = $under->id;
            $userData['username'] = $under->username;
            $userData['image']    = getImage(getFilePath('userProfile') . '/' . @$under->image, null, true);
            $userData['level']    = $depth;
            $data[]               = $userData;
            if ($under->allReferrals->count() > 0 && $layer < $maxLevel) {
                $data = getReferees($under, $maxLevel, $data, $depth + 1, $layer);
            }
        }
    }
    return $data;
}

function adsQuery($id, $type)
{
    $operator = $type == 2 ? '+' : '-';
    $ads      = Advertisement::selectRaw('advertisements.*, users.username, wallets.balance, crypto_currencies.rate AS crypto_rate, crypto_currencies.code AS crypto_code, fiat_currencies.rate AS fiat_rate, fiat_currencies.code AS fiat_code, fiat_gateways.name AS gateway_name')
        ->selectRaw("IF(advertisements.fixed_price > 0, advertisements.fixed_price, (crypto_currencies.rate * fiat_currencies.rate) $operator (crypto_currencies.rate * fiat_currencies.rate * advertisements.margin / 100)) AS rate_value");

    if ($type == 2) {
        $ads->selectRaw('LEAST(advertisements.max, wallets.balance * IF(advertisements.fixed_price > 0, advertisements.fixed_price, (crypto_currencies.rate * fiat_currencies.rate) + (crypto_currencies.rate * fiat_currencies.rate * advertisements.margin / 100))) AS max_limit');
    }

    $ads->leftJoin('users', 'advertisements.user_id', '=', 'users.id')
        ->leftJoin('wallets', function ($join) use ($id) {
            $join->on('advertisements.user_id', '=', 'wallets.user_id')
                ->on('advertisements.crypto_currency_id', '=', 'wallets.crypto_currency_id')
                ->where('advertisements.crypto_currency_id', '=', $id);
        })
        ->leftJoin('crypto_currencies', 'advertisements.crypto_currency_id', '=', 'crypto_currencies.id')
        ->leftJoin('fiat_currencies', 'advertisements.fiat_currency_id', '=', 'fiat_currencies.id')
        ->leftJoin('fiat_gateways', 'advertisements.fiat_gateway_id', '=', 'fiat_gateways.id')
        ->where('advertisements.type', $type)
        ->where('advertisements.status', 1)
        ->where('crypto_currencies.status', 1)
        ->where('fiat_currencies.status', 1)
        ->where('fiat_gateways.status', 1)
        ->where('crypto_currencies.id', $id);

    if (strtolower(activeTemplateName()) == 'elite') {
        $ads->withCount(['tradeRequests as total_trade', 'tradeRequests' => function ($q) {
            $q->completed();
        }]);
    }

    if ($type == 2) {
        $ads->having('advertisements.min', '<=', DB::raw('max_limit'));
    }

    return $ads;
}
