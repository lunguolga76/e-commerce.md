<?php

namespace App\Http\Controllers\Front;

use App\ProductsAttribute;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\Paginator;
use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function listing(Request $request){
        Paginator::useBootstrap();
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

                //If Fabric Filter is selected
                if(isset($data['fabric']) && !empty($data['fabric'])){
                    $categoryProducts->whereIn('products.fabric',$data['fabric']);
                }
                //If Sleeve Filter is selected
                if(isset($data['sleeve']) && !empty($data['sleeve'])){
                    $categoryProducts->whereIn('products.sleeve',$data['sleeve']);
                }
                //If Pattern Filter is selected
                if(isset($data['pattern']) && !empty($data['pattern'])){
                    $categoryProducts->whereIn('products.pattern',$data['pattern']);
                }
                //If Fit Filter is selected
                if(isset($data['fit']) && !empty($data['fit'])){
                    $categoryProducts->whereIn('products.fit',$data['fit']);
                }
                //If Occasion Filter is selected
                if(isset($data['occasion']) && !empty($data['occasion'])){
                    $categoryProducts->whereIn('products.occasion',$data['occasion']);
                }

                //If Sort Option is selected
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
            $url=Route::getFacadeRoot()->current()->uri();
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

                //Product Filters
                $productFilters=Product::productFilters();
                //dd($productFilters);
                $fabricArray=$productFilters['fabricArray'];
                $sleeveArray=$productFilters['sleeveArray'];
                $patternArray=$productFilters['patternArray'];
                $fitArray=$productFilters['fitArray'];
                $occasionArray=$productFilters['occasionArray'];
                $page_name='listing';

                return view('front.products.listing')->with(compact('categoryDetails','categoryProducts',
                    'url','fabricArray','sleeveArray','patternArray','fitArray','occasionArray','page_name'));
            }else{
                abort(404);
            }

        }

    }
    public function detail($id){
        $productDetails=Product::with('category','brand','attributes','images')->find($id)->toArray();
        //dd($productDetails);
       $total_stock=ProductsAttribute::where('product_id',$id)->sum('stock');
       $relatedProducts=Product::where('category_id',$productDetails['category']['id'])->where('id','!=',$id)->
       limit(3)->inRandomOrder()->get()->toArray();
       //dd($relatedProducts);

        return view('front.products.detail')->with(compact('productDetails','total_stock','relatedProducts'));

    }
    public function getProductPrice(Request $request){
        if($request->ajax()){
            $data=$request->all();
            //dd($request);
            $getProductPrice=ProductsAttribute::where(['product_id'=>$data['product_id'],'size'=>$data['size']])->first();
            return $getProductPrice->price;
        }

    }
    public function addtocart(Request $request){
        if($request->isMethod('post')){
            $data=$request->all();
            dd($data);
        }
    }
}
