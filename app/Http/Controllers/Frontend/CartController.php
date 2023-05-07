<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\ShipDistrict;
use App\Models\ShipDivision;
use App\Models\ShipState;
use App\Models\User;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Cart;
use Illuminate\Http\Request;
use App\Models\Product;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    //
    public function AddToCart(Request $request, $id)
    {
        if (Session::has('coupon')) {
            Session::forget('coupon');
        }
        $product = Product::findOrFail($id);

        if ($product->discount_price === NULL) {
            \Cart::add([
                'id' => $id,
                'name' => $request->product_name,
                'qty' => $request->quantity,
                'price' => $product->selling_price,
                'weight' => 1,
                'options' => [
                    'image' => $product->product_thambnail,
                    'color' => $request->color,
                    'size' => $request->size,
                    'vendor_id' => $request->vendor
                ],
            ]);
            return response()->json(['success' => 'Successfully Added on Your Cart']);
        } else {
            \Cart::add([
                'id' => $id,
                'name' => $request->product_name,
                'qty' => $request->quantity,
                'price' => $product->discount_price,
                'weight' => 1,
                'options' => [
                    'image' => $product->product_thambnail,
                    'color' => $request->color,
                    'size' => $request->size,
                    'vendor_id' => $request->vendor
                ],
            ]);
            return response()->json(['success' => 'Successfully Added on Your Cart']);
        }
    }


    public function AddMiniCart()
    {
        $carts = \Cart::content();
        $cartQty = \Cart::count();
        $cartTotal = \Cart::total();
        return response()->json([
            'carts' => $carts,
            'cartQty' => $cartQty,
            'cartTotal' => $cartTotal
        ]);
    }

    public function RemoveMiniCart($rowId)
    {
        \Cart::remove($rowId);
        return response()->json(['success' => 'Product Removed from Cart']);
    }

    public function AddToCartDetails(Request $request, $id)
    {
        if (Session::has('coupon')) {
            Session::forget('coupon');
        }

        $product = Product::findOrFail($id);

        if ($product->discount_price === NULL) {
            \Cart::add([
                'id' => $id,
                'name' => $request->product_name,
                'qty' => $request->quantity,
                'price' => $product->selling_price,
                'weight' => 1,
                'options' => [
                    'image' => $product->product_thambnail,
                    'color' => $request->color,
                    'size' => $request->size,
                    'vendor_id' => $request->vendor
                ],
            ]);

            return response()->json(['success' => 'Successfully Added on Your Cart']);
        } else {
            \Cart::add([
                'id' => $id,
                'name' => $request->product_name,
                'qty' => $request->quantity,
                'price' => $product->discount_price,
                'weight' => 1,
                'options' => [
                    'image' => $product->product_thambnail,
                    'color' => $request->color,
                    'size' => $request->size,
                    'vendor_id' => $request->vendor
                ],
            ]);

            return response()->json(['success' => 'Successfully Added on Your Cart']);
        }

    }

    public function MyCart()
    {
        if (Session::has('coupon')) {
            $coupon_name = Session::get('coupon')['coupon_name'];
            $coupon = Coupon::where('coupon_name', $coupon_name)->first();
            Session::put('coupon', [
                'coupon_name' => $coupon->coupon_name,
                'coupon_discount' => $coupon->coupon_discount,
                'discount_amount' => round(\Cart::total() * $coupon->coupon_discount / 100),
                'total_amount' => round(\Cart::total() - \Cart::total() * $coupon->coupon_discount / 100)
            ]);
        }

        return view('frontend.mycart.view_mycart');
    }

    public function GetCartProduct()
    {
        $carts = \Cart::content();
        $cartQty = \Cart::count();
        $cartTotal = \Cart::total();
        return response()->json([
            'carts' => $carts,
            'cartQty' => $cartQty,
            'cartTotal' => $cartTotal
        ]);
    }

    public function CartRemove($rowId)
    {
        \Cart::remove($rowId);
        if (Session::has('coupon')) {
            $coupon_name = Session::get('coupon')['coupon_name'];
            $coupon = Coupon::where('coupon_name', $coupon_name)->first();
            Session::put('coupon', [
                'coupon_name' => $coupon->coupon_name,
                'coupon_discount' => $coupon->coupon_discount,
                'discount_amount' => round(\Cart::total() * $coupon->coupon_discount / 100),
                'total_amount' => round(\Cart::total() - \Cart::total() * $coupon->coupon_discount / 100)
            ]);
        }
        return response()->json(['success' => 'Successfully Removed from Cart']);
    }

    public function CartDecrement($rowId)
    {
        $row = \Cart::get($rowId);
        \Cart::update($rowId, $row->qty - 1);
        if (Session::has('coupon')) {
            $coupon_name = Session::get('coupon')['coupon_name'];
            $coupon = Coupon::where('coupon_name', $coupon_name)->first();
            Session::put('coupon', [
                'coupon_name' => $coupon->coupon_name,
                'coupon_discount' => $coupon->coupon_discount,
                'discount_amount' => round(\Cart::total() * $coupon->coupon_discount / 100),
                'total_amount' => round(\Cart::total() - \Cart::total() * $coupon->coupon_discount / 100)
            ]);
        }
        return response()->json(['Decrement']);
    }

    public function CartIncrement($rowId)
    {
        $row = \Cart::get($rowId);
        \Cart::update($rowId, $row->qty + 1);
        if (Session::has('coupon')) {
            $coupon_name = Session::get('coupon')['coupon_name'];
            $coupon = Coupon::where('coupon_name', $coupon_name)->first();
            Session::put('coupon', [
                'coupon_name' => $coupon->coupon_name,
                'coupon_discount' => $coupon->coupon_discount,
                'discount_amount' => round(\Cart::total() * $coupon->coupon_discount / 100),
                'total_amount' => round(\Cart::total() - \Cart::total() * $coupon->coupon_discount / 100)
            ]);
        }
        return response()->json(['Increment']);
    }


    public function CouponApply(Request $request)
    {
        $coupon = Coupon::where('coupon_name', $request->coupon_name)
            ->where('coupon_validity', '>=', Carbon::now()->format('Y-m-d'))->first();
        if ($coupon) {
            Session::put('coupon', [
                'coupon_name' => $coupon->coupon_name,
                'coupon_discount' => $coupon->coupon_discount,
                'discount_amount' => round(\Cart::total() * $coupon->coupon_discount / 100),
                'total_amount' => round(\Cart::total() - \Cart::total() * $coupon->coupon_discount / 100)
            ]);
            return response()->json(['validity' => true, 'success' => 'Coupon Applied Successfully']);
        } else {
            return response()->json(['error' => 'Invalid Coupon']);
        }
    }

    public function CouponCalculation()
    {
        if (Session::has('coupon')) {
            return response()->json([
                'subtotal' => \Cart::total(),
                'coupon_name' => session()->get('coupon')['coupon_name'],
                'coupon_discount' => session()->get('coupon')['coupon_discount'],
                'discount_amount' => session()->get('coupon')['discount_amount'],
                'total_amount' => session()->get('coupon')['total_amount']
            ]);
        } else {
            return response()->json([
                'total' => \Cart::total(),
            ]);
        }
    }

    public function CouponRemove()
    {
        Session::forget('coupon');
        return response()->json([
            'success' => 'Coupon Removed Successfully',
        ]);
    }

    public function CheckoutCreate()
    {
        $user = User::where('id', Auth::id())->first();

        $divisions = ShipDivision::orderBy('division_name', 'ASC')->get();
        $districts = ShipDistrict::orderBy('district_name', 'ASC')->get();
        $states = ShipState::orderBy('state_name', 'ASC')->get();

        if (Auth::check()) {
            if (\Cart::total() > 0) {
                $carts = \Cart::content();
                $cartQty = \Cart::count();
                $cartTotal = \Cart::total();

                return view('frontend.checkout.checkout_view', compact('carts', 'cartQty', 'cartTotal', 'user'
                    , 'divisions', 'districts', 'states'));
            } else {
                $notification = [
                    'message' => 'Shopping At Least One Product.',
                    'alert-type' => 'error'
                ];
                return redirect()->to('/')->with($notification);
            }
        } else {
            $notification = [
                'message' => 'You Need to Login First.',
                'alert-type' => 'error'
            ];
            return redirect()->route('login')->with($notification);
        }
    }
}
