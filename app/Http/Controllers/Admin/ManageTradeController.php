<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Trade;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManageTradeController extends Controller
{
    protected function tradeData($scope = null)
    {
        if ($scope) {
            $trades = Trade::$scope();
        } else {
            $trades = Trade::query();
        }

        if (request()->search) {
            $search = request()->search;
            $trades  = $trades->where('uid', $search)->orWhereHas('buyer', function ($buyer) use ($search) {
                $buyer->where('username', 'like', "%$search%");
            })->orWhereHas('seller', function ($seller) use ($search) {
                $seller->where('username', 'like', "%$search%");
            });
        }

        $trades =  $trades->with(['buyer', 'seller', 'fiatGateway', 'crypto', 'fiat'])->orderBy('id', 'desc')->paginate(getPaginate());
        $pageTitle = $this->pageTitle;

        return view('admin.trade.index', compact('pageTitle', 'trades'));
    }

    public function index()
    {
        $this->pageTitle = 'All Trades';
        return $this->tradeData();
    }

    public function running()
    {
        $this->pageTitle = 'Running Trades';
        return $this->tradeData('running');
    }

    public function completed()
    {
        $this->pageTitle = 'Completed Trades';
        return $this->tradeData('completed');
    }

    public function reported()
    {
        $this->pageTitle = 'Reported Trades';
        return $this->tradeData('reported');
    }

    public function details($id)
    {
        $tradeDetails = Trade::findOrFail($id);
        $pageTitle = 'Trade#' . $tradeDetails->uid;

        $title = showAmount($tradeDetails->crypto_amount, 8) . ' ' . $tradeDetails->crypto->code . ' For ' . showAmount($tradeDetails->amount) . ' ' . $tradeDetails->fiat->code . ' via ' . $tradeDetails->fiatGateway->name;
        $title2 = ' Exchange Rate ' . showAmount($tradeDetails->exchange_rate) . ' ' . $tradeDetails->fiat->code . '/' . $tradeDetails->crypto->code;

        return view('admin.trade.details', compact('pageTitle', 'tradeDetails', 'title', 'title2'));
    }

    public function release(Request $request, $id)
    {
        $trade = Trade::where('status', 8)->findOrFail($id);
        $wallet = Wallet::where('user_id', $trade->buyer->id)->where('crypto_currency_id', $trade->crypto_currency_id)->first();

        if (!$wallet) {
            $notify[] = ['error', 'You can not proceed this action'];
            return back()->withNotify($notify);
        }

        $wallet->balance += $trade->crypto_amount;
        $wallet->save();

        $transaction = new Transaction();
        $transaction->user_id = $trade->buyer->id;
        $transaction->crypto_currency_id = $trade->crypto_currency_id;
        $transaction->amount = $trade->crypto_amount;
        $transaction->post_balance = $wallet->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = 'Added for buying ' . $wallet->crypto->name . ' by System';
        $transaction->remark = 'trade_completed';
        $transaction->trx = getTrx();
        $transaction->save();

        $trade->status = Status::TRADE_COMPLETED;
        $trade->completed_at = now();
        $trade->details = 'Crypto amount released by system to buyer';
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

        $emailShortCodes = [
            'name' => $trade->buyer->username,
            'crypto_amount' => showAmount($trade->crypto_amount, 8),
            'crypto_currency' => $trade->crypto->code,
            'fiat_currency' => $trade->fiat->code,
            'buyer' => $trade->buyer->username,
            'seller' => $trade->seller->username,
            'fiat_amount' => showAmount($trade->amount, 2),
            'window' => $trade->window
        ];

        notify($trade->seller, 'TRADE_SETTLED', $emailShortCodes, null, true, $trade->uid);
        notify($trade->buyer, 'TRADE_SETTLED', $emailShortCodes, null, true, $trade->uid);

        if ($trade->advertisement->user->id == $trade->seller_id) {
            $trader = $trade->buyer;
        } else {
            $trader = $trade->seller;
        }

        $general = gs();

        if ($general->trade_commission) {
            levelCommission($trader, $trade->crypto_amount, $trade->crypto_currency_id, $trade->uid, 'trade');
        }

        $notify[] = ['success', 'Released successfully'];
        return back()->withNotify($notify);
    }

    public function return(Request $request, $id)
    {
        $trade = Trade::where('status', 8)->findOrFail($id);
        $wallet = Wallet::where('user_id', $trade->seller->id)->where('crypto_currency_id', $trade->crypto_currency_id)->first();

        if (!$wallet) {
            $notify[] = ['error', 'You can not proceed this action'];
            return back()->withNotify($notify);
        }

        $wallet->balance += ($trade->crypto_amount + $trade->trade_charge);
        $wallet->save();

        $transaction = new Transaction();
        $transaction->user_id = $trade->seller->id;
        $transaction->crypto_currency_id = $trade->crypto_currency_id;
        $transaction->amount = $trade->crypto_amount;
        $transaction->post_balance = $wallet->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = 'Refunded by system';
        $transaction->remark = 'trade_canceled';
        $transaction->trx = getTrx();
        $transaction->save();

        $trade->status = Status::TRADE_COMPLETED;
        $trade->details = 'Crypto amount returned by system to seller';
        $trade->save();

        $emailShortCodes = [
            'name' => $trade->seller->username,
            'crypto_amount' => showAmount($trade->crypto_amount, 8),
            'crypto_currency' => ($trade->crypto->code),
            'fiat_currency' => $trade->fiat->code,
            'buyer' => $trade->buyer->username,
            'seller' => $trade->seller->username,
            'fiat_amount' => showAmount($trade->amount, 2),
            'window' => $trade->window
        ];

        notify($trade->buyer, 'TRADE_SETTLED', $emailShortCodes, null, true, $trade->uid);
        notify($trade->seller, 'TRADE_SETTLED', $emailShortCodes, null, true, $trade->uid);

        $notify[] = ['success', 'Returned Successfully'];
        return back()->withNotify($notify);
    }

    public function chatStore(Request $request, $id)
    {
        $trade = Trade::findOrFail($id);

        if($trade->status != Status::TRADE_DISPUTED){
            $notify[] = ['success', 'You can\'t join this conversation'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'message' => 'required',
            'file' => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png', 'pdf']), 'max:2000'],
        ]);

        $file = null;
        if ($request->hasFile('file')) {
            $file = fileUploader($request->file, getFilePath('chat_file'));
        }

        $chat = new Chat();
        $chat->trade_id = $trade->id;
        $chat->admin = 1;
        $chat->message = $request->message;
        $chat->file = $file;
        $chat->save();

        $shortCodes = [
            'from_user' => ' the system',
            'message' => $chat->message,
            'trade_uid' => $trade->uid,
        ];

        notify($trade->buyer, 'TRADE_CHAT', $shortCodes, null, true, $trade->uid);
        notify($trade->seller, 'TRADE_CHAT', $shortCodes, null, true, $trade->uid);

        $notify[] = ['success', 'Your response is taken successfully'];
        return back()->withNotify($notify);
    }

    public function chatDownload($id)
    {
        $chat = Chat::findOrFail($id);

        if ($chat->file) {
            $file = $chat->file;
            $full_path = fileManager()->chat_file()->path . '/' . $file;
            $title = $chat->file;
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $mimetype = mime_content_type($full_path);
            header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
            header("Content-Type: " . $mimetype);
            return readfile($full_path);
        } else {
            $notify[] = ['error', 'No downloadable file found'];
            return back()->withNotify($notify);
        }
    }
}
