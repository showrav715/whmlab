<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model
{
    use HasFactory; 

    public function hostings(){
        return $this->hasMany(Hosting::class);
    }

    public function domains(){
        return $this->hasMany(Domain::class);
    }

    public function coupon(){
        return $this->belongsTo(Coupon::class);
    }
    
    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
 
    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function scopePending($query){
        return $query->where('status', 2);
    }

    public function scopeCancelled($query){
        return $query->where('status', 3);
    } 

    public function scopeCancel($query){
        return $query->where('status', 3);
    }

    public function showStatus(): Attribute{
    
        return new Attribute(
            get: function(){
           
                if(request()->routeIs('admin*')){
                    $class = "text--small badge font-weight-normal badge--";
                }else{
                    $class = "badge badge-";
                }
        
                $text = 'N/A';
         
                if ($this->status == 1){ 
                    $class .= 'success';
                    $text = self::status()[1];
                }
                elseif ($this->status == 2){
                    $class .= 'warning';
                    $text = self::status()[2];
                }
                elseif($this->status == 3){
                    $class .= 'warning';
                    $text = self::status()[3];
                }
                
                return "<span class='$class'>" . trans($text) . "</span>";
            }
        );
    }

    public static function status(){
        return [
            1=> trans('Active'),
            2=> trans('Pending'), 
            3=> trans('Cancelled'),
        ]; 
    }


}


