<?php

namespace App\Http\Controllers;

use App\Models\QuarterlyVisit;
use App\Models\Region;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class QuarterlyVisitController extends Controller
{
    //     public function AllQuarterlyVisit()
    // {
    //     $id = Auth::user()->id;
    //     $userid = User::findOrFail($id);

    //     if ($userid->hasRole('WarehouseManager')) {
    //         $allvisits = QuarterlyVisit::where('region_id', $userid->region_id)
    //             ->orWhere('user_id', $userid->id)
    //             ->get();
    //     } elseif ($userid->hasRole('RegionalManager')) {
    //         $allvisits = QuarterlyVisit::whereNull('user_id')
    //             ->where('region_id', $userid->region_id)
    //             ->get();
    //     } elseif ($userid->hasRole('CorporateOffice')) {
    //         $allvisits = QuarterlyVisit::where('region_id', $userid->region_id)
    //             ->orWhere('user_id', $userid->id)
    //             ->get();
    //     } else {
    //         $allvisits = QuarterlyVisit::latest()->get();
    //     }

    //     return view('admin.quarterlyvisit.all_quarterlyvisit', compact('allvisits'));
    // } //End Method


    public function AllQuarterlyVisit()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);

        if ($userid->hasRole('WarehouseManager')) {
            $allvisits = QuarterlyVisit::where('user_id', $userid->id)->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $allvisits = QuarterlyVisit::where('region_id', $userid->region_id)
                ->get();
        } elseif ($userid->hasRole('CorporateOffice')) {
            $allvisits = QuarterlyVisit::where('user_id', $userid->id)->get();
        } else {
            $allvisits = QuarterlyVisit::latest()->get();
        }

        return view('admin.quarterlyvisit.all_quarterlyvisit', compact('allvisits'));
    } // End Method

    public function AddQuarterlyVisit()
    {
        $regions = Region::all();
        $allcorporateoffices = User::role('CorporateOffice')->latest()->get();
        return view('admin.quarterlyvisit.add_quarterlyvisit', compact('regions', 'allcorporateoffices'));
    } //End method

    public function StoreQuarterlyVisit(Request $request)
    {
        $exists = QuarterlyVisit::where('phase_id', $request->phase_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($exists) {
            $notification = array(
                'message' => 'Phase ' . $request->phase_id . ' already exists for this user',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }

        if ($request->user_id) {
            $user = User::find($request->user_id);
            if ($user && $user->hasRole('CorporateOffice')) {
                $request->merge(['region_id' => $user->region_id]);
            }
        }

        $data = array_filter([
            'region_id' => $request->region_id,
            'user_id' => $request->user_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'phase_id' => $request->phase_id,
            'created_at' => Carbon::now(),
        ]);

        QuarterlyVisit::create($data);

        $notification = array(
            'message' => 'Quarterly Visit Schedule Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.quarterlyvisit')->with($notification);
    } //End method

    public function EditQuarterlyVisit($id)
    {

        $regions = Region::all();
        $warehouses = User::role('WarehouseManager')->get();
        $visits = QuarterlyVisit::findOrFail($id);
        return view('admin.quarterlyvisit.edit_quarterlyvisit', compact('visits', 'regions', 'warehouses'));
    } //End Method
    public function UpdateQuarterlyVisit(Request $request, $id)
    {
        QuarterlyVisit::findOrFail($id)->update([
            'region_id' => $request->region_id,
            'warehouse_id' => $request->warehouse_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'phase_id' => $request->phase_id,
        ]);

        $notification = array(
            'message' => 'Quarterly Visit Schedule Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.quarterlyvisit')->with($notification);
    } //End Method
    public function DeleteQuarterlyVisit($id)
    {
        QuarterlyVisit::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Quarterly Visit Schedule Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } //End Method

    public function DetailQuarterlyVisit($id)
    {
        $visit = QuarterlyVisit::findOrFail($id);
        return view('admin.quarterlyvisit.detail_quarterlyvisit', compact('visit'));
    } // End Method


    public function UpdateQuarterlyVisitStatus(Request $request, $id)
    {
        if ($request->file('certificate_image')) {

            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('certificate_image')->getClientOriginalExtension();
            $img = $manager->read($request->file('certificate_image'));
            // $img = $img->resize(523, 605);
            $img->toJpeg(80)->save(base_path('public/upload/certificate_image/' . $name_gen));
            $save_url = 'upload/certificate_image/' . $name_gen;


            QuarterlyVisit::findOrFail($id)->update([
                'remark' => $request->remark,
                'visit_date' => $request->visit_date,
                'certificate_image' => $save_url,
            ]);
            $notification = array(
                'message' => 'Quarterly Visit Date Successfully',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Something Went Wrong',
                'alert-type' => 'success'
            );
        }
        return redirect()->back()->with($notification);
    } // End Method
}
