<?php

namespace App\Lib;

class SendServiceEmail{

    public static function orderNotify($invoice, $order){
        $user = $order->user;
        $itemsDescription = null;
        $items = $invoice->items()->where('trx_type', '+')->select('description')->get();

        foreach($items as $item){
            $itemsDescription .= $item->description.'<br>';
        }

        $itemsDescription = (str_replace("\n", "<br/>", $itemsDescription));

        notify($user, 'ORDER_NOTIFICATION', [
            'order_id'=>$order->id,
            'created_at'=>showDateTime($order->created_at),
            'invoice_id'=>$invoice->id,
            'amount'=>showAmount($invoice->amount, currencyFormat:false),
            'name'=>$user->fullname,
            'email'=>$user->email,
            'address'=>@$user->address->address,
            'city'=>@$user->address->city,
            'state'=>@$user->address->state,
            'zip'=>@$user->address->zip,
            'country'=>@$user->address->country,
            'phone'=>@$user->mobile,
            'invoice_link'=>route('user.invoice.view', $invoice->id),
            'client_ip'=>$order->ip_address,
            'order_items'=>$itemsDescription,
        ]);
    } 

    public static function serviceNotify($hosting){
        
        $product = $hosting->product;
        $server = @$hosting->server;
        $act = welcomeEmail()[$product->welcome_email]['act'] ?? null; 
        $user = $hosting->user;

        if(!$act){
            return false;
        }

        if($act == 'HOSTING_ACCOUNT'){    
            notify($user, $act, [
                'service_product_name' => $product->name,
                'service_domain' => $hosting->domain,
                'service_first_payment_amount' => showAmount($hosting->first_payment_amount, currencyFormat:false),
                'service_recurring_amount' => showAmount($hosting->recurring_amount, currencyFormat:false),
                'service_billing_cycle' => billingCycle(@$hosting->billing_cycle, true)['showText'],
                'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
                'service_username' => $hosting->username,
                'service_password' => $hosting->password,
                'ns1' => @$server->ns1,
                'ns2' => @$server->ns2,
                'ns3' => @$server->ns3,
                'ns4' => @$server->ns4,
                'ns1_ip' => @$server->ns1_ip,
                'ns2_ip' => @$server->ns2_ip,
                'ns3_ip' => @$server->ns3_ip,
                'ns4_ip' => @$server->ns4_ip,
                'service_server_ip' => @$server->ip_address,
            ]);
        }
        elseif($act == 'RESELLER_ACCOUNT'){
            notify($user, $act, [
                'service_domain' => $hosting->domain,
                'service_username' => $hosting->username,
                'service_password' => $hosting->password, 
                'service_product_name' => $product->name,
                'service_server_ip' => $hosting->dedicated_ip,
            ]);
        }
        elseif($act == 'VPS_SERVER'){
            notify($user, $act, [
                'service_product_name' => $product->name,
                'service_dedicated_ip' => $hosting->dedicated_ip,
                'service_password' => $hosting->password, 
                'service_assigned_ips' => $hosting->assigned_ips,
                'service_domain' => $hosting->domain,
                'ns1' => @$server->ns1,
                'ns2' => @$server->ns2,
            ]);
        }
        elseif($act == 'OTHER_PRODUCT'){
            notify($user, $act, [
                'service_product_name' => $product->name,
                'service_recurring_amount' => showAmount($hosting->recurring_amount, currencyFormat:false),
                'service_billing_cycle' => billingCycle(@$hosting->billing_cycle, true)['showText'],
                'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y')
            ]);
        }
    }

    public static function domainNotify($domain){
        notify($domain->user, 'DOMAIN_REGISTER', [
            'domain_name' => $domain->domain,
            'domain_reg_date' => showDateTime($domain->reg_date, 'd/m/Y'),
            'domain_reg_period' => $domain->reg_period, 
            'first_payment_amount' => showAmount($domain->first_payment_amount, currencyFormat:false),
            'next_due_date' => showDateTime($domain->next_due_date, 'd/m/Y')
        ]);
    }

    public static function domainRenewNotify($domain){
        notify($domain->user, 'DOMAIN_RENEW_NOTIFICATION', [
            'domain'=>$domain->domain,
            'reg_period'=>$domain->reg_period,
            'recurring_amount'=>showAmount($domain->recurring_amount, currencyFormat:false),
            'next_due_date'=>showDateTime($domain->next_due_date, 'd/m/Y'),
        ]);
    }

    public static function invoicePaymentReminder($invoice){
        notify($invoice->user, 'INVOICE_PAYMENT_REMINDER', [
            'invoice_number' => $invoice->id,
            'invoice_created' => showDatetime($invoice->created, 'd/m/Y'),
            'invoice_due_date' => showDatetime($invoice->due_date, 'd/m/Y'),
            'invoice_link' => route('user.invoice.view', $invoice->id),
        ]); 
    }

    public static function firstInvoiceOverdue($invoice){
        notify($invoice->user, 'FIRST_INVOICE_OVERDUE_NOTICE', [
            'invoice_number' => $invoice->id,
            'invoice_created' => showDatetime($invoice->created, 'd/m/Y'),
            'invoice_due_date' => showDatetime($invoice->due_date, 'd/m/Y'),
            'invoice_link' => route('user.invoice.view', $invoice->id),
        ]); 
    }

    public static function secondInvoiceOverdue($invoice){
        notify($invoice->user, 'SECOND_INVOICE_OVERDUE_NOTICE', [
            'invoice_number' => $invoice->id,
            'invoice_created' => showDatetime($invoice->created, 'd/m/Y'),
            'invoice_due_date' => showDatetime($invoice->due_date, 'd/m/Y'),
            'invoice_link' => route('user.invoice.view', $invoice->id),
        ]);
    }

    public static function thirdInvoiceOverdue($invoice){
        notify($invoice->user, 'THIRD_INVOICE_OVERDUE_NOTICE', [
            'invoice_number' => $invoice->id,
            'invoice_created' => showDatetime($invoice->created, 'd/m/Y'),
            'invoice_due_date' => showDatetime($invoice->due_date, 'd/m/Y'),
            'invoice_link' => route('user.invoice.view', $invoice->id),
        ]); 
    }

    public static function serviceSuspend($hosting, $request){
        $user = $hosting->user;
        $product = $hosting->product;

        notify($user, 'SERVICE_SUSPEND', [
            'service_name' => $product->name,
            'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y'),
            'service_suspension_reason' => $request->suspend_reason,
        ]);
    }

    public static function serviceUnsuspend($hosting){
        $user = $hosting->user;
        $product = $hosting->product;

        notify($user, 'SERVICE_UNSUSPEND', [
            'service_name' => $product->name,
            'service_next_due_date' => showDateTime($hosting->next_due_date, 'd/m/Y')
        ]);
    }

    public static function invoiceRefund($invoice, $refundAmount, $trx){
        notify($invoice->user, 'INVOICE_REFUND_NOTIFICATION', [
            'invoice_id' => $invoice->id,
            'refund_amount' => showAmount($refundAmount, currencyFormat:false),
            'trx_id' => $trx
        ]);
    }
}

