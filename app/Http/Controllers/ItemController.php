<?php

namespace App\Http\Controllers;

use App\Imports\ItemsImport;
use App\Models\Category;
use App\Models\items;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    public function AllItems(){
        $category = Category::orderBy('name', 'ASC')->get();
        $allitems = items::latest()->get();
        return view('admin.items.all_items', compact('allitems','category'));
    }//End Method

    public function AddItems(){
        $category = Category::orderBy('name', 'ASC')->get();
        return view('admin.items.add_items', compact('category'));
    }//End Method

    public function StoreItems(Request $request){

        items::insert([
            'name' => $request->name,
            'cat_id' => $request->cat_id,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Item Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.items')->with($notification);
    }//End Method

    public function EditItems($id){
        $items = items::findOrFail($id);        
        $category = Category::orderBy('name', 'ASC')->get();
        return view('admin.items.edit_items', compact('items','category'));
  
    }

    public function UpdateItems(Request $request,$id){

        items::findOrFail($id)->update([
            'name' => $request->name,
            'cat_id' => $request->cat_id,
        ]);

        $notification = array(
            'message' => 'Item Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.items')->with($notification);
    }//End Method
    
    public function DeleteItems($id)
    {
        items::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Item Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } //End Method




    public function ImportExcel(Request $request)
    {
        $request->validate([
            'cat_id' => 'required|exists:categories,id',
            'item_excel' => 'required|file|mimes:xls,xlsx'
        ]);

        $cat_id = $request->input('cat_id');
        $import = new ItemsImport($cat_id);
        
        Excel::import($import, $request->file('item_excel'));

        $errors = $import->errors;
        $skippedItems = $import->skippedItems;

        // Prepare feedback messages
        $feedback = [];

        if (!empty($errors)) {
            $feedback['errors'] = $errors;
        }

        if (!empty($skippedItems)) {
            $feedback['skipped'] = "The following items were skipped because they already exist: " . implode(', ', $skippedItems);
        }

        if (empty($errors) && empty($skippedItems)) {
            $notification = array(
                'message' => 'Items imported Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->back()->with($notification);
        }

        return redirect()->back()->with($feedback);
    }
    
}
