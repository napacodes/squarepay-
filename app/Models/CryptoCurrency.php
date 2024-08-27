<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CryptoCurrency extends Model
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

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }
}
