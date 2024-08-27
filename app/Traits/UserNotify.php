<?php
namespace App\Traits;

use App\Constants\Status;

trait UserNotify
{
    public static function notifyToUser()
    {
        return [
            'allUsers'              => 'All Users',
            'selectedUsers'         => 'Selected Users',
            'advertisedUsers'       => 'Advertised Users',
            'notAdvertisedUsers'    => 'Not Advertised Users',
            'reviewedUsers'         => 'Review given Users',
            'notReviewedUsers'      => 'Review not given Users',
            'kycUnverified'         => 'Kyc Unverified Users',
            'kycVerified'           => 'Kyc Verified Users',
            'kycPending'            => 'Kyc Pending Users',
            'twoFaDisableUsers'     => '2FA Disable User',
            'twoFaEnableUsers'      => '2FA Enable User',
            'hasDepositedUsers'     => 'Deposited Users',
            'notDepositedUsers'     => 'Not Deposited Users',
            'topDepositedUsers'     => 'Top Deposited Users',
            'hasWithdrawUsers'      => 'Withdraw Users',
            'pendingWithdrawUsers'  => 'Pending Withdraw Users',
            'rejectedWithdrawUsers' => 'Rejected Withdraw Users',
            'pendingTicketUser'     => 'Pending Ticket Users',
            'answerTicketUser'      => 'Answer Ticket Users',
            'closedTicketUser'      => 'Closed Ticket Users',
            'notLoginUsers'         => 'Last Few Days Not Login Users',
        ];
    }

    public function scopeSelectedUsers($query)
    {
        return $query->whereIn('id', request()->user ?? []);
    }

    public function scopeAllUsers($query)
    {
        return $query;
    }

    public function scopeAdvertisedUsers($query)
    {
        return $query->whereHas('advertisements');
    }
    public function scopeNotAdvertisedUsers($query)
    {
        return $query->whereDoesntHave('advertisements');
    }
    public function scopeReviewedUsers($query)
    {
        return $query->whereHas('reviews');
    }
    public function scopeNotReviewedUsers($query)
    {
        return $query->whereDoesntHave('reviews');
    }

    public function scopeTwoFaDisableUsers($query)
    {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableUsers($query)
    {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasDepositedUsers($query)
    {
        return $query->whereHas('deposits');
    }

    public function scopeNotDepositedUsers($query)
    {
        return $query->whereDoesntHave('deposits');
    }

    public function scopeTopDepositedUsers($query)
    {
        return $query->withSum('deposits', 'amount')->orderBy('deposits_sum_amount', 'desc')->take(request()->number_of_top_deposited_user ?? 10);
    }

    public function scopeHasWithdrawUsers($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->approved();
        });
    }

    public function scopePendingWithdrawUsers($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->pending();
        });
    }

    public function scopeRejectedWithdrawUsers($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->rejected();
        });
    }

    public function scopePendingTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY]);
        });
    }

    public function scopeClosedTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_CLOSE);
        });
    }

    public function scopeAnswerTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {

            $q->where('status', Status::TICKET_ANSWER);
        });
    }

    public function scopeNotLoginUsers($query)
    {
        return $query->whereDoesntHave('loginLogs', function ($q) {
            $q->whereDate('created_at', '>=', now()->subDays(request()->number_of_days ?? 10));
        });
    }

    public function scopeKycVerified($query)
    {
        return $query->where('kv', Status::KYC_VERIFIED);
    }

}
