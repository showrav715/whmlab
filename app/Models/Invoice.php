<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Invoice extends Model
{
    use HasFactory;

    protected $casts = [
        'due_date'=>'date', 
        'paid_date'=>'date', 
        'created'=>'date', 
        'next_due_date'=>'date', 
        'last_cron'=>'datetime',  
        'reminder'=>'object'
    ];
    
    protected static function boot(){ 
        parent::boot();
        static::creating(function($invoice){
        if(!$invoice->invoice_number){ 
                $general = gs(); 
            
                if(Self::count() == 0){
                    $invoice->invoice_number = $general->invoice_start; 
                }else{
                    $invoice->invoice_number = (Self::orderBy('id', 'DESC')->first()->invoice_number + $general->invoice_increment);  
                }
               
            }
        });
    }

    public function viewDetails($onlyLink = false){
        
        $url = '#'; 
        $anchorTag  = 'N/A'; 

        if($this->order){
            $url = route('admin.orders.details', $this->order->id);
            $anchorTag = "<a href=".$url.">".trans('Order Details')."</a>";
        }
        if($this->domain_id){
            $url = route('admin.order.domain.details', $this->domain_id);
            $anchorTag = "<a href=".$url.">".trans('Domain Details')."</a>";
        }
        elseif($this->hosting_id){
            $url = route('admin.order.hosting.details', $this->hosting_id);
            $anchorTag = "<a href=".$url.">".trans('Service Details')."</a>";
        }

        if($onlyLink){
            return $url;
        }

        return $anchorTag;
    }

    public function user(){
        return $this->belongsTo(User::class)->withDefault();
    }

    public function domain(){ 
        return $this->belongsTo(Domain::class)->withDefault();
    }

    public function hosting(){
        return $this->belongsTo(Hosting::class)->withDefault();
    }

    public function order(){
        return $this->hasOne(Order::class);
    } 

    public function payments(){
        return $this->hasMany(Deposit::class)->where('status', 1);
    }
 
    public function getTrx(){
        return $this->hasOne(Transaction::class, 'invoice_id');
    } 

    public function items(){
        return $this->hasMany(InvoiceItem::class);
    }  
    
    public function scopeCancelled($query){ 
        return $query->where('status', 4);
    }
    
    public function scopePaid($query){
        return $query->where('status', 1);
    }
    
    public function scopeUnpaid($query){
        return $query->where('status', 2);
    }
    
    public function scopePaymentPending($query){
        return $query->where('status', 3);
    }
    
    public function scopeRefunded($query){
        return $query->where('status', 5);
    }

    public function showStatus(): Attribute{
    
        return new Attribute(
            get: function(){ 
        
                $text = 'N/A';
                $class = "badge badge--";
        
                if($this->status == 1){
                    $class .= 'success';
                    $text = Self::status()[1];
                }
                elseif($this->status == 2){
                    $class .= 'danger';
                    $text = Self::status()[2];
                }
                elseif($this->status == 3){
                    $class .= 'warning';
                    $text = Self::status()[3];  
                }
                elseif($this->status == 4){
                    $class .= 'dark';
                    $text = Self::status()[4]; 
                }
                elseif($this->status == 5){
                    $class .= 'dark';
                    $text = Self::status()[5]; 
                }
             
                return "<span class='$class'>" . trans($text) . "</span>";
            }
        );
    }

    public static function status($implode = false){ 

        $status = [
            1=> trans('Paid'),
            2=> trans('Unpaid'),
            3=> trans('Payment Pending'), 
            4=> trans('Cancelled'),
            5=> trans('Refunded')
        ];

        if($implode){
            return implode(',', array_keys($status));
        }

        return $status;
    }

    public function updateReminder($column = null){

        if($this->reminder && $column){
            $array = (array) $this->reminder;
            $array[$column] = 1;
        }else{
            $array = [
                'unpaid_reminder'=>0,
                'first_over_due_reminder'=>0,
                'second_over_due_reminder'=>0,
                'third_over_due_reminder'=>0,
                'add_late_fee'=>0,
            ];
        }

        return $array;
    }

    public function getInvoiceNumber(): Attribute{
        return new Attribute(
            get: function(){ 
                return '#'.@$this->invoice_number;
            }
        );
    }

} 

