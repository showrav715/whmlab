<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingSetting extends Model
{
    use HasFactory;

    protected $casts = ['create_invoice'=>'object'];

    public function getLateFee($amount, $total = false){

        if($this->invoice_late_fee_type == 1){
            $lateFee = $this->invoice_late_fee_amount;

            if($total){
                return ($amount + $lateFee);
            }

            return $lateFee;
        } 

        $lateFee = ($amount * $this->invoice_late_fee_amount) / 100;

        if($total){
            return ($amount + $lateFee);
        }

        return $lateFee;
    }

    public static function status(){
        return [
            1=> trans('Fixed'), 
            2=> trans('Percentage')
        ]; 
    }

}
 