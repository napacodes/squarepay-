<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Trade;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'message' => 'required',
            'file'    => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png', 'pdf']), 'max:2000'],
        ]);

        $trade = Trade::where(function ($q) {
            $q->orWhere('buyer_id', Auth::id())->orWhere('seller_id', Auth::id());
        })->where(function ($status) {
            $status->whereIn('status', [0, 2, 8]);
        })->findOrFail($id);

        $file = null;

        if ($request->hasFile('file')) {
            $file = fileUploader($request->file, getFilePath('chat_file'));
        }

        $chat           = new Chat();
        $chat->trade_id = $trade->id;
        $chat->user_id  = Auth::id();
        $chat->message  = $request->message;
        $chat->file     = $file;
        $chat->save();

        $formUser = $trade->seller->username;
        $sendTo = $trade->buyer;

        if ($trade->buyer_id == $chat->user_id) {
            $formUser = $trade->buyer->username;
            $sendTo = $trade->seller;
        }

        notify($sendTo, 'TRADE_CHAT', [
            'from_user' => $formUser,
            'message' => $chat->message ,
            'trade_uid' => $trade->uid,
        ], null, true, $trade->uid);

        $notify[] = ['success', 'Your response is taken successfully'];
        return back()->withNotify($notify);
    }

    public function download($tradeId, $id)
    {
        $chat = Chat::where('trade_id', $tradeId)->findOrFail($id);

        if ($chat->file) {
            $file      = $chat->file;
            $full_path = getFilePath('chat_file') . '/' . $file;
            $title     = $chat->file;
            $ext       = pathinfo($file, PATHINFO_EXTENSION);
            $mimetype  = mime_content_type($full_path);
            header('Content-Disposition: attachment; filename="' . $title . '.' . $ext . '";');
            header("Content-Type: " . $mimetype);
            return readfile($full_path);
        } else {
            $notify[] = ['error', 'No downloadable file found'];
            return back()->withNotify($notify);
        }
    }
}
