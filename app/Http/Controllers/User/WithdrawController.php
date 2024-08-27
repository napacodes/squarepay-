<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\CryptoCurrency;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{

    public function withdrawMoney($code)
    {
        $crypto = CryptoCurrency::where('code', $code)->first();

        if (!$crypto) {
            $notify[] = ['error', 'Crypto currency not found'];
            return back()->withNotify($notify);
        }

        $pageTitle   = 'Withdraw ' . $crypto->code;
        $userBalance = Wallet::where('user_id', auth()->id())->where('crypto_currency_id', $crypto->id)->firstOrFail();
        $withdrawals = Withdrawal::where('user_id', auth()->id())->where('status', '!=', Status::PAYMENT_INITIATE)->where('crypto_currency_id', $crypto->id)->with('crypto')->paginate(getPaginate());


        return view('Template::user.withdraw.index', compact('pageTitle', 'userBalance', 'crypto', 'withdrawals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'crypto' => 'required',
            'wallet' => 'required',
            'amount' => 'required|numeric|gt:0'
        ]);

        $crypto = CryptoCurrency::where('code', $request->crypto)->first();

        if (!$crypto) {
            $notify[] = ['error', 'Crypto currency not found'];
            return back()->withNotify($notify);
        }

        $user = auth()->user();
        $userWallet = Wallet::where('user_id', $user->id)->where('crypto_currency_id', $crypto->id)->firstOrFail();

        $charge = $crypto->withdraw_charge_fixed + ($request->amount * $crypto->withdraw_charge_percent / 100);
        $finalWithdrawAmount = $request->amount + $charge;

        if ($finalWithdrawAmount > $userWallet->balance) {
            $notify[] = ['error', 'You do not have sufficient balance for withdraw.'];
            return back()->withNotify($notify);
        }

        $userWallet->balance -= $finalWithdrawAmount;
        $userWallet->save();

        $withdraw                     = new Withdrawal();
        $withdraw->crypto_currency_id = $crypto->id;
        $withdraw->wallet_address     = $request->wallet;
        $withdraw->user_id            = $user->id;
        $withdraw->amount             = $finalWithdrawAmount;
        $withdraw->charge             = $charge;
        $withdraw->payable            = $request->amount;
        $withdraw->trx                = getTrx();
        $withdraw->status             = Status::PAYMENT_PENDING;
        $withdraw->save();

        $transaction                     = new Transaction();
        $transaction->user_id            = $withdraw->user_id;
        $transaction->crypto_currency_id = $withdraw->crypto_currency_id;
        $transaction->amount             = $withdraw->amount;
        $transaction->post_balance       = $userWallet->balance;
        $transaction->charge             = $withdraw->charge;
        $transaction->trx_type           = '-';
        $transaction->details            = showAmount($withdraw->payable, 8) . ' ' . $withdraw->crypto->code . ' Withdraw Successful ';
        $transaction->trx                = $withdraw->trx;
        $transaction->remark             = 'withdraw';
        $transaction->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New withdraw request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.withdraw.data.details', $withdraw->id);
        $adminNotification->save();

        notify($user, 'WITHDRAW_REQUEST', [
            'amount'       => showAmount($withdraw->amount, 8),
            'payable'      => showAmount($withdraw->payable, 8),
            'charge'       => showAmount($withdraw->charge, 8),
            'currency'     => $withdraw->crypto->code,
            'trx'          => $withdraw->trx,
            'post_balance' => showAmount($userWallet->balance, 8)
        ]);

        $notify[] = ['success', 'Withdraw request sent successfully'];
        return to_route('user.withdraw.history')->withNotify($notify);
    }

    public function log(Request $request)
    {
        $pageTitle = "My Withdrawals";
        $withdrawals = Withdrawal::where('user_id', auth()->id())->where('status', '!=', Status::PAYMENT_INITIATE);

        if ($request->crypto) {
            $withdrawals = $withdrawals->where('crypto_currency_id', $request->crypto);
        }

        if ($request->search) {
            $withdrawals = $withdrawals->where('trx', $request->search);
        }

        $withdrawals = $withdrawals->with('crypto')->orderBy('id', 'desc')->paginate(getPaginate());

        $cryptos = CryptoCurrency::orderBy('name')->get();

        return view('Template::user.withdraw.log', compact('pageTitle', 'withdrawals', 'cryptos'));
    }
}
