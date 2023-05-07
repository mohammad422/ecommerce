<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ShipDistrict;
use App\Models\ShipDivision;
use App\Models\ShipState;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShippingAreaController extends Controller
{

    public function AllDivision()
    {
        $divisions = ShipDivision::latest()->get();
        return view('backend.ship.division.division_all', compact('divisions'));
    }

    public function AddDivision()
    {
        return view('backend.ship.division.division_add');
    }

    public function StoreDivision(Request $request)
    {

        ShipDivision::insert([
            'division_name' => $request->division_name,
            'created_at' => Carbon::now()->format('Y-m-d')
        ]);
        $notification = [
            'message' => 'ShipDivision Inserted Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('all.division')->with($notification);
    }

    public function EditDivision($id)
    {
         $division = ShipDivision::findOrFail($id);
        return view('backend.ship.division.division_edit', compact('division'));
    }

    public function UpdateDivision(Request $request)
    {
        $division_id = $request->id;
        ShipDivision::findOrFail($division_id)->update([
            'division_name' => $request->division_name,
            'updated_at' => Carbon::now()->format('Y-m-d')

        ]);
        $notification = [
            'message' => 'ShipDivision Updated Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('all.division')->with($notification);

    }

    public function DeleteDivision($id)
    {
        ShipDivision::findOrFail($id)->delete();

        $notification = [
            'message' => 'ShipDivision Deleted Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }

    public function AllDistrict()
    {
        $districts = ShipDistrict::with('division')->latest()->get();
        return view('backend.ship.district.district_all', compact('districts'));
    }
    public function AddDistrict()
    {
        $divisions = ShipDivision::all();
        return view('backend.ship.district.district_add',compact('divisions'));
    }

    public function StoreDistrict(Request $request)
    {

        ShipDistrict::insert([
            'district_name' => $request->district_name,
            'division_id' => $request->division_id,
            'created_at' => Carbon::now()->format('Y-m-d')
        ]);
        $notification = [
            'message' => 'ShipDistrict Inserted Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('all.district')->with($notification);
    }

    public function EditDistrict($id)
    {
        $divisions = ShipDivision::all();
        $district = ShipDistrict::findOrFail($id);
        return view('backend.ship.district.district_edit', compact('district','divisions'));
    }

    public function UpdateDistrict(Request $request)
    {
        $district_id = $request->id;
        ShipDistrict::findOrFail($district_id)->update([
            'district_name' => $request->district_name,
            'division_id' => $request->division_id,
            'updated_at' => Carbon::now()->format('Y-m-d')

        ]);
        $notification = [
            'message' => 'ShipDistrict Updated Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('all.district')->with($notification);

    }

    public function DeleteDistrict($id)
    {
        ShipDistrict::findOrFail($id)->delete();

        $notification = [
            'message' => 'ShipDistrict Deleted Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }

    public function AllState()
    {
        $states = ShipState::with('division')->with('district')->latest()->get();

        return view('backend.ship.state.state_all', compact('states'));
    }
    public function AddState()
    {
        $divisions = ShipDivision::orderBy('division_name','ASC')->get();
        $districts = ShipDistrict::orderBy('district_name','ASC')->get();
        return view('backend.ship.state.state_add',compact('divisions','districts'));
    }

    public function StoreState(Request $request)
    {

        ShipState::insert([
            'state_name' => $request->state_name,
            'division_id' => $request->division_id,
            'district_id' => $request->district_id,
            'created_at' => Carbon::now()->format('Y-m-d')
        ]);
        $notification = [
            'message' => 'ShipState Inserted Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('all.state')->with($notification);
    }

    public function EditState($id)
    {
        $divisions = ShipDivision::all();
        $districts = ShipDistrict::all();
        $state = ShipState::findOrFail($id);
        return view('backend.ship.state.state_edit', compact('state','districts','divisions'));
    }

    public function UpdateState(Request $request)
    {
        $state_id = $request->id;
        ShipState::findOrFail($state_id)->update([
            'state_name' => $request->state_name,
            'district_id' => $request->district_id,
            'division_id' => $request->division_id,
            'updated_at' => Carbon::now()->format('Y-m-d')

        ]);
        $notification = [
            'message' => 'ShipState Updated Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->route('all.state')->with($notification);

    }

    public function DeleteState($id)
    {
        ShipState::findOrFail($id)->delete();

        $notification = [
            'message' => 'ShipState Deleted Successfully.',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }

    public function GetDistrict($division_id)
    {
        $district = ShipDistrict::where('division_id',$division_id)->orderBy('district_name','ASC')->get();
        return json_encode($district);
    }
}
