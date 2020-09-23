<?php

namespace App\Http\Controllers\Front;

use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function listing($url, Request $request){
        if($request->ajax()){
            $data=$request->all();
            //dd($data);
            $url=$data['url'];
            $categoryCount=Category::where(['url'=>$url,'status'=>1])->count();
            if($categoryCount>0){
                //echo "Category exists";die;
                $categoryDetails=Category::catDetails($url);
                //echo "<pre>";print_r($categoryDetails);die;
                $categoryProducts=Product::with('brand')->whereIn('category_id',
                    $categoryDetails['catIds'])->where('status',1);
                //echo "<pre>";print_r($categoryDetails);
                //echo "<pre>";print_r($categoryProducts);die;

                //If Sort Option is selected by User
                if(isset($data['sort']) && !empty($data['sort'])){
                    if($data['sort']=="product_latest"){
                        $categoryProducts->orderBy('id','Desc');
                    } else if($data['sort']=="product_name_a_z"){
                        $categoryProducts->orderBy('product_name','Asc');
                    }else if($data['sort']=="product_name_z_a"){
                        $categoryProducts->orderBy('product_name','Desc');
                    }else if($data['sort']=="price_lowest"){
                        $categoryProducts->orderBy('product_price','Asc');
                    }else if($data['sort']=="price_highest"){
                        $categoryProducts->orderBy('product_price','Desc');
                    }
                }else{
                    $categoryProducts->orderBy('id','Desc');
                }
                $categoryProducts=$categoryProducts->paginate(30);

                return view('front.products.ajax_products_listing')->with(compact('categoryDetails','categoryProducts',
                    'url'));
            }else{
                abort(404);
            }

        }else{
            $categoryCount=Category::where(['url'=>$url,'status'=>1])->count();
            if($categoryCount>0){
                //echo "Category exists";die;
                $categoryDetails=Category::catDetails($url);
                //echo "<pre>";print_r($categoryDetails);die;
                $categoryProducts=Product::with('brand')->whereIn('category_id',
                    $categoryDetails['catIds'])->where('status',1);
                //echo "<pre>";print_r($categoryDetails);
                //echo "<pre>";print_r($categoryProducts);die;
                $categoryProducts=$categoryProducts->paginate(30);
                return view('front.products.listing')->with(compact('categoryDetails','categoryProducts',
                    'url'));
            }else{
                abort(404);
            }

        }

    }
}
