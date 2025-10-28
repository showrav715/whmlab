<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\GlobalStatus;

class Coupon extends Model
{
    use HasFactory, GlobalStatus;

    public function discount($total){

    	if($this->type == 0){ 
            return ($total * $this->discount) / 100;
    	}
    	else{
            return $this->discount;
    	}

    } 

    public static function domainStatus(){
        return [
            0=> trans('Percentage'), 
            1=> trans('Fixed'),
        ]; 
    }

    public function showStatus(): Attribute{
    
        return new Attribute(
            get: function(){
           
                if(request()->routeIs('admin*')){
                    $class = "text--small badge font-weight-normal badge--";
                }else{
                    $class = "badge badge-";
                }

                if ($this->status == 1){
                    $class .= 'success';
                    $text = trans('Enable');
                }
                else{
                    $class .= 'warning';
                    $text = trans('Disable');
                }
                
                return "<span class='$class'>" . trans($text) . "</span>";
            }
        );
    }

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public static function type(){
        $type = [
            0=> 'Percentage', 
            1=> 'Fixed'
        ]; 
        return $type;
    }

}
