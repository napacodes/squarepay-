<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\CryptoCurrency;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WithdrawController extends Controller
{
    public function withdrawMoney($id)
    {
        $crypto = CryptoCurrency::where('id', $id)->first();

        if (!$crypto) {
            return response()->json([
                'remark' => 'crypto_error',
                'status' => 'error',
                'message' => ['error' => 'Crypto currency not found'],
            ]);
        }

        $userWallet = Wallet::where('user_id', auth()->id())->where('crypto_currency_id', $crypto->id)->first();

        if (!$userWallet) {
            return response()->json([
                'remark' => 'wallet_error',
                'status' => 'error',
                'message' => ['error' => 'User wallet not found'],
            ]);
        }

        $limit             = 0;
        $charge            = $crypto->withdraw_charge_fixed + ($userWallet->balance * $crypto->withdraw_charge_percent / 100);
        $maxWithdrawAmount = $userWallet->balance - $charge;
        $chargeMessage     = '';

        if ($crypto->withdraw_charge_fixed > 0) {
            $chargeMessage .= $crypto->withdraw_charge_fixed . ' ' . $crypto->code . ' + ';
        }

        $chargeMessage .= $crypto->withdraw_charge_percent . '%';

        if ($maxWithdrawAmount > 0) {
            $limit = showAmount($userWallet->balance - $charge, 8);
        }

        $notify[] = 'Make withdrawal';

        return response()->json([
            'remark' => 'make_withdrawal',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data' => [
                'current_balance'  => showAmount($userWallet->balance, 8),
                'charge_message'   => $chargeMessage,
                'limit'            => $limit,
                'crypto'           => $crypto,
                'crypto_image_path' => getFilePath('crypto'),
            ]
        ]);
    }

    public function previousWithdrawals($id)
    {
        $crypto = CryptoCurrency::where('id', $id)->first();

        if (!$crypto) {
            return response()->json([
                'remark' => 'crypto_error',
                'status' => 'error',
                'message' => ['error' => 'Crypto currency not found'],
            ]);
        }

        $pastWithdrawals = Withdrawal::where('user_id', auth()->id())->where('status', '!=', Status::PAYMENT_INITIATE)->where('crypto_currency_id', $crypto->id)->with('crypto')->paginate(getPaginate());

        $notify[] = 'Previous withdrawals';

        return response()->json([
            'remark' => 'past_withdrawals',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data'   => [
                'crypto_image_path' => getFilePath('crypto'),
                'past_withdrawals' => $pastWithdrawals
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'crypto_id'      => 'required|integer:gt:0',
            'wallet_address' => 'required',
            'amount'         => 'required|numeric|gt:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark' => 'validation_error',
                'status' => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $crypto = CryptoCurrency::where('id', $request->crypto_id)->first();

        if (!$crypto) {
            return response()->json([
                'remark' => 'crypto_error',
                'status' => 'error',
                'message' => ['error' => 'Crypto currency not found'],
            ]);
        }

        $user = auth()->user();
        $userWallet = Wallet::where('user_id', $user->id)->where('crypto_currency_id', $crypto->id)->first();

        if (!$userWallet) {
            return response()->json([
                'remark'  => 'wallet_error',
                'status'  => 'error',
                'message' => ['error' => 'User wallet not found'],
            ]);
        }

        $charge = $crypto->withdraw_charge_fixed + ($request->amount * $crypto->withdraw_charge_percent / 100);
        $finalWithdrawAmount = $request->amount + $charge;

        if ($finalWithdrawAmount > $userWallet->balance) {
            return response()->json([
                'remark' => 'balance_low',
                'status' => 'error',
                'message' => ['error' => 'You do not have sufficient balance for withdraw'],
            ]);
        }

        $userWallet->balance -= $finalWithdrawAmount;
        $userWallet->save();

        $withdraw                     = new Withdrawal();
        $withdraw->crypto_currency_id = $crypto->id;
        $withdraw->wallet_address     = $request->wallet_address;
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
        $transaction->details            = showAmount($withdraw->payable, 8) . ' ' . $withdraw->crypto->code . ' Withdraw Successful';
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

        return response()->json([
            'remark' => 'withdrawal_submitted',
            'status' => 'success',
            'message' => ['success' => 'Withdrawal request submitted'],
        ]);
    }

    public function log(Request $request)
    {
        $withdrawals = Withdrawal::where('user_id', auth()->id())->where('status', '!=', Status::PAYMENT_INITIATE);

        if ($request->crypto_id) {
            $withdrawals = $withdrawals->where('crypto_currency_id', $request->crypto_id);
        }

        if ($request->search) {
            $withdrawals = $withdrawals->where('trx', $request->search);
        }

        $withdrawals = $withdrawals->with('crypto')->orderBy('id', 'desc')->paginate(getPaginate());
        $cryptos     = CryptoCurrency::orderBy('name')->get();

        $notify[] = 'Withdrawals';

        return response()->json([
            'remark' => 'withdrawals',
            'status' => 'success',
            'message' => ['success' => $notify],
            'data'   => [
                'crypto_image_path' => getFilePath('crypto'),
                'withdrawals'       => $withdrawals,
                'cryptos'           => $cryptos,
            ]
        ]);
    }
}
