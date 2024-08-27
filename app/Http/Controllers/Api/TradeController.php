<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Chat;
use App\Models\Review;
use App\Models\Trade;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TradeController extends Controller
{

    public $finalAmount;

    public function index()
    {
        $runningTrades   = $this->getTradeData('running');
        $completedTrades = $this->getTradeData('completed');
        $notify[]        = 'User trades';


        return response()->json([
            'remark' => 'user_trades',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data'   => [
                'running_trades'    => $runningTrades,
                'completed_trades'  => $completedTrades,
                'user_image_path' => getFilePath('userProfile'),
                'gateway_image_path' => getFilePath('gateway'),
            ]
        ]);
    }

    public function details($uid)
    {
        $trade = Trade::where('uid', $uid)->where(function ($q) {
            $q->orWhere('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->with(['chats', 'buyer', 'seller', 'crypto', 'fiat', 'fiatGateway'])->first();

        if (!$trade) {
            return response()->json([
                'remark' => 'trade_error',
                'status' => 'error',
                'message' => ['error' => 'Trade not found'],
            ]);
        }

        $appendHeading = showAmount($trade->crypto_amount, 8) . ' ' . $trade->crypto->code . ' for ' . showAmount($trade->amount) . ' ' . $trade->fiat->code . ' via ' . $trade->fiatGateway->name;
        $buyerHeading  = 'Buying ' . $appendHeading;
        $sellerHeading = 'Selling ' . $appendHeading;;

        $subHeading    = 'Exchange Rate: ' . showAmount($trade->exchange_rate) . ' ' . getRateAttributeForApp($trade->advertisement);

        $buyerMszOne   = 'Please pay ' . showAmount($trade->amount) . ' ' . $trade->fiat->code . ' using ' . $trade->fiatGateway->name . ' E-Wallet';

        $buyerMszTwo   = showAmount($trade->crypto_amount, 8) . ' ' . $trade->advertisement->crypto->code . ' will be added to your wallet after seller confirmation about the payment.';

        $sellerMszOne  = 'Once the buyer has confirmed your payment then ' . showAmount($trade->crypto_amount, 8) . ' ' . $trade->advertisement->crypto->code . ' will be available for release';

        $sellerEndTime          = null;
        $sellerRemainingMinutes = null;
        $sellerCancelMsz        = null;
        $sellerDisputeMsz       = null;
        $buyerRemainingMinutes  = null;
        $buyerCancelMsz         = null;
        $buyerDisputeMsz        = null;
        $reportedMsz            = null;
    
        if ($trade->status == Status::TRADE_ESCROW_FUNDED) {
            $sellerEndTime          = $trade->created_at->addMinutes($trade->window);
            $sellerRemainingMinutes = abs($sellerEndTime->diffInMinutes(Carbon::now()));

            if ($sellerEndTime > Carbon::now()) {

                $buyerRemainingMinutes = $sellerRemainingMinutes;
                $sellerCancelMsz       = 'You can cancel this trade after following minutes';
                $buyerCancelMsz        = 'The seller can cancel this trade after following minutes. Please make the payment within that time and mark the trade as paid.';
            } else {

                $sellerRemainingMinutes = null;
                $buyerRemainingMinutes  = null;
                $buyerCancelMsz         = 'The seller can cancel this trade anytime. Please make the payment and mark the trade as paid';
            }
        }

        if ($trade->status == Status::TRADE_BUYER_SENT) {
    
            $sellerEndTime          = Carbon::parse($trade->paid_at)->addMinutes($trade->window);
            $sellerRemainingMinutes = abs($sellerEndTime->diffInMinutes(Carbon::now()));
            
            $buyerMszOne            = null;
            $buyerMszTwo            = null;
            $sellerMszOne           = null;

            if ($sellerEndTime > Carbon::now()) {
                $buyerRemainingMinutes = $sellerRemainingMinutes;
                $sellerDisputeMsz      = 'Buyer can dispute this trade after following minutes';
                $buyerDisputeMsz       = 'You can dispute this trade after following minutes';
            } else {

                $sellerRemainingMinutes = null;
                $buyerRemainingMinutes  = null;
                $sellerDisputeMsz       = 'Buyer can dispute this trade anytime. If you received the amount please release coins';
            }
        }

        if ($trade->status == Status::TRADE_CANCELED || $trade->status == Status::TRADE_COMPLETED || $trade->status == Status::TRADE_DISPUTED) {
            $sellerEndTime          = null;
            $sellerRemainingMinutes = null;
            $sellerCancelMsz        = null;
            $sellerDisputeMsz       = null;
            $buyerRemainingMinutes  = null;
            $buyerCancelMsz         = null;
            $buyerDisputeMsz        = null;
            $buyerMszOne            = null;
            $buyerMszTwo            = null;
            $sellerMszOne           = null;
            $reportedMsz            = null;
        }

        if ($trade->status == Status::TRADE_DISPUTED) {
            $reportedMsz = 'This trade is ' . $trade->details . ' Please wait For the system response.';
        }

        $chatPermission = 0;

        if (($trade->status == Status::TRADE_ESCROW_FUNDED) || ($trade->status == Status::TRADE_BUYER_SENT) || ($trade->status == Status::TRADE_DISPUTED)) {
            $chatPermission = 1;
        }

        $reviewIsPermitted = 0;

        if ($trade->status == Status::TRADE_COMPLETED && $trade->reviewed == Status::TRADE_ESCROW_FUNDED && $trade->advertisement->user_id != auth()->id()) {
            $reviewIsPermitted = 1;
        }

        $reviewCheckMsz    = null;

        $notify[] = 'Trade details';
        
        return response()->json([
            'remark' => 'trade_details',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'chat_file_path'      => getFilePath('chat_file'),
                'user_image_path'     => getFilePath('userProfile'),
                'favicon_image_path'  => siteFavicon(),
                'chat_permission'     => $chatPermission,
                'review_permission'   => $reviewIsPermitted,
                'review_msz'          => $reviewCheckMsz,
                'buyer_heading'       => $buyerHeading,
                'seller_heading'      => $sellerHeading,
                'sub_heading'         => $subHeading,
                'buyer_msz_one'        => $buyerMszOne,
                'buyer_msz_two'        => $buyerMszTwo,
                'seller_msz_one'       => $sellerMszOne,
                'seller_remaining_min' => $sellerRemainingMinutes,
                'seller_cancel_msz'    => $sellerCancelMsz,
                'seller_dispute_msz'   => $sellerDisputeMsz,
                'buyer_remaining_min'  => $buyerRemainingMinutes,
                'buyer_cancel_msz'     => $buyerCancelMsz,
                'buyer_dispute_msz'    => $buyerDisputeMsz,
                'reported_msz'         => $reportedMsz,
                'trade_details'        => $trade
            ]
        ]);
    }

    public function create($id)
    {
        $ad          = Advertisement::active()->with('user')->find($id);
        
        if (!$ad) {
            return response()->json([
                'remark' => 'ad_not_found',
                'status' => 'error',
                'message' => ['error' => 'Advertisement not found'],
            ]);
        }

        $maxLimit    = getMaxLimit($ad->user->wallets, $ad);
        $isTradeable = $this->checkIsTradeable($ad, $maxLimit, true);

        if ($isTradeable[0] == 'error') {
            $notify = $isTradeable;
            return response()->json([
                'remark' => 'trade_error',
                'status' => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        $titleOne = '';
        $titleTwo = 'A user whose username ' . $ad->user->username . ' wishes to ';

        if ($ad->type == 1) {
            $titleOne .= 'Sell ';
            $titleTwo .= 'buy ';
            $heading =  'Selling ' . $ad->crypto->name;
        } else {
            $titleOne .= 'Buy ';
            $titleTwo .= 'sell ';
            $heading =  'Buying ' . $ad->crypto->name;
        }


        $titleOne .= $ad->crypto->name . ' using ' . $ad->fiatGateway->name . ' with ' . $ad->fiat->name . ' (' . $ad->fiat->code . ')';
        $titleTwo .= $ad->crypto->name;

        if ($ad->type == 1) {
            $titleTwo .= ' from ';
        } else {
            $titleTwo .= ' to ';
        }

        $titleTwo .= 'you';

        $basicQuery          = Review::where('advertisement_id', $ad->id);
        $all                 = clone $basicQuery;
        $positive            = clone $basicQuery;
        $negative            = clone $basicQuery;
        $reviews             = clone $basicQuery;
        $allReviewCount      = $all->count();
        $positiveReviewCount = $positive->where('type', 1)->count();
        $negativeReviewCount = $negative->where('type', 0)->count();
        $allReviews          = $reviews->latest()->with(['advertisement.fiatGateway', 'advertisement.fiat', 'user'])->paginate(getPaginate());
        $data = [];

        foreach ($allReviews as $singleReview) {
            $filteredReviews = [];

            $filteredReviews['id']         = $singleReview->id;
            $filteredReviews['user_id']    = $singleReview->user_id;
            $filteredReviews['user_name']  = $singleReview->user->username;
            $filteredReviews['user_image'] = getImage(getFilePath('userProfile') . '/' . $singleReview->user->image, null, true);
            $filteredReviews['type']       = $singleReview->type;
            $filteredReviews['feedback']   = $singleReview->feedback;
            $filteredReviews['created_at'] = $singleReview->created_at;
            $filteredReviews['trade_uid']  = $singleReview->tradeRequest->uid;

            $data[] = $filteredReviews;
        }

        $notify[] = 'New trade';

        return response()->json([
            'remark' => 'new_trade',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'rate'                 => strval(getRate($ad)),
                'avg_speed'            => avgTradeSpeed($ad),
                'max_limit'            => $maxLimit,
                'title_one'            => $titleOne,
                'title_two'            => $titleTwo,
                'heading'              => $heading,
                'ad'                   => $ad,
                'all_review_count'     => $allReviewCount,
                'positive_review_count' => $positiveReviewCount,
                'negative_review_count' => $negativeReviewCount,
                'all_reviews'          => $data,
                'prev_page_url'        => $allReviews->previousPageUrl(),
                'next_page_url'        => $allReviews->nextPageUrl(),
            ]
        ]);
    }

    public function store(Request $request, $id)
    {
        $ad = Advertisement::active()->find($id);

        if (!$ad) {
            return response()->json([
                'remark' => 'ad_not_found',
                'status' => 'error',
                'message' => ['error' => 'Advertisement not found'],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'amount'  => "required|numeric|min:$ad->min|max:$ad->max",
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user              = auth()->user();
        $seller            = $ad->type == 1 ? $user : $ad->user;
        $buyer             = $ad->type == 1 ? $ad->user : $user;
        $this->finalAmount = getAmount((1 / getRate($ad)) * $request->amount, 8);
        $wallet            = Wallet::where('user_id', $seller->id)->where('crypto_currency_id', $ad->crypto_currency_id)->first();
        $maxLimit          = getMaxLimit($ad->user->wallets, $ad);
        $isTradeable       = $this->checkIsTradeable($ad, $maxLimit, $wallet);

        if ($isTradeable[0] == 'error') {
            $notify = $isTradeable;
            return response()->json([
                'remark' => 'trade_error',
                'status' => 'error',
                'message' => ['error' => $notify],
            ]);
        }

        $finalAmount      = $this->finalAmount;
        $general          = gs();
        $tradeCharge      = ($finalAmount * $general->trade_charge) / 100;

        $wallet->balance -= ($finalAmount + $tradeCharge);
        $wallet->save();

        $transaction                      = new Transaction();
        $transaction->user_id             = $seller->id;
        $transaction->crypto_currency_id  = $ad->crypto_currency_id;
        $transaction->amount              = $finalAmount;
        $transaction->post_balance        = $wallet->balance;
        $transaction->charge              = $tradeCharge;
        $transaction->trx_type            = '-';
        $transaction->details             = 'Subtracted from ' . $wallet->crypto->code . ' wallet for a sell trade';
        $transaction->remark              = 'trade_escrow';
        $transaction->trx                 = getTrx();
        $transaction->save();

        $trade                     = new Trade();
        $trade->uid                = getTrx(10);
        $trade->advertisement_id   = $ad->id;
        $trade->seller_id          = $seller->id;
        $trade->buyer_id           = $buyer->id;
        $trade->amount             = $request->amount;
        $trade->crypto_amount      = $finalAmount;
        $trade->trade_charge       = $tradeCharge;
        $trade->crypto_currency_id = $ad->crypto->id;
        $trade->fiat_gateway_id    = $ad->fiatGateway->id;
        $trade->fiat_currency_id   = $ad->fiat->id;
        $trade->window             = $ad->window;
        $trade->exchange_rate      = getRate($ad);
        $trade->status             = Status::TRADE_ESCROW_FUNDED;
        $trade->save();

        $chat           = new Chat();
        $chat->trade_id = $trade->id;
        $chat->user_id  = $user->id;
        $chat->message  = $request->details;
        $chat->file     = null;
        $chat->save();

        notify($ad->user, 'NEW_TRADE', [
            'buyer'           => $buyer->userame,
            'seller'          => $seller->username,
            'fiat_amount'     => showAmount($trade->amount, 2),
            'fiat_currency'   => $ad->fiat->code,
            'crypto_amount'   => showAmount($trade->crypto_amount, 8),
            'crypto_currency' => $ad->crypto->code,
            'window'          => $trade->window
        ], null, true);

        $notify[] = 'Trade stored';

        return response()->json([
            'remark' => 'trade_stored',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'trade_uid' => $trade->uid
            ]
        ]);
    }

    public function cancel(Request $request)
    {
        $trade = Trade::where('status', 0)->where(function ($q) {
            $q->orWhere('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->find($request->trade_id);

        if (!$trade) {
            return response()->json([
                'remark' => 'trade_error',
                'status' => 'error',
                'message' => ['error' => 'Trade not found'],
            ]);
        }

        $endTime = $trade->created_at->addMinutes($trade->window);
        $remainingMinutes = $endTime->diffInMinutes(now());

        if (($trade->seller_id == auth()->id()) && ($endTime > now())) {
            return response()->json([
                'remark' => 'trade_cancel_error',
                'status' => 'success',
                'message' => ['success' => "You can cancel this trade after $remainingMinutes minutes"],
            ]);
        }

        $wallet = Wallet::where('user_id', $trade->seller->id)->where('crypto_currency_id', $trade->crypto_currency_id)->first();

        if (!$wallet) {
            return response()->json([
                'remark' => 'wallet_error',
                'status' => 'error',
                'message' => ['error' => 'You can not proceed this action'],
            ]);
        }

        $canceledBy = 'seller';

        if (auth()->id() == $trade->buyer_id) {
            $canceledBy = 'buyer';
        }

        $trade->status = Status::TRADE_CANCELED;
        $trade->details = 'Canceled by ' . $canceledBy;
        $trade->save();

        $wallet->balance += ($trade->crypto_amount + $trade->trade_charge);
        $wallet->save();

        $transaction                     = new Transaction();
        $transaction->user_id            = $trade->seller->id;
        $transaction->crypto_currency_id = $trade->crypto_currency_id;
        $transaction->amount             = $trade->crypto_amount;
        $transaction->post_balance       = $wallet->balance;
        $transaction->charge             = 0;
        $transaction->trx_type           = '+';
        $transaction->details            = 'Refunded for cancellation of a sell trade';
        $transaction->trx                = getTrx();
        $transaction->remark             = 'trade_canceled';
        $transaction->save();

        $chat           = new Chat();
        $chat->trade_id = $trade->id;
        $chat->user_id  = auth()->id();
        $chat->message  = auth()->user()->username . ' canceled this trade';
        $chat->file     = null;
        $chat->save();

        $emailShortCodes = [
            'name'            => auth()->user()->username,
            'crypto_amount'   => showAmount($trade->crypto_amount, 8),
            'crypto_currency' => $trade->crypto->code,
            'fiat_currency'   => $trade->fiat->code,
            'buyer'           => $trade->buyer->username,
            'seller'          => $trade->seller->username,
            'fiat_amount'     => showAmount($trade->amount, 2),
            'window'          => $trade->window
        ];

        notify($trade->buyer, 'TRADE_CANCELED', $emailShortCodes, null, true, $trade->uid);
        notify($trade->seller, 'TRADE_CANCELED', $emailShortCodes, null, true, $trade->uid);

        return response()->json([
            'remark' => 'trade_canceled',
            'status' => 'success',
            'message' => ['success' => 'Trade canceled successfully'],
        ]);
    }

    public function paid(Request $request)
    {
        $trade = Trade::where('status', 0)->where('buyer_id', auth()->id())->find($request->trade_id);

        if (!$trade) {
            return response()->json([
                'remark' => 'trade_error',
                'status' => 'error',
                'message' => ['error' => 'Trade not found'],
            ]);
        }

        $trade->status  = Status::TRADE_BUYER_SENT;
        $trade->details = 'Paid by buyer';
        $trade->paid_at = Carbon::now();
        $trade->save();

        $chat           = new Chat();
        $chat->trade_id = $trade->id;
        $chat->user_id  = auth()->id();
        $chat->message  = auth()->user()->username . ' has marked this trade as paid. Check if you have received the payment';
        $chat->file     = null;
        $chat->save();

        notify($trade->seller, 'BUYER_PAID', [
            'fiat_currency'   => $trade->fiat->code,
            'buyer'           => $trade->buyer->username,
            'seller'          => $trade->seller->username,
            'fiat_amount'     => showAmount($trade->amount, 2),
            'crypto_amount'   => showAmount($trade->crypto_amount, 8),
            'crypto_currency' => $trade->crypto->code,
            'window'          => $trade->window
        ], null, true, $trade->uid);

        return response()->json([
            'remark' => 'trade_paid',
            'status' => 'success',
            'message' => ['success' => 'Trade paid successfully'],
        ]);
    }

    public function dispute(Request $request)
    {
        $user  = auth()->user();
        $trade = Trade::where('status', 2)->where(function ($q) use ($user) {
            $q->orWhere('buyer_id', $user->id)->orWhere('seller_id', $user->id);
        })->find($request->trade_id);

        if (!$trade) {
            return response()->json([
                'remark' => 'trade_error',
                'status' => 'error',
                'message' => ['error' => 'Trade not found'],
            ]);
        }

        if ($trade->buyer_id == $user->id && $trade->paid_at && ((Carbon::parse($trade->paid_at)->addMinutes($trade->window)) > Carbon::now())) {

            return response()->json([
                'remark' => 'trade_dispute_error',
                'status' => 'error',
                'message' => ['error' => 'You can not proceed this action right now'],
            ]);
        }

        $reportedBy = 'seller';

        if ($user->id == $trade->buyer_id) {
            $reportedBy = 'buyer';
        }

        $trade->status      = Status::TRADE_DISPUTED;
        $trade->details     = 'Reported by ' . $reportedBy;
        $trade->reported_by = $user->id;
        $trade->save();

        $chat           = new Chat();
        $chat->trade_id = $trade->id;
        $chat->user_id  = null;
        $chat->admin    = 1;
        $chat->message  = 'Disputed By - ' . $user->username . ' Please Solve Issue with buyer and seller';
        $chat->file     = null;
        $chat->save();

        $emailShortCodes = [
            'name'            => $user->username,
            'crypto_amount'   => showAmount($trade->crypto_amount, 8),
            'crypto_currency' => $trade->crypto->code,
            'fiat_currency'   => $trade->fiat->code,
            'buyer'           => $trade->buyer->username,
            'seller'          => $trade->seller->username,
            'fiat_amount'     => showAmount($trade->amount, 2),
            'window'          => $trade->window
        ];

        notify($trade->seller, 'TRADE_REPORTED', $emailShortCodes, null, true, $trade->uid);
        notify($trade->buyer, 'TRADE_REPORTED', $emailShortCodes, null, true, $trade->uid);

        return response()->json([
            'remark' => 'trade_disputed',
            'status' => 'success',
            'message' => ['success' => 'Trade reported successfully'],
        ]);
    }

    public function release(Request $request)
    {
        $trade = Trade::where('status', 2)->where('seller_id', auth()->id())->find($request->trade_id);

        if (!$trade) {
            return response()->json([
                'remark' => 'trade_error',
                'status' => 'error',
                'message' => ['error' => 'Trade not found'],
            ]);
        }

        $wallet = Wallet::where('user_id', $trade->buyer->id)->where('crypto_currency_id', $trade->crypto_currency_id)->first();

        if (!$wallet) {
            return response()->json([
                'remark' => 'wallet_error',
                'status' => 'error',
                'message' => ['error' => 'Wallet not found'],
            ]);
        }

        $trade->status       = Status::TRADE_COMPLETED;
        $trade->details      = 'Trade released by seller';
        $trade->completed_at = now();
        $trade->save();

        $processingMin = now()->diffInMinutes(Carbon::parse($trade->created_at));

        $trade->advertisement->completed_trade += 1;
        $trade->advertisement->total_min       += $processingMin;
        $trade->advertisement->save();


        $trade->advertisement->user->total_min       += $processingMin;
        $trade->advertisement->user->save();

        $trade->buyer->completed_trade += 1;
        $trade->buyer->save();

        $trade->seller->completed_trade += 1;
        $trade->seller->save();

        $wallet->balance += $trade->crypto_amount;
        $wallet->save();

        $transaction                     = new Transaction();
        $transaction->user_id            = $trade->buyer->id;
        $transaction->crypto_currency_id = $trade->crypto_currency_id;
        $transaction->amount             = $trade->crypto_amount;
        $transaction->post_balance       = $wallet->balance;
        $transaction->charge             = 0;
        $transaction->trx_type           = '+';
        $transaction->details            = 'Added for buying ' . $wallet->crypto->name;
        $transaction->remark             = 'trade_completed';
        $transaction->trx                = getTrx();
        $transaction->save();

        $chat           = new Chat();
        $chat->trade_id = $trade->id;
        $chat->user_id  = auth()->id();
        $chat->message  = auth()->user()->username . ' has marked this as completed';
        $chat->file     = null;
        $chat->save();

        $emailShortCodes = [
            'name'            => $trade->buyer->username,
            'crypto_amount'   => showAmount($trade->crypto_amount, 8),
            'crypto_currency' => $trade->crypto->code,
            'fiat_currency'   => $trade->fiat->code,
            'buyer'           => $trade->buyer->username,
            'seller'          => $trade->seller->username,
            'fiat_amount'     => showAmount($trade->amount, 2),
            'window'          => $trade->window
        ];

        notify($trade->seller, 'TRADE_COMPLETED', $emailShortCodes, null, true, $trade->uid);
        notify($trade->buyer, 'TRADE_COMPLETED', $emailShortCodes, null, true, $trade->uid);

        if ($trade->advertisement->user->id == $trade->seller_id) {
            $trader = $trade->buyer;
        } else {
            $trader = $trade->seller;
        }

        $general = gs();

        if ($general->trade_commission) {
            levelCommission($trader, $trade->crypto_amount, $trade->crypto_currency_id, $trade->uid, 'trade');
        }

        return response()->json([
            'remark' => 'trade_released',
            'status' => 'success',
            'message' => ['success' => 'Trade released successfully'],
        ]);
    }

    protected function getTradeData($scope)
    {
        $trades = Trade::$scope()->where(function ($q) {
            $q->where('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->with(['fiat', 'fiatGateway', 'crypto', 'buyer', 'seller'])->latest()->paginate(getPaginate());

        return $trades;
    }

    protected function checkIsTradeable($ad, $maxLimit, $wallet)
    {
        if (!$ad) {
            return ['error', 'Advertisement is not found or currently disabled'];
        }

        if (!$ad->status) {
            return ['error', 'This advertisement is currently disabled'];
        }

        if ($ad->user_id == auth()->id()) {
            return ['error', 'You can\'t trade on your own advertisement'];
        }

        if (!$ad->crypto->status) {
            return ['error', 'Trading with this crypto currency is currently disabled'];
        }

        if (!$ad->fiatGateway->status) {
            return ['error', 'Trading with this payment method is currently disabled'];
        }

        if (!$ad->fiat->status) {
            return ['error', 'Trading with this fiat currency is currently disabled'];
        }

        if ($ad->type == 2 && $maxLimit < $ad->min) {
            return ['error', 'Seller doesn\'t have enough balance'];
        }

        if (!$wallet) {
            return ['error', 'You can not proceed this action'];
        }

        if (@$wallet->balance && $wallet->balance < $this->finalAmount) {
            return ['error', 'Seller doesn\'t  have sufficient balance'];
        }

        return ['success'];
    }
}
