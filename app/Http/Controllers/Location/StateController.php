<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;
use App\Models\Region;
use Carbon\Carbon;

class StateController extends Controller
{
    public function AddState()
    {

        return view('admin.location.state');
    } // End Method

    public function StoreState(Request $request)
    {

        $name = $request->name;
        State::insert([
            'name' => $name,
            'created_at' => Carbon::now(),
        ]);
        $notification = array(
            'message' => 'State/Zone Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.state')->with($notification);
    } // End Method

    public function AllState()
    {

        $allstate = State::all();

        return view('admin.location.all_state', compact('allstate'));
    } // End Method


    public function EditState($id)
    {  
        $state = State::findOrFail($id);
        return view('admin.location.edit_state', compact('state'));

    } // End Method

    public function UpdateState(Request $request)
    {
        $state_id = $request->id;
        State::findorFail($state_id)->update([
            'name' => $request->name,
        ]);

       
        $notification = array(
            'message' => 'State/Zone Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.state')->with($notification);
        
    } // End Method

    public function DeleteState($id)
    {
        State::findOrFail($id)->delete();

        $notification = array(
            'message' => 'State Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.state')->with($notification);
        
    } // End Method

    public function GetRegion($state_id){
        $subloc= Region::where('state_id',$state_id)->orderBy('name', 'ASC')->get();
        return json_encode($subloc);
    
    }

}
