<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainPricing extends Model
{
    use HasFactory;

    protected $appends = ['firstPrice']; 

    public function getFirstPriceAttribute(){
        $data = $this;

        if($data->one_year_price >= 0){
            $array = ['year'=>1, 'price'=> getAmount($data->one_year_price)];
        }
        elseif($data->two_year_price >= 0){
            $array = ['year'=>2, 'price'=> getAmount($data->two_year_price)];
        }
        elseif($data->three_year_price >= 0){
            $array = ['year'=>3, 'price'=> getAmount($data->three_year_price)];
        }
        elseif($data->four_year_price >= 0){
            $array = ['year'=>4, 'price'=> getAmount($data->four_year_price)];
        }
        elseif($data->five_year_price >= 0){
            $array = ['year'=>5, 'price'=> getAmount($data->five_year_price)];
        }
        elseif($data->six_year_price >= 0){
            $array = ['year'=>6, 'price'=> getAmount($data->six_year_price)];
        }

        return $array ?? [];
    }

    public function singlePrice($regPeriod, $type = false){
        $data = $this;

        $array = [
            1=>['price'=>$data->one_year_price, 'id_protection'=>$data->one_year_id_protection],
            2=>['price'=>$data->two_year_price, 'id_protection'=>$data->two_year_id_protection],
            3=>['price'=>$data->three_year_price, 'id_protection'=>$data->three_year_id_protection],
            4=>['price'=>$data->four_year_price, 'id_protection'=>$data->four_year_id_protection],
            5=>['price'=>$data->five_year_price, 'id_protection'=>$data->five_year_id_protection],
            6=>['price'=>$data->six_year_price, 'id_protection'=>$data->six_year_id_protection],
        ];

        if(!$type){
            return $array[$regPeriod]['price'];
        }

        return $array[$regPeriod]['id_protection'];   
    }

    public function renewPrice(){
        $data = $this;

        $array = [
            1=>['price'=>$data->one_year_price, 'renew'=>$data->one_year_renew],
            2=>['price'=>$data->two_year_price, 'renew'=>$data->two_year_renew],
            3=>['price'=>$data->three_year_price, 'renew'=>$data->three_year_renew],
            4=>['price'=>$data->four_year_price, 'renew'=>$data->four_year_renew],
            5=>['price'=>$data->five_year_price, 'renew'=>$data->five_year_renew],
            6=>['price'=>$data->six_year_price, 'renew'=>$data->six_year_renew],
        ];

        array_walk($array, function(&$item){
            $item = $item['price'] >= 0 ? $item : null;
        });

        return array_filter($array);

    }




}
