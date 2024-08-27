<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, UserNotify;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','ver_code','kyc_data'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime'
    ];


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id','desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status','!=',Status::PAYMENT_INITIATE);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status','!=',Status::PAYMENT_INITIATE);
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function refBy()
    {
        return $this->belongsTo(User::class, 'ref_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class,'ref_by');
    }

    public function allReferrals(){

        return $this->referrals()->with('refBy');
    }

    public function commissions()
    {
        return $this->hasMany(CommissionLog::class, 'to_id')->orderBy('id', 'desc');
    }

    public function feedbacks()
    {
        return $this->hasMany(Review::class, 'to_id');
    }

    public function positiveFeedbacks()
    {
        return $this->hasMany(Review::class, 'to_id')->where('type', 1);
    }

    public function negativeFeedbacks()
    {
        return $this->hasMany(Review::class, 'to_id')->where('type', 0);
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function mobileNumber(): Attribute
    {
        return new Attribute(
            get: fn () => $this->dial_code . $this->mobile,
        );
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', Status::USER_ACTIVE)->where('ev',Status::VERIFIED)->where('sv',Status::VERIFIED);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query)
    {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeKycUnverified($query)
    {
        return $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query)
    {
        return $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeEmailVerified($query)
    {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query)
    {
        return $query->where('sv', Status::VERIFIED);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

}
