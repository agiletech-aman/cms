<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\State;
use Carbon\Carbon;

class RegionController extends Controller
{
    public function AllRegion()
    {

        $allregion = Region::all();
        return view('admin.location.all_region', compact('allregion'));
    } //End Method

    public function AddRegion()
    {

        $state = State::orderBy('name', 'ASC')->get();
        return view('admin.location.region', compact('state'));
    } //End Method

    public function StoreRegion(Request $request)
    {


        $request->validate([
            'name' => 'required',
        ], [

            'name.required' => 'Regions is Required',
        ]);


        Region::insert([
            'state_id' => $request->state_id,
            'name' => $request->name,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Region Inserted  Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.region')->with($notification);
    } //End Method

    public function EditRegion($id)
    {
        $regions = Region::findOrFail($id);
        $state = State::orderBy('name', 'ASC')->get();
        return view('admin.location.region_edit', compact('regions', 'state'));
    } //End Method

    public function UpdateRegion(Request $request)
    {
        $state_id = $request->id;

        $request->validate([
            'name' => 'required',
        ], [

            'name.required' => 'Regions is Required',
        ]);

        Region::findorFail($state_id)->update([
            'state_id' => $request->state_id,
            'name' => $request->name,
        ]);

        $notification = array(
            'message' => 'Regions Updated  Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.region')->with($notification);
    } //End Method

    public function DeleteRegion($id){
        Region::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Region Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }//End Method

}
