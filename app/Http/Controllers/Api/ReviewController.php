<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store(Request $request, $uniqueId)
    {
        $validator = Validator::make($request->all(), [
            'type'     => 'required|in:1,0',
            'feedback' => 'required|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'  => 'validation_error',
                'status'  => 'error',
                'message' => ['error' => $validator->errors()->all()],
            ]);
        }

        $user = auth()->user();

        $trade = Trade::where('uid', $uniqueId)->where('status', 1)->where(function ($q) use ($user) {
            $q->orWhere('buyer_id', $user->id)->orWhere('seller_id', $user->id);
        })->first();

        if (!$trade || ($trade->advertisement->user_id == $user->id)) {
            return response()->json([
                'remark'  => 'tarde_error',
                'status'  => 'error',
                'message' => ['error' => 'Trade not found or unauthorized action'],
            ]);
        }

        $existingReviewCheck = Review::where('advertisement_id', $trade->advertisement_id)->where('user_id', $user->id)->first();

        if ($existingReviewCheck) {
            $review = $existingReviewCheck;
        } else {
            $review                   = new Review();
            $review->user_id          = $user->id;
            $review->advertisement_id = $trade->advertisement->id;

            // Mark trade as reviewed by someone
            $trade->reviewed = 1;
        }

        $review->trade_id = $trade->id;
        $review->type     = $request->type;
        $review->to_id    = $trade->advertisement->user_id;
        $review->feedback = $request->feedback;
        $review->save();

        $trade->save();

        return response()->json([
            'remark'  => 'review_stored',
            'status'  => 'success',
            'message' => ['success' => 'Thanks for your feedback. Keep trading with us'],
        ]);
    }

    public function check($uid)
    {
        $trade = Trade::where('uid', $uid)->where('status', 1)->first();

        if (!$trade || $trade->advertisement->user_id == auth()->id()) {
            return response()->json([
                'remark'  => 'trade_error',
                'status'  => 'error',
                'message' => ['error' => 'Unauthorized action or trade not found'],
            ]);
        }

        $reviewCheck = Review::where('trade_id', $trade->id)->where('user_id', auth()->id())->first();
        $isPermitted = 0;

        if ($reviewCheck || $trade->reviewed) {
            return response()->json([
                'remark'  => 'review_error',
                'status'  => 'error',
                'message' => ['error' => 'You are not allowed to proceed this action'],
            ]);
        }

        if (!$reviewCheck && !$trade->reviewed) {
            $isPermitted = 1;
        }

        if ($isPermitted) {
            $notify[] = 'You are able to make review';

            return response()->json([
                'remark'  => 'reviewable',
                'status'  => 'success',
                'message' => ['success' => $notify],
                'data'    => [
                    'is_permitted' => $isPermitted,
                ],
            ]);
        }
    }
}
