<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function tradeRequest()
    {
        return $this->belongsTo(Trade::class, 'trade_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
