<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Region;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class AdminController extends Controller
{
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'User Logout Successfully',
            'alert-type' => 'success'
        );


        return redirect('/login')->with($notification);
    } //End Method


    public function Profile()
    {
        $id = Auth::user()->id;
        $adminData = User::find($id);
        return view('admin.admin_profile_view', compact('adminData'));
    } //End Method

    public function EditProfile()
    {
        $id = Auth::user()->id;
        $editData = User::find($id);
        return view('admin.admin_profile_edit', compact('editData'));
    } //End Method



    public function StoreProfile(Request $request)
{
    $id = Auth::user()->id;
    $data = User::find($id);

    $data->name = $request->name;
    $data->email = $request->email;
    $data->username = $request->username;

    // 🔴 REMOVE IMAGE IF CHECKED
    if ($request->has('remove_profile_image') && $request->remove_profile_image == 1) {

        if (!empty($data->profile_image) &&
            file_exists(public_path('upload/admin_images/' . $data->profile_image))) {

            unlink(public_path('upload/admin_images/' . $data->profile_image));
        }

        $data->profile_image = null;
    }

    // 📤 UPLOAD NEW IMAGE (IF ANY)
    if ($request->file('profile_image')) {

        // delete old image if exists
        if (!empty($data->profile_image) &&
            file_exists(public_path('upload/admin_images/' . $data->profile_image))) {

            unlink(public_path('upload/admin_images/' . $data->profile_image));
        }

        $file = $request->file('profile_image');
        $filename = date('YmdHi') . '_' . $file->getClientOriginalName();
        $file->move(public_path('upload/admin_images'), $filename);
        $data->profile_image = $filename;
    }

    $data->save();

    $notification = [
        'message' => 'Admin Profile Updated Successfully',
        'alert-type' => 'success'
    ];

    return redirect()->route('admin.profile')->with($notification);
}


    // public function StoreProfile(Request $request)
    // {
    //     $id = Auth::user()->id;
    //     $data = User::find($id);
    //     $data->name = $request->name;
    //     $data->email = $request->email;
    //     $data->username = $request->username;

       

    //     if ($request->file('profile_image')) {
    //         $file = $request->file('profile_image');
    //         $filename = date('Ymdhi') . $file->getClientOriginalName();
    //         $file->move(public_path('upload/admin_images'), $filename);
    //         $data['profile_image'] = $filename;
    //     }
    //     $data->save();

    //     $notification = array(
    //         'message' => 'Admin Profile Updated Successfully',
    //         'alert-type' => 'success'
    //     );

    //     return redirect()->route('admin.profile')->with($notification);
    // } //End Method

    public function ChangePassword()
    {

        return view('admin.admin_change_password');
    } // End Method
    public function UpdatePassword(Request $request)
    {
        $validateData = $request->validate([
            'oldpassword' => 'required',
            'newpassword' => 'required',
            'confirm_password' => 'required|same:newpassword',
        ]);

        $hashedPassword = Auth::user()->password;
        if (Hash::check($request->oldpassword, $hashedPassword)) {
            $users = User::find(Auth::id());
            $users->password = bcrypt($request->newpassword);

            $users->save();

            session()->flash('message', 'Password Updated Successfully');
            return redirect()->back();
        } else {

            session()->flash('message', 'Old Password is not match');
            return redirect()->back();
        }
    } // End Method

    public function AllUser()
    {

        $alluser = User::withoutRole('SuperAdmin')->latest()->get();
        return view('admin.user.all_user', compact('alluser'));
    } // End Method


    public function AddUser()
    {
        $uid = Auth::user()->id;
        $userid = User::findOrFail($uid);

        $states = State::all();
        if ($userid->hasRole('SuperAdmin')) {
            $roles = Role::all();
        } else {
            $roles = Role::where('name', '!=', 'SuperAdmin')->get();
        }

        return view('admin.user.add_user', compact('roles', 'states'));
    } // End Method

    public function StoreUser(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'lowercase', 'max:255', 'unique:' . User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        ]);
        $user = new User();

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->state_id = $request->state_id;
        $user->region_id = $request->region_id;
        $user->password = Hash::make($request->password);
        $user->status = 'inactive';
        $user->save();


        if ($request->roles) {
            $user->assignRole(intval($request->roles));
        }



        $notification = array(
            'message' => 'User Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.user')->with($notification);
    } // End Method

    public function EditUser($id)
    {
        $states = State::all();
        $regions = Region::all();
        $roles = Role::all();
        $user = User::findOrFail($id);
        return view('admin.user.edit_user', compact('user', 'roles', 'states', 'regions'));
    } // End Method

    public function UpdateUser(Request $request)
    {
        $user_id = $request->id;

        $user = User::findOrFail($user_id);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->state_id = $request->state_id;
        $user->region_id = $request->region_id;
        $user->status = 'inactive';
        $user->save();

        $user->roles()->detach();
        if ($request->roles) {
            $user->assignRole(intval($request->roles));
        }

        $notification = array(
            'message' => 'User Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.user')->with($notification);
    } // End Method

    public function DeleteUser($id)
    {
        $user = User::findOrFail($id);

        if (!is_null($user)) {
            $user->delete();
        }

        $notification = array(
            'message' => 'User Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } // End Method


    public function InactiveUser($id)
    {
        User::findOrFail($id)->update(['status' => 'inactive']);

        $notification = array(
            'message' => 'User Inactive Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    } //End Method

    public function ActiveUser($id)
    {
        User::findOrFail($id)->update(['status' => 'active']);

        $notification = array(
            'message' => 'User Active Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    } //End Method

    public function GetUser(Request $request)
    {
        $region_id = $request->input('region_id');
        $warehouse_users = User::role('WarehouseManager')->whereIn('region_id', $region_id)->get();

        $response = [
            'warehouse_users' => ''
        ];

        foreach ($warehouse_users as $user) {
            $response['warehouse_users'] .= "<option value='{$user->id}'>{$user->name}</option>";
        }



        return json_encode($response);
    }
    public function GetWarehouse(Request $request)
    {
        $region_id = $request->input('region_id');
        $warehouse_users = User::role('WarehouseManager')->where('region_id', $region_id)->get();

        $response = [
            'warehouse_users' => '<option value=""></option>'
        ];

        foreach ($warehouse_users as $user) {
            $response['warehouse_users'] .= "<option value='{$user->id}'>{$user->name}</option>";
        }



        return json_encode($response);
    }

    public function Viewchangepass($id)
    {

        $user = User::findOrFail($id);
        return view('admin.user.change_password', compact('user'));
    } // End Method

    public function ChangeUserPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed'],
        ]);

        $user = User::findOrFail($request->input('id'));


        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('all.user')->with([
            'message' => 'Password changed successfully!',
            'alert-type' => 'success',
        ]);
    }
}
