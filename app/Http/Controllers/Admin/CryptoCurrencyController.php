<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\CryptoCurrency;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class CryptoCurrencyController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Crypto Currencies';
        $cryptos   = CryptoCurrency::searchable(['name', 'code', 'symbol'])->latest()->paginate(getPaginate());

        return view('admin.crypto.index', compact('pageTitle', 'cryptos'));
    }

    public function add()
    {
        $pageTitle = 'Add New Crypto Currency';
        return view('admin.crypto.form', compact('pageTitle'));
    }

    public function edit($id)
    {
        $crypto    = CryptoCurrency::findOrFail($id);
        $pageTitle = 'Update ' . $crypto->name;
        return view('admin.crypto.form', compact('pageTitle', 'crypto'));
    }

    public function store(Request $request, $id = 0)
    {
        $this->validation($request, $id);

        if ($id) {
            $crypto         = CryptoCurrency::findOrFail($id);
            $notification   = 'Crypto currency updated successfully';
        } else {
            $crypto       = new CryptoCurrency();
            $notification = 'Crypto currency added successfully';
        }

        if ($request->hasFile('image')) {
            $fileName = fileUploader($request->image, getFilePath('crypto'), getFileSize('crypto'), @$crypto->image);
            $crypto->image = $fileName;
        }

        $crypto->name                    = $request->name;
        $crypto->code                    = $request->code;
        $crypto->symbol                  = $request->symbol;
        $crypto->rate                    = $request->rate;
        $crypto->deposit_charge_fixed    = $request->deposit_charge_fixed;
        $crypto->deposit_charge_percent  = $request->deposit_charge_percent;
        $crypto->withdraw_charge_fixed   = $request->withdraw_charge_fixed;
        $crypto->withdraw_charge_percent = $request->withdraw_charge_percent;
        $crypto->save();

        $notify[] = ['success', $notification];

        return back()->withNotify($notify);
    }

    protected function validation($request, $id)
    {

        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'image'                   => [$imageValidation, 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'name'                    => 'required|max:40',
            'code'                    => 'required|max:40',
            'symbol'                  => 'required|max:40',
            'rate'                    => 'required|numeric|gt:0',
            'deposit_charge_fixed'    => 'required|numeric|min:0',
            'deposit_charge_percent'  => 'required|numeric|max:100|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'withdraw_charge_fixed'   => 'required|numeric|min:0',
            'withdraw_charge_percent' => 'required|numeric|max:100|min:0|regex:/^\d+(\.\d{1,2})?$/'
        ]);
    }

    function updateStatus($id)
    {
        $cryptoCurrency = CryptoCurrency::findOrFail($id);

        if ($cryptoCurrency->status == Status::ENABLE) {
            $cryptoCurrency->status = Status::DISABLE;
            $notification = 'Crypto currency disabled successfully';
        } else {
            $cryptoCurrency->status = Status::ENABLE;
            $notification = 'Crypto currency enabled successfully';
        }

        $cryptoCurrency->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }
}
