<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductConfiguration extends Model{
    
    use HasFactory;

    public function group(){
        return $this->belongsTo(ConfigurableGroup::class, 'configurable_group_id');
    }

    public function activeGroup(){ 
        return $this->group()->where('status', 1)->withDefault();
    }
 
}
