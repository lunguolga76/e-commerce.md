<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Session;
use Auth;

class UsersController extends Controller
{
    public function loginRegister(){
        return view('front.users.login_register');
    }
    public function registerUser(Request $request){
        if($request->isMethod('post')){
            $data=$request->all();
            //dd($data);
            //Check if user already exist
            $userCount=User::where('email',$data['email'])->count();
            if($userCount>0){
                $message="Email already exist";
                session::flash('error_message',$message);
                return redirect()->back();
            }else{
                //Register the User
                $user= new User;
                $user->name=$data['name'];
                $user->mobile=$data['mobile'];
                $user->email=$data['email'];
                $user->password=bcrypt($data['password']);
                $user->save();

                if(\Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
                   // dd(Auth::user());
                    return redirect('cart');
                }

            }
        }

    }
    public function logoutUser(){
        Auth::logout();
        return redirect('/');
    }
}
