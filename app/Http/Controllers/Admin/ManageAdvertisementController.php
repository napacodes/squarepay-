<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Advertisement;

class ManageAdvertisementController extends Controller
{
    public function index($userId = 0)
    {
        $pageTitle      = 'Advertisements';
        $search         = request()->search;
        $advertisements = Advertisement::query();

        if($userId){
            $advertisements->where('user_id', $userId);
        }

        if ($search) {
            $advertisements = $advertisements->whereHas('user', function ($ad) use ($search) {
                $ad->active()->where('username', 'like', "%$search%");
            });
        }

        $advertisements = $advertisements->latest()->with(['fiat', 'fiatGateway', 'crypto', 'user', 'user.wallets'])->latest()->paginate(getPaginate());
        return view('admin.advertisement.index', compact('pageTitle', 'advertisements'));
    }

    function updateStatus($id)
    {
        $advertisement = Advertisement::findOrFail($id);

        if ($advertisement->status == Status::ENABLE) {
            $advertisement->status = Status::DISABLE;
            $notification = 'Advertisement disabled successfully';
        } else {
            $advertisement->status = Status::ENABLE;
            $notification = 'Advertisement enabled successfully';
        }

        $advertisement->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }
}
