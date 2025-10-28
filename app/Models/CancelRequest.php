<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CancelRequest extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class)->withDefault();
    }

    public function service(){
        return $this->belongsTo(Hosting::class, 'hosting_id');
    }

    public function scopePending($query){
        return $query->where('status', 2);
    }

    public function scopeCompleted($query){
        return $query->where('status', 1);
    }

    public function showStatus(): Attribute{
    
        return new Attribute(
            get: function(){
           
                $class = "text--small badge font-weight-normal badge--";

                if ($this->status == 1){
                    $class .= 'success';
                    $text = trans('Completed');
                }
                else{
                    $class .= 'danger';
                    $text = trans('Pending');
                }
                
                return "<span class='$class'>" . trans($text) . "</span>";
            }
        );
    }

    public static function status(){
        return [
            1=> trans('Completed'),
            2=> trans('Pending')
        ];  
    }

    public static function type($implode = false){ 

        $type = [
            1=> trans('Immediate'),
            2=> trans('End of Billing Period')
        ]; 

        if($implode){
            return implode(',', array_keys($type));
        }
        
        return $type;
    }


}



