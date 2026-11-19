<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Complaint;
use App\Models\ComplaintImage;
use App\Models\items;
use App\Models\OldComplaint;
use App\Models\Region;
use App\Models\State;
use App\Models\User;
use App\Notifications\ComplaintNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function AllNewComplaint()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);
        if ($userid->hasRole(['SuperAdmin', 'Railtel', 'CorporateOffice', 'Vendor'])) {
            $allcomplaints = Complaint::latest()->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $allcomplaints = Complaint::where('user_id', $userid->id)
                ->orWhereHas('user', function ($query) use ($userid) {
                    $query->where('region_id', $userid->region_id);
                })->orderBy('created_at', 'DESC')->get();
        } else {
            $allcomplaints = Complaint::where('user_id', $userid->id)->orderBy('created_at', 'DESC')->get();
        }


        return view('admin.complaint.all_new_complaint', compact('allcomplaints'));
    } //End Method

    public function AddComplaint()
    {

        $newcategory = Category::whereIn('type', [1, 3])->get();
        $oldcategory = Category::where('type', 2)->get();
        // $cccategory = Category::where('type',3)->get();

        return view('admin.complaint.add_complaint', compact('newcategory', 'oldcategory'));
    } //End Method

    public function StoreComplaint(Request $request)
    {
        $user_admin = User::role('Railtel')->get();
        $loggedInUser = User::find(Auth::id());

        $users = User::find(Auth::id());
        $lastid = Complaint::latest()->first();


        $number = $lastid ? $lastid->id + 1 : 1;
        $num = str_pad($number, 4, "0", STR_PAD_LEFT);

        $complaintUser = $request->has('complaint_for') ? User::find($request->complaint_for) : $loggedInUser;


        $nameForComplaintId = $complaintUser ? strtoupper($complaintUser->name) : strtoupper($users->username);
        $complaint_id = 'CWC/IPCCTV/N/' . $nameForComplaintId . '/' . $num;

        $complaintExists = Complaint::where('user_id', $users->id)
            ->where('cat_id', $request->cat_id)
            ->where('item_id', $request->item_id)
            ->where('complaint_status', '!=', 4)
            ->exists();

        if ($complaintExists) {
            return redirect()->route('all.new.complaint')->with([
                'message' => 'Complaint Already Added',
                'alert-type' => 'warning',
            ]);
        }

        if ($users->hasRole('RegionalManager')) {
            $complaint = Complaint::create([
                'item_id' => $request->item_id,
                'cat_id' => $request->cat_id,
                'complaint_id' => $complaint_id,
                'subject' => $request->subject,
                'description' => $request->description,
                'complaint_status' => 0,
                'user_id' => $request->complaint_for,
                'raised_by' => Auth::id(),
            ]);
        } else {
            $complaint = Complaint::create([
                'item_id' => $request->item_id,
                'cat_id' => $request->cat_id,
                'complaint_id' => $complaint_id,
                'subject' => $request->subject,
                'description' => $request->description,
                'complaint_status' => 0,
                'user_id' => $users->id,
                'raised_by' => Auth::id(),
            ]);
        }

        DB::table('alert_logs')->insert([
            'complaint_id' => $complaint->id,
            'complaint_type' => 1,
            'user_id' => $complaint->user_id,
            'complaint_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $region_id = $users->region_id;
        $regionalManagers = User::where('region_id', $region_id)->role('RegionalManager')->get();

     //   Notification::send($user_admin, new ComplaintNotification($complaint));
      //  Notification::send($complaintUser, new ComplaintNotification($complaint));
    //    Notification::send($regionalManagers, new ComplaintNotification($complaint));

        $notification = array(
            'message' => 'Complaint Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('complaint', 0)->with($notification);
    }


    public function EditComplaint($id)
    {
        $category = Category::whereIn('type', [1, 3])->get();

        $complaint = Complaint::findOrFail($id);
        $items = items::where('cat_id', $complaint->cat_id)->get();

        return view('admin.complaint.edit_complaint', compact('category', 'complaint', 'items'));
    } // End Method

    public function UpdateComplaint(Request $request, $id)
    {

        $userID = Auth::user()->id;
        $userid = User::findOrFail($userID);
        if ($userid->hasRole('SuperAdmin')) {
            Complaint::findOrFail($id)->update([
                'item_id' => $request->item_id,
                'cat_id' => $request->cat_id,
                'subject' => $request->subject,
                'description' => $request->description,
                'created_at' => $request->created_at,
            ]);
        } elseif ($userid->hasRole(['RegionalManager', 'WarehouseManager', 'CorporateOffice',])) {
            Complaint::findOrFail($id)->update([
                'item_id' => $request->item_id,
                'cat_id' => $request->cat_id,
                'subject' => $request->subject,
                'description' => $request->description,
            ]);
        } else {
            Complaint::findOrFail($id)->update([
                'item_id' => $request->item_id,
                'subject' => $request->subject,
                'description' => $request->description,
            ]);
        }



        $notification = array(
            'message' => 'Complaint Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.new.complaint')->with($notification);
    } // End Method

    public function DeleteComplaint($id)
    {
        Complaint::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Complaint Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } //End Method

    ///////// COmplaint Status //////////////

    public function Complaint($id)
    {
        $uid = Auth::user()->id;
        $userid = User::findOrFail($uid);
        if ($userid->hasRole('SuperAdmin')) {
            $complaints = Complaint::where('complaint_status', $id)->orderBy('created_at', 'DESC')->get();
            $warehouses = User::role('WarehouseManager')->get();
            $regionalManagers = User::role('RegionalManager')->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $complaints = Complaint::join('users', 'users.id', '=', 'complaints.user_id')->where('users.region_id', $userid->region_id)->where('complaints.complaint_status', $id)->orderBy('complaints.created_at', 'DESC')->get(['complaints.id', 'complaints.user_id', 'complaints.item_id', 'complaints.cat_id', 'complaints.complaint_id', 'complaints.subject', 'complaints.description', 'complaints.complaint_status', 'complaints.created_at', 'complaints.closed_date', 'complaints.resolved_date', 'complaints.remark', 'complaints.root_cause']);
            $warehouses = User::role('WarehouseManager')->where('users.region_id', $userid->region_id)->get();
            $regionalManagers = User::role('RegionalManager')->where('users.region_id', $userid->region_id)->get();
        } elseif ($userid->hasRole(['WarehouseManager',])) {
            $complaints = Complaint::where('user_id', $userid->id)->where('complaint_status', $id)->orderBy('created_at', 'DESC')->get();
            $warehouses = User::role('WarehouseManager')->where('users.id', $userid->id)->get();
            $regionalManagers = User::role('RegionalManager')->where('users.region_id', $userid->region_id)->get();
        } else {
            $complaints = Complaint::where('complaint_status', $id)->get();
            $warehouses = User::role('WarehouseManager')->get();
            $regionalManagers = User::role('RegionalManager')->get();
        }
        $regions = Region::all();
        return view('admin.complaint.complaints', compact('complaints', 'warehouses', 'regionalManagers', 'regions'));
    }

    public function DetailComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);
        return view('admin.complaint.detail_complaint', compact('complaint'));
    } // End Method


    // public function UpdateComplaintStatus(Request $request, $id)
    // {
    //     $messages = [
    //         'complaint_image.*.image' => 'Each file must be an image.',
    //         'complaint_image.*.mimes' => 'Only jpeg, png, jpg, gif, and svg formats are allowed.',
    //         'complaint_image.*.max' => 'Each image must not exceed 2MB.',
    //         'visit_date.required_if' => 'Visit date is required when the status is In Progress.',
    //         'visit_date.date' => 'Visit date must be a valid date.',
    //     ];

    //     // Validate the request with custom messages
    //     $request->validate([
    //         'complaint_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //         'complaint_status' => 'required|integer',
    //         'resolved_date' => 'nullable|date',
    //         'closed_date' => 'nullable|date',
    //         'visit_date' => 'nullable|date|required_if:complaint_status,1',
    //         'remark' => 'nullable|string|max:255',
    //     ], $messages);

    //     $user = Auth::user();

    //     $complaint = Complaint::findOrFail($id); // Fetch the complaint
    //     $complaintUser = User::find($complaint->user_id);
    //     $superAdmins = User::role('Railtel')->get(); // Fetch SuperAdmins

    //     $statusMessages = [
    //         0 => 'Open',
    //         1 => 'Inprogress',
    //         2 => 'Resolved',
    //         3 => 'Duplicate',
    //         4 => 'Closed'
    //     ];
    //     $complaint_msg = $statusMessages[$request->complaint_status] ?? 'Unknown Status';

    //     // Insert into alert_logs
    //     DB::table('alert_logs')->insert([
    //         'complaint_id' => $id,
    //         'complaint_type' => 1,
    //         'user_id' => $user->id,
    //         'complaint_status' => $request->complaint_status,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     // Add a comment
    //     $comment = Comment::create([
    //         'complaint_id' => $id,
    //         'comment' => 'This complaint is ' . $complaint_msg,
    //         'remark' => $request->remark,
    //         'user_id' => $user->id,
    //         'created_at' => Carbon::now(),
    //     ]);

    //     // Handle image uploads
    //     if ($request->hasFile('complaint_image')) {
    //         foreach ($request->file('complaint_image') as $multi_image) {
    //             $manager = new ImageManager(new Driver());
    //             $name_gen = hexdec(uniqid()) . '.' . $multi_image->getClientOriginalExtension();
    //             $img = $manager->read($multi_image);
    //             $img->toJpeg(80)->save(base_path('public/upload/complaint_image/' . $name_gen));
    //             $save_url = 'upload/complaint_image/' . $name_gen;

    //             ComplaintImage::create([
    //                 'complaint_id' => $id,
    //                 'user_id' => $user->id,
    //                 'complaint_image' => $save_url,
    //                 'comment_id' => $comment->id, // Storing the comment ID
    //                 'created_at' => Carbon::now(),
    //             ]);
    //         }
    //     }

    //     // Prepare update data
    //     $updateData = [
    //         'complaint_status' => $request->complaint_status,
    //         'status_change_by' => $user->id,
    //     ];

    //     // Set specific dates based on status
    //     if ($request->complaint_status == 2) { // Resolved
    //         $updateData['resolved_date'] = $request->resolved_date ? Carbon::parse($request->resolved_date) : null;
    //     } elseif ($request->complaint_status == 4) { // Closed
    //         $updateData['closed_date'] = $request->closed_date;
    //     } elseif ($request->complaint_status == 1) { // In Progress
    //         $updateData['visit_date'] = $request->visit_date;
    //     }

    //     // Update the complaint
    //     $complaint->update($updateData);

    //     // Send notifications
    //     Notification::send($superAdmins, new ComplaintNotification($complaint, 'update'));
    //     Notification::send($complaintUser, new ComplaintNotification($complaint, 'update'));

    //     return redirect()->back()->with([
    //         'message' => 'Complaint Status Updated Successfully',
    //         'alert-type' => 'success'
    //     ]);
    // }



    // public function UpdateComplaintStatus(Request $request, $id)
    // {
    //     $messages = [
    //         'complaint_files.*.mimes' => 'Only jpeg, png, jpg, gif, svg, and pdf formats are allowed.',
    //         'complaint_files.*.max' => 'Each file must not exceed 1MB.',
    //         'visit_date.required_if' => 'Visit date is required when the status is In Progress.',
    //         'visit_date.date' => 'Visit date must be a valid date.',
    //     ];

    //     // Validate the request with custom messages
    //     $request->validate([
    //         'complaint_files.*' => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
    //         'complaint_status' => 'required|integer',
    //         'resolved_date' => 'nullable|date',
    //         'closed_date' => 'nullable|date',
    //         'visit_date' => 'nullable|date|required_if:complaint_status,1',
    //         'remark' => 'nullable|string',
    //     ], $messages);

    //     $user = Auth::user();

    //     $complaint = Complaint::findOrFail($id); // Fetch the complaint
    //     $complaintUser = User::find($complaint->user_id);
    //     $superAdmins = User::role('Railtel')->get(); // Fetch SuperAdmins

    //     $statusMessages = [
    //         0 => 'Open',
    //         1 => 'Inprogress',
    //         2 => 'Resolved',
    //         3 => 'Duplicate',
    //         4 => 'Closed'
    //     ];
    //     $complaint_msg = $statusMessages[$request->complaint_status] ?? 'Unknown Status';

    //     // Insert into alert_logs
    //     DB::table('alert_logs')->insert([
    //         'complaint_id' => $id,
    //         'complaint_type' => 1,
    //         'user_id' => $user->id,
    //         'complaint_status' => $request->complaint_status,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     // Add a comment
    //     $comment = Comment::create([
    //         'complaint_id' => $id,
    //         'comment' => 'This complaint is ' . $complaint_msg,
    //         'remark' => $request->remark,
    //         'user_id' => $user->id,
    //         'created_at' => Carbon::now(),
    //     ]);

    //     // Handle file uploads
    //     if ($request->hasFile('complaint_files')) {
    //         foreach ($request->file('complaint_files') as $file) {
    //             $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
    //             $file_type = $file->getClientMimeType();
    //             $save_url = '';

    //             // Determine file type category for database storage
    //             $fileTypeCategory = 'other';
    //             if (strpos($file_type, 'image') !== false) {
    //                 $fileTypeCategory = 'image';
    //             } elseif (strpos($file_type, 'pdf') !== false) {
    //                 $fileTypeCategory = 'pdf';
    //             }

    //             // Process file based on type
    //             if ($fileTypeCategory === 'image') {
    //                 // Process image with intervention/image
    //                 $manager = new ImageManager(new Driver());
    //                 $img = $manager->read($file);
    //                 $img->toJpeg(80)->save(public_path('upload/complaint_files/' . $name_gen));
    //                 $save_url = 'upload/complaint_files/' . $name_gen;
    //             } else {
    //                 // For PDFs and other files, just move the file
    //                 $file->move(public_path('upload/complaint_files'), $name_gen);
    //                 $save_url = 'upload/complaint_files/' . $name_gen;
    //             }

    //             // Create the complaint image/file record
    //             ComplaintImage::create([
    //                 'complaint_id' => $id,
    //                 'user_id' => $user->id,
    //                 'complaint_image' => $save_url,

    //                 // 'file_type' => $fileTypeCategory, // Store simplified file type
    //                 'comment_id' => $comment->id,
    //                 'created_at' => Carbon::now(),
    //             ]);
    //         }
    //     }
    //     // Prepare update data
    //     $updateData = [
    //         'complaint_status' => $request->complaint_status,
    //         'status_change_by' => $user->id,
    //     ];

    //     // Set specific dates based on status
    //     if ($request->complaint_status == 2) { // Resolved
    //         $updateData['resolved_date'] = $request->resolved_date ? Carbon::parse($request->resolved_date) : null;
    //     } elseif ($request->complaint_status == 4) { // Closed
    //         $updateData['closed_date'] = $request->closed_date;
    //     } elseif ($request->complaint_status == 1) { // In Progress
    //         $updateData['visit_date'] = $request->visit_date;
    //     }

    //     // Update the complaint
    //     $complaint->update($updateData);

    //     // Send notifications
    //  //   Notification::send($superAdmins, new ComplaintNotification($complaint, 'update'));
    //   //  Notification::send($complaintUser, new ComplaintNotification($complaint, 'update'));

    //     return redirect()->back()->with([
    //         'message' => 'Complaint Status Updated Successfully',
    //         'alert-type' => 'success'
    //     ]);
    // } // end method


    
    
//      public function UpdateComplaintStatus(Request $request, $id)
// {
//     $complaint = Complaint::findOrFail($id);
//     $user = Auth::user();

//     $messages = [
//         'complaint_files.*.mimes' => 'Only jpeg, png, jpg, gif, svg, and pdf formats are allowed.',
//         'complaint_files.*.max' => 'Each file must not exceed 1MB.',
//         'visit_date.required_if' => 'Visit date is required when the status is In Progress.',
//         'visit_date.date' => 'Visit date must be a valid date/time.',
//         'resolved_date.date' => 'Resolved date must be a valid date/time.',
//         'closed_date.date' => 'Closed date must be a valid date/time.',
//         'reopen_date.date' => 'Reopen date must be a valid date/time.',
//     ];

//     $validator = Validator::make($request->all(), [
//         'complaint_files.*' => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
//         'complaint_status'  => 'required|integer',
//         'visit_date'        => 'nullable|date|required_if:complaint_status,1',
//         'resolved_date'     => 'nullable|date',
//         'closed_date'       => 'nullable|date',
//         'reopen_date'       => 'nullable|date',
//         'remark'            => 'nullable|string',
//     ], $messages);


//     ///////validator--ash---

//     $validator->after(function ($validator) use ($complaint, $request) {

//     // Convert stored DB values
//     $createdAt   = Carbon::parse($complaint->created_at);
//     $storedVisit = $complaint->visit_date ? Carbon::parse($complaint->visit_date) : null;
//     $storedResolved = $complaint->resolved_date ? Carbon::parse($complaint->resolved_date) : null;
//     $storedReopen = $complaint->reopen_date ? Carbon::parse($complaint->reopen_date) : null;
//     $storedClosed = $complaint->closed_date ? Carbon::parse($complaint->closed_date) : null;

//     // Convert incoming request values
//     $visit = $request->filled('visit_date') ? Carbon::parse($request->visit_date) : $storedVisit;
//     $resolved = $request->filled('resolved_date') ? Carbon::parse($request->resolved_date) : $storedResolved;
//     $reopen = $request->filled('reopen_date') ? Carbon::parse($request->reopen_date) : $storedReopen;
//     $closed = $request->filled('closed_date') ? Carbon::parse($request->closed_date) : $storedClosed;


//     // ====== RULE 1: IN PROGRESS ======
//     if ((int)$request->complaint_status === 1) {
//         if ($visit && $visit->lt($createdAt)) {
//             $validator->errors()->add(
//                 'visit_date',
//                 "In Progress date/time cannot be before : " . $createdAt->format('d-m-Y H:i:s')
//             );
//         }
//     }


//     // ====== RULE 2: RESOLVED ======
//     if ((int)$request->complaint_status === 2) {

//         // baseline = highest previous timestamp (created, visit)
//         $baseline = $visit ?: $storedVisit ?: $createdAt;

//         if ($resolved && $resolved->lt($baseline)) {
//             $validator->errors()->add(
//                 'resolved_date',
//                 "Resolved date/time cannot be before : " . $baseline->format('d-m-Y H:i:s')
//             );
//         }
//     }


//     // ====== RULE 3: REOPEN ======
//     if ((int)$request->complaint_status === 0) {

//         // baseline = highest previous timestamp (created, visit, resolved)
//         $baseline = $resolved ?: $storedResolved ?: $visit ?: $storedVisit ?: $createdAt;

//         if ($reopen && $reopen->lt($baseline)) {
//             $validator->errors()->add(
//                 'reopen_date',
//                 "Reopen date/time cannot be before : " . $baseline->format('d-m-Y H:i:s')
//             );
//         }
//     }


//     // ====== RULE 4: CLOSED ======
//     if ((int)$request->complaint_status === 4) {

//         // baseline = highest of all previous timestamps
//         // created < visit < resolved < reopen
//         $baseline = collect([
//             $createdAt,
//             $visit,
//             $storedVisit,
//             $resolved,
//             $storedResolved,
//             $reopen,
//             $storedReopen
//         ])->filter()->sort()->last();

//         if ($closed && $closed->lt($baseline)) {
//             $validator->errors()->add(
//                 'closed_date',
//                 "Closed date/time cannot be before : " . $baseline->format('d-m-Y H:i:s')
//             );
//         }
//     }
// });

//     // Chronological checks
//     // $validator->after(function ($validator) use ($complaint, $request) {
//     //     $createdAt = Carbon::parse($complaint->created_at);

//     //     // Helper to parse safely
//     //     $parseOrNull = function ($val) {
//     //         try {
//     //             return $val ? Carbon::parse($val) : null;
//     //         } catch (\Exception $e) {
//     //             return null;
//     //         }
//     //     };

//     //     // Determine preferred baselines (use request first, then stored, then created)
//     //     $requestVisit = $parseOrNull($request->input('visit_date'));
//     //     $storedVisit  = !empty($complaint->visit_date) ? Carbon::parse($complaint->visit_date) : null;

//     //     $requestResolved = $parseOrNull($request->input('resolved_date'));
//     //     $storedResolved  = !empty($complaint->resolved_date) ? Carbon::parse($complaint->resolved_date) : null;

//     //     $requestClosed = $parseOrNull($request->input('closed_date'));
//     //     $storedClosed  = !empty($complaint->closed_date) ? Carbon::parse($complaint->closed_date) : null;

//     //     $requestReopen = $parseOrNull($request->input('reopen_date'));
//     //     $storedReopen  = !empty($complaint->reopen_date) ? Carbon::parse($complaint->reopen_date) : null;

//     //     // 1) If setting In Progress: visit_date must exist and must be >= created_at
//     //     if ((int)$request->complaint_status === 1) {
//     //         $visit = $requestVisit ?? $storedVisit;
//     //         if (!$visit) {
//     //             $validator->errors()->add('visit_date', 'Visit date is required for In Progress status.');
//     //         } else {
//     //             if ($visit->lt($createdAt)) {
//     //                 $validator->errors()->add(
//     //                     'visit_date',
//     //                     'Visit date/time cannot be before complaint created date/time: ' . $createdAt->format('d-m-Y H:i:s')
//     //                 );
//     //             }
//     //         }
//     //     }

//     //     // 2) If setting Resolved: resolved_date must be >= the most recent In Progress (prefer request, stored) else created_at
//     //     if ((int)$request->complaint_status === 2) {
//     //         $resolved = $requestResolved;
//     //         if (!$resolved && $storedResolved) $resolved = $storedResolved; // allow using already stored resolved (rare)
//     //         if ($resolved) {
//     //             // baseline: prefer request visit, then stored visit, then createdAt
//     //             $baseline = $requestVisit ?? $storedVisit ?? $createdAt;
//     //             if ($resolved->lt($baseline)) {
//     //                 $validator->errors()->add(
//     //                     'resolved_date',
//     //                     'Resolved date/time cannot be before In Progress date/time (baseline): ' . $baseline->format('d-m-Y H:i:s')
//     //                 );
//     //             }
//     //         } else {
//     //             // if resolved date not supplied, but status set to Resolved, we may allow null (depends on your business); keep optional
//     //         }
//     //     }

//     //     // 3) If setting Closed: closed_date must be >= the most recent resolved (prefer request resolved, then stored resolved),
//     //     //    if no resolved then >= most recent visit, else >= created_at.
//     //     if ((int)$request->complaint_status === 4) {
//     //         $closed = $requestClosed ?? $storedClosed;
//     //         if (!$closed) {
//     //             $validator->errors()->add('closed_date', 'Closed date is required for Closed status.');
//     //         } else {
//     //             // baseline priority: requestResolved > storedResolved > requestVisit > storedVisit > createdAt
//     //             $baseline = $requestResolved ?? $storedResolved ?? $requestVisit ?? $storedVisit ?? $createdAt;
//     //             if ($closed->lt($baseline)) {
//     //                 $validator->errors()->add(
//     //                     'closed_date',
//     //                     'Closed date/time cannot be before baseline date/time: ' . $baseline->format('d-m-Y H:i:s')
//     //                 );
//     //             }
//     //         }
//     //     }

//     //     // 4) If setting Reopen (status = 0): reopen_date must be >= closed_date (prefer request closed, then stored closed),
//     //     //    if closed not present use resolved (request/stored), otherwise createdAt
//     //     if ((int)$request->complaint_status === 0 && ($request->filled('reopen_date') || $storedReopen)) {
//     //         $reopen = $requestReopen ?? $storedReopen;
//     //         if ($reopen) {
//     //             $baseline = $requestClosed ?? $storedClosed ?? $requestResolved ?? $storedResolved ?? $createdAt;
//     //             if ($reopen->lt($baseline)) {
//     //                 $validator->errors()->add(
//     //                     'reopen_date',
//     //                     'Reopen date/time cannot be before Closed/Resolved baseline: ' . $baseline->format('d-m-Y H:i:s')
//     //                 );
//     //             }
//     //         } else {
//     //             $validator->errors()->add('reopen_date', 'Reopen date is required for ReOpen status.');
//     //         }
//     //     }
//     // });

//     // Will redirect back with errors automatically
//     $validator->validate();

//     // ---------- existing logic after validation ----------
//     $statusMessages = [
//         0 => 'Open',
//         1 => 'Inprogress',
//         2 => 'Resolved',
//         3 => 'Duplicate',
//         4 => 'Closed'
//     ];
//     $complaint_msg = $statusMessages[$request->complaint_status] ?? 'Unknown Status';

//     DB::table('alert_logs')->insert([
//         'complaint_id' => $id,
//         'complaint_type' => 1,
//         'user_id' => $user->id,
//         'complaint_status' => $request->complaint_status,
//         'created_at' => now(),
//         'updated_at' => now(),
//     ]);

//     $comment = Comment::create([
//         'complaint_id' => $id,
//         'comment' => 'This complaint is ' . $complaint_msg,
//         'remark' => $request->remark,
//         'user_id' => $user->id,
//         'created_at' => Carbon::now(),
//     ]);

//     // file handling (same as your code)
//     // file handling (SAFE – NO GD, NO Intervention)
// if ($request->hasFile('complaint_files')) {
//     foreach ($request->file('complaint_files') as $file) {

//         $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

//         // just move file (works for image + pdf)
//         $file->move(public_path('upload/complaint_files'), $name_gen);

//         ComplaintImage::create([
//             'complaint_id' => $id,
//             'user_id' => $user->id,
//             'complaint_image' => 'upload/complaint_files/' . $name_gen,
//             'comment_id' => $comment->id,
//             'created_at' => now(),
//         ]);
//     }
// }


//     // update fields if provided
//     $updateData = [
//         'complaint_status' => $request->complaint_status,
//         'status_change_by' => $user->id,
//     ];
//     if ($request->filled('visit_date')) {
//         $updateData['visit_date'] = Carbon::parse($request->visit_date);
//     }
//     if ($request->filled('resolved_date')) {
//         $updateData['resolved_date'] = Carbon::parse($request->resolved_date);
//     }
//     if ($request->filled('closed_date')) {
//         $updateData['closed_date'] = Carbon::parse($request->closed_date);
//     }
//     if ($request->filled('reopen_date')) {
//         $updateData['reopen_date'] = Carbon::parse($request->reopen_date);
//     }

//     $complaint->update($updateData);

//     return redirect()->back()->with([
//         'message' => 'Complaint Status Updated Successfully',
//         'alert-type' => 'success'
//     ]);
// }

   public function UpdateComplaintStatus(Request $request, $id)
{
    $complaint = Complaint::findOrFail($id);
    $user = Auth::user();
 
    $messages = [
        'complaint_files.*.mimes' => 'Only jpeg, png, jpg, gif, svg, and pdf formats are allowed.',
        'complaint_files.*.max' => 'Each file must not exceed 1MB.',
        'visit_date.required_if' => 'Visit date is required when the status is In Progress.',
        'visit_date.date' => 'Visit date must be a valid date/time.',
        'resolved_date.date' => 'Resolved date must be a valid date/time.',
        'closed_date.date' => 'Closed date must be a valid date/time.',
        'reopen_date.date' => 'Reopen date must be a valid date/time.',
    ];
 
    $validator = Validator::make($request->all(), [
        'complaint_files.*' => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
        'complaint_status'  => 'required|integer',
        'visit_date'        => 'nullable|date|required_if:complaint_status,1',
        'resolved_date'     => 'nullable|date',
        'closed_date'       => 'nullable|date',
        'reopen_date'       => 'nullable|date',
        'remark'            => 'nullable|string',
        'root_cause'        => ['nullable', 'required_if:complaint_status,2', \Illuminate\Validation\Rule::in(Complaint::ROOT_CAUSES)],
        'engineer_id'       => 'nullable|exists:engineers,id',
    ], $messages + [
        'root_cause.required_if' => 'Root cause is required when the status is Resolved.',
        'root_cause.in' => 'Please select a valid root cause.',
    ]);
 
 
    ///////validator--ash---
 
    $validator->after(function ($validator) use ($complaint, $request) {
 
    // Convert stored DB values
    $createdAt   = Carbon::parse($complaint->created_at);
    $storedVisit = $complaint->visit_date ? Carbon::parse($complaint->visit_date) : null;
    $storedResolved = $complaint->resolved_date ? Carbon::parse($complaint->resolved_date) : null;
    $storedReopen = $complaint->reopen_date ? Carbon::parse($complaint->reopen_date) : null;
    $storedClosed = $complaint->closed_date ? Carbon::parse($complaint->closed_date) : null;
 
    // Convert incoming request values
    $visit = $request->filled('visit_date') ? Carbon::parse($request->visit_date) : $storedVisit;
    $resolved = $request->filled('resolved_date') ? Carbon::parse($request->resolved_date) : $storedResolved;
    $reopen = $request->filled('reopen_date') ? Carbon::parse($request->reopen_date) : $storedReopen;
    $closed = $request->filled('closed_date') ? Carbon::parse($request->closed_date) : $storedClosed;
 
 
    // ====== RULE 1: IN PROGRESS ======
    if ((int)$request->complaint_status === 1) {
        if ($visit && $visit->lt($createdAt)) {
            $validator->errors()->add(
                'visit_date',
                "In Progress date/time cannot be before : " . $createdAt->format('d-m-Y H:i:s')
            );
        }

        // Multiple visits: a new visit cannot be before the previous visit
        if ($request->filled('visit_date') && $storedVisit && $visit->lt($storedVisit)) {
            $validator->errors()->add(
                'visit_date',
                "Visit date/time cannot be before previous visit : " . $storedVisit->format('d-m-Y H:i:s')
            );
        }
    }
 
 
    // ====== RULE 2: RESOLVED ======
    if ((int)$request->complaint_status === 2) {
 
        // baseline = highest previous timestamp (created, visit)
        $baseline = $visit ?: $storedVisit ?: $createdAt;
 
        if ($resolved && $resolved->lt($baseline)) {
            $validator->errors()->add(
                'resolved_date',
                "Resolved date/time cannot be before : " . $baseline->format('d-m-Y H:i:s')
            );
        }
    }
 
 
    // ====== RULE 3: REOPEN ======
    if ((int)$request->complaint_status === 0) {
 
        // baseline = highest previous timestamp (created, visit, resolved)
        $baseline = $resolved ?: $storedResolved ?: $visit ?: $storedVisit ?: $createdAt;
 
        if ($reopen && $reopen->lt($baseline)) {
            $validator->errors()->add(
                'reopen_date',
                "Reopen date/time cannot be before : " . $baseline->format('d-m-Y H:i:s')
            );
        }
    }
 
 
    // ====== RULE 4: CLOSED ======
    if ((int)$request->complaint_status === 4) {
 
        // baseline = highest of all previous timestamps
        // created < visit < resolved < reopen
        $baseline = collect([
            $createdAt,
            $visit,
            $storedVisit,
            $resolved,
            $storedResolved,
            $reopen,
            $storedReopen
        ])->filter()->sort()->last();
 
        if ($closed && $closed->lt($baseline)) {
            $validator->errors()->add(
                'closed_date',
                "Closed date/time cannot be before : " . $baseline->format('d-m-Y H:i:s')
            );
        }
    }
 
            // ====== RULE 5: NO FUTURE TIME ======
    $now = Carbon::now();
 
    $checkFuture = function ($field, $value) use ($validator, $now) {
        if ($value && $value->gt($now)) {
            $validator->errors()->add(
                $field,
                " You cannot select future date and time.". " Current time: " . $now->format('d-m-Y H:i:s')
            );
        }
    };
 
    $checkFuture('visit_date', $visit);
    $checkFuture('resolved_date', $resolved);
    $checkFuture('reopen_date', $reopen);
    $checkFuture('closed_date', $closed);
 
});
 
    // Chronological checks
    // $validator->after(function ($validator) use ($complaint, $request) {
    //     $createdAt = Carbon::parse($complaint->created_at);
 
    //     // Helper to parse safely
    //     $parseOrNull = function ($val) {
    //         try {
    //             return $val ? Carbon::parse($val) : null;
    //         } catch (\Exception $e) {
    //             return null;
    //         }
    //     };
 
    //     // Determine preferred baselines (use request first, then stored, then created)
    //     $requestVisit = $parseOrNull($request->input('visit_date'));
    //     $storedVisit  = !empty($complaint->visit_date) ? Carbon::parse($complaint->visit_date) : null;
 
    //     $requestResolved = $parseOrNull($request->input('resolved_date'));
    //     $storedResolved  = !empty($complaint->resolved_date) ? Carbon::parse($complaint->resolved_date) : null;
 
    //     $requestClosed = $parseOrNull($request->input('closed_date'));
    //     $storedClosed  = !empty($complaint->closed_date) ? Carbon::parse($complaint->closed_date) : null;
 
    //     $requestReopen = $parseOrNull($request->input('reopen_date'));
    //     $storedReopen  = !empty($complaint->reopen_date) ? Carbon::parse($complaint->reopen_date) : null;
 
    //     // 1) If setting In Progress: visit_date must exist and must be >= created_at
    //     if ((int)$request->complaint_status === 1) {
    //         $visit = $requestVisit ?? $storedVisit;
    //         if (!$visit) {
    //             $validator->errors()->add('visit_date', 'Visit date is required for In Progress status.');
    //         } else {
    //             if ($visit->lt($createdAt)) {
    //                 $validator->errors()->add(
    //                     'visit_date',
    //                     'Visit date/time cannot be before complaint created date/time: ' . $createdAt->format('d-m-Y H:i:s')
    //                 );
    //             }
    //         }
    //     }
 
    //     // 2) If setting Resolved: resolved_date must be >= the most recent In Progress (prefer request, stored) else created_at
    //     if ((int)$request->complaint_status === 2) {
    //         $resolved = $requestResolved;
    //         if (!$resolved && $storedResolved) $resolved = $storedResolved; // allow using already stored resolved (rare)
    //         if ($resolved) {
    //             // baseline: prefer request visit, then stored visit, then createdAt
    //             $baseline = $requestVisit ?? $storedVisit ?? $createdAt;
    //             if ($resolved->lt($baseline)) {
    //                 $validator->errors()->add(
    //                     'resolved_date',
    //                     'Resolved date/time cannot be before In Progress date/time (baseline): ' . $baseline->format('d-m-Y H:i:s')
    //                 );
    //             }
    //         } else {
    //             // if resolved date not supplied, but status set to Resolved, we may allow null (depends on your business); keep optional
    //         }
    //     }
 
    //     // 3) If setting Closed: closed_date must be >= the most recent resolved (prefer request resolved, then stored resolved),
    //     //    if no resolved then >= most recent visit, else >= created_at.
    //     if ((int)$request->complaint_status === 4) {
    //         $closed = $requestClosed ?? $storedClosed;
    //         if (!$closed) {
    //             $validator->errors()->add('closed_date', 'Closed date is required for Closed status.');
    //         } else {
    //             // baseline priority: requestResolved > storedResolved > requestVisit > storedVisit > createdAt
    //             $baseline = $requestResolved ?? $storedResolved ?? $requestVisit ?? $storedVisit ?? $createdAt;
    //             if ($closed->lt($baseline)) {
    //                 $validator->errors()->add(
    //                     'closed_date',
    //                     'Closed date/time cannot be before baseline date/time: ' . $baseline->format('d-m-Y H:i:s')
    //                 );
    //             }
    //         }
    //     }
 
    //     // 4) If setting Reopen (status = 0): reopen_date must be >= closed_date (prefer request closed, then stored closed),
    //     //    if closed not present use resolved (request/stored), otherwise createdAt
    //     if ((int)$request->complaint_status === 0 && ($request->filled('reopen_date') || $storedReopen)) {
    //         $reopen = $requestReopen ?? $storedReopen;
    //         if ($reopen) {
    //             $baseline = $requestClosed ?? $storedClosed ?? $requestResolved ?? $storedResolved ?? $createdAt;
    //             if ($reopen->lt($baseline)) {
    //                 $validator->errors()->add(
    //                     'reopen_date',
    //                     'Reopen date/time cannot be before Closed/Resolved baseline: ' . $baseline->format('d-m-Y H:i:s')
    //                 );
    //             }
    //         } else {
    //             $validator->errors()->add('reopen_date', 'Reopen date is required for ReOpen status.');
    //         }
    //     }
    // });
 
    // Will redirect back with errors automatically
    $validator->validate();
 
    // ---------- existing logic after validation ----------
    $statusMessages = [
        0 => 'Open',
        1 => 'Inprogress',
        2 => 'Resolved',
        3 => 'Duplicate',
        4 => 'Closed'
    ];
    $complaint_msg = $statusMessages[$request->complaint_status] ?? 'Unknown Status';
 
    DB::table('alert_logs')->insert([
        'complaint_id' => $id,
        'complaint_type' => 1,
        'user_id' => $user->id,
        'complaint_status' => $request->complaint_status,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
 
    $comment = Comment::create([
        'complaint_id' => $id,
        'comment' => 'This complaint is ' . $complaint_msg,
        'remark' => $request->remark,
        'user_id' => $user->id,
        'created_at' => Carbon::now(),
    ]);
 
    // file handling (same as your code)
    // file handling (SAFE – NO GD, NO Intervention)
if ($request->hasFile('complaint_files')) {
    foreach ($request->file('complaint_files') as $file) {
 
        $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
 
        // just move file (works for image + pdf)
        $file->move(public_path('upload/complaint_files'), $name_gen);
 
        ComplaintImage::create([
            'complaint_id' => $id,
            'user_id' => $user->id,
            'complaint_image' => 'upload/complaint_files/' . $name_gen,
            'comment_id' => $comment->id,
            'created_at' => now(),
        ]);
    }
}
 
 
    // update fields if provided
    $updateData = [
        'complaint_status' => $request->complaint_status,
        'status_change_by' => $user->id,
    ];
    if ($request->filled('visit_date')) {
        // complaints.visit_date keeps the latest visit; full history goes to complaint_visits
        $updateData['visit_date'] = Carbon::parse($request->visit_date);

        if ((int) $request->complaint_status === 1) {
            \App\Models\ComplaintVisit::create([
                'complaint_id' => $complaint->id,
                'user_id'      => $user->id,
                'engineer_id'  => $request->engineer_id ?: $complaint->engineer_id,
                'comment_id'   => $comment->id,
                'visit_date'   => Carbon::parse($request->visit_date),
                'remark'       => $request->remark,
            ]);
        }
    }
    if ($request->filled('resolved_date')) {
        $updateData['resolved_date'] = Carbon::parse($request->resolved_date);
    }
    if ($request->filled('closed_date')) {
        $updateData['closed_date'] = Carbon::parse($request->closed_date);
    }
    if ($request->filled('reopen_date')) {
        $updateData['reopen_date'] = Carbon::parse($request->reopen_date);
    }
    if ($request->filled('root_cause')) {
        $updateData['root_cause'] = $request->root_cause;
    }
    if ($request->filled('engineer_id')) {
        $updateData['engineer_id'] = $request->engineer_id;
    }

    $complaint->update($updateData);

    // Resolved -> mail to addresses configured in .env (RESOLVED_COMPLAINT_MAIL_TO)
    if ((int) $request->complaint_status === 2 && !empty(config('complaint.resolved_mail_to'))) {
        try {
            \Illuminate\Support\Facades\Mail::to(config('complaint.resolved_mail_to'))
                ->send(new \App\Mail\ComplaintResolvedMail($complaint->fresh(), $user->name));
        } catch (\Throwable $e) {
            // Status update must not fail because of mail problems
            \Illuminate\Support\Facades\Log::error('Resolved complaint mail failed: ' . $e->getMessage());
        }
    }

    return redirect()->back()->with([
        'message' => 'Complaint Status Updated Successfully',
        'alert-type' => 'success'
    ]);
}
 



    public function ClosedComplaint($id)
    {
        $uid = Auth::user()->id;
        $userid = User::findOrFail($uid);

        Complaint::findOrFail($id)->update([
            'complaint_status' => 4,
            'status_change_by' => $userid->id,
        ]);

        Comment::insert([
            'complaint_id' => $id,
            'comment' => 'This complaint is Closed',
            'user_id' => $userid->id,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Complaint Closed Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } // End Method

    public function ComplaintPiechart()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);
        if ($userid->hasRole('SuperAdmin')) {
            $statuses = Complaint::select('complaint_status', DB::raw('count(*) as total'))
                ->groupBy('complaint_status')
                ->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $statuses = Complaint::select('complaints.complaint_status', DB::raw('count(*) as total'))->join('users', 'complaints.user_id', '=', 'users.id')->where('users.region_id', $userid->region_id)
                ->groupBy('complaints.complaint_status')
                ->get();
        } elseif ($userid->hasRole('WarehouseManager')) {
            $statuses = Complaint::select('complaints.complaint_status', DB::raw('count(*) as total'))->join('users', 'complaints.user_id', '=', 'users.id')->where('users.id', $userid->id)
                ->groupBy('complaints.complaint_status')
                ->get();
        } else {
            $statuses = Complaint::select('complaint_status', DB::raw('count(*) as total'))
                ->groupBy('complaint_status')
                ->get();
        }
        $data = [
            'chart' => [
                'type' => 'pie'
            ],
            'title' => [
                'text' => 'New Complaint Status'
            ],
            'series' => [
                [
                    'name' => 'CMS Complaint status',
                    'data' => $statuses->map(function ($status) {
                        switch ($status->complaint_status) {
                            case 0:
                                $statusName = 'Open';
                                break;
                            case 1:
                                $statusName = 'Inprogress';
                                break;
                            case 2:
                                $statusName = 'Resolved';
                                break;
                            case 3:
                                $statusName = 'Duplicate';
                                break;
                            case 4:
                                $statusName = 'Closed';
                                break;

                            default:
                                $statusName = 'Unknown';
                        }

                        return ['name' => $statusName, 'y' => $status->total];
                    })
                ]
            ]
        ];

        return response()->json($data);
    } // End Method




    // 


    public function ComplaintMonthlyHistogram(Request $request)
{
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);
    // ✅ get selected year (default = current year)
    $year = $request->year ?? date('Y');

    $query = Complaint::select(
        DB::raw('MONTH(complaints.created_at) as month_no'),
        DB::raw('MONTHNAME(complaints.created_at) as month'),
        'complaint_status',
        DB::raw('COUNT(*) as total')
    )
    ->whereYear('complaints.created_at', $year)
    ->groupBy('month_no', 'month', 'complaint_status')
    ->orderBy('month_no', 'ASC');

    // 🔐 role based filter
    if ($userid->hasRole('RegionalManager')) {
        $query->join('users', 'complaints.user_id', '=', 'users.id')
              ->where('users.region_id', $userid->region_id);
    } elseif ($userid->hasRole('WarehouseManager')) {
        $query->where('complaints.user_id', $userid->id);
    }

    $statuses = $query->get();

    // Month list (Jan–Dec in order)
    $months = $statuses->pluck('month')->unique()->values();

    $histogramData = [
        'Open' => [],
        'Resolved' => [],
        'Closed' => [],
    ];

    foreach ($months as $month) {
        $data = $statuses->where('month', $month);

        $histogramData['Open'][]     = $data->where('complaint_status', 0)->sum('total');
        $histogramData['Resolved'][] = $data->where('complaint_status', 2)->sum('total');
        $histogramData['Closed'][]   = $data->where('complaint_status', 4)->sum('total');
    }

    return response()->json([
        'chart' => ['type' => 'bar'],
        'title' => ['text' => ""],
        'xaxis' => [
            'categories' => $months,
            'title' => ['text' => 'Month']
        ],
        'yaxis' => [
            'title' => ['text' => 'Number of New Complaints']
        ],
        'series' => [
            ['name' => 'Open', 'data' => $histogramData['Open']],
            ['name' => 'Resolved', 'data' => $histogramData['Resolved']],
            ['name' => 'Closed', 'data' => $histogramData['Closed']],
        ]
    ]);
}
    // public function ComplaintMonthlyHistogram()
    // {
    //     $id = Auth::user()->id;
    //     $userid = User::findOrFail($id);

    //     $year = $request->year ?? date('Y');


    //     // Base query to select complaint status, month, and count
    //     $query = Complaint::select(
    //         DB::raw('MONTHNAME(complaints.created_at) as month'),
    //         'complaint_status',
    //         DB::raw('count(*) as total')
    //     )->whereYear('complaints.created_at', date('Y')) // Filter for the current year
    //         ->groupBy('month', 'complaint_status')
    //         ->orderBy(DB::raw('MONTH(complaints.created_at)'), 'ASC'); // Order by month

    //     // Adjust the query based on the user's role
    //     if ($userid->hasRole('RegionalManager')) {
    //         $query->join('users', 'complaints.user_id', '=', 'users.id')
    //             ->where('users.region_id', $userid->region_id);
    //     } elseif ($userid->hasRole('WarehouseManager')) {
    //         $query->where('user_id', $userid->id);
    //     }

    //     // Execute the query and get the results
    //     $statuses = $query->get();

    //     // Structure the data for the histogram
    //     $histogramData = [
    //         'Open' => [],
    //         'Resolved' => [],
    //         'Closed' => [],
    //     ];

    //     $months = $statuses->groupBy('month')->keys()->all(); // Get all unique months

    //     foreach ($months as $month) {
    //         $monthlyData = $statuses->where('month', $month);

    //         $histogramData['Open'][] = $monthlyData->where('complaint_status', 0)->sum('total');
    //         $histogramData['Resolved'][] = $monthlyData->where('complaint_status', 2)->sum('total');
    //         $histogramData['Closed'][] = $monthlyData->where('complaint_status', 4)->sum('total');
    //     }

    //     $data = [
    //         'chart' => [
    //             'type' => 'bar'
    //         ],
    //         'title' => [
    //             'text' => ''
    //         ],
    //         'xaxis' => [
    //             'categories' => $months,
    //             'title' => [
    //                 'text' => 'Month'
    //             ]
    //         ],
    //         'yaxis' => [
    //             'title' => [
    //                 'text' => 'Number of New Complaints'
    //             ]
    //         ],
    //         'series' => [
    //             [
    //                 'name' => 'Open',
    //                 'data' => $histogramData['Open']
    //             ],
    //             [
    //                 'name' => 'Resolved',
    //                 'data' => $histogramData['Resolved']
    //             ],
    //             [
    //                 'name' => 'Closed',
    //                 'data' => $histogramData['Closed']
    //             ]
    //         ]
    //     ];

    //     return response()->json($data);
    // } // End Method

    // 




    public function OldComplaintMonthlyHistogram(Request $request)
{
    $userid = Auth::user();
    $year = $request->year ?? date('Y');

    $query = OldComplaint::select(
        DB::raw('MONTH(old_complaints.created_at) as month_no'),
        DB::raw('MONTHNAME(old_complaints.created_at) as month'),
        'old_complaints.complaint_status',
        DB::raw('COUNT(*) as total')
    )
    ->whereYear('old_complaints.created_at', $year)
    ->groupBy('month_no', 'month', 'old_complaints.complaint_status')
    ->orderBy('month_no', 'ASC');

    if ($userid->hasRole('RegionalManager')) {
        $query->join('users', 'old_complaints.user_id', '=', 'users.id')
              ->where('users.region_id', $userid->region_id);
    } elseif ($userid->hasRole('WarehouseManager')) {
        $query->where('old_complaints.user_id', $userid->id);
    }

    $statuses = $query->get();
    $months = $statuses->pluck('month')->unique()->values();

    $histogramData = ['Open'=>[], 'Resolved'=>[], 'Closed'=>[]];

    foreach ($months as $month) {
        $data = $statuses->where('month', $month);
        $histogramData['Open'][]     = $data->where('complaint_status', 0)->sum('total');
        $histogramData['Resolved'][] = $data->where('complaint_status', 2)->sum('total');
        $histogramData['Closed'][]   = $data->where('complaint_status', 4)->sum('total');
    }

    return response()->json([
        'chart' => ['type' => 'bar'],
        'title' => ['text' => ''],
        'xaxis' => ['categories' => $months],
        'yaxis' => ['title' => ['text' => 'Number of Old Complaints']],
        'series' => [
            ['name'=>'Open','data'=>$histogramData['Open']],
            ['name'=>'Resolved','data'=>$histogramData['Resolved']],
            ['name'=>'Closed','data'=>$histogramData['Closed']],
        ]
    ]);
}

//     public function OldComplaintMonthlyHistogram(Request $request)
// {
//         $id = Auth::user()->id;
//         $userid = User::findOrFail($id);
//     // ✅ get selected year (default = current year)
//     $year = $request->year ?? date('Y');

//     $query = OldComplaint::select(
//         DB::raw('MONTH(old_complaints.created_at) as month_no'),
//         DB::raw('MONTHNAME(old_complaints.created_at) as month'),
//         'old_complaint_status',
//         DB::raw('COUNT(*) as total')
//     )
//     ->whereYear('old_complaints.created_at', $year)
//     ->groupBy('month_no', 'month', 'old_complaint_status')
//     ->orderBy('month_no', 'ASC');

//     // 🔐 role based filter
//     if ($userid->hasRole('RegionalManager')) {
//         $query->join('users', 'old_complaints.user_id', '=', 'users.id')
//               ->where('users.region_id', $userid->region_id);
//     } elseif ($userid->hasRole('WarehouseManager')) {
//         $query->where('old_complaints.user_id', $userid->id);
//     }

//     $statuses = $query->get();

//     // Month list (Jan–Dec in order)
//     $months = $statuses->pluck('month')->unique()->values();

//     $histogramData = [
//         'Open' => [],
//         'Resolved' => [],
//         'Closed' => [],
//     ];

//     foreach ($months as $month) {
//         $data = $statuses->where('month', $month);

//         $histogramData['Open'][]     = $data->where('complaint_status', 0)->sum('total');
//         $histogramData['Resolved'][] = $data->where('complaint_status', 2)->sum('total');
//         $histogramData['Closed'][]   = $data->where('complaint_status', 4)->sum('total');
//     }

//     return response()->json([
//         'chart' => ['type' => 'bar'],
//         'title' => ['text' => ""],
//         'xaxis' => [
//             'categories' => $months,
//             'title' => ['text' => 'Month']
//         ],
//         'yaxis' => [
//             'title' => ['text' => 'Number of Old Complaints']
//         ],
//         'series' => [
//             ['name' => 'Open', 'data' => $histogramData['Open']],
//             ['name' => 'Resolved', 'data' => $histogramData['Resolved']],
//             ['name' => 'Closed', 'data' => $histogramData['Closed']],
//         ]
//     ]);
// }
    // public function OldComplaintMonthlyHistogram()
    // {
    //     $id = Auth::user()->id;
    //     $userid = User::findOrFail($id);

    //     // Base query to select complaint status, month, and count
    //     $query = OldComplaint::select(
    //         DB::raw('MONTHNAME(old_complaints.created_at) as month'),
    //         'complaint_status',
    //         DB::raw('count(*) as total')
    //     )->whereYear('old_complaints.created_at', date('Y')) // Filter for the current year
    //         ->groupBy('month', 'complaint_status')
    //         ->orderBy(DB::raw('MONTH(old_complaints.created_at)'), 'ASC'); // Order by month

    //     // Adjust the query based on the user's role
    //     if ($userid->hasRole('RegionalManager')) {
    //         $query->join('users', 'complaints.user_id', '=', 'users.id')
    //             ->where('users.region_id', $userid->region_id);
    //     } elseif ($userid->hasRole('WarehouseManager')) {
    //         $query->where('user_id', $userid->id);
    //     }

    //     // Execute the query and get the results
    //     $statuses = $query->get();

    //     // Structure the data for the histogram
    //     $histogramData = [
    //         'Open' => [],
    //         'Resolved' => [],
    //         'Closed' => [],
    //     ];

    //     $months = $statuses->groupBy('month')->keys()->all(); // Get all unique months

    //     foreach ($months as $month) {
    //         $monthlyData = $statuses->where('month', $month);

    //         $histogramData['Open'][] = $monthlyData->where('complaint_status', 0)->sum('total');
    //         $histogramData['Resolved'][] = $monthlyData->where('complaint_status', 2)->sum('total');
    //         $histogramData['Closed'][] = $monthlyData->where('complaint_status', 4)->sum('total');
    //     }

    //     $data = [
    //         'chart' => [
    //             'type' => 'bar'
    //         ],
    //         'title' => [
    //             'text' => ''
    //         ],
    //         'xaxis' => [
    //             'categories' => $months,
    //             'title' => [
    //                 'text' => 'Month'
    //             ]
    //         ],
    //         'yaxis' => [
    //             'title' => [
    //                 'text' => 'Number of Old Complaints'
    //             ]
    //         ],
    //         'series' => [
    //             [
    //                 'name' => 'Open',
    //                 'data' => $histogramData['Open']
    //             ],
    //             [
    //                 'name' => 'Resolved',
    //                 'data' => $histogramData['Resolved']
    //             ],
    //             [
    //                 'name' => 'Closed',
    //                 'data' => $histogramData['Closed']
    //             ]
    //         ]
    //     ];

    //     return response()->json($data);
    // } // End Method
// 


//  REGION WISE NEW COMPLAINT 
    

public function ComplaintRegionwiseData()
{
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);

    $query = Complaint::join('users', 'complaints.user_id', '=', 'users.id')
        ->join('regions', 'users.region_id', '=', 'regions.id')
        ->select(
            'regions.id as region_id',
            'regions.name as region_name',
            'complaints.complaint_status',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('regions.id', 'regions.name', 'complaints.complaint_status')
        ->orderBy('regions.name', 'ASC');

    // Role based filtering
    if ($userid->hasRole('RegionalManager')) {
        $query->where('regions.id', $userid->region_id);
    } elseif ($userid->hasRole('WarehouseManager')) {
        $query->where('users.id', $userid->id);
    }

    $statuses = $query->get();

    // Prepare chart data
    $regionNames = $statuses->pluck('region_name')->unique()->values();

    $regionwiseData = [
        'Open' => [],
        'Resolved' => [],
        'Closed' => [],
    ];

    foreach ($regionNames as $region) {
        $data = $statuses->where('region_name', $region);

        $regionwiseData['Open'][]     = $data->where('complaint_status', 0)->sum('total');
        $regionwiseData['Resolved'][] = $data->where('complaint_status', 2)->sum('total');
        $regionwiseData['Closed'][]   = $data->where('complaint_status', 4)->sum('total');
    }

    return response()->json([
        'chart' => ['type' => 'bar'],
        'title' => ['text' => 'New CCTV Region-wise Complaint Status'],
        'xaxis' => [
            'categories' => $regionNames,
            'title' => ['text' => 'Region']
        ],
        'yaxis' => [
            'title' => ['text' => 'Number of New Complaints']
        ],
        'series' => [
            ['name' => 'Open', 'data' => $regionwiseData['Open']],
            ['name' => 'Resolved', 'data' => $regionwiseData['Resolved']],
            ['name' => 'Closed', 'data' => $regionwiseData['Closed']],
        ]
    ]);
}


public function OldComplaintRegionwiseData()
{
    $id = Auth::user()->id;
    $userid = User::findOrFail($id);

    $query = OldComplaint::join('users', 'old_complaints.user_id', '=', 'users.id')
        ->join('regions', 'users.region_id', '=', 'regions.id')
        ->select(
            'regions.id as region_id',
            'regions.name as region_name',
            'old_complaints.complaint_status',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('regions.id', 'regions.name', 'old_complaints.complaint_status')
        ->orderBy('regions.name', 'ASC');

    // Role based filtering
    if ($userid->hasRole('RegionalManager')) {
        $query->where('regions.id', $userid->region_id);
    } elseif ($userid->hasRole('WarehouseManager')) {
        $query->where('users.id', $userid->id);
    }

    $statuses = $query->get();

    // Prepare chart data
    $regionNames = $statuses->pluck('region_name')->unique()->values();

    $regionwiseData = [
        'Open' => [],
        'Resolved' => [],
        'Closed' => [],
    ];

    foreach ($regionNames as $region) {
        $data = $statuses->where('region_name', $region);

        $regionwiseData['Open'][]     = $data->where('complaint_status', 0)->sum('total');
        $regionwiseData['Resolved'][] = $data->where('complaint_status', 2)->sum('total');
        $regionwiseData['Closed'][]   = $data->where('complaint_status', 4)->sum('total');
    }

    return response()->json([
        'chart' => ['type' => 'bar'],
        'title' => ['text' => 'Old CCTV Region-wise Complaint Status'],
        'xaxis' => [
            'categories' => $regionNames,
            'title' => ['text' => 'Region']
        ],
        'yaxis' => [
            'title' => ['text' => 'Number of Old Complaints']
        ],
        'series' => [
            ['name' => 'Open', 'data' => $regionwiseData['Open']],
            ['name' => 'Resolved', 'data' => $regionwiseData['Resolved']],
            ['name' => 'Closed', 'data' => $regionwiseData['Closed']],
        ]
    ]);
}///End method

      
    public function ComplaintWarehousewiseData()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);

        // Base query to select complaint status, state_id, user_id (warehouse), and complaint count
        $query = Complaint::select(
            'users.state_id as state_id',
            'users.id as user_id', // Warehouse (user with WarehouseManager role)
            DB::raw('count(*) as total_complaints')
        )
            ->join('users', 'complaints.user_id', '=', 'users.id') // Join with users table to get state and warehouse information
            ->groupBy('users.state_id', 'users.id') // Group by state_id and user_id (warehouse)
            ->orderBy('users.state_id', 'ASC')
            ->orderBy('users.id', 'ASC');

        // Adjust the query based on the user's role
        if ($userid->hasRole('RegionalManager')) {
            // Filter by the regional manager's region
            $query->where('users.region_id', $userid->region_id);
        }

        // Execute the query and get the results
        $complaintsData = $query->get();

        // Get the state names for each state_id
        $states = State::whereIn('id', $complaintsData->pluck('state_id'))->pluck('name', 'id');

        // Get the warehouse names for each user_id who has the 'WarehouseManager' role
        $warehouses = User::role('WarehouseManager')->whereIn('id', $complaintsData->pluck('user_id'))->pluck('name', 'id');

        // Prepare chart data for warehouse-wise total complaints
        $chartData = [];
        $chartData['chart'] = ['type' => 'bar'];
        $chartData['title'] = ['text' => 'Total Complaints per Warehouse for the Region'];
        $chartData['xaxis'] = [
            'categories' => $warehouses->values()->toArray(),
            'title' => ['text' => 'Warehouse']
        ];
        $chartData['yaxis'] = ['title' => ['text' => 'Number of Complaints']];

        // Build the series for the chart
        $chartData['series'] = [
            [
                'name' => 'Complaints',
                'data' => []
            ]
        ];

        // Accumulate the total number of complaints for each warehouse
        foreach ($warehouses as $warehouseId => $warehouseName) {
            $warehouseComplaints = $complaintsData->where('user_id', $warehouseId)->sum('total_complaints');
            $chartData['series'][0]['data'][] = $warehouseComplaints;
        }

        return response()->json($chartData);
    }   //End Method

public function OldComplaintWarehousewiseData()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);

        // Base query to select complaint status, state_id, user_id (warehouse), and complaint count
        $query = OldComplaint::select(
            'users.state_id as state_id',
            'users.id as user_id', // Warehouse (user with WarehouseManager role)
            DB::raw('count(*) as total_complaints')
        )
            ->join('users', 'old_complaints.user_id', '=', 'users.id') // Join with users table to get state and warehouse information
            ->groupBy('users.state_id', 'users.id') // Group by state_id and user_id (warehouse)
            ->orderBy('users.state_id', 'ASC')
            ->orderBy('users.id', 'ASC');

        // Adjust the query based on the user's role
        if ($userid->hasRole('RegionalManager')) {
            // Filter by the regional manager's region
            $query->where('users.region_id', $userid->region_id);
        }

        // Execute the query and get the results
        $complaintsData = $query->get();

        // Get the state names for each state_id
        $states = State::whereIn('id', $complaintsData->pluck('state_id'))->pluck('name', 'id');

        // Get the warehouse names for each user_id who has the 'WarehouseManager' role
        $warehouses = User::role('WarehouseManager')->whereIn('id', $complaintsData->pluck('user_id'))->pluck('name', 'id');

        // Prepare chart data for warehouse-wise total complaints
        $chartData = [];
        $chartData['chart'] = ['type' => 'bar'];
        $chartData['title'] = ['text' => 'Total Complaints per Warehouse for the Region'];
        $chartData['xaxis'] = [
            'categories' => $warehouses->values()->toArray(),
            'title' => ['text' => 'Warehouse']
        ];
        $chartData['yaxis'] = ['title' => ['text' => 'Number of Complaints']];

        // Build the series for the chart
        $chartData['series'] = [
            [
                'name' => 'Complaints',
                'data' => []
            ]
        ];

        // Accumulate the total number of complaints for each warehouse
        foreach ($warehouses as $warehouseId => $warehouseName) {
            $warehouseComplaints = $complaintsData->where('user_id', $warehouseId)->sum('total_complaints');
            $chartData['series'][0]['data'][] = $warehouseComplaints;
        }

        return response()->json($chartData);
    }   //End Method



    public function UptimeComplaint(Request $request)
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);

        $start_date = !empty($request->start)
            ? Carbon::createFromFormat('d M, Y', $request->start)->startOfDay()
            : null;

        $end_date = !empty($request->end)
            ? Carbon::createFromFormat('d M, Y', $request->end)->endOfDay()
            : null;

        // Total time in the given range
        $total_time = $start_date && $end_date
            ? $end_date->diffInMinutes($start_date)
            : null;

        // Query shutdown complaints
        $shutdownComplaintsQuery = Complaint::where('cat_id', 31)
            ->whereBetween('created_at', [$start_date, $end_date]);

        $shutdownComplaints = $shutdownComplaintsQuery->get();

        // Calculate unavailability time
        $unavailabilityTime = $shutdownComplaints->sum(function ($complaint) use ($end_date) {
            $created_at = Carbon::parse($complaint->created_at);
            $resolved_date = $complaint->resolved_date ? Carbon::parse($complaint->resolved_date) : null;

            if ($complaint->complaint_status == 0) {
                // Open complaints: use end_date as the resolution time
                return $end_date->diffInMinutes($created_at);
            } elseif ($complaint->complaint_status == 2 || $complaint->complaint_status == 4) {
                // Resolved or Closed complaints: use resolved_date
                return $resolved_date ? $resolved_date->diffInMinutes($created_at) : 0;
            }

            return 0;
        });

        // Calculate uptime percentage
        $uptimePercentage = $total_time && $unavailabilityTime !== null
            ? (($total_time - $unavailabilityTime) / $total_time) * 100
            : 0;

        // Prepare response data
        $data = [
            'chart' => [
                'type' => 'pie'
            ],
            'uptimePercentage' => $uptimePercentage,
            'series' => [
                [
                    'name' => 'Uptime/Downtime Analysis',
                    'data' => [
                        ['name' => 'Uptime', 'y' => round($uptimePercentage, 2)],
                        ['name' => 'Downtime', 'y' => round(100 - $uptimePercentage, 2)]
                    ]
                ]
            ]
        ];

        return response()->json($data);
    }    // End Method


    public function NewComplaintVisit()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);
        if ($userid->hasRole(['SuperAdmin', 'Railtel' , 'Vendor'])) {
            $allcomplaints = Complaint::whereNotNull('visit_date')->latest()->get();
        } else {
            $allcomplaints = Complaint::where('user_id', $userid->id)
                ->whereNotNull('visit_date')->orderBy('created_at', 'DESC')->get();
        }


        return view('admin.complaint.new_complaint_visit', compact('allcomplaints'));
    } //End Method

    // public function checkUnresolvedComplaints()
    // {

    //     if (!Auth::user()->hasRole(['SuperAdmin', 'Railtel'])) {
    //         return redirect()->back()->with([
    //             'message' => 'You do not have permission to perform this action',
    //             'alert-type' => 'error'
    //         ]);
    //     }

    //     // Find all open complaints older than 48 hours
    //     $unresolvedComplaints = Complaint::where('complaint_status', 0)
    //         ->where('created_at', '<', Carbon::now()->subHours(48))
    //         ->get();

    //     $count = $unresolvedComplaints->count();

    //     foreach ($unresolvedComplaints as $complaint) {

    //         $adminUsers = User::role('Railtel')->get();


    //         $complaintUser = $complaint->complaint_for ? User::find($complaint->complaint_for): User::find($complaint->user_id);


    //         Notification::send($adminUsers, new ComplaintNotification($complaint, 'unresolved'));

    //         if ($complaintUser) {
    //             Notification::send($complaintUser, new ComplaintNotification($complaint, 'unresolved'));
    //         }
    //     }

    //     $notification = [
    //         'message' => "Sent notifications for {$count} unresolved complaints",
    //         'alert-type' => 'success'
    //     ];

    //     return redirect()->back()->with($notification);
    // }


    public function checkUnresolvedComplaints()
    {
        if (!Auth::user()->hasRole(['SuperAdmin', 'Railtel', 'Vendor'])) {
            return redirect()->back()->with([
                'message' => 'You do not have permission to perform this action',
                'alert-type' => 'error'
            ]);
        }

        $unresolvedComplaints = Complaint::where('complaint_status', 0)
            ->where('created_at', '<', Carbon::now()->subHours(48))
            ->get();
        $count = $unresolvedComplaints->count();

        foreach ($unresolvedComplaints as $complaint) {
            $complaintUser = User::find($complaint->user_id);
            $adminUsers = User::role('Railtel')->get();
            if ($complaintUser) {
                Notification::send($complaintUser, new ComplaintNotification($complaint, 'unresolved'));
            }
            Notification::send($adminUsers, new ComplaintNotification($complaint, 'unresolved'));
        }
        $notification = [
            'message' => "Sent notifications for {$count} unresolved complaints",
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    } // End Method



}
