<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\GlobalStatus;

class ConfigurableGroup extends Model{
    
    use HasFactory, GlobalStatus;

    public function options(){ 
        return $this->hasMany(ConfigurableGroupOption::class); 
    } 
  
    public function products() {
        return $this->belongsToMany(ProductConfiguration::class, 'product_configurations', 'configurable_group_id', 'product_id');
    } 
 
    public function getProducts() {  
        return $this->hasMany(ProductConfiguration::class);
    }
  
    public function activeOptions() {  
        return $this->options()->where('status', 1);
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
 