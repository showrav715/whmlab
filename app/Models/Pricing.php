<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    public function scopePriceFilter($price){
        return $price->where('monthly', '>=', 0)
                    ->orWhere('quarterly', '>=', 0)
                    ->orWhere('semi_annually', '>=', 0)
                    ->orWhere('annually', '>=', 0)
                    ->orWhere('biennially', '>=', 0)
                    ->orWhere('triennially', '>=', 0);
    }

}
 