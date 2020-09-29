<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Product;
use App\ProductsAttribute;
use App\ProductsImage;
use App\Section;
use App\Brand;
use Illuminate\Http\Request;
use Image;
use Session;
use App\Category;





class ProductsController extends Controller
{
    public function products()
    {
        $products = Product::with(['category' => function ($query) {
            $query->select('id', 'category_name');
        }, 'section' => function ($query) {
            $query->select('id', 'name');
        }])->get();
        Session::put('page', 'products');
        // $products=json_decode(json_encode($products));
        //  dd($products);
        // echo "<pre>";print_r($products);die;
        return view('admin.products.products')->with(compact('products'));
    }

    public function updateProductStatus(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            /*echo "<pre>";print_r($data);die;*/
            if ($data['status'] == "Active") {
                $status = 0;
            } else {
                $status = 1;
            }
            Product::where('id', $data['product_id'])->update(['status' => $status]);
            return response()->json(['status' => $status, 'product_id' => $data['product_id']]);
        }
    }

    public function deleteProduct($id)
    {
        //Delete Product
        Product::where('id', $id)->delete();
        $message = 'Product  has been deleted successfully!';
        session::flash('success_message', $message);
        return redirect()->back();
    }

    public function addEditProduct(Request $request,$id=null){
        if($id==""){
            $title = "Add Product";
            $product = new Product;
            $productdata = array();
            $message = "Product added successfully!";
        }else{
            $title = "Edit Product";
            $productdata = Product::find($id);
            $productdata = json_decode(json_encode($productdata),true);
            /*echo "<pre>"; print_r($productdata); die;*/
            $product = Product::find($id);
            $message = "Product updated successfully!";
        }

        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/

            // Product Validations
            $rules = [
                'category_id' => 'required',
                'brand_id' => 'required',
                'product_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'product_code' => 'required|regex:/^[\w-]*$/',
                'product_price' => 'required|numeric',
                'product_color' => 'required|regex:/^[\pL\s\-]+$/u'
            ];
            $customMessages = [
                'category_id.required' => 'Category is required',
                'product_name.required' => 'Product Name is required',
                'product_name.regex' => 'Valid Product Name is required',
                'product_code.required' => 'Product Code is required',
                'product_code.regex' => 'Valid Product Code is required',
                'product_price.required' => 'Product Price is required',
                'product_price.numeric' => 'Valid Product Price is required',
                'product_color.required' => 'Product Color is required',
                'product_color.regex' => 'Valid Product Color is required',
            ];
            $this->validate($request,$rules,$customMessages);

            // Upload Product Image
            if($request->hasFile('main_image')){
                $image_tmp = $request->file('main_image');
                if($image_tmp->isValid()){
                    // Get Original Image Name
                    $image_name = $image_tmp->getClientOriginalName();
                    // Get Image Extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate New Image Name
                    $imageName = $image_name.'-'.rand(111,99999).'.'.$extension;
                    // Set Paths for small, medium and large images
                    $large_image_path = 'images/product_images/large/'.$imageName;
                    $medium_image_path = 'images/product_images/medium/'.$imageName;
                    $small_image_path = 'images/product_images/small/'.$imageName;
                    // Upload Large Image
                    Image::make($image_tmp)->save($large_image_path); // W:1040 H:1200
                    // Upload Medium and Small Images after Resize
                    Image::make($image_tmp)->resize(520,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(260,300)->save($small_image_path);
                    // Save Product Main Image in products table
                    $product->main_image = $imageName;
                }
            }

            // Upload Product Video
            if($request->hasFile('product_video')){
                $video_tmp = $request->file('product_video');
                if($video_tmp->isValid()){
                    // Upload Video
                    $video_name = $video_tmp->getClientOriginalName();
                    $extension = $video_tmp->getClientOriginalExtension();
                    $videoName = $video_name.'-'.rand().'.'.$extension;
                    $video_path = 'videos/product_videos/';
                    $video_tmp->move($video_path,$videoName);
                    // Save Video in products table
                    $product->product_video = $videoName;
                }
            }

            // Save Product details in products table
            $categoryDetails = Category::find($data['category_id']);
            $product->section_id = $categoryDetails['section_id'];
            $product->brand_id = $data['brand_id'];
            $product->category_id = $data['category_id'];
            $product->product_name = $data['product_name'];
            $product->product_code = $data['product_code'];
            $product->product_color = $data['product_color'];
            $product->product_price = $data['product_price'];
            $product->product_discount = $data['product_discount'];
            $product->product_weight = $data['product_weight'];
            $product->description = $data['description'];
            $product->wash_care = $data['wash_care'];
            $product->fabric = $data['fabric'];
            $product->pattern = $data['pattern'];
            $product->sleeve = $data['sleeve'];
            $product->fit = $data['fit'];
            $product->occasion = $data['occasion'];
            $product->meta_title = $data['meta_title'];
            $product->meta_keywords = $data['meta_keywords'];
            $product->meta_description = $data['meta_description'];
            if(!empty($data['is_featured'])){
                $product->is_featured = $data['is_featured'];
            }else{
                $product->is_featured ="No";

            }
            $product->status = 1;
            $product->save();
            session::flash('success_message',$message);
            return redirect('admin/products');
        }

        //Product Filters
        $productFilters=Product::productFilters();
        //dd($productFilters);
        $fabricArray=$productFilters['fabricArray'];
        $sleeveArray=$productFilters['sleeveArray'];
        $patternArray=$productFilters['patternArray'];
        $fitArray=$productFilters['fitArray'];
        $occasionArray=$productFilters['occasionArray'];



        // Sections with Categories and Sub Categories
        $categories = Section::with('categories')->get();
        $categories = json_decode(json_encode($categories),true);
        /*echo "<pre>"; print_r($categories); die;*/

        // Get All Brands
        $brands = Brand::where('status',1)->get();
        $brands = json_decode(json_encode($brands),true);

        return view('admin.products.add_edit_product')->
        with(compact('title','fabricArray','sleeveArray','patternArray','fitArray','occasionArray','categories','productdata','brands'));
    }

    public function deleteProductImage($id)
    {
        //Get Product Image
        $productImage = Product::select('main_image')->where('id', $id)->first();

        //Get Product Image Path

        $small_image_path = 'images/product_images/small/';
        $medium_image_path = 'images/product_images/medium/';
        $large_image_path = 'images/product_images/large/';


        //Delete Small Product images  if exist in Small Folder
        if (file_exists($small_image_path.$productImage->main_image)) {
            unlink($small_image_path.$productImage->main_image);
        }
        //Delete Medium Product images  if exist in Medium Folder
        if (file_exists($medium_image_path.$productImage->main_image)) {
            unlink($medium_image_path . $productImage->main_image);
        }
        //Delete Large Product images  if exist in Large Folder
        if (file_exists($large_image_path.$productImage->main_image)) {
            unlink($large_image_path.$productImage->main_image);
        }
        //Delete Product image from products table
        Product::where('id', $id)->update(['main_image' => '']);

        $message = 'Product image has been deleted successfully!';
        session::flash('success_message', $message);
        return redirect()->back();

    }

    public function deleteProductVideo($id)
    {
        //Get Product Video
        $productVideo = Product::select('product_video')->where('id', $id)->first();

        //Get Product Video Path
        $product_video_path = 'videos/product-videos/';

        //Delete Product Video from videos folder if exist
        if (file_exists($product_video_path.$productVideo->product_video)) {
            unlink($product_video_path.$productVideo->product_video);
        }
        //Delete Product Video from Products table
        Product::where('id', $id)->update(['product_video' => '']);

        $message = 'Product Video has been deleted successfully!';
        session::flash('success_message', $message);
        return redirect()->back();
    }
    public function addAttributes(Request $request,$id){
        if($request->isMethod('post')){
            $data=$request->all();
            //dd($data);
            foreach ($data['sku'] as $key=>$value){
                if(!empty($value)){
                    //SKU already exist
                    $attrCountSKU=ProductsAttribute::where('sku',$value)->count();
                    if($attrCountSKU>0){
                        $message = 'SKU already exist. Please add another SKU';
                        session::flash('error_message', $message);
                        return redirect()->back();
                    }
                    //Size already exist
                    $attrCountSize=ProductsAttribute::where(['product_id'=>$id,'size'=>$data['size'][$key]])->count();
                    if($attrCountSize>0){
                        $message = 'Size already exist. Please add another Size';
                        session::flash('error_message', $message);
                        return redirect()->back();
                    }

                    $attribute=new ProductsAttribute;
                    $attribute->product_id=$id;
                    $attribute->sku=$value;
                    $attribute->size=$data['size'][$key];
                    $attribute->price=$data['price'][$key];
                    $attribute->stock=$data['stock'][$key];
                    $attribute->status=1;
                    $attribute->save();
                }
                $success_message = 'Product Attributes has been added successfully';
                session::flash('success_message', $success_message);
                return redirect()->back();
            }
        }
        //echo"test";die;
        $productdata=Product::select('id','product_name','product_code','product_color','main_image')->with('attributes')->find($id);
        $productdata=json_decode(json_encode($productdata),true);
        //dd($productdata);
        $title='Product Attributes';
        return view('admin.products.add_attributes')->with(compact('productdata','title'));

    }
    public function editAttributes (Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            //echo "<pre>"; print_r($data);die;
            foreach ($data['attrId'] as $key => $attr) {
                if (!empty($attr)) {
                    ProductsAttribute::where(['id' => $data['attrId'][$key]])->update(['price' => $data['price'][$key],
                        'stock' => $data['stock'][$key]]);
                }
            }
                $success_message = 'Product Attributes has been updated successfully';
                session::flash('success_message', $success_message);
                return redirect()->back();
            }
        }

        public function updateAttributeStatus(Request $request)
        {
            if ($request->ajax()) {
                $data = $request->all();
                //echo "<pre>";print_r($data);die;
                if ($data['status'] == "Active") {
                    $status = 0;
                } else {
                    $status = 1;
                }
                ProductsAttribute::where('id', $data['attribute_id'])->update(['status' => $status]);
                return response()->json(['status' => $status, 'attribute_id' => $data['attribute_id']]);
            }
        }
    public function deleteAttribute($id)
    {
        //Delete Attribute
        ProductsAttribute::where('id', $id)->delete();
        $message = 'Product  Attribute has been deleted successfully!';
        session::flash('success_message', $message);
        return redirect()->back();
    }
    public function addImages(Request $request,$id){
        if($request->isMethod('post')){
            if($request->hasFile('images')){
                $images = $request->file('images');
                /*echo "<pre>"; print_r($images); die;*/
                foreach ($images as $key => $image) {

                    $productImage = new ProductsImage;
                    $image_tmp = Image::make($image);
                    // $originalName = $image->getClientOriginalName();
                    $extension = $image->getClientOriginalExtension();
                    $imageName = rand(111,999999).time().".".$extension;

                    // Set Paths for small, medium and large images
                    $large_image_path = 'images/product_images/large/'.$imageName;
                    $medium_image_path = 'images/product_images/medium/'.$imageName;
                    $small_image_path = 'images/product_images/small/'.$imageName;
                    // Upload Large Image
                    Image::make($image_tmp)->save($large_image_path); // W:1000 H:1000
                    // Upload Medium and Small Images after Resize
                    Image::make($image_tmp)->resize(500,500)->save($medium_image_path);
                    Image::make($image_tmp)->resize(250,250)->save($small_image_path);
                    // Save Product Main Image in products table
                    $productImage->image = $imageName;
                    $productImage->product_id = $id;
                    $productImage->status = 1;
                    $productImage->save();
                }

                $message = 'Product Images has been added successfully!';
                session::flash('success_message',$message);
                return redirect()->back();
            }
        }
        $productdata = Product::with('images')->select('id','product_name','product_code','product_color','main_image')->find($id);
        $productdata = json_decode(json_encode($productdata),true);
        /*echo "<pre>"; print_r($productdata); die;*/
        $title = "Product Images";
        return view('admin.products.add_images')->with(compact('title','productdata'));
    }
    public function updateImageStatus(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            //echo "<pre>";print_r($data);die;
            if ($data['status'] == "Active") {
                $status = 0;
            } else {
                $status = 1;
            }
            ProductsAttribute::where('id', $data['image_id'])->update(['status' => $status]);
            return response()->json(['status' => $status, 'image_id' => $data['image_id']]);
        }
    }
    public function deleteImage($id)
    {
        //Get Product Image
        $productImage = ProductsImage::select('image')->where('id', $id)->first();

        //Get Product Image Path

        $small_image_path = 'images/product_images/small/';
        $medium_image_path = 'images/product_images/medium/';
        $large_image_path = 'images/product_images/large/';


        //Delete Small Product images  if exist in Small Folder
        if (file_exists($small_image_path.$productImage->image)) {
            unlink($small_image_path.$productImage->image);
        }
        //Delete Medium Product images  if exist in Medium Folder
        if (file_exists($medium_image_path.$productImage->image)) {
            unlink($medium_image_path . $productImage->image);
        }
        //Delete Large Product images  if exist in Large Folder
        if (file_exists($large_image_path.$productImage->image)) {
            unlink($large_image_path.$productImage->image);
        }
        //Delete Product image from products_images table
        ProductsImage::where('id', $id)->delete(['image' => '']);

        $message = 'Image has been deleted successfully!';
        session::flash('success_message', $message);
        return redirect()->back();

    }


}
