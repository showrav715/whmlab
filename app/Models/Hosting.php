<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Hosting extends Model
{
    use HasFactory;

    protected $casts = [ 
        'next_due_date'=>'date', 
        'next_invoice_date'=>'date', 
        'suspend_date'=>'date',  
        'termination_date'=>'date',
        'reg_date'=>'date',
        'last_cron'=>'datetime'
    ];

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function user(){
        return $this->belongsTo(User::class)->withDefault();
    }

    public function product(){
        return $this->belongsTo(Product::class)->withDefault();
    }

    public function deposit(){ 
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }
  
    public function server(){
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function hostingConfigs(){
        return $this->hasMany(HostingConfig::class, 'hosting_id');
    }
 
    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function cancelRequest(){
        return $this->hasOne(CancelRequest::class, 'hosting_id');
    }

    public function invoices(){
        return $this->hasMany(Invoice::class, 'hosting_id');
    }

    public function details(){
        return $this->hasOne(InvoiceItem::class);
    }

    public function showStatus(): Attribute{
    
        return new Attribute(
            get: function(){
           
                if(request()->routeIs('admin*')){
                    $class = "text--small badge font-weight-normal badge--";
                }else{
                    $class = "badge badge--";
                }
        
                $text = 'N/A'; 
        
                if ($this->status == 1){
                    $class .= 'success';
                    $text = Self::status()[1];
                } 
                if ($this->status == 2){ 
                    $class .= 'danger';
                    $text = Self::status()[2];
                }
                elseif ($this->status == 3){
                    $class .= 'warning';
                    $text = Self::status()[3];
                }
                elseif ($this->status == 4){
                    $class .= 'dark';
                    $text = Self::status()[4];
                }
                elseif ($this->status == 5){
                    $class .= 'warning';
                    $text = Self::status()[5];
                }
                 
                return "<span class='$class'>" . trans($text) . "</span>";
            }
        );
    }

    public static function status($implode = false){

        $status = [
            1=> trans('Active'), 
            2=> trans('Pending'), 
            3=> trans('Suspended'),
            4=> trans('Terminated'), 
            5=> trans('Cancelled'),
        ]; 

        if($implode){
            return implode(',', array_keys($status));
        }

        return $status;
    }

}
