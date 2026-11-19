<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegionalManagerController extends Controller
{
    public function AllRegionalManager()
    {

        $allregionalmanagers = User::role('RegionalManager')->latest()->get();
        return view('admin.regionalmanager.all_regionalmanager', compact('allregionalmanagers'));
    } //End Method

    public function AddRegionalManager()
    {
        $states = State::all();
        return view('admin.regionalmanager.add_regionalmanager',compact('states'));
    } // End Method


    public function StoreRegionalManager(Request $request)
    {

        $request->validate([
            'username' => ['required', 'string', 'lowercase', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        ]);

        $user = new User();

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->state_id = $request->state_id;
        $user->region_id = $request->region_id ;
        $user->password = Hash::make($request->password);
        $user->status = 'inactive';
        $user->assignRole(intval(4));
        $user->save();

        $notification = array(
            'message' => 'Regional Manager Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.regionalmanager')->with($notification);
    } // End Method

    public function EditRegionalManager($id)
    {
        
        $states = State::all();
        $regions = Region::all();
        $user = User::findOrFail($id);
        return view('admin.regionalmanager.edit_regionalmanager', compact('user', 'states','regions'));
    } // End Method

    public function UpdateRegionalManager(Request $request)
    {
       

        $user_id = $request->id;

        $user = User::findOrFail($user_id);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->state_id = $request->state_id;
        $user->region_id = $request->region_id ;
        $user->status = 'inactive';
        $user->save();

       

        $notification = array(
            'message' => 'Regional Manager Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.regionalmanager')->with($notification);
    } // End Method

    public function DeleteRegionalManager($id)
    {
        $user = User::findOrFail($id);

        if (!is_null($user)) {
            $user->delete();
        }

        $notification = array(
            'message' => 'Regional Manager Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } // End Method


    public function InactiveRegionalManager($id){
        User::findOrFail($id)->update(['status' => 'inactive']);

        $notification = array(
            'message' => 'Regional Manager Inactive Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }//End Method
    
    public function ActiveRegionalManager($id){
        User::findOrFail($id)->update(['status' => 'active']);

        $notification = array(
            'message' => 'Regional Manager Active Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }//End Method
}
