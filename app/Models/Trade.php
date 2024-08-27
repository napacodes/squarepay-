<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function fiat()
    {
        return $this->belongsTo(FiatCurrency::class, 'fiat_currency_id');
    }

    public function fiatGateway()
    {
        return $this->belongsTo(FiatGateway::class);
    }

    public function crypto()
    {
        return $this->belongsTo(CryptoCurrency::class, 'crypto_currency_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // SCOPES
    public function scopeRunning($query)
    {
        return $query->whereNotIn('status', [Status::TRADE_COMPLETED, Status::TRADE_CANCELED]);
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotIn('status', [Status::TRADE_ESCROW_FUNDED, Status::TRADE_BUYER_SENT, Status::TRADE_DISPUTED]);
    }

    public function scopeReported($query)
    {
        return $query->where('status', Status::TRADE_DISPUTED);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';
            if ($this->status == Status::TRADE_ESCROW_FUNDED) {
                $html = '<span class="badge badge--warning">' . trans('Escrow Funded') . '</span>';
            } elseif ($this->status == Status::TRADE_COMPLETED) {
                $html = '<span><span class="badge badge--success">' . trans('Completed') . '</span>';
            } elseif ($this->status == Status::TRADE_BUYER_SENT) {
                $html = '<span><span class="badge badge--primary">' . trans('Buyer Paid') . '</span>';
            } elseif ($this->status == Status::TRADE_DISPUTED) {
                $html = '<span><span class="badge badge--danger">' . trans('Reported') . '</span>';
            } else {
                $html = '<span><span class="badge badge--dark">' . trans('Cancelled') . '</span>';
            }
            return $html;
        });
    }

    public function typeBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';
            if ($this->buyer_id == auth()->user()->id) {
                $html = '<span class="badge badge--primary">' . trans("Buy") . '</span>';
            } else {
                $html = '<span class="badge badge--warning">' . trans("Sell") . '</span>';
            }
            return $html;
        });
    }
}
