<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentWindow;
use Illuminate\Http\Request;

class PaymentWindowController extends Controller
{
    public function index()
    {
        $pageTitle      = 'Payment Windows';
        $paymentWindows = PaymentWindow::orderBy('minute')->get();
        return view('admin.fiat.payment_window', compact('pageTitle', 'paymentWindows'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'minute' => 'required|integer|gt:0|unique:payment_windows,minute,' . $id
        ]);

        if ($id) {
            $paymentWindow = PaymentWindow::findOrFail($id);
            $notification       = 'Payment window updated successfully';
        } else {
            $paymentWindow = new PaymentWindow();
            $notification       = 'Payment window added successfully';
        }

        $paymentWindow->minute = $request->minute;
        $paymentWindow->save();

        $notify[] = ['success', $notification];

        return back()->withNotify($notify);
    }

    public function remove($id)
    {
        PaymentWindow::where('id', $id)->delete();
        $notify[] = ['success', 'Payment window removed successfully'];
        return back()->withNotify($notify);
    }
}
