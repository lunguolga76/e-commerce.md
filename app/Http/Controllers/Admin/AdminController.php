<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Hash;
use Image;


class AdminController extends Controller
{
    public function dashboard()

    {
        Session::put('page','dashboard');
        return view('admin.admin_dashboard');
    }

    public function settings()
    {
        Session::put('page','settings');
        /*echo "<pre>";print_r( Auth::guard('admin')->user());die();*/
        $adminDetails = Admin::where('email', Auth::guard('admin')->user()->email)->first();
        return view('admin.admin_settings')->with(compact('adminDetails'));
    }

    public function login(Request $request)
    {
        if ($request->isMethod('POST')) {
            $data = $request->all();
            /*echo "<pre>"; print_r($data);die;*/
            $rules = [
                'email' => 'required|email|max:255',
                'password' => 'required',

            ];
            $customMessages = [
                'email.required' => 'Email address is required',
                'email.email' => 'Valid Email is required',
                'password.required' => 'Password is required',
            ];
            $this->validate($request, $rules, $customMessages);


            if (Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password']])) {
                return redirect('admin/dashboard');
            } else {
                Session::flash('error_message', 'Invalid email or password');
                return redirect()->back();


            }
        }

        return view('admin.admin_login');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin');
    }

    public function chkCurrentPassword(Request $request)
    {
        $data = $request->all();
        /*echo "<pre>";print_r($data);*/
        /*echo "<pre>";print_r( Auth::guard('admin')->user()->password);die();*/
        /*echo Auth::guard('admin')->user()->password();die;*/
        if (Hash::check($data['current_pwd'], Auth::guard('admin')->user()->password)) {
            echo "true";
        } else {
            echo "false";
        }

    }

    public function updateCurrentPassword(Request $request)
    {

        if ($request->isMethod('post')) {
            $data = $request->all();
            /*echo "<pre>";print_r($data);die;*/
            //Check if the Current Password is correct//
            if (Hash::check($data['current_pwd'], Auth::guard('admin')->user()->password)) {
                //Check if the new and confirmed password is matching
                if ($data['new_pwd'] == $data['confirm_pwd']) {
                    Admin::where('id', Auth::guard('admin')->user()->id)->update(['password' => bcrypt($data['new_pwd'])]);
                    Session::flash('success_message', 'Password has been updated successfully!');
                } else {
                    Session::flash('error_message', 'New password and Confirmed password not match');
                }
            } else {
                Session::flash('error_message', 'Your current password is incorrect');
            }
            return redirect()->back();
        }
    }
    public function updateAdminDetails(Request $request){
        Session::put('page','update-admin-details');
        if($request->isMethod('post')){
            $data=$request->all();
            //echo "<pre>";print_r($data);die;
            $rules=[
                'admin_name'=>'required|regex:/^[\pL\s\-]+$/u',
                'admin_mobile'=>'required|numeric',
                'admin_image'=>'mimes:jpeg,jpg,png,gif|required|max:10000',
            ];
            $customMessages=[
                'admin_name.required'=>'Name is required',
                'admin_name.alpha'=>'Valid Name is required',
                'admin_mobile.required'=>'Admin mobile is required',
                'admin_mobile.numeric'=>'Valid Mobile is required',
                'admin_image.mimes'=>'Valid Image is required',

            ];
            $this->validate($request,$rules,$customMessages);
            //Upload Admin Image
            if($request->hasFile('admin_image')){
                $image_tmp=$request->file('admin_image');
                if ($image_tmp->isValid()){
                    //Get Image Extension
                    $extension=$image_tmp->getClientOriginalExtension();
                    //generate New Image Name
                    $imageName=rand(111,99999).'.'.$extension;
                    $imagePath='images/admin_img/admin_photos'.$imageName;
                    //Upload the image
                    Image::make($image_tmp)->save($imagePath);

                }else if(!empty($data['current_admin_image'])){
                    $imageName=$data['current_admin_image'];
                }else{
                    $imageName="";
                }
            }
            //Update Admin Details
            Admin::where('email',Auth::guard('admin')->user()->email)
                ->update(['name'=>$data['admin_name'],'mobile'=>$data['admin_mobile'],'image'=>$imageName]);
            session::flash('success_message','Admin details updated successfully');
            return redirect()->back();
        }
        return view('admin.update_admin_details');
    }


}
