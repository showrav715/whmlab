<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\GlobalStatus;

class ConfigurableGroupSubOption extends Model{ 
    
    use HasFactory, GlobalStatus; 

    public function group(){
        return $this->belongsTo(ConfigurableGroup::class, 'configurable_group_id', 'id');
    }

    public function price(){
        return $this->hasOne(Pricing::class, 'configurable_group_sub_option_id', 'id');
    }

    public function getOnlyPrice(){
        return $this->price()->select([
                'configurable_group_sub_option_id', 'id', 'monthly_setup_fee', 'quarterly_setup_fee', 'semi_annually_setup_fee', 'annually_setup_fee', 'biennially_setup_fee', 'triennially_setup_fee', 'monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially'
            ]);
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

}
