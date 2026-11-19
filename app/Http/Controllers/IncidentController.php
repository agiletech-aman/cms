<?php

namespace App\Http\Controllers;


use App\Models\IncidentComment;
use App\Models\Incident;
use App\Models\IncidentImage;
use App\Models\Region;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ComplaintNotification;
use Illuminate\Support\Facades\Validator;

class IncidentController extends Controller
{
    public function AllIncident()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);
        if ($userid->hasRole(['SuperAdmin', 'Railtel', 'Vendor'])) {
            $allincidents = Incident::latest()->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $allincidents = Incident::join('users', 'users.id', '=', 'incidents.user_id')
                ->where('users.region_id', $userid->region_id)
                ->select('incidents.*')
                ->orderBy('incidents.created_at', 'DESC')
                ->get();
        } else {
            $allincidents = Incident::where('user_id', $userid->id)->orderBy('created_at', 'DESC')->get();
        }
        return view('admin.incident.all_incident', compact('allincidents'));
    }



    public function StoreIncident(Request $request)
    {
        $user_admin = User::role('Railtel')->get();

        $loggedInUser = User::find(Auth::id());

        $complaintUser = $request->has('complaint_for') ? User::find($request->complaint_for) : $loggedInUser;


        $users = User::find(Auth::id());

        $lastid = Incident::latest()->first();
        $number = $lastid ? $lastid->id + 1 : 1;
        $num = str_pad($number, 4, "0", STR_PAD_LEFT);

        $nameForIncidentId = $complaintUser ? strtoupper($complaintUser->name) : strtoupper($users->username);
        $incident_id = 'CWC/IPCCTV/I/' . $nameForIncidentId . '/' . $num;

        $user_id = $users->hasRole('RegionalManager') ? $request->complaint_for : $users->id;


        if (!$user_id) {
            return redirect()->back()->with([
                'message' => 'Error: No user assigned to incident.',
                'alert-type' => 'error'
            ]);
        }

        // Store the incident
        $incident = Incident::create([
            'incident_id' => $incident_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'incident_status' => 0,
            'user_id' => $user_id,
            'raised_by' => Auth::id(),
            
        ]);


        DB::table('alert_logs')->insert([
            'complaint_id' => $incident->id,
            'complaint_type' => 3,
            'user_id' => $incident->user_id,
            'complaint_status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Notification::send($user_admin, new IncidentNotification($incident));


        return redirect()->route('all.incident')->with([
            'message' => 'Incident Added Successfully',
            'alert-type' => 'success'
        ]);
    }


    public function EditIncident($id)
    {
        $incident = Incident::findOrFail($id);
        return view('admin.incident.edit_incident', compact('incident'));
    } // End Method

    public function UpdateIncident(Request $request, $id)
    {

        $userID = Auth::user()->id;
        $userid = User::findOrFail($userID);
        if ($userid->hasRole('SuperAdmin')) {
            Incident::findOrFail($id)->update([
                'subject' => $request->subject,
                'description' => $request->description,
                'created_at' => $request->created_at,
            ]);
        } elseif ($userid->hasRole(['RegionalManager', 'WarehouseManager', 'CorporateOffice',])) {
            Incident::findOrFail($id)->update([
                'subject' => $request->subject,
                'description' => $request->description,
            ]);
        } else {
            Incident::findOrFail($id)->update([
                'subject' => $request->subject,
                'description' => $request->description,
            ]);
        }



        $notification = array(
            'message' => 'Incident Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.incident')->with($notification);
    } // End Method

    public function DeleteIncident($id)
    {
        Incident::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Incident Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    } //End Method

    public function DetailIncident($id)
    {
        $incident = Incident::findOrFail($id);
        return view('admin.incident.detail_incident', compact('incident'));
    } // End Method


    // public function UpdateIncidentStatus(Request $request, $id)
    // {
    //     $messages = [
    //         'incident_image.*.image' => 'Each file must be an image.',
    //         'incident_image.*.mimes' => 'Only jpeg, png, jpg, gif, and svg formats are allowed.',
    //         'incident_image.*.max' => 'Each image must not exceed 2MB.',
    //         'visit_date.required' => 'Visit date is required when the status is In Progress.',
    //         'visit_date.date' => 'Visit date must be a valid date.',
    //     ];
    //     // Validate the request with custom messages
    //     $request->validate([
    //         'incident_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //         'incident_status' => 'required|integer',
    //         'resolved_date' => 'nullable|date',
    //         'visit_date' => 'nullable|date',
    //         'closed_date' => 'nullable|date',
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
    //     $incident_msg = $statusMessages[$request->incident_status] ?? 'Unknown Status';

    //     // DB::table('alert_logs')->insert([
    //     //     'incident_id' => $id,
    //     //     'complaint_type' => 3,
    //     //     'user_id' => $userid->id,
    //     //     'incident_status' => $request->incident_status,
    //     //     'created_at' => now(),
    //     //     'updated_at' => now(),
    //     // ])

    //     $comment = IncidentComment::create([
    //         'incident_id' => $id,
    //         'comment' => 'This incident is ' . $incident_msg,
    //         'remark' => $request->remark,
    //         'user_id' => $userid->id,
    //         'created_at' => Carbon::now(),
    //     ]);

    //     if ($request->file('incident_image')) {
    //         $image = $request->file('incident_image');
    //         foreach ($image as $multi_image) {
    //             $manager = new ImageManager(new Driver());
    //             $name_gen = hexdec(uniqid()) . '.' . $multi_image->getClientOriginalExtension();
    //             $img = $manager->read($multi_image);
    //             $img->toJpeg(80)->save(base_path('public/upload/incident_image/' . $name_gen));
    //             $save_url = 'upload/incident_image/' . $name_gen;

    //             IncidentImage::insert([
    //                 'incident_id' => $id,
    //                 'user_id' => $userid->id,
    //                 'incident_image' => $save_url,
    //                 'comment_id' => $comment->id,  // Storing the comment ID
    //                 'created_at' => Carbon::now(),
    //             ]);
    //         }
    //     }

    //     $updateData = [
    //         'incident_status' => $request->incident_status,
    //         'status_change_by' => $userid->id,
    //     ];

    //     if ($request->incident_status == 2) { // Resolved
    //         $resolvedDate = $request->resolved_date ? Carbon::parse($request->resolved_date) : null;
    //         $updateData['resolved_date'] = $resolvedDate;
    //     } elseif ($request->incident_status == 4) { // Closed
    //         $updateData['closed_date'] = $request->closed_date;
    //     } elseif ($request->incident_status == 1) { // In Progress
    //         $updateData['visit_date'] = $request->visit_date;
    //     }

    //     Incident::findOrFail($id)->update($updateData);


    //     $notification = array(
    //         'message' => 'Incident Status Change Successfully',
    //         'alert-type' => 'success'
    //     );

    //     return redirect()->back()->with($notification);
    // } // End Method



    // public function UpdateIncidentStatus(Request $request, $id)
    // {
    //     $messages = [
    //         'incident_image.*.image' => 'Only jpeg, png, jpg, gif, svg, and pdf formats are allowed.',
    //         'incident_image.*.mimes' => 'Only jpeg, png, jpg, gif, and svg formats are allowed.',
    //         'incident_image.*.max' => 'Each image must not exceed 2MB.',
    //         'visit_date.required' => 'Visit date is required when the status is In Progress.',
    //         'visit_date.date' => 'Visit date must be a valid date.',
    //     ];
    //     // Validate the request with custom messages
    //     $request->validate([
    //         'incident_files.*' => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
    //         'incident_status' => 'required|integer',
    //         'resolved_date' => 'nullable|date',
    //         'visit_date' => 'nullable|date',
    //         'closed_date' => 'nullable|date',
    //         'remark' => 'nullable|string',
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
    //     $incident_msg = $statusMessages[$request->incident_status] ?? 'Unknown Status';

    //     // DB::table('alert_logs')->insert([
    //     //     'incident_id' => $id,
    //     //     'complaint_type' => 3,
    //     //     'user_id' => $userid->id,
    //     //     'incident_status' => $request->incident_status,
    //     //     'created_at' => now(),
    //     //     'updated_at' => now(),
    //     // ])

    //     $comment = IncidentComment::create([
    //         'incident_id' => $id,
    //         'comment' => 'This incident is ' . $incident_msg,
    //         'remark' => $request->remark,
    //         'user_id' => $userid->id,
    //         'created_at' => Carbon::now(),
    //     ]);

    //     // Handle file uploads
    //     if ($request->hasFile('incident_files')) {
    //         foreach ($request->file('incident_files') as $file) {
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
    //             IncidentImage::insert([
    //                 'incident_id' => $id,
    //                 'user_id' => $userid->id,
    //                 'incident_image' => $save_url,
                    
    //                 'comment_id' => $comment->id,  // Storing the comment ID
    //                 'created_at' => Carbon::now(),
    //             ]);
    //         }
    //     }

    //     $updateData = [
    //         'incident_status' => $request->incident_status,
    //         'status_change_by' => $userid->id,
    //     ];

    //     if ($request->incident_status == 2) { // Resolved
    //         $resolvedDate = $request->resolved_date ? Carbon::parse($request->resolved_date) : null;
    //         $updateData['resolved_date'] = $resolvedDate;
    //     } elseif ($request->incident_status == 4) { // Closed
    //         $updateData['closed_date'] = $request->closed_date;
    //     } elseif ($request->incident_status == 1) { // In Progress
    //         $updateData['visit_date'] = $request->visit_date;
    //     }

    //     Incident::findOrFail($id)->update($updateData);


    //     $notification = array(
    //         'message' => 'Incident Status Change Successfully',
    //         'alert-type' => 'success'
    //     );

    //     return redirect()->back()->with($notification);
    // } // End Method

// public function UpdateIncidentStatus(Request $request, $id)
// {
//     $messages = [
//         'incident_files.*.mimes' => 'Only jpeg, png, jpg, gif, svg, and pdf formats are allowed.',
//         'incident_files.*.max'   => 'Each file must not exceed 2MB.',
//         'visit_date.required'    => 'Visit date is required when the status is In Progress.',
//         'visit_date.date'        => 'Visit date must be a valid date/time.',
//         'resolved_date.date'     => 'Resolved date must be a valid date/time.',
//         'closed_date.date'       => 'Closed date must be a valid date/time.',
//         'reopen_date.date'       => 'Reopen date must be a valid date/time.',
//     ];

//     // fetch incident early (we need created_at and stored dates)
//     $incident = Incident::findOrFail($id);

//     // Basic validator (formats + required_if if you want)
//     $validator = Validator::make($request->all(), [
//         'incident_files.*'  => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
//         'incident_status'   => 'required|integer',
//         'visit_date'        => 'nullable|date',
//         'resolved_date'     => 'nullable|date',
//         'closed_date'       => 'nullable|date',
//         'reopen_date'       => 'nullable|date',
//         'remark'            => 'nullable|string',
//     ], $messages);

//     // Chronological business rules
//     $validator->after(function ($validator) use ($incident, $request) {
//         $createdAt = Carbon::parse($incident->created_at);

//         // safe parser helper (return Carbon or null)
//         $safeParse = function ($key) use ($request) {
//             try {
//                 if ($request->filled($key)) return Carbon::parse($request->input($key));
//             } catch (\Exception $e) {
//                 // let base validator handle invalid formats
//             }
//             return null;
//         };

//         // request-supplied
//         $reqVisit    = $safeParse('visit_date');
//         $reqResolved = $safeParse('resolved_date');
//         $reqReopen   = $safeParse('reopen_date');
//         $reqClosed   = $safeParse('closed_date');

//         // stored (existing) values on DB
//         $storedVisit    = !empty($incident->visit_date) ? Carbon::parse($incident->visit_date) : null;
//         $storedResolved = !empty($incident->resolved_date) ? Carbon::parse($incident->resolved_date) : null;
//         $storedReopen   = !empty($incident->reopen_date) ? Carbon::parse($incident->reopen_date) : null;
//         $storedClosed   = !empty($incident->closed_date) ? Carbon::parse($incident->closed_date) : null;

//         // effective (prefer request over stored)
//         $visit    = $reqVisit ?? $storedVisit;
//         $resolved = $reqResolved ?? $storedResolved;
//         $reopen   = $reqReopen ?? $storedReopen;
//         $closed   = $reqClosed ?? $storedClosed;

//         $status = (int) $request->input('incident_status');

//         // 1) In Progress (1): visit_date must be provided and >= created_at
//         if ($status === 1) {
//             if (!$reqVisit && !$storedVisit) {
//                 $validator->errors()->add('visit_date', 'Visit date is required for In Progress status.');
//             } else {
//                 $checkVisit = $reqVisit ?? $storedVisit;
//                 if ($checkVisit && $checkVisit->lt($createdAt)) {
//                     $validator->errors()->add(
//                         'visit_date',
//                         'Visit date/time cannot be before : ' . $createdAt->format('d-m-Y H:i:s')
//                     );
//                 }
//             }
//         }

//         // 2) Resolved (2): resolved_date (if supplied) must be >= visit (req->stored) otherwise >= created_at
//         if ($status === 2 && $resolved) {
//             $baseline = $visit ?? $createdAt;
//             if ($resolved->lt($baseline)) {
//                 $validator->errors()->add(
//                     'resolved_date',
//                     'Resolved date/time cannot be before : ' . $baseline->format('d-m-Y H:i:s')
//                 );
//             }
//         }

//         // 3) ReOpen (0): if reopen_date supplied, must be >= resolved (req/stored) or visit (req/stored) or created_at
//         if ($status === 0 && $reqReopen) {
//             // baseline priority: reqResolved > storedResolved > reqVisit > storedVisit > createdAt
//             $baseline = $reqResolved ?? $storedResolved ?? $reqVisit ?? $storedVisit ?? $createdAt;
//             if ($reqReopen->lt($baseline)) {
//                 $validator->errors()->add(
//                     'reopen_date',
//                     'Reopen date/time cannot be before : ' . $baseline->format('d-m-Y H:i:s')
//                 );
//             }
//         }

//         // 4) Closed (4): closed_date required and must be >= latest(reopen,resolved,visit,createdAt)
//         if ($status === 4) {
//             if (!$reqClosed && !$storedClosed) {
//                 $validator->errors()->add('closed_date', 'Closed date is required for Closed status.');
//             } else {
//                 $checkClosed = $reqClosed ?? $storedClosed;

//                 // Collect candidates in order: reqReopen, storedReopen, reqResolved, storedResolved, reqVisit, storedVisit, createdAt
//                 $candidates = [];
//                 if ($reqReopen)    $candidates[] = $reqReopen;
//                 if ($storedReopen) $candidates[] = $storedReopen;
//                 if ($reqResolved)  $candidates[] = $reqResolved;
//                 if ($storedResolved) $candidates[] = $storedResolved;
//                 if ($reqVisit)     $candidates[] = $reqVisit;
//                 if ($storedVisit)  $candidates[] = $storedVisit;
//                 $candidates[] = $createdAt;

//                 // Pick the latest candidate
//                 $latest = $candidates[0];
//                 foreach ($candidates as $c) {
//                     if ($c->gt($latest)) $latest = $c;
//                 }

//                 if ($checkClosed->lt($latest)) {
//                     $validator->errors()->add(
//                         'closed_date',
//                         'Closed date/time cannot be before : ' . $latest->format('d-m-Y H:i:s')
//                     );
//                 }
//             }
//         }
//     });

//     // perform validation (redirects back with $errors if fails)
//     $validator->validate();

//     // ------ existing logic (insert comment, files, update incident) ------
//     $uid = Auth::user()->id;
//     $userid = User::findOrFail($uid);

//     $statusMessages = [
//         0 => 'Open',
//         1 => 'Inprogress',
//         2 => 'Resolved',
//         3 => 'Duplicate',
//         4 => 'Closed'
//     ];
//     $incident_msg = $statusMessages[$request->incident_status] ?? 'Unknown Status';

//     $comment = IncidentComment::create([
//         'incident_id' => $id,
//         'comment'     => 'This incident is ' . $incident_msg,
//         'remark'      => $request->remark,
//         'user_id'     => $userid->id,
//         'created_at'  => Carbon::now(),
//     ]);

//     // Handle file uploads (same as your original)
//     // Handle file uploads (NO GD / NO Intervention)
// if ($request->hasFile('incident_files')) {
//     foreach ($request->file('incident_files') as $file) {

//         $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

//         $file->move(public_path('upload/complaint_files'), $name_gen);

//         $save_url = 'upload/complaint_files/' . $name_gen;

//         IncidentImage::insert([
//             'incident_id'    => $id,
//             'user_id'        => $userid->id,
//             'incident_image' => $save_url,
//             'comment_id'     => $comment->id,
//             'created_at'     => Carbon::now(),
//         ]);
//     }
// }


//     // Prepare update data and store provided dates
//     $updateData = [
//         'incident_status' => $request->incident_status,
//         'status_change_by'=> $userid->id,
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

//     Incident::findOrFail($id)->update($updateData);

//     $notification = [
//         'message'    => 'Incident Status Changed Successfully',
//         'alert-type' => 'success'
//     ];

//     return redirect()->back()->with($notification);
// }///Ashi

public function UpdateIncidentStatus(Request $request, $id)
{
    $messages = [
        'incident_files.*.mimes' => 'Only jpeg, png, jpg, gif, svg, and pdf formats are allowed.',
        'incident_files.*.max'   => 'Each file must not exceed 2MB.',
        'visit_date.required'    => 'Visit date is required when the status is In Progress.',
        'visit_date.date'        => 'Visit date must be a valid date/time.',
        'resolved_date.date'     => 'Resolved date must be a valid date/time.',
        'closed_date.date'       => 'Closed date must be a valid date/time.',
        'reopen_date.date'       => 'Reopen date must be a valid date/time.',
    ];
 
    // fetch incident early (we need created_at and stored dates)
    $incident = Incident::findOrFail($id);
 
    // Basic validator (formats + required_if if you want)
    $validator = Validator::make($request->all(), [
        'incident_files.*'  => 'nullable|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
        'incident_status'   => 'required|integer',
        'visit_date'        => 'nullable|date',
        'resolved_date'     => 'nullable|date',
        'closed_date'       => 'nullable|date',
        'reopen_date'       => 'nullable|date',
        'remark'            => 'nullable|string',
    ], $messages);
 
    // Chronological business rules
    $validator->after(function ($validator) use ($incident, $request) {
        $createdAt = Carbon::parse($incident->created_at);
 
        // safe parser helper (return Carbon or null)
        $safeParse = function ($key) use ($request) {
            try {
                if ($request->filled($key)) return Carbon::parse($request->input($key));
            } catch (\Exception $e) {
                // let base validator handle invalid formats
            }
            return null;
        };
 
        // request-supplied
        $reqVisit    = $safeParse('visit_date');
        $reqResolved = $safeParse('resolved_date');
        $reqReopen   = $safeParse('reopen_date');
        $reqClosed   = $safeParse('closed_date');
 
        // stored (existing) values on DB
        $storedVisit    = !empty($incident->visit_date) ? Carbon::parse($incident->visit_date) : null;
        $storedResolved = !empty($incident->resolved_date) ? Carbon::parse($incident->resolved_date) : null;
        $storedReopen   = !empty($incident->reopen_date) ? Carbon::parse($incident->reopen_date) : null;
        $storedClosed   = !empty($incident->closed_date) ? Carbon::parse($incident->closed_date) : null;
 
        // effective (prefer request over stored)
        $visit    = $reqVisit ?? $storedVisit;
        $resolved = $reqResolved ?? $storedResolved;
        $reopen   = $reqReopen ?? $storedReopen;
        $closed   = $reqClosed ?? $storedClosed;
 
        $status = (int) $request->input('incident_status');
 
        // 1) In Progress (1): visit_date must be provided and >= created_at
        if ($status === 1) {
            if (!$reqVisit && !$storedVisit) {
                $validator->errors()->add('visit_date', 'Visit date is required for In Progress status.');
            } else {
                $checkVisit = $reqVisit ?? $storedVisit;
                if ($checkVisit && $checkVisit->lt($createdAt)) {
                    $validator->errors()->add(
                        'visit_date',
                        'Visit date/time cannot be before : ' . $createdAt->format('d-m-Y H:i:s')
                    );
                }
            }
        }
 
        // 2) Resolved (2): resolved_date (if supplied) must be >= visit (req->stored) otherwise >= created_at
        if ($status === 2 && $resolved) {
            $baseline = $visit ?? $createdAt;
            if ($resolved->lt($baseline)) {
                $validator->errors()->add(
                    'resolved_date',
                    'Resolved date/time cannot be before : ' . $baseline->format('d-m-Y H:i:s')
                );
            }
        }
 
        // 3) ReOpen (0): if reopen_date supplied, must be >= resolved (req/stored) or visit (req/stored) or created_at
        if ($status === 0 && $reqReopen) {
            // baseline priority: reqResolved > storedResolved > reqVisit > storedVisit > createdAt
            $baseline = $reqResolved ?? $storedResolved ?? $reqVisit ?? $storedVisit ?? $createdAt;
            if ($reqReopen->lt($baseline)) {
                $validator->errors()->add(
                    'reopen_date',
                    'Reopen date/time cannot be before : ' . $baseline->format('d-m-Y H:i:s')
                );
            }
        }
 
        // 4) Closed (4): closed_date required and must be >= latest(reopen,resolved,visit,createdAt)
        if ($status === 4) {
            if (!$reqClosed && !$storedClosed) {
                $validator->errors()->add('closed_date', 'Closed date is required for Closed status.');
            } else {
                $checkClosed = $reqClosed ?? $storedClosed;
 
                // Collect candidates in order: reqReopen, storedReopen, reqResolved, storedResolved, reqVisit, storedVisit, createdAt
                $candidates = [];
                if ($reqReopen)    $candidates[] = $reqReopen;
                if ($storedReopen) $candidates[] = $storedReopen;
                if ($reqResolved)  $candidates[] = $reqResolved;
                if ($storedResolved) $candidates[] = $storedResolved;
                if ($reqVisit)     $candidates[] = $reqVisit;
                if ($storedVisit)  $candidates[] = $storedVisit;
                $candidates[] = $createdAt;
 
                // Pick the latest candidate
                $latest = $candidates[0];
                foreach ($candidates as $c) {
                    if ($c->gt($latest)) $latest = $c;
                }
 
                if ($checkClosed->lt($latest)) {
                    $validator->errors()->add(
                        'closed_date',
                        'Closed date/time cannot be before : ' . $latest->format('d-m-Y H:i:s')
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
 
    // perform validation (redirects back with $errors if fails)
    $validator->validate();
 
    // ------ existing logic (insert comment, files, update incident) ------
    $uid = Auth::user()->id;
    $userid = User::findOrFail($uid);
 
    $statusMessages = [
        0 => 'Open',
        1 => 'Inprogress',
        2 => 'Resolved',
        3 => 'Duplicate',
        4 => 'Closed'
    ];
    $incident_msg = $statusMessages[$request->incident_status] ?? 'Unknown Status';
 
    $comment = IncidentComment::create([
        'incident_id' => $id,
        'comment'     => 'This incident is ' . $incident_msg,
        'remark'      => $request->remark,
        'user_id'     => $userid->id,
        'created_at'  => Carbon::now(),
    ]);
 
    // Handle file uploads (same as your original)
    // Handle file uploads (NO GD / NO Intervention)
if ($request->hasFile('incident_files')) {
    foreach ($request->file('incident_files') as $file) {
 
        $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
 
        $file->move(public_path('upload/complaint_files'), $name_gen);
 
        $save_url = 'upload/complaint_files/' . $name_gen;
 
        IncidentImage::insert([
            'incident_id'    => $id,
            'user_id'        => $userid->id,
            'incident_image' => $save_url,
            'comment_id'     => $comment->id,
            'created_at'     => Carbon::now(),
        ]);
    }
}
 
 
    // Prepare update data and store provided dates
    $updateData = [
        'incident_status' => $request->incident_status,
        'status_change_by'=> $userid->id,
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
 
    Incident::findOrFail($id)->update($updateData);
 
    $notification = [
        'message'    => 'Incident Status Changed Successfully',
        'alert-type' => 'success'
    ];
 
    return redirect()->back()->with($notification);
}


    public function IncidentVisit()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);
        if ($userid->hasRole(['SuperAdmin', 'Railtel'])) {
            $allincidents = Incident::whereNotNull('visit_date')->latest()->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $allincidents = Incident::join('users', 'users.id', '=', 'incidents.user_id')->whereNotNull('visit_date')->where('users.region_id', $userid->region_id)->orderBy('incidents.created_at', 'DESC')->get();

        } elseif ($userid->hasRole('WarehouseManager')) {
            $allincidents = Incident::where('user_id', $userid->id)
                ->whereNotNull('visit_date')->orderBy('created_at', 'DESC')->get();
        } else {
            $allincidents = Incident::whereNotNull('visit_date')->latest()->get();
        }
        return view('admin.incident.incident_visit', compact('allincidents'));
    }// end method

    public function Incident($id)
    {
        $uid = Auth::user()->id;
        $userid = User::findOrFail($uid);

        if ($userid->hasRole(['SuperAdmin','Railtel'])) {
            $incidents = Incident::where('incident_status', $id)
                ->orderBy('created_at', 'DESC')
                ->get();
            $warehouses = User::role('WarehouseManager')->get();
            $regionalManagers = User::role('RegionalManager')->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $incidents = Incident::join('users', 'users.id', '=', 'incidents.user_id')
                ->where('users.region_id', $userid->region_id)
                ->where('incidents.incident_status', $id)
                ->orderBy('incidents.created_at', 'DESC')
                ->get(['incidents.id', 'incidents.user_id', 'incidents.incident_id', 'incidents.subject', 'incidents.description', 'incidents.incident_status', 'incidents.created_at', 'incidents.closed_date', 'incidents.resolved_date']);
            $warehouses = User::role('WarehouseManager')->where('users.region_id', $userid->region_id)->get();
            $regionalManagers = User::role('RegionalManager')->where('users.region_id', $userid->region_id)->get();
        } elseif ($userid->hasRole(['WarehouseManager'])) {

            $incidents = Incident::where('user_id', $userid->id)
                ->where('incident_status', $id)
                ->orderBy('created_at', 'DESC')
                ->get();
            $warehouses = User::role('WarehouseManager')->where('users.id', $userid->id)->get();
            $regionalManagers = User::role('RegionalManager')->where('users.region_id', $userid->region_id)->get();
        } else {
            $incidents = Incident::where('incident_status', $id)->get();
            $warehouses = User::role('WarehouseManager')->get();
            $regionalManagers = User::role('RegionalManager')->get();
        }

        $regions = Region::all();
        return view('admin.incident.incidents', compact('incidents', 'warehouses', 'regionalManagers', 'regions'));
    } // end method


    ///ch

// INCIDENT PAGE
public function addIncident()
{
    return view('admin.incident.add_incident');
}
//ch



}
