<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manage Referral';
        $levels = Referral::get();
        return view('admin.referral', compact('pageTitle', 'levels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'level'           => 'required|array|min:1',
            'level.*'         => 'required|integer|min:1',
            'percent'         => 'required|array|min:1',
            'percent.*'       => 'required|numeric|gt:0|regex:/^\d+(\.\d{1,2})?$/',
            'commission_type' => 'required|in:deposit,trade',
        ], [
            'level.required'     => 'Minimum one level field is required',
            'level.*.required'   => 'Minimum one level value is required',
            'level.*.integer'    => 'Provide integer number as level',
            'level.*.min'        => 'Level should be grater than 0',
            'percent.required'   => 'Minimum one percentage field is required',
            'percent.*.required' => 'Minimum one percentage value is required',
            'percent.*.integer'  => 'Provide integer number as percentage',
            'percent.*.min'      => 'Percentage should be grater than 0',
        ]);

        Referral::where('commission_type', $request->commission_type)->delete();

        for ($i = 0; $i < count($request->level); $i++) {
            $referral                  = new Referral();
            $referral->level           = $request->level[$i];
            $referral->percent         = $request->percent[$i];
            $referral->commission_type = $request->commission_type;
            $referral->save();
        }

        $notify[] = ['success', 'Created successfully'];
        return back()->withNotify($notify);
    }

    public function updateStatus($type)
    {
        $generalSetting = gs();
        if (@$generalSetting->$type == 1) {
            @$generalSetting->$type = 0;
            $generalSetting->save();
        } elseif (@$generalSetting->$type == 0) {
            @$generalSetting->$type = 1;
            $generalSetting->save();
        } else {
            $notify[] = ['error', 'Something went wrong'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Updated successfully'];
        return back()->withNotify($notify);
    }
}
