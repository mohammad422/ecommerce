<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\VendorRegNotification;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    public function VendorDashboard()
    {
        return view('vendor.index');
    }

    public function VendorLogin()
    {
        return view('vendor.vendor_login');
    }

    public function VendorDestroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/vendor/login');
    }

    public function VendorProfile()
    {
        $id= Auth::id();
        $vendorData= User::find($id);
        return view('vendor.vendor_profile_view',compact('vendorData'));
    }

    public function VendorProfileStore(Request $request)
    {
        $id= Auth::id();
        $data= User::find($id);
        $data->name= $request->name;
        $data->email = $request->email;
        $data->phone= $request->phone;
        $data->address = $request->address;
        if($request->file('photo')){
            $file= $request->file('photo');
            @unlink(public_path('upload/vendor_images/'.$data->photo));
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/vendor_images'),$filename);
            $data['photo']=$filename;
        }
        $data->save();
        $notification = [
            'message'=>'Vendor Profile Updated Successfully.',
            'alert-type'=>'success'
        ];
        return redirect()->back()->with($notification);
    }

    public function VendorChangePassword()
    {
        return view('vendor.vendor_change_password');
    }

    public function VendorUpdatePassword(Request $request)
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

    public function BecomeVendor()
    {
         return view('auth.become_vendor');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function VendorRegister(Request $request): RedirectResponse
    {
      $vuser = User::where('role','admin')->get();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed'],
        ]);

        $user = User::insert([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'vendor_join' => $request->vendor_join,
            'password' => Hash::make($request->password),
            'role' => 'vendor',
            'status' => 'inactive',
        ]);
        $notification = [
            'message'=>'Vendor Registered Successfully.',
            'alert-type'=>'success'
        ];
        Notification::send($vuser, new VendorRegNotification($request));
        return redirect()->route('vendor.login')->with($notification);
    }
}
