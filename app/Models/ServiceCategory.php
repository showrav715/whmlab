<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\GlobalStatus;

class ServiceCategory extends Model{
      
    use HasFactory, GlobalStatus;

    public function scopeActive($query = null){
        
        if($query){
            $query = $query;
        }else{
            $query = $this;
        }
        
        return $query->where('status', 1);
    }

    public function products($filter = false){ 

        $with = ['getConfigs']; 

        if($filter){
            array_push($with, 'price');  
        }

        return $this->hasMany(Product::class, 'category_id')
            ->when($filter, function($query){  
                $query->where('status', 1)->whereHas('price', function($price){ 
                    $price->priceFilter($price); 
                });  
        })->with($with);
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


  