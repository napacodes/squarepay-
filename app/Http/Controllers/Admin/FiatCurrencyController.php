<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\FiatCurrency;
use Illuminate\Http\Request;

class FiatCurrencyController extends Controller
{

    public function index()
    {
        $pageTitle = 'All Fiat Currencies';
        $fiats     = FiatCurrency::searchable(['name','code','symbol'])->latest()->paginate(getPaginate());
        return view('admin.fiat.index',compact('pageTitle', 'fiats'));
    }

    public function store(Request $request, $id = 0) {
        $request->validate([
            'name'   => 'required|max:40',
            'code'   => 'required|max:40',
            'symbol' => 'required|max:40',
            'rate'   => 'required|numeric|gt:0',
        ]);

        if ($id) {
            $fiat         = FiatCurrency::findOrFail($id);
            $notification = 'Fiat currency updated successfully';
            $fiat->status = $request->status ? Status::ENABLE : Status::DISABLE;
        }else{
            $fiat         = new FiatCurrency();
            $notification = 'Fiat currency added successfully';
        }

        $fiat->name   = $request->name;
        $fiat->code   = $request->code;
        $fiat->symbol = $request->symbol;
        $fiat->rate   = $request->rate;
        $fiat->save();

        $notify[] = ['success', $notification];

        return back()->withNotify($notify);
    }

}
