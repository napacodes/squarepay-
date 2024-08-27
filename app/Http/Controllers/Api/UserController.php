<?php

namespace App\Http\Controllers\Api;

use App\Models\Form;
use App\Models\User;
use App\Models\Trade;
use App\Models\Wallet;
use App\Models\Referral;
use App\Constants\Status;
use App\Lib\FormProcessor;
use App\Models\DeviceToken;
use App\Models\FiatGateway;
use App\Models\Transaction;
use App\Models\CryptoWallet;
use Illuminate\Http\Request;
use App\Models\CommissionLog;
use App\Models\CryptoCurrency;
use App\Models\NotificationLog;
use App\Rules\FileTypeValidate;
use Illuminate\Validation\Rule;
use App\Lib\GoogleAuthenticator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function dashboard()
    {
        $this->insertNewCryptoWallets();

        $user                = auth()->user();
        $wallets             = Wallet::where('user_id', $user->id)
                                ->leftJoin('crypto_currencies','crypto_currencies.id','=','crypto_currency_id')
                                ->selectRaw("crypto_currencies.id as cryptoId, code as cryptoCode, balance, image as cryptoImage, (balance * crypto_currencies.rate) as balanceInUsd")
                                ->orderByRaw("wallets.id desc")
                                ->get();
        $basicQuery          = $user->advertisements();
        $totalBuyAd          = clone $basicQuery;
        $totalSellAd         = clone $basicQuery;
        $totalBuyAdCount     = $totalBuyAd->where('type', 1)->count();
        $totalSellAdCount    = $totalSellAd->where('type', 2)->count();
        $referralLink        = route('user.register', [auth()->user()->username]);
        $runningTradeCount   = $this->getTradeData('running');
        $completedTradeCount = $this->getTradeData('completed');

        $advertisements = $basicQuery->active()
            ->latest()
            ->limit(10)
            ->whereHas('crypto', function ($crypto) {
                return $crypto->active();
            })
            ->whereHas('fiatGateway', function ($fiatGateway) {
                return $fiatGateway->active();
            })
            ->whereHas('fiatGateway', function ($fiatGateway) {
                return $fiatGateway->active();
            })
            ->with(['crypto', 'fiatGateway', 'fiat'])
            ->get();


        $data = [];

        foreach ($advertisements as $ad) {
            $maxLimit    = getMaxLimit($ad->user->wallets, $ad);
            $isPublished = getPublishStatus($ad, $maxLimit);
            $advertise   = [];

            if ($isPublished) {
                $advertise['id']                 = $ad->id;
                $advertise['crypto_code']        = $ad->crypto->code;
                $advertise['crypto_image']       = $ad->crypto->image;
                $advertise['fiat_gateway']       = $ad->fiatGateway->name;
                $advertise['fiat_gateway_image'] = $ad->fiatGateway->image;
                $advertise['rate']               = strval(getRate($ad));
                $advertise['rate_attribute']     = getRateAttributeForApp($ad);
                $advertise['window']             = $ad->window . ' Minutes';
                $advertise['status']             = $ad->status ? 'Enabled' : 'Disabled';
                $advertise['fixed_margin']       = strip_tags($ad->marginValue);
                $advertise['type']               = $ad->type == 1 ? 'Buy' : 'Sell';
                $data[] = $advertise;
            }
        }

        $user->image = getImage(getFilePath('userProfile') . '/' . $user->image, null, true);

        $notify[] = 'User dashboard';
        return response()->json([
            'remark' => 'user_dashboard',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'gateway_image_path'    => getFilePath('gateway'),
                'wallets'               => $wallets,
                'total_buy_add'         => $totalBuyAdCount,
                'total_sell_add'        => $totalSellAdCount,
                'running_trade_count'   => $runningTradeCount,
                'completed_trade_count' => $completedTradeCount,
                'crypto_image_path'     => getFilePath('crypto'),
                'user_info'             => $user,
                'ads'                   => $data,
                'referral_link'         => $referralLink,
            ]
        ]);
    }

    protected function insertNewCryptoWallets()
    {
        $walletId  = Wallet::where('user_id', auth()->id())->pluck('crypto_currency_id');
        $cryptos   = CryptoCurrency::latest()->whereNotIn('id', $walletId)->pluck('id');
        $data      = [];

        foreach ($cryptos as $id) {
            $wallet['crypto_currency_id'] = $id;
            $wallet['user_id']            = auth()->id();
            $wallet['balance']            = 0;
            $data[]                       = $wallet;
        }

        if (!empty($data)) {
            Wallet::insert($data);
        }
    }
    
    protected function getTradeData($scope)
    {
        $trades = Trade::$scope()->where(function ($q) {
            $q->where('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->count();

        return $trades;
    }
    
    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            $notify[] = 'You\'ve already completed your profile';
            return response()->json([
                'remark'  => 'already_completed',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        $countryData  = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $validator = Validator::make($request->all(), [
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:users|min:6',
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user->country_code = $request->country_code;
        $user->mobile       = $request->mobile;
        $user->username     = $request->username;

        $user->address      = $request->address;
        $user->city         = $request->city;
        $user->state        = $request->state;
        $user->zip          = $request->zip;
        $user->country_name = @$request->country;
        $user->dial_code    = $request->mobile_code;

        $user->profile_complete = Status::YES;
        $user->save();

        $notify[] = 'Profile completed successfully';
        return response()->json([
            'remark'  => 'profile_completed',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'user' => $user,
            ],
        ]);
    }

    public function kycForm()
    {
        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = 'Your KYC is under review';
            return response()->json([
                'remark'  => 'under_review',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = 'You are already KYC verified';
            return response()->json([
                'remark'  => 'already_verified',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        $form     = Form::where('act', 'kyc')->first();
        $notify[] = 'KYC field is below';
        return response()->json([
            'remark'  => 'kyc_form',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'form' => $form->form_data,
            ],
        ]);
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act', 'kyc')->first();
        if (!$form) {
            $notify[] = 'Invalid KYC request';
            return response()->json([
                'remark'  => 'invalid_request',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);

        $validator = Validator::make($request->all(), $validationRule);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }
        $user = auth()->user();
        foreach (@$user->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $userData = $formProcessor->processFormData($request, $formData);

        $user->kyc_data             = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv                   = Status::KYC_PENDING;
        $user->save();

        $notify[] = 'KYC data submitted successfully';
        return response()->json([
            'remark'  => 'kyc_submitted',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);

    }

    public function depositHistory()
    {
        $deposits = auth()->user()->deposits()->searchable(['trx']);

        if (request()->crypto_id) {
            $deposits = $deposits->where('crypto_currency_id', request()->crypto_id);
        }

        $deposits = $deposits->with(['crypto'])->orderBy('id', 'desc')->apiQuery();
        $cryptos  = CryptoCurrency::orderBy('name')->get();

        $notify[] = 'Deposit data';

        return response()->json([
            'remark'  => 'deposits',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'crypto_image_path' => getFilePath('crypto'),
                'deposits'          => $deposits,
                'cryptos'           => $cryptos,
            ],
        ]);
    }

    public function transactions(Request $request)
    {
        $remarks      = Transaction::distinct('remark')->whereNotNull('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id())->where('crypto_currency_id', '!=', null);

        if ($request->search) {
            $transactions = $transactions->where('trx', $request->search);
        }

        if ($request->type) {
            $type = $request->type == 'plus' ? '+' : '-';
            $transactions = $transactions->where('trx_type', $type);
        }

        if ($request->crypto_id) {
            $transactions = $transactions->where('crypto_currency_id', $request->crypto_id);
        }

        if ($request->remark) {
            $transactions = $transactions->where('remark', $request->remark);
        }

        $transactions = $transactions->with(['crypto'])->orderBy('id', 'desc')->paginate(getPaginate());
        $cryptos      = CryptoCurrency::orderBy('name')->get();
        $notify[]     = 'Transactions data';

        return response()->json([
            'remark' => 'transactions',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'crypto_image_path' => getFilePath('crypto'),
                'transactions'     => $transactions,
                'remarks'          => $remarks,
                'cryptos'          => $cryptos,
            ]
        ]);
    }

    public function submitProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname'  => 'required',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required'  => 'The last name field is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();

        if ($request->hasFile('image')) {
            try {
                $old         = $user->image;
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = 'Couldn\'t upload your image';
                return response()->json([
                    'remark'  => 'validation_error',
                    'status'  => 'error',
                    'message' => ['error' => $notify],
                ]);
            }
        }

        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;

        $user->address = $request->address;
        $user->city    = $request->city;
        $user->state   = $request->state;
        $user->zip     = $request->zip;

        $user->save();

        $notify[] = 'Profile updated successfully';
        return response()->json([
            'remark'  => 'profile_updated',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function submitPassword(Request $request)
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', $passwordValidation],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password       = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = 'Password changed successfully';
            return response()->json([
                'remark'  => 'password_changed',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        } else {
            $notify[] = 'The password doesn\'t match!';
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
    }

    public function addDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            $notify[] = 'Token already exists';
            return response()->json([
                'remark'  => 'token_exists',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::YES;
        $deviceToken->save();

        $notify[] = 'Token saved successfully';
        return response()->json([
            'remark'  => 'token_saved',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function show2faForm()
    {
        $ga        = new GoogleAuthenticator();
        $user      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $notify[]  = '2FA Qr';
        return response()->json([
            'remark'  => '2fa_qr',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'secret'      => $secret,
                'qr_code_url' => $qrCodeUrl,
            ],
        ]);
    }

    public function create2fa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required',
            'code'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code, $request->secret);
        if ($response) {
            $user->tsc = $request->secret;
            $user->ts  = Status::ENABLE;
            $user->save();

            $notify[] = 'Google authenticator activated successfully';
            return response()->json([
                'remark'  => '2fa_qr',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        } else {
            $notify[] = 'Wrong verification code';
            return response()->json([
                'remark'  => 'wrong_verification',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
    }

    public function disable2fa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts  = Status::DISABLE;
            $user->save();
            $notify[] = 'Two factor authenticator deactivated successfully';
            return response()->json([
                'remark'  => '2fa_qr',
                'status'  => 'success',
                'message' => ['success' => $notify],
            ]);
        } else {
            $notify[] = 'Wrong verification code';
            return response()->json([
                'remark'  => 'wrong_verification',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
    }

    public function pushNotifications()
    {
        $notifications = NotificationLog::where('user_id', auth()->id())->where('sender', 'firebase')->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[]      = 'Push notifications';
        return response()->json([
            'remark'  => 'notifications',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'notifications' => $notifications,
            ],
        ]);
    }

    public function pushNotificationsRead($id)
    {
        $notification = NotificationLog::where('user_id', auth()->id())->where('sender', 'firebase')->find($id);
        if (!$notification) {
            $notify[] = 'Notification not found';
            return response()->json([
                'remark'  => 'notification_not_found',
                'status'  => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        $notify[]                = 'Notification marked as read successfully';
        $notification->user_read = 1;
        $notification->save();

        return response()->json([
            'remark'  => 'notification_read',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function userInfo()
    {
        $notify[] = 'User information';
        return response()->json([
            'remark'  => 'user_info',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'user' => auth()->user(),
            ],
        ]);
    }

    public function deleteAccount()
    {
        $user           = auth()->user();
        $user->username = 'deleted_' . $user->username;
        $user->email    = 'deleted_' . $user->email;
        $user->save();

        $user->tokens()->delete();

        $notify[] = 'Account deleted successfully';
        return response()->json([
            'remark'  => 'account_deleted',
            'status'  => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function wallets()
    {
        $wallets  = Wallet::where('user_id', auth()->id())->with('crypto')->latest()->get();
        $notify[] = 'User wallets';

        return response()->json([
            'remark'  => 'wallets',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'wallets'           => $wallets,
                'crypto_image_path' => getFilePath('crypto'),
            ],
        ]);
    }

    public function singleWallet($id)
    {
        $crypto = CryptoCurrency::find($id);

        if (!$crypto) {
            return response()->json([
                'remark'  => 'crypto_error',
                'status'  => 'error',
                'message' => ['error' => 'Crypto currency not found'],
            ]);
        }

        $basicQuery            = CryptoWallet::where('user_id', auth()->id())->where('crypto_currency_id', $crypto->id);
        $totalAddress          = clone $basicQuery;
        $totalCryptoWallet     = clone $basicQuery;
        $totalAddressCount     = $totalAddress->count();
        $cryptoWalletAddresses = $totalCryptoWallet->latest()->paginate(getPaginate());

        $notify[] = 'User receiving wallets';

        return response()->json([
            'remark'  => 'wallets_address',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'total_address_count'     => $totalAddressCount,
                'crypto_wallet_addresses' => $cryptoWalletAddresses,
                'crypto'                  => $crypto,
                'crypto_image_path'       => getFilePath('crypto'),
            ],
        ]);
    }

    public function publicProfile(Request $request, $username)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = User::where('username', $username)->active()->first();

        if (!$user) {
            return response()->json([
                'remark'  => 'user_error',
                'status'  => 'error',
                'message' => ['error' => 'User not found or banned'],
            ]);
        }

        $basicQuery = $user->advertisements()->active()
            ->whereHas('fiat', function ($q) {
                $q->active();
            })->whereHas('crypto', function ($q) {
            $q->active();
        })
            ->whereHas('fiatGateway', function ($q) {
                $q->active();
            });

        $buyAds        = clone $basicQuery;
        $sellAds       = clone $basicQuery;
        $latestBuyAds  = $buyAds->where('type', 2)->active();
        $latestSellAds = $sellAds->where('type', 1)->active();

        if ($request->crypto_id) {
            $latestBuyAds  = $latestBuyAds->where('crypto_currency_id', $request->crypto_id);
            $latestSellAds = $latestSellAds->where('crypto_currency_id', $request->crypto_id);
        }

        if ($request->fiat_gateway_id) {
            $latestBuyAds  = $latestBuyAds->where('fiat_gateway_id', $request->fiat_gateway_id);
            $latestSellAds = $latestSellAds->where('fiat_gateway_id', $request->fiat_gateway_id);
        }

        if ($request->amount) {
            $latestBuyAds  = $latestBuyAds->where('min', '<=', $request->amount)->where('max', '>=', $request->amount);
            $latestSellAds = $latestSellAds->where('min', '<=', $request->amount)->where('max', '>=', $request->amount);
        }

        $latestBuyAds  = $latestBuyAds->latest()->with(['crypto', 'user.wallets', 'fiatGateway', 'fiat'])->paginate(getPaginate());
        $latestSellAds = $latestSellAds->latest()->with(['crypto', 'user.wallets', 'fiatGateway', 'fiat'])->paginate(getPaginate());

        $buyData  = [];
        $sellData = [];

        foreach ($latestBuyAds as $ad) {
            $maxLimit  = getMaxLimit($ad->user->wallets, $ad);
            $show      = $maxLimit >= $ad->min ? true : false;
            $advertise = [];

            if ($show) {
                $advertise['id']                 = $ad->id;
                $advertise['user_username']      = $ad->user->username;
                $advertise['user_id']            = $ad->user->id;
                $advertise['user_image']         = getImage(getFilePath('userProfile') . '/' . $ad->user->image, null, true);
                $advertise['fiat_gateway']       = $ad->fiatGateway->name;
                $advertise['fiat_gateway_image'] = $ad->fiatGateway->image;
                $advertise['rate']               = strval(getRate($ad));
                $advertise['rate_attribute']     = getRateAttributeForApp($ad);
                $advertise['window']             = $ad->window . ' Minutes';
                $advertise['max_limit']          = showAmount($ad->min) . ' - ' . showAmount($maxLimit) . ' ' . $ad->fiat->code;
                $advertise['avg_speed']          = avgTradeSpeed($ad);

                $buyData[] = $advertise;
            }
        }

        foreach ($latestSellAds as $ad) {
            $advertise['id']                 = $ad->id;
            $advertise['user_username']      = $ad->user->username;
            $advertise['user_id']            = $ad->user->id;
            $advertise['user_image']         = getImage(getFilePath('userProfile') . '/' . $ad->user->image, null, true);
            $advertise['fiat_gateway']       = $ad->fiatGateway->name;
            $advertise['fiat_gateway_image'] = $ad->fiatGateway->image;
            $advertise['rate']               = strval(getRate($ad));
            $advertise['rate_attribute']     = getRateAttributeForApp($ad);
            $advertise['window']             = $ad->window . ' Minutes';
            $advertise['max_limit']          = showAmount($ad->min) . ' - ' . showAmount($ad->max) . ' ' . $ad->fiat->code;
            $advertise['avg_speed']          = avgTradeSpeed($ad);

            $sellData[] = $advertise;
        }

        $allReviewCount      = $user->positiveFeedBacks->count() + $user->negativeFeedBacks->count();
        $positiveReviewCount = $user->positiveFeedBacks->count();
        $negativeReviewCount = $user->negativeFeedBacks->count();
        $cryptos             = CryptoCurrency::active()->orderBy('name')->get();
        $fiatGateways        = FiatGateway::active()->orderBy('name')->get();

        $notify[] = 'User public profile';

        return response()->json([
            'remark'  => 'public_profile',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'gateway_image_path'     => getFilePath('gateway'),
                'user'                   => $user,
                'buy_ads'                => $buyData,
                'sell_ads'               => $sellData,
                'all_review_count'       => $allReviewCount,
                'buy_ads_next_page_url'  => $latestBuyAds->nextPageUrl(),
                'sell_ads_next_page_url' => $latestSellAds->nextPageUrl(),
                'positive_review_count'  => $positiveReviewCount,
                'negative_review_count'  => $negativeReviewCount,
                'cryptos'                => $cryptos,
                'fiat_gateways'          => $fiatGateways,
            ],
        ]);
    }

    public function referralCommissions()
    {
        $referralLogs = CommissionLog::where('to_id', auth()->id())->with('bywho', 'crypto')->latest()->paginate(getPaginate());
        $notify[]     = 'User referral commissions';

        return response()->json([
            'remark'  => 'referral_commissions',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'crypto_image_path' => getFilePath('crypto'),
                'referral_logs'     => $referralLogs,
            ],
        ]);
    }

    public function myRef()
    {
        $maxLevel = Referral::max('level');
        $referees = getReferees(auth()->user(), $maxLevel);

        $notify[] = 'Refereed users';

        return response()->json([
            'remark'  => 'referred_users',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'referral_users' => $referees,
            ],
        ]);
    }

}
