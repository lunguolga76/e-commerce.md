<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;

class IndexController extends Controller
{
   public function index(){
       //Get Featured Items
       $featuredItemsCount=Product::where('is_featured','Yes')->where('status',1)->count();
       $featuredItems=Product::where('is_featured','Yes')->where('status',1)->get()->toArray();
       //dd($featuredItems);
       $featuredItemsChunk=array_chunk($featuredItems,4);
       //dd($featuredItemsChunk);

       //Get New Products
       $newProducts=Product::orderBy('id','Desc')->where('status',1)->limit(3)->get()->toArray();
       //dd($newProducts);
       $page_name='index';
       return view('front.index')->with(compact('page_name','featuredItemsChunk','featuredItemsCount','newProducts'));
   }
}
