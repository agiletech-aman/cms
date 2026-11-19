<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\items;
use App\Models\OldComment;
use App\Models\OldComplaint;
use App\Models\OldComplaintImage;
use App\Models\Region;
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



class OldComplaintController extends Controller
{

    public function AllOldComplaint()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);
        if ($userid->hasRole(['SuperAdmin', 'Railtel', 'CorporateOffice', 'Vendor'])) {
            $allcomplaints = OldComplaint::latest()->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $allcomplaints = OldComplaint::where('user_id', $userid->id)
                ->orWhereHas('user', function ($query) use ($userid) {
                    $query->where('region_id', $userid->region_id);
                })->orderBy('created_at', 'DESC')->get();
        } else {
            $allcomplaints = OldComplaint::where('user_id', $userid->id)->orderBy('created_at', 'DESC')->get();
        }


        return view('admin.oldcomplaint.all_old_complaint', compact('allcomplaints'));
    } //End Method


    public function StoreOldComplaint(Request $request)
    {
        $user_admin = User::role('Railtel')->get();
        $loggedInUser = User::find(Auth::id());

        $complaintUser = $request->has('complaint_for') ? User::find($request->complaint_for) : $loggedInUser;

        $users = User::find(Auth::id());

        $lastid = OldComplaint::latest()->first();
        $number = $lastid ? $lastid->id + 1 : 1;
        $num = str_pad($number, 4, "0", STR_PAD_LEFT);

        $nameForComplaintId = $complaintUser ? strtoupper($complaintUser->name) : strtoupper($users->username);
        $complaint_id = 'CWC/IPCCTV/O/' . $nameForComplaintId . '/' . $num;

        // Check if user is a Regional Manager and get the correct user_id
        $user_id = $users->hasRole('RegionalManager') ? $request->complaint_for : $users->id;

        // Ensure $user_id is not null
        if (!$user_id) {
            return redirect()->back()->with([
                'message' => 'Error: No user assigned to complaint.',
                'alert-type' => 'error'
            ]);
        }


        $complaint = OldComplaint::create([
            'cat_id' => $request->oldcat_id,
            'complaint_id' => $complaint_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'complaint_status' => 0,
            'user_id' => $user_id,
            'raised_by' => Auth::id(),
        ]);


        DB::table('alert_logs')->insert([
            'complaint_id' => $complaint->id,
            'complaint_type' => 2,
            'user_id' => $complaint->user_id,
            'complaint_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        // Notification::send($user_admin, new ComplaintNotification($complaint));
       // Notification::send($user_admin, new ComplaintNotification($complaint));
    //    Notification::send($complaintUser, new ComplaintNotification($complaint));


        return redirect()->route('all.old.complaint')->with([
            'message' => 'Complaint Added Successfully',
            'alert-type' => 'success'
        ]);
    }



    public function EditOldComplaint($id)
    {
        $category = Category::where('type', 2)->get();

        $complaint = OldComplaint::findOrFail($id);

        return view('admin.oldcomplaint.edit_old_complaint', compact('complaint', 'category'));
    } // End Method


    public function UpdateOldComplaint(Request $request, $id)
    {

        $userID = Auth::user()->id;
        $userid = User::findOrFail($userID);
        if ($userid->hasRole('SuperAdmin')) {
            OldComplaint::findOrFail($id)->update([
                'cat_id' => $request->oldcat_id,
                'subject' => $request->subject,
                'description' => $request->description,
                'created_at' => $request->created_at,
            ]);
        } elseif ($userid->hasRole(['RegionalManager', 'WarehouseManager', 'CorporateOffice',])) {
            OldComplaint::findOrFail($id)->update([
                'cat_id' => $request->oldcat_id,
                'subject' => $request->subject,
                'description' => $request->description,
            ]);
        } else {
            OldComplaint::findOrFail($id)->update([
                'cat_id' => $request->oldcat_id,
                'subject' => $request->subject,
                'description' => $request->description,
            ]);
        }

        $notification = array(
            'message' => 'Complaint Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.old.complaint')->with($notification);
    } // End Method
    public function DeleteOldComplaint($id)
    {
        OldComplaint::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Complaint Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } //End Method


    // public function UpdateOldComplaintStatus(Request $request, $id)
    // {
    //     $user_admin = User::role('Railtel')->get();
    //     $loggedInUser = User::find(Auth::id());

    //     $complaint = OldComplaint::findOrFail($id); // Fetch the complaint to use in notifications
    //     $complaintUser = User::find($complaint->user_id);


    //     $messages = [
    //         'complaint_image.*.image' => 'Each file must be an image.',
    //         'complaint_image.*.mimes' => 'Only jpeg, png, jpg, gif, and svg formats are allowed.',
    //         'complaint_image.*.max' => 'Each image must not exceed 2MB.',
    //     ];
    //     // Validate the request with custom messages
    //     $request->validate([
    //         'complaint_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //         'complaint_status' => 'required|integer',
    //         'resolved_date' => 'nullable|date',
    //         'closed_date' => 'nullable|date',
    //         'visit_date' => 'nullable|date',
    //         'remark' => 'nullable|string|max:255',
    //     ], $messages);

    //     $uid = Auth::user()->id;
    //     $userid = User::findOrFail($uid);

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
    //         'complaint_type' => 2,
    //         'user_id' => $userid->id,
    //         'complaint_status' => $request->complaint_status,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     $comment = OldComment::create([
    //         'complaint_id' => $id,
    //         'comment' => 'This complaint is ' . $complaint_msg,
    //         'remark' => $request->remark,
    //         'user_id' => $userid->id,
    //         'created_at' => Carbon::now(),
    //     ]);

    //     if ($request->file('complaint_image')) {
    //         $image = $request->file('complaint_image');
    //         foreach ($image as $multi_image) {
    //             $manager = new ImageManager(new Driver());
    //             $name_gen = hexdec(uniqid()) . '.' . $multi_image->getClientOriginalExtension();
    //             $img = $manager->read($multi_image);
    //             $img->toJpeg(80)->save(base_path('public/upload/complaint_image/' . $name_gen));
    //             $save_url = 'upload/complaint_image/' . $name_gen;

    //             OldComplaintImage::insert([
    //                 'complaint_id' => $id,
    //                 'user_id' => $userid->id,
    //                 'complaint_image' => $save_url,
    //                 'comment_id' => $comment->id,  // Storing the comment ID
    //                 'created_at' => Carbon::now(),
    //             ]);
    //         }
    //     }

    //     $updateData = [
    //         'complaint_status' => $request->complaint_status,
    //         'status_change_by' => $userid->id,
    //     ];

    //     // Check for resolved_date or closed_date
    //     if ($request->complaint_status == 2) { // Resolved
    //         $resolvedDate = $request->resolved_date ? Carbon::parse($request->resolved_date) : null;
    //         $updateData['resolved_date'] = $resolvedDate;
    //     } elseif ($request->complaint_status == 4) { // Closed
    //         $updateData['closed_date'] = $request->closed_date;
    //     } elseif ($request->complaint_status == 1) { // In Progress
    //         $updateData['visit_date'] = $request->visit_date;
    //     }

    //     $complaint->update($updateData);

    //     // Notify SuperAdmin and the complaint user
    //     Notification::send($user_admin, new ComplaintNotification($complaint, 'update'));
    //     Notification::send($complaintUser, new ComplaintNotification($complaint, 'update'));

    //     return redirect()->back()->with([
    //         'message' => 'Complaint Status Changed Successfully',
    //         'alert-type' => 'success'
    //     ]);
    // } // End Method



    // public function UpdateOldComplaintStatus(Request $request, $id)
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

    //     $complaint = OldComplaint::findOrFail($id); // Fetch the complaint
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

    //     $comment = OldComment::create([
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
    //             OldComplaintImage::create([
    //                 'complaint_id' => $id,
    //                 'user_id' => $user->id,
    //                 'complaint_image' => $save_url,
    //                 // 'file_type' => $fileTypeCategory,
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


    //     // Check for resolved_date or closed_date
    //     if ($request->complaint_status == 2) { // Resolved
    //         $resolvedDate = $request->resolved_date ? Carbon::parse($request->resolved_date) : null;
    //         $updateData['resolved_date'] = $resolvedDate;
    //     } elseif ($request->complaint_status == 4) { // Closed
    //         $updateData['closed_date'] = $request->closed_date;
    //     } elseif ($request->complaint_status == 1) { // In Progress
    //         $updateData['visit_date'] = $request->visit_date;
    //     }

    //     $complaint->update($updateData);

    //     // Send notifications
    //   //  Notification::send($superAdmins, new ComplaintNotification($complaint, 'update'));
    // //    Notification::send($complaintUser, new ComplaintNotification($complaint, 'update'));

    //     return redirect()->back()->with([
    //         'message' => 'Complaint Status Changed Successfully',
    //         'alert-type' => 'success'
    //     ]);
    // } // End Method


//     public function UpdateOldComplaintStatus(Request $request, $id)
// {
//     $messages = [
//         'complaint_files.*.mimes' => 'Only jpeg, png, jpg, gif, svg, and pdf formats are allowed.',
//         'complaint_files.*.max'   => 'Each file must not exceed 1MB.',
//         'visit_date.required_if'  => 'Visit date is required when the status is In Progress.',
//         'visit_date.date'         => 'Visit date must be a valid date.',
//         'resolved_date.date'      => 'Resolved date must be a valid date.',
//         'closed_date.date'        => 'Closed date must be a valid date.',
//         'reopen_date.date'        => 'Reopen date must be a valid date.',
//     ];

//     $complaint = OldComplaint::findOrFail($id); // fetch existing record (we need created_at, stored dates)
//     $user = Auth::user();

//     // Basic validation (format + required_if for visit_date)
//     $validator = Validator::make($request->all(), [
//         'complaint_files.*' => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
//         'complaint_status'  => 'required|integer',
//         'visit_date'        => 'nullable|date|required_if:complaint_status,1',
//         'resolved_date'     => 'nullable|date',
//         'closed_date'       => 'nullable|date',
//         'reopen_date'       => 'nullable|date',
//         'remark'            => 'nullable|string',
//     ], $messages);

//     // Chronological business rules (enforced after base validation)
//     $validator->after(function ($validator) use ($complaint, $request) {
//         $createdAt = Carbon::parse($complaint->created_at);

//         // safe parser helper (returns Carbon or null)
//         $parse = function ($key) use ($request) {
//             try {
//                 if ($request->filled($key)) return Carbon::parse($request->input($key));
//             } catch (\Exception $e) {
//                 // let base validator handle invalid date format
//             }
//             return null;
//         };

//         // Request-supplied values
//         $reqVisit    = $parse('visit_date');
//         $reqResolved = $parse('resolved_date');
//         $reqReopen   = $parse('reopen_date');
//         $reqClosed   = $parse('closed_date');

//         // Stored (existing) values
//         $storedVisit    = !empty($complaint->visit_date) ? Carbon::parse($complaint->visit_date) : null;
//         $storedResolved = !empty($complaint->resolved_date) ? Carbon::parse($complaint->resolved_date) : null;
//         $storedReopen   = !empty($complaint->reopen_date) ? Carbon::parse($complaint->reopen_date) : null;
//         $storedClosed   = !empty($complaint->closed_date) ? Carbon::parse($complaint->closed_date) : null;

//         // Effective (prefer request over stored)
//         $visit    = $reqVisit ?? $storedVisit;
//         $resolved = $reqResolved ?? $storedResolved;
//         $reopen   = $reqReopen ?? $storedReopen;
//         $closed   = $reqClosed ?? $storedClosed;

//         $status = (int) $request->input('complaint_status');

//         // 1) In Progress (1): visit_date must exist and be >= created_at
//         if ($status === 1) {
//             if (!$visit) {
//                 $validator->errors()->add('visit_date', 'Visit date is required for In Progress status.');
//             } else {
//                 if ($visit->lt($createdAt)) {
//                     $validator->errors()->add(
//                         'visit_date',
//                         'In Progress (visit) date/time cannot be before : ' . $createdAt->format('d-m-Y H:i:s')
//                     );
//                 }
//             }
//         }

//         // 2) Resolved (2): resolved_date must be >= visit_date if visit exists, otherwise >= created_at
//         if ($status === 2) {
//             if ($resolved) {
//                 $baseline = $visit ?? $createdAt;
//                 if ($resolved->lt($baseline)) {
//                     $validator->errors()->add(
//                         'resolved_date',
//                         'Resolved date/time cannot be before : ' . $baseline->format('d-m-Y H:i:s')
//                     );
//                 }
//             } else {
//                 // optional: require resolved_date when marking Resolved. Currently not forced.
//             }
//         }

//         // 3) ReOpen (0): if reopen_date provided, must be >= resolved_date (fallbacks applied)
//         if ($status === 0) {
//             if ($reqReopen) {
//                 // baseline priority: storedResolved/requestResolved -> storedVisit/requestVisit -> createdAt
//                 $baseline = $resolved ?? $storedResolved ?? $visit ?? $storedVisit ?? $createdAt;
//                 if ($reqReopen->lt($baseline)) {
//                     $validator->errors()->add(
//                         'reopen_date',
//                         'Reopen date/time cannot be before : ' . $baseline->format('d-m-Y H:i:s')
//                     );
//                 }
//             }
//             // if no reopen_date provided assume system timestamp will be used
//         }

//         // 4) Closed (4): closed_date must be >= latest of (reopen, resolved, visit, createdAt)
//         if ($status === 4) {
//             if (!$closed) {
//                 $validator->errors()->add('closed_date', 'Closed date is required for Closed status.');
//             } else {
//                 // build candidate baselines and choose the latest timestamp
//                 $candidates = [];
//                 if ($reqReopen)    $candidates[] = $reqReopen;
//                 if ($storedReopen) $candidates[] = $storedReopen;
//                 if ($reqResolved)  $candidates[] = $reqResolved;
//                 if ($storedResolved) $candidates[] = $storedResolved;
//                 if ($reqVisit)     $candidates[] = $reqVisit;
//                 if ($storedVisit)  $candidates[] = $storedVisit;
//                 $candidates[] = $createdAt;

//                 // determine latest baseline
//                 usort($candidates, function ($a, $b) {
//                     return $a->timestamp <=> $b->timestamp;
//                 });
//                 $baseline = end($candidates);

//                 if ($closed->lt($baseline)) {
//                     $validator->errors()->add(
//                         'closed_date',
//                         'Closed date/time cannot be before : ' . $baseline->format('d-m-Y H:i:s')
//                     );
//                 }
//             }
//         }
//     });

//     // Run validation (redirects back with $errors if any)
//     $validator->validate();

//     // ---------- existing flow: insert alert, comment, files, update complaint ----------
//     $statusMessages = [
//         0 => 'Open',
//         1 => 'Inprogress',
//         2 => 'Resolved',
//         3 => 'Duplicate',
//         4 => 'Closed'
//     ];
//     $complaint_msg = $statusMessages[$request->complaint_status] ?? 'Unknown Status';

//     DB::table('alert_logs')->insert([
//         'complaint_id'     => $id,
//         'complaint_type'   => 1,
//         'user_id'          => $user->id,
//         'complaint_status' => $request->complaint_status,
//         'created_at'       => now(),
//         'updated_at'       => now(),
//     ]);

//     $comment = OldComment::create([
//         'complaint_id' => $id,
//         'comment'      => 'This complaint is ' . $complaint_msg,
//         'remark'       => $request->remark,
//         'user_id'      => $user->id,
//         'created_at'   => Carbon::now(),
//     ]);

//     // handle uploaded files (same as your original)
//     // SAFE FILE HANDLING (NO GD, NO INTERVENTION)
// if ($request->hasFile('complaint_files')) {
//     foreach ($request->file('complaint_files') as $file) {

//         $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

//         // Just move file (works for images + pdf)
//         $file->move(public_path('upload/complaint_files'), $name_gen);

//         OldComplaintImage::create([
//             'complaint_id'    => $id,
//             'user_id'         => $user->id,
//             'complaint_image' => 'upload/complaint_files/' . $name_gen,
//             'comment_id'      => $comment->id,
//             'created_at'      => now(),
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
//     if ($request->filled('reopen_date')) {
//         $updateData['reopen_date'] = Carbon::parse($request->reopen_date);
//     }
//     if ($request->filled('closed_date')) {
//         $updateData['closed_date'] = Carbon::parse($request->closed_date);
//     }

//     $complaint->update($updateData);

//     return redirect()->back()->with([
//         'message'    => 'Complaint Status Changed Successfully',
//         'alert-type' => 'success'
//     ]);
// }


public function UpdateOldComplaintStatus(Request $request, $id)
{
    $messages = [
        'complaint_files.*.mimes' => 'Only jpeg, png, jpg, gif, svg, and pdf formats are allowed.',
        'complaint_files.*.max'   => 'Each file must not exceed 1MB.',
        'visit_date.required_if'  => 'Visit date is required when the status is In Progress.',
        'visit_date.date'         => 'Visit date must be a valid date.',
        'resolved_date.date'      => 'Resolved date must be a valid date.',
        'closed_date.date'        => 'Closed date must be a valid date.',
        'reopen_date.date'        => 'Reopen date must be a valid date.',
    ];
 
    $complaint = OldComplaint::findOrFail($id); // fetch existing record (we need created_at, stored dates)
    $user = Auth::user();
 
    // Basic validation (format + required_if for visit_date)
    $validator = Validator::make($request->all(), [
        'complaint_files.*' => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
        'complaint_status'  => 'required|integer',
        'visit_date'        => 'nullable|date|required_if:complaint_status,1',
        'resolved_date'     => 'nullable|date',
        'closed_date'       => 'nullable|date',
        'reopen_date'       => 'nullable|date',
        'remark'            => 'nullable|string',
    ], $messages);
 
    // Chronological business rules (enforced after base validation)
    $validator->after(function ($validator) use ($complaint, $request) {
        $createdAt = Carbon::parse($complaint->created_at);
 
        // safe parser helper (returns Carbon or null)
        $parse = function ($key) use ($request) {
            try {
                if ($request->filled($key)) return Carbon::parse($request->input($key));
            } catch (\Exception $e) {
                // let base validator handle invalid date format
            }
            return null;
        };
 
        // Request-supplied values
        $reqVisit    = $parse('visit_date');
        $reqResolved = $parse('resolved_date');
        $reqReopen   = $parse('reopen_date');
        $reqClosed   = $parse('closed_date');
 
        // Stored (existing) values
        $storedVisit    = !empty($complaint->visit_date) ? Carbon::parse($complaint->visit_date) : null;
        $storedResolved = !empty($complaint->resolved_date) ? Carbon::parse($complaint->resolved_date) : null;
        $storedReopen   = !empty($complaint->reopen_date) ? Carbon::parse($complaint->reopen_date) : null;
        $storedClosed   = !empty($complaint->closed_date) ? Carbon::parse($complaint->closed_date) : null;
 
        // Effective (prefer request over stored)
        $visit    = $reqVisit ?? $storedVisit;
        $resolved = $reqResolved ?? $storedResolved;
        $reopen   = $reqReopen ?? $storedReopen;
        $closed   = $reqClosed ?? $storedClosed;
 
        $status = (int) $request->input('complaint_status');
 
        // 1) In Progress (1): visit_date must exist and be >= created_at
        if ($status === 1) {
            if (!$visit) {
                $validator->errors()->add('visit_date', 'Visit date is required for In Progress status.');
            } else {
                if ($visit->lt($createdAt)) {
                    $validator->errors()->add(
                        'visit_date',
                        'In Progress (visit) date/time cannot be before : ' . $createdAt->format('d-m-Y H:i:s')
                    );
                }
            }
        }
 
        // 2) Resolved (2): resolved_date must be >= visit_date if visit exists, otherwise >= created_at
        if ($status === 2) {
            if ($resolved) {
                $baseline = $visit ?? $createdAt;
                if ($resolved->lt($baseline)) {
                    $validator->errors()->add(
                        'resolved_date',
                        'Resolved date/time cannot be before : ' . $baseline->format('d-m-Y H:i:s')
                    );
                }
            } else {
                // optional: require resolved_date when marking Resolved. Currently not forced.
            }
        }
 
        // 3) ReOpen (0): if reopen_date provided, must be >= resolved_date (fallbacks applied)
        if ($status === 0) {
            if ($reqReopen) {
                // baseline priority: storedResolved/requestResolved -> storedVisit/requestVisit -> createdAt
                $baseline = $resolved ?? $storedResolved ?? $visit ?? $storedVisit ?? $createdAt;
                if ($reqReopen->lt($baseline)) {
                    $validator->errors()->add(
                        'reopen_date',
                        'Reopen date/time cannot be before : ' . $baseline->format('d-m-Y H:i:s')
                    );
                }
            }
            // if no reopen_date provided assume system timestamp will be used
        }
 
        // 4) Closed (4): closed_date must be >= latest of (reopen, resolved, visit, createdAt)
        if ($status === 4) {
            if (!$closed) {
                $validator->errors()->add('closed_date', 'Closed date is required for Closed status.');
            } else {
                // build candidate baselines and choose the latest timestamp
                $candidates = [];
                if ($reqReopen)    $candidates[] = $reqReopen;
                if ($storedReopen) $candidates[] = $storedReopen;
                if ($reqResolved)  $candidates[] = $reqResolved;
                if ($storedResolved) $candidates[] = $storedResolved;
                if ($reqVisit)     $candidates[] = $reqVisit;
                if ($storedVisit)  $candidates[] = $storedVisit;
                $candidates[] = $createdAt;
 
                // determine latest baseline
                usort($candidates, function ($a, $b) {
                    return $a->timestamp <=> $b->timestamp;
                });
                $baseline = end($candidates);
 
                if ($closed->lt($baseline)) {
                    $validator->errors()->add(
                        'closed_date',
                        'Closed date/time cannot be before : ' . $baseline->format('d-m-Y H:i:s')
                    );
                }
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
 
    // Run validation (redirects back with $errors if any)
    $validator->validate();
 
    // ---------- existing flow: insert alert, comment, files, update complaint ----------
    $statusMessages = [
        0 => 'Open',
        1 => 'Inprogress',
        2 => 'Resolved',
        3 => 'Duplicate',
        4 => 'Closed'
    ];
    $complaint_msg = $statusMessages[$request->complaint_status] ?? 'Unknown Status';
 
    DB::table('alert_logs')->insert([
        'complaint_id'     => $id,
        'complaint_type'   => 1,
        'user_id'          => $user->id,
        'complaint_status' => $request->complaint_status,
        'created_at'       => now(),
        'updated_at'       => now(),
    ]);
 
    $comment = OldComment::create([
        'complaint_id' => $id,
        'comment'      => 'This complaint is ' . $complaint_msg,
        'remark'       => $request->remark,
        'user_id'      => $user->id,
        'created_at'   => Carbon::now(),
    ]);
 
    // handle uploaded files (same as your original)
    // SAFE FILE HANDLING (NO GD, NO INTERVENTION)
if ($request->hasFile('complaint_files')) {
    foreach ($request->file('complaint_files') as $file) {
 
        $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
 
        // Just move file (works for images + pdf)
        $file->move(public_path('upload/complaint_files'), $name_gen);
 
        OldComplaintImage::create([
            'complaint_id'    => $id,
            'user_id'         => $user->id,
            'complaint_image' => 'upload/complaint_files/' . $name_gen,
            'comment_id'      => $comment->id,
            'created_at'      => now(),
        ]);
    }
}
 
 
    // update fields if provided
    $updateData = [
        'complaint_status' => $request->complaint_status,
        'status_change_by' => $user->id,
    ];
 
    if ($request->filled('visit_date')) {
        $updateData['visit_date'] = Carbon::parse($request->visit_date);
    }
    if ($request->filled('resolved_date')) {
        $updateData['resolved_date'] = Carbon::parse($request->resolved_date);
    }
    if ($request->filled('reopen_date')) {
        $updateData['reopen_date'] = Carbon::parse($request->reopen_date);
    }
    if ($request->filled('closed_date')) {
        $updateData['closed_date'] = Carbon::parse($request->closed_date);
    }
 
    $complaint->update($updateData);
 
    return redirect()->back()->with([
        'message'    => 'Complaint Status Changed Successfully',
        'alert-type' => 'success'
    ]);
}
 



    public function DetailOldComplaint($id)
    {
        $complaint = OldComplaint::findOrFail($id);
        return view('admin.oldcomplaint.detail_old_complaint', compact('complaint'));
    } // End Method

    public function OldComplaintVisit()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);
        if ($userid->hasRole(['SuperAdmin', 'Railtel' , 'Vendor'])) {
            $allcomplaints = OldComplaint::whereNotNull('visit_date')->latest()->get();
        } else {
            $allcomplaints = OldComplaint::where('user_id', $userid->id)
                ->whereNotNull('visit_date')->orderBy('created_at', 'DESC')->get();
        }


        return view('admin.complaint.new_complaint_visit', compact('allcomplaints'));
    } //End Method

    public function OldComplaint($id)
    {
        $uid = Auth::user()->id;
        $userid = User::findOrFail($uid);

        if ($userid->hasRole('SuperAdmin')) {
            $oldComplaints = OldComplaint::where('complaint_status', $id)->orderBy('created_at', 'DESC')->get();
            $warehouses = User::role('WarehouseManager')->get();
            $regionalManagers = User::role('RegionalManager')->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $oldComplaints = OldComplaint::join('users', 'users.id', '=', 'old_complaints.user_id')
                ->where('users.region_id', $userid->region_id)
                ->where('old_complaints.complaint_status', $id)
                ->orderBy('old_complaints.created_at', 'DESC')
                ->get(['old_complaints.id', 'old_complaints.user_id', 'old_complaints.cat_id', 'old_complaints.complaint_id', 'old_complaints.subject', 'old_complaints.description', 'old_complaints.complaint_status', 'old_complaints.created_at', 'old_complaints.closed_date', 'old_complaints.resolved_date', 'old_complaints.remark']);
            $warehouses = User::role('WarehouseManager')->where('users.region_id', $userid->region_id)->get();
            $regionalManagers = User::role('RegionalManager')->where('users.region_id', $userid->region_id)->get();
        } elseif ($userid->hasRole(['WarehouseManager',])) {
            $oldComplaints = OldComplaint::where('user_id', $userid->id)
                ->where('complaint_status', $id)
                ->orderBy('created_at', 'DESC')
                ->get();
            $warehouses = User::role('WarehouseManager')->where('users.id', $userid->id)->get();
            $regionalManagers = User::role('RegionalManager')->where('users.region_id', $userid->region_id)->get();
        } else {
            $oldComplaints = OldComplaint::where('complaint_status', $id)->get();
            $warehouses = User::role('WarehouseManager')->get();
            $regionalManagers = User::role('RegionalManager')->get();
        }

        $regions = Region::all();
        return view('admin.oldcomplaint.old_complaints', compact('oldComplaints', 'warehouses', 'regionalManagers', 'regions'));
    } // end method


    public function OldComplaintPiechart()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);
        if ($userid->hasRole('SuperAdmin')) {
            $statuses = OldComplaint::select('complaint_status', DB::raw('count(*) as total'))
                ->groupBy('complaint_status')
                ->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $statuses = OldComplaint::select('old_complaints.complaint_status', DB::raw('count(*) as total'))->join('users', 'old_complaints.user_id', '=', 'users.id')->where('users.region_id', $userid->region_id)
                ->groupBy('old_complaints.complaint_status')
                ->get();
        } elseif ($userid->hasRole('WarehouseManager')) {
            $statuses = OldComplaint::select('old_complaints.complaint_status', DB::raw('count(*) as total'))->join('users', 'old_complaints.user_id', '=', 'users.id')->where('users.id', $userid->id)
                ->groupBy('old_complaints.complaint_status')
                ->get();
        } else {
            $statuses = OldComplaint::select('complaint_status', DB::raw('count(*) as total'))
                ->groupBy('complaint_status')
                ->get();
        }
        $data = [
            'chart' => [
                'type' => 'pie'
            ],
            'title' => [
                'text' => 'Old Complaint Status'
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



    ///ch
    // OLD INFRA COMPLAINT PAGE

// ch
public function addOldComplaint()
{
    // type = 2 → Old Infrastructure (as per your DB)
    $oldcategory = Category::where('type', 2)->get();

    return view('admin.oldcomplaint.add_old_complaint', compact('oldcategory'));
}
// // ch
}
