<?php

namespace App\Http\Controllers\Front;

use App\ProductsAttribute;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\Paginator;
use App\Category;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use App\Cart;

class ProductsController extends Controller
{
    public function listing(Request $request)
    {
        Paginator::useBootstrap();
        if ($request->ajax()) {
            $data = $request->all();
            //dd($data);
            $url = $data['url'];
            $categoryCount = Category::where(['url' => $url, 'status' => 1])->count();
            if ($categoryCount > 0) {
                //echo "Category exists";die;
                $categoryDetails = Category::catDetails($url);
                //echo "<pre>";print_r($categoryDetails);die;
                $categoryProducts = Product::with('brand')->whereIn('category_id',
                    $categoryDetails['catIds'])->where('status', 1);
                //echo "<pre>";print_r($categoryDetails);
                //echo "<pre>";print_r($categoryProducts);die;

                //If Fabric Filter is selected
                if (isset($data['fabric']) && !empty($data['fabric'])) {
                    $categoryProducts->whereIn('products.fabric', $data['fabric']);
                }
                //If Sleeve Filter is selected
                if (isset($data['sleeve']) && !empty($data['sleeve'])) {
                    $categoryProducts->whereIn('products.sleeve', $data['sleeve']);
                }
                //If Pattern Filter is selected
                if (isset($data['pattern']) && !empty($data['pattern'])) {
                    $categoryProducts->whereIn('products.pattern', $data['pattern']);
                }
                //If Fit Filter is selected
                if (isset($data['fit']) && !empty($data['fit'])) {
                    $categoryProducts->whereIn('products.fit', $data['fit']);
                }
                //If Occasion Filter is selected
                if (isset($data['occasion']) && !empty($data['occasion'])) {
                    $categoryProducts->whereIn('products.occasion', $data['occasion']);
                }

                //If Sort Option is selected
                if (isset($data['sort']) && !empty($data['sort'])) {
                    if ($data['sort'] == "product_latest") {
                        $categoryProducts->orderBy('id', 'Desc');
                    } else if ($data['sort'] == "product_name_a_z") {
                        $categoryProducts->orderBy('product_name', 'Asc');
                    } else if ($data['sort'] == "product_name_z_a") {
                        $categoryProducts->orderBy('product_name', 'Desc');
                    } else if ($data['sort'] == "price_lowest") {
                        $categoryProducts->orderBy('product_price', 'Asc');
                    } else if ($data['sort'] == "price_highest") {
                        $categoryProducts->orderBy('product_price', 'Desc');
                    }
                } else {
                    $categoryProducts->orderBy('id', 'Desc');
                }
                $categoryProducts = $categoryProducts->paginate(30);

                return view('front.products.ajax_products_listing')->with(compact('categoryDetails', 'categoryProducts',
                    'url'));
            } else {
                abort(404);
            }

        } else {
            $url = Route::getFacadeRoot()->current()->uri();
            $categoryCount = Category::where(['url' => $url, 'status' => 1])->count();
            if ($categoryCount > 0) {
                //echo "Category exists";die;
                $categoryDetails = Category::catDetails($url);
                //echo "<pre>";print_r($categoryDetails);die;
                $categoryProducts = Product::with('brand')->whereIn('category_id',
                    $categoryDetails['catIds'])->where('status', 1);
                //echo "<pre>";print_r($categoryDetails);
                //echo "<pre>";print_r($categoryProducts);die;
                $categoryProducts = $categoryProducts->paginate(30);

                //Product Filters
                $productFilters = Product::productFilters();
                //dd($productFilters);
                $fabricArray = $productFilters['fabricArray'];
                $sleeveArray = $productFilters['sleeveArray'];
                $patternArray = $productFilters['patternArray'];
                $fitArray = $productFilters['fitArray'];
                $occasionArray = $productFilters['occasionArray'];
                $page_name = 'listing';

                return view('front.products.listing')->with(compact('categoryDetails', 'categoryProducts',
                    'url', 'fabricArray', 'sleeveArray', 'patternArray', 'fitArray', 'occasionArray', 'page_name'));
            } else {
                abort(404);
            }

        }

    }

    public function detail($id)
    {
        $productDetails = Product::with(['category', 'brand', 'attributes' => function ($query) {
            $query->where('status', 1);

        }, 'images'])->find($id)->toArray();
        //dd($productDetails);
        $total_stock = ProductsAttribute::where('product_id', $id)->sum('stock');
        $relatedProducts = Product::where('category_id', $productDetails['category']['id'])->where('id', '!=', $id)->
        limit(3)->inRandomOrder()->get()->toArray();
        //dd($relatedProducts);

        return view('front.products.detail')->with(compact('productDetails', 'total_stock', 'relatedProducts'));

    }

    public function getProductPrice(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            //dd($request);
            $getDiscountedAttrPrice = Product::getDiscountedAttrPrice($data['product_id'], $data['size']);

            return $getDiscountedAttrPrice;
        }

    }

    public function addtocart(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            // dd($data);
            //Check if Product Stock is Available or Not
            $getProductStock = ProductsAttribute::where(['product_id' => $data['product_id'], 'size' => $data['size']])->first()->toArray();
            //dd($getProductStock['stock']);
            if ($getProductStock['stock'] < $data['quantity']) {
                $message = "Required quantity is not available!";
                session::flash('error_message', $message);
                return redirect()->back();
            }
            //Generate Session Id if not exist
            $session_id = Session::get('session_id');
            if (empty($session_id)) {
                $session_id = Session::getId();
                Session::put('session_id', $session_id);
            }
            //Check if the product already exist in User Cart
            if (Auth::check()) {
                //User is logged in
                $countProducts = Cart::where(['product_id' => $data['product_id'],
                    'size' => $data['size'], 'user_id' => Auth::user()->id])->count();

            } else {
                //User is not logged in
                $countProducts = Cart::where(['product_id' => $data['product_id'],
                    'size' => $data['size'], 'session_id' => Session::get('session_id')])->count();
            }
            if ($countProducts > 0) {
                $message = "Product already exists in the cart!";
                session::flash('error_message', $message);
                return redirect()->back();
            }
            //Save Product in Cart
            $cart = new Cart;
            $cart->session_id = $session_id;
            $cart->product_id = $data['product_id'];
            $cart->size = $data['size'];
            $cart->quantity = $data['quantity'];
            $cart->save();

            $message = "Product has been added to the cart";
            session::flash('success_message', $message);
            return redirect('cart');

            /*Cart::insert(['session_id'=>$session_id,
                'product_id'=>$data['product_id'],
                'size'=>$data['size'],
                'quantity'=>$data['quantity']]);*/
        }
    }

    public function cart()
    {
        $userCartItems = Cart::userCartItems();
        //dd($userCartItems);
        return view('front.products.cart')->with(compact('userCartItems'));

    }

    public function updateCartItemsQty(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            //dd($data);
            //Get Cart Details
            $cartDetails=Cart::find($data['cartid']);

            //Get Available Product Stock
            $availableStock = ProductsAttribute::select('stock')->
            where(['product_id'=>$cartDetails['product_id'],'size'=>$cartDetails['size']])->first()->toArray();

           /* echo "Demanded stock:".$data['qty'];
            echo "<br>";
            echo "Available stock:".$availableStock['stock'];die;*/

            //Check if Stock is available or not
            if($data['qty']>$availableStock['stock']){
                $userCartItems = Cart::userCartItems();
                return response()->json([
                    'status'=>false,
                    'message'=>'Product Size is not Avilable',
                    'view' => (string)View::make('front.products.cart_items')->with(compact('userCartItems'))
                ]);
            }
            //Check if Size is available
            $availableSize=ProductsAttribute::where(['product_id'=>$cartDetails['product_id'],
                'size'=>$cartDetails['size'],'status'=>1])->count();
            if($availableSize==0){
                $userCartItems = Cart::userCartItems();
                return response()->json([
                    'status'=>false,
                    'message'=>'Product Stock is not Avilable',
                    'view' => (string)View::make('front.products.cart_items')->with(compact('userCartItems'))
                ]);
            }

            //Get Cart Details
            Cart::where('id', $data['cartid'])->update(['quantity' => $data['qty']]);
            $userCartItems = Cart::userCartItems();
            return response()->json([
                'status'=>true,
                'view' => (string)View::make('front.products.cart_items')->with(compact('userCartItems'))]);
        }

    }
}
