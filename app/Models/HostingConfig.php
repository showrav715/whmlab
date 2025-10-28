<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostingConfig extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'hosting_id', 'configurable_group_option_id', 'configurable_group_sub_option_id'];    

    public function select(){
        return $this->belongsTo(ConfigurableGroupOption::class, 'configurable_group_option_id')->withDefault();
    }
 
    public function option(){ 
        return $this->belongsTo(ConfigurableGroupSubOption::class, 'configurable_group_sub_option_id')->withDefault();
    }

}
