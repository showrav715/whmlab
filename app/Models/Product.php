<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\GlobalStatus;

class Product extends Model 
{
    use HasFactory, GlobalStatus;
 
    public function configures() {
        return $this->belongsToMany(ProductConfiguration::class, 'product_configurations', 'product_id', 'configurable_group_id');
    } 
  
    public function getConfigs() {   
        return $this->hasMany(ProductConfiguration::class);
    } 
 
    public function price() {
        return $this->hasOne(Pricing::class);
    }

    public function serverGroup() {
        return $this->belongsTo(ServerGroup::class, 'server_group_id'); 
    }

    public function serviceCategory() { 
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function getItemAttribute(){
        return productType()[$this->product_type];
    }

    public function showPaymentType(): Attribute{
    
        return new Attribute(
            get: function(){
           
                if(request()->routeIs('admin*')){
                    $class = "text--small badge font-weight-normal badge--";
                }else{
                    $class = "badge badge-";
                }

                if ($this->payment_type == 1){
                    $class .= 'success';
                    $text = trans('One Time');
                }
                elseif($this->payment_type == 2){ 
                    $class .= 'primary';
                    $text = trans('Recurring');
                }
                
                return "<span class='$class'>" . trans($text) . "</span>";
            }
        );
    }

    public function showStock(): Attribute{
    
        return new Attribute(
            get: function(){
           
                if(request()->routeIs('admin*')){
                    $class = "text--small badge font-weight-normal badge--";
                }else{
                    $class = "badge badge-";
                }

                if($this->stock_control == 1){
                    return $this->stock_quantity;
                }
             
                $class .= 'warning';
                $text = trans('Disable');
                
                return "<span class='$class'>" . trans($text) . "</span>";
            }
        );
    }

    public function showDomainRegister(): Attribute{
    
        return new Attribute(
            get: function(){
           
                if(request()->routeIs('admin*')){
                    $class = "text--small badge font-weight-normal badge--";
                }else{
                    $class = "badge badge-";
                }

                if ($this->domain_register == 1){
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

    public function process(){
        
        $result = 'manual';

        if($this->server_group_id && $this->module_option == 1){
            $result = 'automation';
        }
        elseif($this->server_group_id && $this->module_option == 2){
            $result = 'semi_automation';
        }
        elseif($this->server_group_id && $this->module_option == 3){
            $result = 'manual';
        }

        return $result;
    }


}
