<?php

namespace App;


use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Cart extends Model
{
    use HasFactory;

    public static function userCartItems(){
        if(Auth::check()){

            $userCartItems = Cart::with(['product'=>function($query){
                $query->select('id','product_name','product_code','product_color','main_image');

            }])->where('user_id',Auth::user()->id)->orderBy('id','Desc')->get()->toArray();

        }else {
            $userCartItems = Cart::with(['product'=>function($query){
                $query->select('id','product_name','product_code','product_color','main_image');

            }])->with('product')->where('session_id', Session::get('session_id'))->orderBy('id','Desc')->get()->toArray();
        }
            return $userCartItems;
        }
        public function product(){
        return $this->belongsTo('App\Product','product_id');
        }
}
