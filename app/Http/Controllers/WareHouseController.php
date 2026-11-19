<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WareHouseController extends Controller
{
    public function AllWarehouse()
    {

       // $allwarehouse = User::role('WarehouseManager')->latest()->get();
         $allwarehouse = User::with('region')->role('WarehouseManager')->latest()->get();
        return view('admin.warehouse.all_warehouse', compact('allwarehouse'));
    } //End Method

    public function AddWarehouse()
    {
        $states = State::all();
        return view('admin.warehouse.add_warehouse', compact('states'));
    } //End Method

    public function StoreWarehouse(Request $request)
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
        $user->assignRole(intval(3));
        $user->save();


        $notification = array(
            'message' => 'Warehouse Added  Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.warehouse')->with($notification);
    }

    public function EditWarehouse($id)
    {
       
        $states = State::all();
        $regions = Region::all();
        $user = User::findOrFail($id);
        return view('admin.warehouse.edit_warehouse', compact('user', 'states', 'regions'));
    } //End Method

    public function UpdateWarehouse(Request $request,$id)
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
            'message' => 'Warehouse Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.warehouse')->with($notification);
    }//End Method
    public function DeleteWarehouse($id)
    {
        $user = User::findOrFail($id);

        if (!is_null($user)) {
            $user->delete();
        }

        $notification = array(
            'message' => 'Warehouse Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } //End Method

    public function InactiveWarehouse($id){
        User::findOrFail($id)->update(['status' => 'inactive']);

        $notification = array(
            'message' => 'Warehouse Inactive Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }//End Method
    
    public function ActiveWarehouse($id){
        User::findOrFail($id)->update(['status' => 'active']);

        $notification = array(
            'message' => 'Warehouse Active Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }//End Method
}


