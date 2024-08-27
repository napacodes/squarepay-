<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiatCurrency extends Model
{
    use HasFactory, GlobalStatus;

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }

    public function tradeRequests()
    {
        return $this->hasMany(Trade::class, 'trade_id');
    }
}
