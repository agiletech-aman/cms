<?php

namespace App\Http\Controllers;

use App\Models\Engineer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EngineerController extends Controller
{
    public function AllEngineer()
    {
        $engineers = Engineer::latest()->get();
        return view('admin.engineer.all_engineer', compact('engineers'));
    } //End Method

    public function AddEngineer()
    {
        return view('admin.engineer.add_engineer');
    } //End Method

    public function StoreEngineer(Request $request)
    {
        Engineer::create($this->validated($request));

        return redirect()->route('all.engineer')->with([
            'message' => 'Engineer Added Successfully',
            'alert-type' => 'success',
        ]);
    } //End Method

    public function EditEngineer($id)
    {
        $engineer = Engineer::findOrFail($id);
        return view('admin.engineer.edit_engineer', compact('engineer'));
    } //End Method

    public function UpdateEngineer(Request $request, $id)
    {
        $engineer = Engineer::findOrFail($id);
        $engineer->update($this->validated($request, $engineer->id));

        return redirect()->route('all.engineer')->with([
            'message' => 'Engineer Updated Successfully',
            'alert-type' => 'success',
        ]);
    } //End Method

    public function DeleteEngineer($id)
    {
        Engineer::findOrFail($id)->delete();

        return redirect()->back()->with([
            'message' => 'Engineer Deleted Successfully',
            'alert-type' => 'success',
        ]);
    } //End Method

    private function validated(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('engineers', 'email')->ignore($ignoreId)],
            'phone' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
        ], [
            'phone.regex' => 'Enter a valid 10 digit mobile number.',
            'email.unique' => 'This email is already added.',
        ]);
    }
}
