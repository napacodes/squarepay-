<?php

namespace App\Models;

use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use ApiQuery;

    protected $casts = [
        'detail' => 'object',
    ];

    protected $hidden = ['detail'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function crypto()
    {
        return $this->belongsTo(CryptoCurrency::class, 'crypto_currency_id');
    }
}
