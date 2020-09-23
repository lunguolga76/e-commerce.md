<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Section;
use Illuminate\Http\Request;
use App\Category;
use Session;
use Image;

class CategoryController extends Controller
{
   public function categories(){
       Session::put('page','categories');
       $categories=Category::with(['section','parentcategory'])->get();
       /*$Categories = json_decode(json_encode($categories));
      echo "<pre>";print_r($categories);die;*/
       return view('admin.categories.categories')->with(compact('categories'));
   }
    public function updateCategoryStatus(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            /*echo "<pre>";print_r($data);die;*/
            if ($data['status'] == "Active") {
                $status = 0;
            } else {
                $status = 1;
            }
            Category::where('id', $data['category_id'])->update(['status' => $status]);
            return response()->json(['status' => $status, 'category_id' => $data['category_id']]);
        }
    }
    public function addEditCategory(Request $request, $id=null){
       if($id==""){
           //Add Category Functionality
           $title="Add Category";
           $category= new Category;
           $categorydata=array();
           $getCategories=array();
           $message="Category added successfully!!!";

       }else {
           //Edit Category Functionality
           $title = "Edit Category";
         $categorydata=Category::where('id',$id)->first();
         $category=json_decode(json_encode($categorydata),true);
         $getCategories=Category::with('subcategories')->where(['parent_id'=>0,'section_id'=>$categorydata['section_id']])->get();
         $getCategories=json_decode(json_encode($getCategories),true);
           /*echo "<pre>"; print_r($categorydata); die;*/
           $category=Category::find($id);
           $message="Category updated successfully!!!";
       }
           if($request->isMethod('post')){
               $data=$request->all();
               /*echo "<pre>";print_r($data);die;*/

               //Category Validations
               $rules=[
                   'category_name'=>'required|regex:/^[\pL\s\-]+$/u',
                   'section_id'=>'required',
                   'url'=>'required',
                   'category_image'=>'image',
               ];
               $customMessages=[
                   'category_name.required'=>'Category Name is required',
                   'category_name.regex'=>'Valid Category Name is required',
                   'section_id.required'=>'Section is required',
                   'url.required'=>'Category URL is required',
                   'category_image.image'=>'Valid Category Image is required',

               ];
               $this->validate($request,$rules,$customMessages);

               //Upload Category Image
               if($request->hasFile('category_image')) {
                   $image_tmp = $request->file('category_image');
                   if ($image_tmp->isValid()) {
                       //Get Image Extension
                       $extension = $image_tmp->getClientOriginalExtension();
                       //generate New Image Name
                       $imageName = rand(111, 99999) . '.' . $extension;
                       $imagePath = 'images/category_images' . $imageName;
                       //Upload the image
                       Image::make($image_tmp)->save($imagePath);
                       //Save Category Image
                       $category->category_image=$imageName;

                   }
               }
               if(empty($data['category_discount'])){
                   $data['category_discount']="";
               }
               if(empty($data['description'])){
                   $data['description']="";
               }
               if(empty($data['meta_title'])){
                   $data['meta_title']="";
               }
               if(empty($data['meta_description'])){
                   $data['meta_description']="";
               }
               if(empty($data['meta_keywords'])){
                   $data['meta_keywords']="";
               }
               /*echo "<pre>";print_r($data);die;*/
               $category->parent_id=$data['parent_id'];
               $category->section_id=$data['section_id'];
               $category->category_name=$data['category_name'];
               $category->category_discount=$data['category_discount'];
               $category->description=$data['description'];
               $category->url=$data['url'];
               $category->meta_title=$data['meta_title'];
               $category->meta_description=$data['meta_description'];
               $category->meta_keywords=$data['meta_keywords'];
               $category->status=1;
               $category->save();
               session::flash('success_message',$message);
               return redirect('admin/categories');

           }


       //Get All Sections
        $getSections=Section::get();
       return view('admin.categories.add_edit_category')->with(compact('title','getSections','categorydata','getCategories'));

    }

    public function appendCategoryLevel(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $getCategories = Category::with('subcategories')->where(['section_id' => $data['section_id'], 'parent_id' => 0, 'status' => 1])->get();
            $getCategories = json_decode(json_encode($getCategories), true);
            /*echo "<pre>"; print_r($getCategories); die;*/
            return view('admin.categories.append_categories_level')->with(compact('getCategories'));
        }
    }
        public function deleteCategoryImage($id){
         //Get Category Image
            $categoryImage=Category::select('category_image')->where('id',$id)->first();

            //Get Category Image Path
            $category_image_path='images/category_images';
                //Delete Category images from category_images folder if exist
            if(file_exists($category_image_path.$categoryImage->category_image)){
                unlink($category_image_path.$categoryImage->category_image);
            }
            //Delete Category image from categories table
            Category::where('id',$id)->update(['category_image'=>'']);

            $message='Category image has been deleted successfully!';
            session::flash('success_message',$message);
            return redirect()->back();

        }
        public function deleteCategory($id){
       //Delete Category
            Category::where('id',$id)->delete();
            $message='Category  has been deleted successfully!';
            session::flash('success_message',$message);
            return redirect()->back();
        }

}
