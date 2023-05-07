<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ShipDistrict;
use App\Models\ShipDivision;
use App\Models\ShipState;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function DistrictGetAjax($division_id)
    {
        $ship = ShipDistrict::where('division_id', $division_id)->orderBy('district_name', 'ASC')->get();

        return json_encode($ship);
    }

    public function StateGetAjax($district_id)
    {
        $state = ShipState::where('district_id', $district_id)->orderBy('state_name', 'ASC')->get();

        return json_encode($state);
    }

    public function CheckoutStore(Request $request)
    {

        $data = [];
        $data['shipping_name'] = $request->shipping_name;
        $data['shipping_email'] = $request->shipping_email;
        $data['shipping_phone'] = $request->shipping_phone;

        $data['division_id'] = $request->division_id;
        $data['district_id'] = $request->district_id;
        $data['state_id'] = $request->state_id;
        $data['post_code'] = $request->post_code;

        $data['shipping_address'] = $request->shipping_address;
        $data['notes'] = $request->notes;

        $cartTotal = \Cart::total();

        if($request->payment_option === 'stripe'){
              return view('frontend.payment.stripe',compact('data','cartTotal'));
        }elseif($request->payment_option === 'card'){
            return 'Card Page';
        }else{
            return view('frontend.payment.cash',compact('data','cartTotal'));
        }

    }
}
