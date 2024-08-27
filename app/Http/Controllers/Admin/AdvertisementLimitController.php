<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdLimit;
use Illuminate\Http\Request;

class AdvertisementLimitController extends Controller
{
    public function index()
    {
        $pageTitle = 'Advertisement Limit';
        $limits    = AdLimit::latest()->paginate(getPaginate());
        return view('admin.advertisement.limit', compact('pageTitle', 'limits'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'completed_trade' => 'required|integer|min:0',
            'ad_limit'        => 'required|integer|min:0'
        ]);

        if ($id) {
            $limit   = AdLimit::findOrFail($id);
            $message = 'Advertisement limit updated successfully';
        } else {
            $limit   = new AdLimit();
            $message = 'Advertisement limit added successfully';
        }

        $limit->completed_trade = $request->completed_trade;
        $limit->ad_limit        = $request->ad_limit;
        $limit->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function remove($id)
    {
        AdLimit::where('id', $id)->delete();

        $notify[] = ['success', 'Advertisement limit removed successfully'];
        return back()->withNotify($notify);
    }
}
