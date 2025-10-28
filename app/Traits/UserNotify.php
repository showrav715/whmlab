<?php
namespace App\Traits;

use App\Constants\Status;

trait UserNotify
{
    public static function notifyToUser(){
        return [
            'allUsers'              => 'All Users',
            'selectedUsers'         => 'Selected Users',
            'kycUnverified'         => 'Kyc Unverified Users',
            'kycVerified'           => 'Kyc Verified Users',
            'kycPending'            => 'Kyc Pending Users',
            'withBalance'           => 'With Balance Users',
            'emptyBalanceUsers'     => 'Empty Balance Users',
            'twoFaDisableUsers'     => '2FA Disable User',
            'twoFaEnableUsers'      => '2FA Enable User',
            'hasDepositedUsers'       => 'Deposited Users',
            'notDepositedUsers'       => 'Not Deposited Users',
            'pendingDepositedUsers'   => 'Pending Deposited Users',
            'rejectedDepositedUsers'  => 'Rejected Deposited Users',
            'topDepositedUsers'     => 'Top Deposited Users',
            'hasWithdrawUsers'      => 'Withdraw Users',
            'pendingWithdrawUsers'  => 'Pending Withdraw Users',
            'rejectedWithdrawUsers' => 'Rejected Withdraw Users',
            'pendingTicketUser'     => 'Pending Ticket Users',
            'answerTicketUser'      => 'Answer Ticket Users',
            'closedTicketUser'      => 'Closed Ticket Users',

            'pendingOrderUser'      => 'Pending Order Users',
            'cancelledOrderUser'      => 'Cancelled Order Users',
            'activeOrderUser'      => 'Active Order Users',
            'allOrderUser'      => 'All Order Users',

            'paidInvoiceUser'      => 'Paid Invoice Users',
            'unpaidInvoiceUser'      => 'Unpaid Invoice Users',
            'paymentPendingInvoiceUser'      => 'Payment Pending Invoice Users',
            'cancelledInvoiceUser'      => 'Cancelled Invoice Users',
            'refundedInvoiceUser'      => 'Refunded Invoice Users',
            'allInvoiceUser'      => 'All Invoice Users',

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

    public function scopeEmptyBalanceUsers($query)
    {
        return $query->where('balance', '<=', 0);
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
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        });
    }

    public function scopeNotDepositedUsers($query)
    {
        return $query->whereDoesntHave('deposits', function ($q) {
            $q->successful();
        });
    }

    public function scopePendingDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->pending();
        });
    }

    public function scopeRejectedDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->rejected();
        });
    }

    public function scopeTopDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        })->withSum(['deposits'=>function($q){
            $q->successful();
        }], 'amount')->orderBy('deposits_sum_amount', 'desc')->take(request()->number_of_top_deposited_user ?? 10);
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

    public function scopePendingOrderUser($query)
    {
        return $query->whereHas('orders', function($order){
            $order->pending();
        });
    }

    public function scopeCancelledOrderUser($query)
    {
        return $query->whereHas('orders', function($order){
            return $order->cancelled();
        });
    }

    public function scopeActiveOrderUser($query)
    {
        return $query->whereHas('orders', function($order){
            return $order->active();
        });
    }

    public function scopeAllOrderUser($query)
    {
        return $query->whereHas('orders');
    }

    public function scopePaidInvoiceUser($query)
    {
        return $query->whereHas('invoices', function($invoice){
            $invoice->paid();
        });
    }

    public function scopeUnpaidInvoiceUser($query)
    {
        return $query->whereHas('invoices', function($invoice){
            $invoice->unpaid();
        });
    }

    public function scopePaymentPendingInvoiceUser($query)
    {
        return $query->whereHas('invoices', function($invoice){
            $invoice->paymentPending();
        });
    }

    public function scopeCancelledInvoiceUser($query)
    {
        return $query->whereHas('invoices', function($invoice){
            $invoice->cancelled();
        });
    }

    public function scopeRefundedInvoiceUser($query)
    {
        return $query->whereHas('invoices', function($invoice){
            $invoice->refunded();
        });
    }

    public function scopeAllInvoiceUser($query)
    {
        return $query->whereHas('invoices');
    }
}
