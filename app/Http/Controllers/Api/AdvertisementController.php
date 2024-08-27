<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\CryptoCurrency;
use App\Models\FiatCurrency;
use App\Models\FiatGateway;
use App\Models\AdLimit;
use App\Models\PaymentWindow;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvertisementController extends Controller {

    public function search(Request $request) {
        $cryptoCurrency = CryptoCurrency::where('id', $request->crypto_id)->active()->first();
        if (!$cryptoCurrency) {
            return response()->json([
                'remark' => 'crypto_error',
                'status' => 'error',
                'message' => ['error' => 'Crypto currency not found'],
            ]);
        }

        $advertisements = $this->adsQuery($cryptoCurrency->id, $request);
        $data = [];

        foreach ($advertisements as $ad) {
            $advertise = [];
            $advertise['id']                 = $ad->id;
            $advertise['user_username']      = $ad->username;
            $advertise['user_image']         = getImage(getFilePath('userProfile') . '/' . $ad->user_image, null, true);
            $advertise['fiat_gateway']       = $ad->gateway_name;
            $advertise['fiat_gateway_image'] = getImage(getFilePath('gateway') . '/' . $ad->gateway_image, getFileSize('gateway'));
            $advertise['rate']               = strval(showAmount($ad->rate_value));
            $advertise['rate_attribute']     = getRateAttributeForApp($ad);
            $advertise['window']             = $ad->window . ' Minutes';
            $advertise['max_limit']          = showAmount($ad->min) . ' - ' . showAmount($ad->max_limit) . ' ' . $ad->fiat_code;
            $advertise['avg_speed']          = avgTradeSpeed($ad);
            $data[] = $advertise;
        }

        $response['type']               = $request->type == 2 ? 'Buy' : 'Sell';
        $response['ads']                = $data;
        $response['prev_page_url']      = $advertisements->previousPageUrl();
        $response['next_page_url']      = $advertisements->nextPageUrl();
        $notify[]                       = 'Advertisement search result';

        return response()->json([
            'remark' => 'ad_search_result',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data'   => $response
        ]);
    }

    public function index() {
        $adCount        = Advertisement::where('user_id', auth()->id())->count();
        $advertisements = Advertisement::where('user_id', auth()->id())->latest()
            ->with(['user.wallets'])->paginate(getPaginate());

        $notify[]       = 'Advertisement of user';

        $data = [];

        foreach ($advertisements as $ad) {
            $maxLimit    = getMaxLimit($ad->user->wallets, $ad);
            $isPublished = getPublishStatus($ad, $maxLimit);
            $advertise   = [];

            $advertise['id']                 = $ad->id;
            $advertise['crypto_code']        = $ad->crypto->code;
            $advertise['crypto_image']       = getImage(getFilePath('crypto') . '/' . $ad->crypto->image, getFileSize('crypto'));
            $advertise['fiat_gateway']       = $ad->fiatGateway->name;
            $advertise['fiat_gateway_image'] = getImage(getFilePath('gateway') . '/' . $ad->fiatGateway->image, getFileSize('gateway'));
            $advertise['rate']               = strval(getRate($ad));
            $advertise['rate_attribute']     = getRateAttributeForApp($ad);
            $advertise['window']             = $ad->window . ' Minutes';
            $advertise['status']             = $ad->status ? 'Enabled' : 'Disabled';
            $advertise['publish']            = $isPublished;
            $advertise['unpublish_reason']   = array_values(getAdUnpublishReason($ad, $maxLimit));
            $advertise['fixed_margin']       = strip_tags($ad->marginValue);
            $advertise['type']               = $ad->type == 1 ? 'Buy' : 'Sell';

            $data[] = $advertise;
        }

        return response()->json([
            'remark' => 'user_all_ad',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'ad_count'          => $adCount,
                'ads'               => $data,
                'prev_page_url'     => $advertisements->previousPageUrl(),
                'next_page_url'     => $advertisements->nextPageUrl(),
            ]
        ]);
    }

    public function new() {
        $isPermitted    = $this->checkAdLimit();

        if (!$isPermitted) {
            return response()->json([
                'remark' => 'ad_limit',
                'status' => 'error',
                'message' => ['error' => 'You have reached the maximum limit for advertising. Complete more trade to publish more advertisement'],
            ]);
        }

        $cryptos        = CryptoCurrency::active()->orderBy('name')->get();
        $paymentWindows = PaymentWindow::orderBy('minute')->get();
        $fiatGateways   = FiatGateway::getGateways();

        $notify[] = 'New advertisement';

        $sellChargeMessage = $this->sellChargeMessage();

        return response()->json([
            'remark' => 'new_ad_form',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'sell_charge_message' => $sellChargeMessage,
                'cryptos'            => $cryptos,
                'payment_windows'    => $paymentWindows,
                'fiat_gateways'      => $fiatGateways,
            ]
        ]);
    }


    protected function validation($request) {
        return Validator::make($request->all(), [
            'type'            => 'required|in:1,2',
            'crypto_id'       => 'required|integer:gt:0',
            'fiat_gateway_id' => 'required|integer:gt:0',
            'fiat_id'         => 'required|integer:gt:0',
            'price_type'      => 'required|in:1,2',
            'margin'          => 'required_if:price_type,1|numeric|min:0',
            'fixed_price'     => 'required_if:price_type,2|numeric|gt:0',
            'window'          => 'required|integer|gt:0',
            'min'             => 'required|numeric|gt:0',
            'max'             => 'required|numeric|gt:min',
            'details'         => 'required',
            'terms'           => 'required'
        ]);
    }

    public function store(Request $request, $id = 0) {
        $validator = $this->validation($request);

        if ($validator->fails()) {
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $check = $this->checkData($request, $id);

        if ($check[0] == 'error') {
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $check],
            ]);
        }

        if ($id) {
            $advertisement = Advertisement::where('user_id', auth()->id())->findOrFail($id);
            $remark        = 'advertisement_updated';
            $notify        = 'Your advertisement updated successfully';
        } else {
            $advertisement          = new Advertisement();
            $advertisement->user_id = auth()->id();
            $remark                 = 'advertisement_stored';
            $notify                 = 'Your advertisement added successfully';
        }

        $advertisement->type               = $request->type;
        $advertisement->crypto_currency_id = $request->crypto_id;
        $advertisement->fiat_gateway_id    = $request->fiat_gateway_id;
        $advertisement->fiat_currency_id   = $request->fiat_id;
        $advertisement->margin             = $request->margin ? $request->margin : 0;
        $advertisement->fixed_price        = $request->fixed_price ? $request->fixed_price : 0;
        $advertisement->window             = $request->window;
        $advertisement->min                = $request->min;
        $advertisement->max                = $request->max;
        $advertisement->details            = $request->details;
        $advertisement->terms              = $request->terms;
        $advertisement->save();

        return response()->json([
            'remark' => $remark,
            'status' => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    public function edit($id) {
        $advertisement = Advertisement::where('user_id', auth()->id())->find($id);

        if (!$advertisement) {
            return response()->json([
                'remark' => 'ad_not_found',
                'status' => 'error',
                'message' => ['error' => 'Advertisement not found'],
            ]);
        }

        $cryptos        = CryptoCurrency::orderBy('name')->get();
        $paymentWindows = PaymentWindow::orderBy('minute')->get();

        $fiatGateways   = FiatGateway::get()->map(function ($gateway) {
            $fiat = FiatCurrency::whereIn('id', $gateway->code)->get();
            $gateway['fiat'] = $fiat;
            return $gateway;
        });

        $notify[] = 'Edit advertisement';
        $sellChargeMessage = $this->sellChargeMessage();

        return response()->json([
            'remark' => 'edit_ad_form',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'sell_charge_message' => $sellChargeMessage,
                'ad'                 => $advertisement,
                'cryptos'            => $cryptos,
                'payment_windows'    => $paymentWindows,
                'fiat_gateways'      => $fiatGateways,
            ]
        ]);
    }

    public function reviews($id) {
        $reviews = Review::where('advertisement_id', $id)->where('to_id', auth()->id())->with(['user'])->paginate(getPaginate());
        $data    = [];

        foreach ($reviews as $review) {
            $reviewData['user_image']   = getImage(getFilePath('userProfile') . '/' . $review->user->image, null, true);
            $reviewData['user_name']    = $review->user->username;
            $reviewData['user_id']      = $review->user_id;
            $reviewData['type']         = $review->type;
            $reviewData['feedback']     = $review->feedback;
            $reviewData['created_at']   = $review->created_at;
            $data[] = $reviewData;
        }

        $notify[] = 'Feedbacks by ad';

        return response()->json([
            'remark'  => 'feedbacks_by_ad',
            'status'  => 'success',
            'message' => ['success' => $notify],
            'data'    => [
                'reviews'       => $data,
                'next_page_url' => $reviews->nextPageUrl(),
            ]
        ]);
    }

    function statusUpdate($id) {
        $advertisement = Advertisement::where('user_id', auth()->id())->find($id);

        if (!$advertisement) {
            return response()->json([
                'remark' => 'ad_not_found',
                'status' => 'error',
                'message' => ['error' => 'Advertisement not found'],
            ]);
        }

        if ($advertisement->status == Status::ENABLE) {
            $advertisement->status = Status::DISABLE;
            $notify = 'Advertisement deactivated successfully';
        } else {
            $advertisement->status = Status::ENABLE;
            $notify = 'Advertisement activated successfully';
        }

        $advertisement->save();

        return response()->json([
            'remark' => 'ad_status_changed',
            'status' => 'success',
            'message' => ['success' => $notify],
        ]);
    }

    protected function checkAdLimit() {
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

    protected function checkData($request, $id) {
        if (!$id) {
            $isPermitted  = $this->checkAdLimit();

            if (!$isPermitted) {
                return ['error', 'You have reached the maximum limit of creating advertisement'];
            }
        }

        $crypto      = CryptoCurrency::query();
        $fiatGateway = FiatGateway::query();
        $fiat        = FiatCurrency::query();

        if (!$id) {
            $crypto      = $crypto->active();
            $fiatGateway = $fiatGateway->active();
            $fiat        = $fiat->active();
        }

        $crypto = $crypto->where('id', $request->crypto_id)->first();

        if (!$crypto) {
            return ['error', 'Crypto currency not found or disabled'];
        }

        $fiatGateway = $fiatGateway->where('id', $request->fiat_gateway_id)->first();

        if (!$fiatGateway) {
            return ['error', 'Fiat gateway not found or disabled'];
        }

        $fiat = $fiat->where('id', $request->fiat_id)->first();

        if (!$fiat) {
            return ['error', 'Fiat currency not found or disabled'];
        }

        $request->merge([
            'crypto' => $crypto,
            'fiat' => $fiat,
        ]);

        if (getRate($request) <= 0) {
            return ['error', 'Price Equation must be positive greater than zero'];
        }

        return ['success'];
    }

    protected function sellChargeMessage() {
        $general = gs();
        return $general->trade_charge > 0 ? 'For selling ' . getAmount($general->trade_charge) . '% will be charged for each completed trade' : '';
    }

    protected function adsQuery($id, $request) {

        $type           = $request->type == 1 ? 2 : 1;
        $fiatGateway    = $request->fiat_gateway_id;
        $fiat           = $request->fiat_id;
        $countryCode    = $request->country_code;
        $amount         = $request->amount;
        $operator       = $type == 2 ? '+' : '-';

        $ads            = Advertisement::selectRaw('advertisements.*, users.username, users.image as user_image, wallets.balance, crypto_currencies.rate AS crypto_rate, crypto_currencies.code AS crypto_code, fiat_currencies.rate AS fiat_rate, fiat_currencies.code AS fiat_code, fiat_gateways.name AS gateway_name, fiat_gateways.image as gateway_image')
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
        if ($type == 2) {
            $ads->having('advertisements.min', '<=', DB::raw('max_limit'));
        }

        if ($fiatGateway) {
            $fiatGatewayCheck = FiatGateway::where('id', $fiatGateway)->active()->first();
            $ads->where('advertisements.fiat_gateway_id', @$fiatGatewayCheck->id);
        }

        if ($fiat) {
            $fiatCheck        = FiatCurrency::where('id', $fiat)->active()->first();
            $ads->where('advertisements.fiat_currency_id', @$fiatCheck->id);
        }

        if ($countryCode != 'all') {
            $ads->whereHas('user', function ($q) use ($countryCode) {
                $q->active()->where('country_code', $countryCode);
            });
        }

        if ($amount) {
            $ads->where('advertisements.min', '<=', $amount)->where('advertisements.max', '>=', $amount);
        }

        $ads = $ads->orderBy('advertisements.id', 'desc')->paginate(getPaginate());

        return $ads;
    }
}