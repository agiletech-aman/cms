<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\items;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function AllCategory(){
        $allcategory = Category::latest()->get();
        return view('admin.category.all_category', compact('allcategory'));
    }//End Method

    public function AddCategory(){
        return view('admin.category.add_category');
    }//End Method

    public function StoreCategory(Request $request){

        Category::insert([
            'name' => $request->name,
            'type' => $request->type,
                'paid_days' => $request->paid_days,
                'ld_amount' => $request->ld_amount,
                'ld_amount_7days' => $request->ld_amount_7days,
                'milestone_amount' => $request->milestone_amount,
            'specification' => $request->specification,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Item Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.category')->with($notification);
    }//End Method

    public function EditCategory($id){
        $category = Category::findOrFail($id);
        return view('admin.category.edit_category', compact('category'));
  
    }

    public function UpdateCategory(Request $request,$id){
        $userID = Auth::user()->id;
        $userid = User::findOrFail($userID);
        if ($userid->hasRole('SuperAdmin')) {
            Category::findOrFail($id)->update([
                'name' => $request->name,
                'type' => $request->type,
                'paid_days' => $request->paid_days,
                'ld_amount' => $request->ld_amount,
                'ld_amount_7days' => $request->ld_amount_7days,
                'milestone_amount' => $request->milestone_amount,
                'specification' => $request->specification,
            ]);
        }else{
            Category::findOrFail($id)->update([
                'name' => $request->name,
                'type' => $request->type,
                'specification' => $request->specification,
            ]);
        }
       

        $notification = array(
            'message' => 'Item Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.category')->with($notification);
    }//End Method
    
    public function DeleteCategory($id)
    {
        Category::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Item Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } //End Method

    public function GetItem($cat_id){
        $ajaxitem= items::where('cat_id',$cat_id)->orderBy('name', 'ASC')->get();
        return json_encode($ajaxitem);
    
    }
}
