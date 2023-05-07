<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    //
    public function AllCoupon()
    {
        $coupons = Coupon::latest()->get();
        return view('backend.coupon.coupon_all', compact('coupons'));
    }

    public function AddCoupon()
    {
        $coupons = Coupon::orderBy('coupon_name', 'ASC')->get();

        return view('backend.coupon.coupon_add', compact('coupons'));
    }

    public function StoreCoupon(Request $request)
    {

        Coupon::insert([
            'coupon_name' => $request->coupon_name,
            'coupon_discount' => $request->coupon_discount,
            'coupon_validity' => $request->coupon_validity,
            'created_at' => Carbon::now()->format('Y-m-d')
        ]);
        $notification = [
            'message' => 'Coupon Inserted Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('all.coupon')->with($notification);
    }

    public function EditCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('backend.coupon.coupon_edit', compact('coupon'));
    }

    public function UpdateCoupon(Request $request)
    {
        $coupon_id = $request->id;
        Coupon::findOrFail($coupon_id)->update([
            'coupon_name' => $request->coupon_name,
            'coupon_discount' => $request->coupon_discount,
            'coupon_validity' => $request->coupon_validity,
            'updated_at' => Carbon::now()->format('Y-m-d')

        ]);
        $notification = [
            'message' => 'Coupon Updated Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('all.coupon')->with($notification);

    }

    public function DeleteCoupon($id)
    {
        Coupon::findOrFail($id)->delete();

        $notification = [
            'message' => 'Coupon Deleted Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }
}
