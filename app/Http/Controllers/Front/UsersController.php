<?php

namespace App\Http\Controllers\Front;

use App\Cart;
use App\Http\Controllers\Controller;
use App\Sms;
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
                    //Update User cart with user id
                    if(!empty(Session::get('session_id'))){
                        $user_id=Auth::user()->id;
                        $session_id=Session::get('session_id');
                        Cart::where('session_id',$session_id)->update(['user_id'=>$user_id]);
                    }
                    //Send Register Sms
                    $message='Dear customer, you have been successfully registered.Login to your account to access orders and offers';
                    $mobile=$data['mobile'];
                    Sms::sendSms($mobile,$message);
                    return redirect('cart');
                }
            }
        }
    }
    public function checkEmail(Request $request){
        //Check if email already exist
        $data=$request->all();
        $userCount=User::where('email',$data['email'])->count();
        if($userCount>0){
            return "false";
        }else{
            return "true";
        }


    }
    public function loginUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                //Update User cart with user id
                if(!empty(Session::get('session_id'))){
                    $user_id=Auth::user()->id;
                    $session_id=Session::get('session_id');
                    Cart::where('session_id',$session_id)->update(['user_id'=>$user_id]);
                }

                return redirect('/cart');
            } else {
                $message = "Invalid Username or Password";
                Session::flash('error_message', $message);
                return redirect()->back();
            }
        }
    }
    public function logoutUser(){
        Auth::logout();
        return redirect('/');
    }
}
