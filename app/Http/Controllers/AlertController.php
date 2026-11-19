<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AlertController extends Controller
{

   /* public function AlertLog()
{
    $id = Auth::user()->id;
    $user = User::findOrFail($id); // Get the authenticated user

    $query = DB::table('alert_logs')
        ->join('complaints', 'alert_logs.complaint_id', '=', 'complaints.id')
        ->join('users', 'alert_logs.user_id', '=', 'users.id')
        ->select(
            'alert_logs.id',
            'complaints.id as complaintID',
            'complaints.complaint_id',
            'alert_logs.complaint_type',
            'users.username',
            'alert_logs.complaint_status',
            'alert_logs.view',
            'alert_logs.created_at',
            'alert_logs.updated_at'
        )
        ->orderBy('alert_logs.created_at', 'desc');

    // **Role-based filtering**
    if ($user->hasRole(['SuperAdmin', 'Railtel', 'CorporateOffice'])) {
        // SuperAdmin, Admin, CorporateOffice can see all alerts
    } elseif ($user->hasRole('RegionalManager')) {
        // RegionalManager sees alerts of users in their region
        $query->whereExists(function ($subQuery) use ($user) {
            $subQuery->select(DB::raw(1))
                ->from('users as u')
                ->whereColumn('u.id', 'complaints.user_id')
                ->where('u.region_id', $user->region_id);
        });
    } elseif ($user->hasRole('WarehouseManager')) {
        // WarehouseManager sees only their own alerts
        $query->where('alert_logs.user_id', $user->id);
    } else {
        // If user has no matching role, return an empty collection
        $complaintStatuses = collect();
        return view('admin.alert.alert_log', compact('complaintStatuses'));
    }

    $complaintStatuses = $query->get();
    
    return view('admin.alert.alert_log', compact('complaintStatuses'));
} */
/*
public function AlertLog()
{
    $id = Auth::user()->id;
    $user = User::findOrFail($id); // Get the authenticated user

    // ✅ Build base query with conditional joins
    $query = DB::table('alert_logs')
        // join complaints table if complaint_type = 1
        ->leftJoin('complaints', function ($join) {
            $join->on('alert_logs.complaint_id', '=', 'complaints.id')
                ->where('alert_logs.complaint_type', 1);
        })
        // join old_complaints table if complaint_type = 2
        ->leftJoin('old_complaints', function ($join) {
            $join->on('alert_logs.complaint_id', '=', 'old_complaints.id')
                ->where('alert_logs.complaint_type', 2);
        })
        // join incidents table if complaint_type = 3
        ->leftJoin('incidents', function ($join) {
            $join->on('alert_logs.complaint_id', '=', 'incidents.id')
                ->where('alert_logs.complaint_type', 3);
        })
        // join users table always
        ->join('users', 'alert_logs.user_id', '=', 'users.id')
        ->select(
            'alert_logs.id',
            'alert_logs.complaint_type',
            'users.username',
            'alert_logs.complaint_status',
            'alert_logs.view',
            'alert_logs.created_at',
            'alert_logs.updated_at',

            // ✅ Correct complaintID depending on table
            DB::raw('CASE 
                WHEN alert_logs.complaint_type = 1 THEN complaints.id
                WHEN alert_logs.complaint_type = 2 THEN old_complaints.id
                WHEN alert_logs.complaint_type = 3 THEN incidents.id
            END as complaintID'),

            // ✅ Correct complaint/incident unique ID depending on table
            DB::raw('CASE 
                WHEN alert_logs.complaint_type = 1 THEN complaints.complaint_id
                WHEN alert_logs.complaint_type = 2 THEN old_complaints.complaint_id
                WHEN alert_logs.complaint_type = 3 THEN incidents.incident_id
            END as complaint_unique_id')
        )
        ->orderBy('alert_logs.created_at', 'desc');

    // ✅ Role-based filtering
    if ($user->hasRole(['SuperAdmin', 'Railtel', 'CorporateOffice'])) {
        // These roles can see all alerts (no filter)
    } elseif ($user->hasRole('RegionalManager')) {
        // RegionalManager sees alerts of users in their region
        $query->whereExists(function ($subQuery) use ($user) {
            $subQuery->select(DB::raw(1))
                ->from('users as u')
                // because alert_logs can be linked to any table,
                // we use alert_logs.user_id instead of complaints.user_id directly
                ->whereColumn('u.id', 'alert_logs.user_id')
                ->where('u.region_id', $user->region_id);
        });
    } elseif ($user->hasRole('WarehouseManager')) {
        // WarehouseManager sees only their own alerts
        $query->where('alert_logs.user_id', $user->id);
    } else {
        // If user has no matching role, return empty collection
        $complaintStatuses = collect();
        return view('admin.alert.alert_log', compact('complaintStatuses'));
    }

    $complaintStatuses = $query->get();

    return view('admin.alert.alert_log', compact('complaintStatuses'));
}
*/

public function AlertLog()
{
    $id = Auth::user()->id;
    $user = User::findOrFail($id);

    $query = DB::table('alert_logs')
        ->leftJoin('complaints', function ($join) {
            $join->on('alert_logs.complaint_id', '=', 'complaints.id')
                ->where('alert_logs.complaint_type', 1);
        })
        ->leftJoin('old_complaints', function ($join) {
            $join->on('alert_logs.complaint_id', '=', 'old_complaints.id')
                ->where('alert_logs.complaint_type', 2);
        })
        ->leftJoin('incidents', function ($join) {
            $join->on('alert_logs.complaint_id', '=', 'incidents.id')
                ->where('alert_logs.complaint_type', 3);
        })
        ->join('users', 'alert_logs.user_id', '=', 'users.id')
        ->select(
            'alert_logs.id',
            'alert_logs.complaint_type',
            'users.username',
            'alert_logs.complaint_status',
            'alert_logs.view',
            'alert_logs.created_at',
            'alert_logs.updated_at',
            DB::raw('CASE 
                WHEN alert_logs.complaint_type = 1 THEN complaints.id
                WHEN alert_logs.complaint_type = 2 THEN old_complaints.id
                WHEN alert_logs.complaint_type = 3 THEN incidents.id
            END as complaintID'),
            DB::raw('CASE 
                WHEN alert_logs.complaint_type = 1 THEN complaints.complaint_id
                WHEN alert_logs.complaint_type = 2 THEN old_complaints.complaint_id
                WHEN alert_logs.complaint_type = 3 THEN incidents.incident_id
            END as complaint_unique_id')
        )
        ->orderBy('alert_logs.created_at', 'desc');

    if ($user->hasRole(['SuperAdmin', 'Railtel', 'CorporateOffice'])) {
        // These roles can see all alerts (no filter)
    } elseif ($user->hasRole('RegionalManager')) {
        $query->whereExists(function ($subQuery) use ($user) {
            $subQuery->select(DB::raw(1))
                ->from('users as u')
                ->whereColumn('u.id', 'alert_logs.user_id')
                ->where('u.region_id', $user->region_id);
        });
    } elseif ($user->hasRole('WarehouseManager')) {
        $query->where('alert_logs.user_id', $user->id);
    } else {
        $complaintStatuses = collect();
        return view('admin.alert.alert_log', compact('complaintStatuses'));
    }

    // ✅ Exclude records where complaint_unique_id resolved to null
    $query->whereRaw('CASE 
        WHEN alert_logs.complaint_type = 1 THEN complaints.complaint_id
        WHEN alert_logs.complaint_type = 2 THEN old_complaints.complaint_id
        WHEN alert_logs.complaint_type = 3 THEN incidents.incident_id
    END IS NOT NULL');

    $complaintStatuses = $query->get();
    return view('admin.alert.alert_log', compact('complaintStatuses'));
}
    public function ReadLog($id)
    {
        DB::table('alert_logs')
            ->where('id', $id)
            ->update(['view' => '1']);

        $notification = array(
            'message' => 'Log Read Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    } //End Method

    public function UnReadLog($id)
    {
        DB::table('alert_logs')
            ->where('id', $id)
            ->update(['view' => '0']);

        $notification = array(
            'message' => 'Log Unread Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    } //End Method
}





