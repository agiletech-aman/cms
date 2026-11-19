<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CorporateOfficeController extends Controller
{
    public function AllCorporateOffice()
    {

        $allcorporateoffices = User::role('CorporateOffice')->latest()->get();
        return view('admin.corporateoffice.all_corporateoffice', compact('allcorporateoffices'));
    } //End Method

    public function AddCorporateOffice()
    {
        $states = State::all();
        return view('admin.corporateoffice.add_corporateoffice',compact('states'));
    } // End Method


    public function StoreCorporateOffice(Request $request)
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
        $user->assignRole(intval(5));
        $user->save();

        $notification = array(
            'message' => 'Corporate Office Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.corporateoffice')->with($notification);
    } // End Method

    public function EditCorporateOffice($id)
    {
        
        $states = State::all();
        $regions = Region::all();
        $user = User::findOrFail($id);
        return view('admin.corporateoffice.edit_corporateoffice', compact('user', 'states','regions'));
    } // End Method

    public function UpdateCorporateOffice(Request $request)
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
            'message' => 'Corporate Office Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.corporateoffice')->with($notification);
    } // End Method

    public function DeleteCorporateOffice($id)
    {
        $user = User::findOrFail($id);

        if (!is_null($user)) {
            $user->delete();
        }

        $notification = array(
            'message' => 'Corporate Office Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } // End Method


    public function InactiveCorporateOffice($id){
        User::findOrFail($id)->update(['status' => 'inactive']);

        $notification = array(
            'message' => 'Corporate Office Inactive Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }//End Method
    
    public function ActiveCorporateOffice($id){
        User::findOrFail($id)->update(['status' => 'active']);

        $notification = array(
            'message' => 'Corporate Office Active Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }//End Method
}
