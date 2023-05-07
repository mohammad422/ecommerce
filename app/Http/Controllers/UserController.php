<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function UserDashboard()
    {
        $id = Auth::id();
        $userData = User::find($id);
        return view('index',compact('userData'));
    }
    public function UserProfileStore(Request $request)
    {
        $id= Auth::id();
        $data= User::find($id);
        $data->name= $request->name;
        $data->username= $request->username;
        $data->email = $request->email;
        $data->phone= $request->phone;
        $data->address = $request->address;
        if($request->file('photo')){
            $file= $request->file('photo');
            @unlink(public_path('upload/user_images/'.$data->photo));
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/user_images'),$filename);
            $data['photo']=$filename;
        }
        $data->save();
        $notification = [
            'message'=>'User Profile Updated Successfully.',
            'alert-type'=>'success'
        ];
        return redirect()->back()->with($notification);
    }

    public function UserLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        $notification = [
            'message'=>'User Logout Successfully.',
            'alert-type'=>'success'
        ];
        return redirect('/login')->with($notification);
    }

    public function UserUpdatePassword(Request $request)
    {
        $request->validate([
            'old_password'=> 'required',
            'new_password'=>'required|confirmed'
        ]);

        if(!Hash::check($request->old_password , auth::user()->password)){
            return back()->with('error',"old password Doesn't match!!");
        }
        User::whereId(Auth::id())->update([
            'password'=> Hash::make($request->new_password)
        ]);
        return back()->with('status','Password Change Successfully!');

    }
}
