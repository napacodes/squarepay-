<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;

class DepositController extends Controller
{
    public function deposit($userId = null)
    {
        $pageTitle = 'Deposit History';
        $deposits  = Deposit::searchable(['trx', 'user:username'])->dateFilter()->with('user', 'crypto')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }
    
    public function details($id)
    {
        $deposit   = Deposit::where('id', $id)->with(['user', 'crypto'])->firstOrFail();
        $pageTitle = $deposit->user->username . ' requested ' . showAmount($deposit->amount);
        $details   = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
        return view('admin.deposit.detail', compact('pageTitle', 'deposit', 'details'));
    }
}
