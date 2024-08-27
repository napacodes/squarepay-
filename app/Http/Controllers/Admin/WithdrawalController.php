<?php

namespace App\Http\Controllers\Admin;

use App\Models\Wallet;
use App\Constants\Status;
use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\CryptoCurrency;
use App\Http\Controllers\Controller;

class WithdrawalController extends Controller
{
    public function pending($userId = null)
    {
        $pageTitle   = 'Pending Withdrawals';
        $withdrawals = $this->withdrawalData('pending', userId: $userId);
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals'));
    }

    public function approved($userId = null)
    {
        $pageTitle   = 'Approved Withdrawals';
        $withdrawals = $this->withdrawalData('approved', userId: $userId);
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals'));
    }

    public function rejected($userId = null)
    {
        $pageTitle   = 'Rejected Withdrawals';
        $withdrawals = $this->withdrawalData('rejected', userId: $userId);
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals'));
    }

    public function all($userId = null)
    {
        $pageTitle      = 'All Withdrawals';
        $withdrawalData = $this->withdrawalData($scope = null, $summary = true, userId: $userId);
        $withdrawals    = $withdrawalData['data'];
        $summary        = $withdrawalData['summary'];
        $successful     = $summary['successful'];
        $pending        = $summary['pending'];
        $rejected       = $summary['rejected'];

        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals', 'successful', 'pending', 'rejected'));
    }

    protected function withdrawalData($scope = null, $summary = false, $userId = null)
    {
        if ($scope) {
            $withdrawals = Withdrawal::$scope();
        } else {
            $withdrawals = Withdrawal::where('status', '!=', Status::PAYMENT_INITIATE);
        }

        if ($userId) {
            $withdrawals = $withdrawals->where('user_id', $userId);
        }

        $withdrawals = $withdrawals->searchable(['trx', 'user:username'])->dateFilter();

        $request = request();
        if ($request->method) {
            $withdrawals = $withdrawals->where('method_id', $request->method);
        }
        if (!$summary) {
            return $withdrawals->with(['user', 'crypto'])->orderBy('id', 'desc')->paginate(getPaginate());
        } else {

            $successful = clone $withdrawals;
            $pending    = clone $withdrawals;
            $rejected   = clone $withdrawals;

            $successfulSummary = $successful->where('status', Status::PAYMENT_SUCCESS)->count();
            $pendingSummary    = $pending->where('status', Status::PAYMENT_PENDING)->count();
            $rejectedSummary   = $rejected->where('status', Status::PAYMENT_REJECT)->count();

            return [
                'data'    => $withdrawals->with(['user', 'crypto'])->orderBy('id', 'desc')->paginate(getPaginate()),
                'summary' => [
                    'successful' => $successfulSummary,
                    'pending'    => $pendingSummary,
                    'rejected'   => $rejectedSummary,
                ],
            ];
        }
    }

    public function details($id)
    {
        $withdrawal = Withdrawal::where('id', $id)->where('status', '!=', Status::PAYMENT_INITIATE)->with(['user', 'crypto'])->firstOrFail();
        $pageTitle  = $withdrawal->user->username . ' Withdraw Requested ' . showAmount($withdrawal->amount);
        $details    = $withdrawal->withdraw_information ? json_encode($withdrawal->withdraw_information) : null;

        return view('admin.withdraw.detail', compact('pageTitle', 'withdrawal', 'details'));
    }

    public function approve(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $withdraw                 = Withdrawal::where('id', $request->id)->where('status', Status::PAYMENT_PENDING)->with('user')->firstOrFail();
        $withdraw->status         = Status::PAYMENT_SUCCESS;
        $withdraw->admin_feedback = $request->details;
        $withdraw->save();

        notify($withdraw->user, 'WITHDRAW_APPROVE', [
            'amount'        => showAmount($withdraw->amount, 8),
            'payable'       => showAmount($withdraw->payable, 8),
            'charge'        => showAmount($withdraw->charge, 8),
            'currency'      => $withdraw->crypto->code,
            'trx'           => $withdraw->trx,
            'admin_details' => $request->details,
        ]);

        $notify[] = ['success', 'Withdrawal approved successfully'];
        return to_route('admin.withdraw.data.pending')->withNotify($notify);
    }

    public function reject(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $withdraw = Withdrawal::where('id', $request->id)->where('status', Status::PAYMENT_PENDING)->with('user')->firstOrFail();

        $withdraw->status         = Status::PAYMENT_REJECT;
        $withdraw->admin_feedback = $request->details;
        $withdraw->save();

        $user = $withdraw->user;
        $crypto = CryptoCurrency::where('code', $withdraw->crypto->code)->firstOrFail();
        $userBalance = Wallet::where('user_id', $user->id)->where('crypto_currency_id', $crypto->id)->firstOrFail();
        $userBalance->balance += $withdraw->amount;
        $userBalance->save();

        $transaction = new Transaction();
        $transaction->user_id = $withdraw->user_id;
        $transaction->crypto_currency_id = $withdraw->crypto_currency_id;
        $transaction->amount = $withdraw->amount;
        $transaction->post_balance = $userBalance->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->remark = 'withdraw_reject';
        $transaction->details = showAmount($withdraw->amount, 8) . ' ' . $withdraw->crypto->code . ' Refunded from withdrawal rejection';
        $transaction->trx = $withdraw->trx;
        $transaction->save();

        notify($user, 'WITHDRAW_REJECT', [
            'amount' => showAmount($withdraw->amount, 8),
            'payable' => showAmount($withdraw->payable, 8),
            'charge' => showAmount($withdraw->charge, 8),
            'currency' => $withdraw->crypto->code,
            'trx' => $withdraw->trx,
            'post_balance' => showAmount($userBalance->balance, 8),
            'admin_details' => $request->details
        ]);

        $notify[] = ['success', 'Withdrawal rejected successfully'];
        return to_route('admin.withdraw.data.pending')->withNotify($notify);
    }

}
