<?php

namespace App\Http\Controllers\Admin;

use App\Banner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Image;

class BannersController extends Controller
{
    public  function banners(){
        Session::put('page', 'banners');
        $banners=Banner::get()->toArray();
       //dd($banners);
        return view('admin.banners.banners')->with(compact('banners'));
    }
    public function addeditBanner(Request $request,$id=null){
        if($id==""){
            //Add Banner
            $banner= new Banner;
            $title="Add Banner Image";
            $message="Banner added successfully!";

        }else{
            //Edit Banner
            $banner=Banner::find($id);
           // echo "<pre>"; print_r($banner); die;
            $title="Edit Banner Image";
            $message="Banner updated successfully!";
        }
        if($request->isMethod('post')) {
            $data = $request->all();
            //dd($data);
            $banner->link = $data['link'];
            $banner->title = $data['title'];
            $banner->alt = $data['alt'];

            // Upload Banner Image
            if ($request->hasFile('image')) {
                $image_tmp = $request->file('image');
                if ($image_tmp->isValid()) {
                    // Get Original Image Name
                    $image_name = $image_tmp->getClientOriginalName();
                    // Get Image Extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate New Image Name
                    $imageName = $image_name . '-' . rand(111, 99999) . '.' . $extension;
                    // Set Paths for small, medium and large images
                    $banner_image_path = 'images/banner_images/' . $imageName;
                    // Upload Banner Image after Resize
                    Image::make($image_tmp)->resize(1170, 480)->save($banner_image_path);
                    // Save Banner Image in banner table
                    $banner->image = $imageName;
                }
                }

                $banner->save();
                session::flash('success_message',$message);
                return redirect('admin/banners');
        }
        return view('admin.banners.add_edit_banner')->with(compact('title','banner'));
    }

    public function updateBannerStatus(Request $request)
    {

        if ($request->ajax()) {
            $data = $request->all();
            /*echo "<pre>";print_r($data);die;*/
            if ($data['status'] == "Active") {
                $status = 0;
            } else {
                $status = 1;
            }
            Banner::where('id', $data['banner_id'])->update(['status' => $status]);
            return response()->json(['status' => $status, 'banner_id' => $data['banner_id']]);
        }
    }
    public function deleteBanner($id)
    {
        //Get Banner Image
        $bannerImage=Banner::where('id',$id)->first();

        //Get Image Banner Path
        $banner_image_path='images/banner_images/';

        //Delete Image Banner if exist in banner folder
        if(file_exists($banner_image_path.$bannerImage->image)){
            unlink($banner_image_path.$bannerImage->image);

            //Delete Banner Image from Banner Table
            Banner::where('id',$id)->delete();

            session::flash('success_message', 'Banner deleted successfully!!!');
            return redirect()->back();
        }




        /*$message = 'Banner  has been deleted successfully!';
        session::flash('success_message', $message);
        return redirect()->back();*/
    }
}
