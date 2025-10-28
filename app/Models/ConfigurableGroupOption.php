<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\GlobalStatus;

class ConfigurableGroupOption extends Model{
    
    use HasFactory, GlobalStatus;

    public function group(){
        return $this->belongsTo(ConfigurableGroup::class, 'configurable_group_id', 'id');
    }

    public function subOptions(){
        return $this->hasMany(ConfigurableGroupSubOption::class, 'configurable_group_option_id', 'id');
    } 
 
    public function activeSubOptions(){ 
        return $this->subOptions()->where('status', 1);
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
