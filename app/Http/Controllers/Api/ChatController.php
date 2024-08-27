<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Trade;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'file'    => ['nullable',new FileTypeValidate(['jpg', 'jpeg', 'png', 'pdf']),'max:2000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark' =>'validation_error',
                'status' =>'error',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }

        $trade = Trade::where(function($q){
            $q->orWhere('buyer_id', auth()->id())->orWhere('seller_id', auth()->id());
        })->where(function($status){
            $status->whereIn('status', [0, 2, 8]);
        })->find($id);

        if (!$trade) {
            return response()->json([
                'remark'=>'trade_error',
                'status'=>'error',
                'message'=>['error'=>'trade not found'],
            ]);
        }

        $file = null;

        if($request->hasFile('file')) {
            $file = fileUploader($request->file, getFilePath('chat_file'));
        }

        $chat           = new Chat();
        $chat->trade_id = $trade->id;
        $chat->user_id  = auth()->id();
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

        return response()->json([
            'remark'=>'chat_stored',
            'status'=>'success',
            'message'=>['success'=>'Your response is taken successfully'],
        ]);
    }

    public function download($tradeId, $id)
    {
        $chat = Chat::where('trade_id', $tradeId)->find($id);

        if (!$chat) {
            return response()->json([
                'remark'=>'chat_error',
                'status'=>'error',
                'message'=>['error'=>'you can not proceed this action'],
            ]);
        }

        if ($chat->file) {
            $notify[] = 'Chat file found';
            return response()->json([
                'remark'=>'chat_found',
                'status'=>'success',
                'message'=>['success'=>$notify],
                'data'=>[
                    'chat_file' => getFilePath('chat_file').'/'.$chat->file
                ]
            ]);

        }else{
            return response()->json([
                'remark'=>'chat_error',
                'status'=>'error',
                'message'=>['error'=>'No downloadable file found'],
            ]);
        }
    }
}
