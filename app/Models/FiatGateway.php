<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class FiatGateway extends Model
{
    use  GlobalStatus;

    protected $casts = [
        'code' => 'array'
    ];

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }

    public function tradeRequests()
    {
        return $this->hasMany(Trade::class, 'trade_id');
    }

    // End Scopes
    public static function getGateways()
    {
        return self::active()->orderBy('name')->get()->map(function ($gateway) {
            $fiat            = FiatCurrency::active()->whereIn('id', $gateway->code)->get(['id', 'code', 'name', 'symbol', 'rate', 'status']);
            $gateway['fiat'] = $fiat;
            return $gateway;
        });
    }
}
