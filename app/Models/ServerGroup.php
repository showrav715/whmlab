<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\GlobalStatus;

class ServerGroup extends Model{
    
    use HasFactory, GlobalStatus;

    public function servers(){
        return $this->hasMany(Server::class, 'server_group_id');
    }

    public function scopeActive($query){ 
        return $query->where('status', 1);
    }

    public function getType(): Attribute{
    
        return new Attribute(
            get: function(){
                
                $type = '';

                if ($this->type == 1){
                    $type = 'Cpanel';
                }
                elseif ($this->type == 2){
                    $type = 'Directadmin';
                }
                elseif ($this->type == 3){
                    $type = 'Plesk';
                }
                
                return $type;
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

}
