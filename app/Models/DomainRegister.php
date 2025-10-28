<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\GlobalStatus;

class DomainRegister extends Model
{ 
    use HasFactory, GlobalStatus;

    protected $casts = ['params'=>'object'];

    public static function getDefault(){
        return Self::default()->active()->first(); 
    }

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function scopeDefault($query){
        return $query->where('default', 1);
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

    public function showTestMode(): Attribute{
    
        return new Attribute(
            get: function(){
           
                if(request()->routeIs('admin*')){
                    $class = "text--small badge font-weight-normal badge--";
                }else{
                    $class = "badge badge-";
                }

                if ($this->test_mode == 1){
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

    public function showDefault(): Attribute{
    
        return new Attribute(
            get: function(){
           
                if(request()->routeIs('admin*')){
                    $class = "text--small badge font-weight-normal badge--";
                }else{
                    $class = "badge badge-";
                }

                if ($this->default == 1){
                    $class .= 'primary';
                    $text = trans('Default');
                }
                else{
                    $class .= 'danger';
                    $text = trans('Selectable');
                }
                
                return "<span class='$class'>" . trans($text) . "</span>";
            }
        );
    }

}
