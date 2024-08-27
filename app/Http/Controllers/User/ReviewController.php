<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Trade;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $uniqueId)
    {
        $request->validate([
            'type'     => 'required|in:1,0',
            'feedback' => 'required|max:500',
        ], [
            'type.required' => 'Positive or negative, one needs to be checked'
        ]);

        $user = auth()->user();

        $trade = Trade::where('uid', $uniqueId)->where('status', 1)->where(function ($q) use ($user) {
            $q->orWhere('buyer_id', $user->id)->orWhere('seller_id', $user->id);
        })->first();

        if (!$trade || ($trade->advertisement->user_id == $user->id)) {
            abort(401, 'Unauthorized Action');
        }

        $existingReviewCheck = Review::where('advertisement_id', $trade->advertisement_id)->where('user_id', $user->id)->first();

        if ($existingReviewCheck) {
            $review = $existingReviewCheck;
        } else {
            $review                   = new Review();
            $review->user_id          = $user->id;
            $review->advertisement_id = $trade->advertisement->id;
            // Mark trade as reviewed by someone
            $trade->reviewed          = 1;
        }

        $review->trade_id         = $trade->id;
        $review->type             = $request->type;
        $review->to_id            = $trade->advertisement->user_id;
        $review->feedback         = $request->feedback;
        $review->save();

        $trade->save();

        $notify[] = ['success', 'Thanks for your feedback. Keep trading with us'];
        return back()->withNotify($notify);
    }
}
