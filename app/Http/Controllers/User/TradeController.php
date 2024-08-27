<?php

namespace App\Http\Controllers\User;

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

class TradeController extends Controller
{
    public function running()
    {
        $this->pageTitle     = 'Running Trades';
        return $this->getTradeData('running');
    }

    public function completed()
    {
        $this->pageTitle     = 'Completed Trades';
        return $this->getTradeData('completed');
    }

    public function newTrade($id)
    {
        $ad          = Advertisement::findOrFail($id);
        $maxLimit    = getMaxLimit($ad->user->wallets, $ad);
        $isTradeable = $this->checkIsTradeable($ad, $maxLimit, true);

        if ($isTradeable[0] == 'error') {
            $notify[] = $isTradeable;
            return back()->withNotify($notify);
        }

        $pageTitle           = 'New Trade Request';
        $basicQuery          = Review::where('advertisement_id', $ad->id);
        $allReview           = clone $basicQuery;
        $positiveReviewCount = clone $basicQuery;
        $negativeReviewCount = clone $basicQuery;
        $positive            = $positiveReviewCount->where('type', 1)->count();
        $negative            = $negativeReviewCount->where('type', 0)->count();
        $reviews             = $allReview->latest()->with(['advertisement.fiatGateway', 'advertisement.fiat', 'user', 'tradeRequest'])->paginate(getPaginate());

        return view('Template::user.trade.create', compact('pageTitle', 'ad', 'maxLimit', 'positive', 'negative', 'reviews'));
    }

    public function sendTradeRequest(Request $request, $id)
    {
        $ad = Advertisement::findOrFail($id);

        $request->validate([
            'amount'  => "required|numeric|min:$ad->min|max:$ad->max",
            'details' => 'required',
        ]);

        $user     = auth()->user();
        $seller   = $ad->type == 1 ? $user : $ad->user;
        $buyer    = $ad->type == 1 ? $ad->user : $user;
        $maxLimit = getMaxLimit($ad->user->wallets, $ad);

        $this->finalAmount = getAmount((1 / getRate($ad)) * $request->amount, 8);
        $wallet            = Wallet::where('user_id', $seller->id)->where('crypto_currency_id', $ad->crypto_currency_id)->first();
        $isTradeable       = $this->checkIsTradeable($ad, $maxLimit, $wallet);

        // If not tradeable
        if ($isTradeable[0] == 'error') {
            $notify[] = $isTradeable;
            return back()->withNotify($notify);
        }

        $finalAmount = $this->finalAmount;
        $general     = gs();
        $tradeCharge = ($finalAmount * $general->trade_charge) / 100;

        $wallet->balance -= ($finalAmount + $tradeCharge);
        $wallet->save();

        $transaction                     = new Transaction();
        $transaction->user_id            = $seller->id;
        $transaction->crypto_currency_id = $ad->crypto_currency_id;
        $transaction->amount             = $finalAmount;
        $transaction->post_balance       = $wallet->balance;
        $transaction->charge             = $tradeCharge;
        $transaction->trx_type           = '-';
        $transaction->details            = 'Subtracted from ' . $wallet->crypto->code . ' wallet for a sell trade';
        $transaction->remark             = 'trade_escrow';
        $transaction->trx                = getTrx();
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
            'buyer'           => $buyer->username,
            'seller'          => $seller->username,
            'fiat_amount'     => showAmount($trade->amount, 2),
            'fiat_currency'   => $ad->fiat->code,
            'crypto_amount'   => showAmount($trade->crypto_amount, 8),
            'crypto_currency' => $ad->crypto->code,
            'window'          => $trade->window
        ]);

        $notify[] = ['success', 'Your request is taken successfully'];
        return redirect()->route('user.trade.request.details', $trade->uid)->withNotify($notify);
    }

    public function details($id)
    {
        $trade = Trade::where('uid', $id)->where(function ($q) {
            $q->orWhere('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->with('chats')->firstOrFail();

        $pageTitle = 'Trade Details';
        $title = '';

        if ($trade->seller_id == auth()->id()) {
            $title .= 'Selling ';
        } else {
            $title .= 'Buying ';
        }

        $title .= showAmount($trade->crypto_amount, 8) . ' ' . $trade->crypto->code . ' for ' . showAmount($trade->amount) . ' ' . $trade->fiat->code . ' via ' . $trade->fiatGateway->name;
        $title2 = ' Exchange Rate: ' . showAmount($trade->exchange_rate) . ' ' . $trade->fiat->code . '/' . $trade->crypto->code;

        $reviews = Review::where('trade_id', $trade->id)->where(function ($q) {
            $q->where('user_id', auth()->id())->orWhere('to_id', auth()->id());
        })->with('user')->paginate(1);

        return view('Template::user.trade.details', compact('pageTitle', 'trade', 'title', 'title2', 'reviews'));
    }

    public function cancel($id)
    {

        $trade = Trade::where('status', 0)->where(function ($q) {
            $q->orWhere('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->findOrFail($id);

        $endTime = $trade->created_at->addMinutes($trade->window);
        $remainingMinutes = $endTime->diffInMinutes(now());

        if (($trade->seller_id == auth()->id()) && ($endTime > now())) {
            $notify[] = ['error', "You can cancel this trade after $remainingMinutes minutes"];
            return back()->withNotify($notify);
        }

        $wallet = Wallet::where('user_id', $trade->seller->id)->where('crypto_currency_id', $trade->crypto_currency_id)->first();

        if (!$wallet) {
            $notify[] = ['error', 'You can not proceed this action'];
            return back()->withNotify($notify);
        }

        $canceledBy = 'seller';

        if (auth()->id() == $trade->buyer_id) {
            $canceledBy = 'buyer';
        }

        $trade->status  = Status::TRADE_CANCELED;
        $trade->details = 'Canceled by ' . $canceledBy;
        $trade->save();

        $wallet->balance += ($trade->crypto_amount + $trade->trade_charge);
        $wallet->save();

        $transaction                        = new Transaction();
        $transaction->user_id               = $trade->seller->id;
        $transaction->crypto_currency_id    = $trade->crypto_currency_id;
        $transaction->amount                = $trade->crypto_amount;
        $transaction->post_balance          = $wallet->balance;
        $transaction->charge                = 0;
        $transaction->trx_type              = '+';
        $transaction->details               = 'Refunded for cancellation of a sell trade';
        $transaction->trx                   = getTrx();
        $transaction->remark                = 'trade_canceled';
        $transaction->save();

        $chat = new Chat();
        $chat->trade_id = $trade->id;
        $chat->user_id          = auth()->id();
        $chat->message          = auth()->user()->username . ' canceled this trade';
        $chat->file             = null;
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

        $notify[] = ['success', 'Cancelled Successfully'];
        return back()->withNotify($notify);
    }

    public function paid($id)
    {
        $trade = Trade::where('status', 0)->where('buyer_id', auth()->id())->findOrFail($id);

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
        ], null, true);

        $notify[] = ['success', 'Marked as paid successfully'];
        return back()->withNotify($notify);
    }

    public function dispute($id)
    {
        $user = auth()->user();

        $trade = Trade::where('status', 2)->where(function ($q) use ($user) {
            $q->orWhere('buyer_id', $user->id)->orWhere('seller_id', $user->id);
        })->findOrFail($id);

        if ($trade->buyer_id == $user->id && $trade->paid_at && ((Carbon::parse($trade->paid_at)->addMinutes($trade->window)) > Carbon::now())) {

            $notify[] = ['error', 'You can not proceed this action right now'];
            return back()->withNotify($notify);
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
        $chat->message  = 'Disputed by - ' . $user->username . ' please solve issue with buyer and seller';
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

        $notify[] = ['success', 'Disputed successfully'];
        return back()->withNotify($notify);
    }

    public function release($id)
    {
        $trade  = Trade::where('status', 2)->where('seller_id', auth()->id())->findOrFail($id);
        $wallet = Wallet::where('user_id', $trade->buyer->id)->where('crypto_currency_id', $trade->crypto_currency_id)->first();

        if (!$wallet) {
            $notify[] = ['error', 'You can not proceed this action'];
            return back()->withNotify($notify);
        }

        $trade->status = Status::TRADE_COMPLETED;
        $trade->details = 'Trade released by seller';
        $trade->completed_at = now();
        $trade->save();

        $processingMin = now()->diffInMinutes(Carbon::parse($trade->created_at));

        $trade->advertisement->completed_trade += 1;
        $trade->advertisement->total_min += $processingMin;
        $trade->advertisement->save();

        $trade->advertisement->user->total_min += $processingMin;
        $trade->advertisement->user->save();

        $trade->buyer->completed_trade += 1;
        $trade->buyer->save();

        $trade->seller->completed_trade += 1;
        $trade->seller->save();

        $wallet->balance += $trade->crypto_amount;
        $wallet->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $trade->buyer->id;
        $transaction->crypto_currency_id    = $trade->crypto_currency_id;
        $transaction->amount       = $trade->crypto_amount;
        $transaction->post_balance = $wallet->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->details      = 'Added for buying ' . $wallet->crypto->name;
        $transaction->remark       = 'trade_completed';
        $transaction->trx          = getTrx();
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

        $notify[] = ['success', 'Trade released successfully'];
        return back()->withNotify($notify);
    }

    protected function getTradeData($scope)
    {
        $pageTitle     = $this->pageTitle;
        $tradeRequests = Trade::$scope()->where(function ($q) {
            $q->where('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->with(['fiat', 'fiatGateway', 'crypto', 'buyer', 'seller'])->latest()->paginate(getPaginate());

        return view('Template::user.trade.index', compact('pageTitle', 'tradeRequests'));
    }

    protected function checkIsTradeable($ad, $maxLimit, $wallet)
    {
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
