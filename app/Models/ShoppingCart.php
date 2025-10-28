<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model{

    use HasFactory;

    protected $casts = ['config_options'=>'array'];

    public function user(){
        return $this->belongsTo(User::class)->withDefault();
    }

    public function domainRegister(){
        return $this->belongsTo(DomainRegister::class)->withDefault();
    }

    public function coupon(){
        return $this->belongsTo(Coupon::class)->withDefault();
    }

    public function getDomain(){
        return $this->belongsTo(Domain::class, 'domain_id', 'id')->withDefault();
    }

    public function product(){
        return $this->belongsTo(Product::class)->withDefault();
    }

    public function scopeDomainRenew($query){
        return $query->where('type', 4);
    }

    public static function type(){

        $type = [
            1=> trans('Only Hosting'), 
            2=> trans('Hosting and Domain both'),
            3=> trans('Only Domain'),
            4=> trans('Domain Renew'),
        ]; 

        return $type;
    }

}
 