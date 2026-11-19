<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\Incident;
use App\Models\OldComplaint;
use App\Models\Region;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function ReportNewComplaint()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);
        if ($userid->hasRole('SuperAdmin')) {
            $allcomplaints = Complaint::latest()->get();
            $warehouses = User::role('WarehouseManager')->get();
            $regionalManagers = User::role('RegionalManager')->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $allcomplaints = Complaint::join('users', 'users.id', '=', 'complaints.user_id')->where('users.region_id', $userid->region_id)->orderBy('complaints.created_at', 'DESC')->get(['complaints.id', 'complaints.user_id', 'complaints.item_id','complaints.cat_id', 'complaints.complaint_id', 'complaints.subject', 'complaints.description', 'complaints.complaint_status', 'complaints.created_at', 'complaints.closed_date','complaints.resolved_date', 'complaints.remark']);
            $warehouses = User::role('WarehouseManager')->where('users.region_id', $userid->region_id)->get();
            $regionalManagers = User::role('RegionalManager')->where('users.region_id', $userid->region_id)->get();
        } elseif ($userid->hasRole(['WarehouseManager', 'CorporateOffice'])) {
            $allcomplaints = Complaint::where('user_id', $userid->id)->orderBy('created_at', 'DESC')->get();
            $warehouses = User::role('WarehouseManager')->where('id', $userid->id)->get();
            $regionalManagers = User::role('RegionalManager')->where('users.region_id', $userid->region_id)->get();
        } else {
            $allcomplaints = Complaint::latest()->get();
            $warehouses = User::role('WarehouseManager')->get();
            $regionalManagers = User::role('RegionalManager')->get();
        }

        $allcomplaints->loadMissing(['statusUpdates', 'uploadedFiles']);
        $regions = Region::all();
        return view('admin.report.all_new_complaint', compact('allcomplaints', 'warehouses', 'regionalManagers', 'regions'));
    } //End Method

    public function ReportOldComplaint()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);
        if ($userid->hasRole('SuperAdmin')) {
            $allcomplaints = OldComplaint::latest()->get();
            $warehouses = User::role('WarehouseManager')->get();
            $regionalManagers = User::role('RegionalManager')->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $allcomplaints = OldComplaint::join('users', 'users.id', '=', 'old_complaints.user_id')->where('users.region_id', $userid->region_id)->orderBy('old_complaints.created_at', 'DESC')->get(['old_complaints.id', 'old_complaints.user_id', 'old_complaints.complaint_id', 'old_complaints.subject', 'old_complaints.description', 'old_complaints.complaint_status', 'old_complaints.created_at', 'old_complaints.closed_date', 'old_complaints.remark']);
            $warehouses = User::role('WarehouseManager')->where('users.region_id', $userid->region_id)->get();
            $regionalManagers = User::role('RegionalManager')->where('users.region_id', $userid->region_id)->get();
        } elseif ($userid->hasRole(['WarehouseManager', 'CorporateOffice'])) {
            $allcomplaints = OldComplaint::where('user_id', $userid->id)->orderBy('created_at', 'DESC')->get();
            $warehouses = User::role('WarehouseManager')->where('id', $userid->id)->get();
            $regionalManagers = User::role('RegionalManager')->where('users.region_id', $userid->region_id)->get();
        } else {
            $allcomplaints = OldComplaint::latest()->get();
            $warehouses = User::role('WarehouseManager')->get();
            $regionalManagers = User::role('RegionalManager')->get();
        }

        $allcomplaints->loadMissing(['statusUpdates', 'uploadedFiles']);
        $regions = Region::all();
        return view('admin.report.all_old_complaint', compact('allcomplaints', 'warehouses', 'regionalManagers', 'regions'));
    } //End Method

    public function ReportIncident()
    {
        $id = Auth::user()->id;
        $userid = User::findOrFail($id);
        if ($userid->hasRole('SuperAdmin')) {
            $allincidents = Incident::latest()->get();
            $warehouses = User::role('WarehouseManager')->get();
            $regionalManagers = User::role('RegionalManager')->get();
        } elseif ($userid->hasRole('RegionalManager')) {
            $allincidents = Incident::join('users', 'users.id', '=', 'incidents.user_id')->where('users.region_id', $userid->region_id)->orderBy('incidents.created_at', 'DESC')->get(['incidents.id', 'incidents.user_id', 'incidents.incident_id', 'incidents.subject', 'incidents.description', 'incidents.complaint_image', 'incidents.incident_status', 'incidents.created_at', 'incidents.closed_date', 'incidents.resolved_date']);
            $warehouses = User::role('WarehouseManager')->where('users.region_id', $userid->region_id)->get();
            $regionalManagers = User::role('RegionalManager')->where('users.region_id', $userid->region_id)->get();
        } elseif ($userid->hasRole(['WarehouseManager', 'CorporateOffice'])) {
            $allincidents = Incident::where('user_id', $userid->id)->orderBy('created_at', 'DESC')->get();
            $warehouses = User::role('WarehouseManager')->where('id', $userid->id)->get();
            $regionalManagers = User::role('RegionalManager')->where('users.region_id', $userid->region_id)->get();
        } else {
            $allincidents = Incident::latest()->get();
            $warehouses = User::role('WarehouseManager')->get();
            $regionalManagers = User::role('RegionalManager')->get();
        }

        $allincidents->loadMissing(['statusUpdates', 'uploadedFiles']);
        $regions = Region::all();
        return view('admin.report.all_incident', compact('allincidents', 'warehouses', 'regionalManagers', 'regions'));
   
    }

    public function SearchNewComplaint(Request $request)
    {
        // Get the current user
        $uid = Auth::user()->id;
        $userid = User::findOrFail($uid);

        // Get common date filters
        $start_date = !empty($request->start) ? Carbon::createFromFormat('d M, Y', $request->start)->startOfDay() : null;
        $end_date = !empty($request->end) ? Carbon::createFromFormat('d M, Y', $request->end)->endOfDay() : null;

        // Get the base query based on user role
        $complaints = $this->getBaseQuery($userid, $request);

        // Apply filters to the query
        if (!empty($request->complaint_id)) {
            $complaints->where('complaints.complaint_id', $request->complaint_id);
        }

        if (!empty($request->region_id)) {
            $complaints->whereIn('region_id', $request->region_id);
        }

        if (!empty($request->complaint_status)) {
            $complaints->whereIn('complaints.complaint_status', $request->complaint_status);
        }

        if (!empty($request->warehouse_id)) {
            $complaints->whereIn('complaints.user_id', $request->warehouse_id);
        }

        if ($start_date && $end_date) {
            $complaints->whereBetween('complaints.created_at', [$start_date, $end_date]);
        }

        $complaints = $complaints->orderBy('complaints.created_at', 'DESC')->get([
            'complaints.id',
            'complaints.user_id',
            'complaints.cat_id',
            'complaints.item_id','complaints.cat_id',
            'complaints.complaint_id',
            'complaints.subject',
            'complaints.description',
            'complaints.complaint_status',
            'complaints.created_at',
            'complaints.resolved_date',
            'complaints.closed_date',
            'complaints.remark'
        ]);
        $complaints->loadMissing(['statusUpdates', 'uploadedFiles']);

        $warehouses = User::role('WarehouseManager')->get();
        $regions = Region::all();
        if ($userid->hasRole('RegionalManager')) {
            $warehouses = $warehouses->where('region_id', $userid->region_id);
        } elseif ($userid->hasRole('WarehouseManager')) {
            $warehouses = $warehouses->where('id', $userid->id);
        }
        
        $selectedStatus = $request->complaint_status ?? [];

        return view('admin.report.new_search', compact('complaints', 'warehouses', 'regions','selectedStatus'));
    }
    private function getBaseQuery($userid, $request)
    {
        $baseQuery = Complaint::join('users', 'users.id', '=', 'complaints.user_id');

        if ($userid->hasRole('SuperAdmin')) {
            return $baseQuery;
        } elseif ($userid->hasRole('RegionalManager')) {
            return $baseQuery->where('users.region_id', $userid->region_id);
        } elseif ($userid->hasRole('WarehouseManager')) {
            return $baseQuery->where('users.id', $userid->id);
        } elseif ($userid->hasRole('CorporateOffice') || $userid->hasRole('Railtel')) {
            return $baseQuery;
        }

        return $baseQuery; // Default case
    } // End Method

  
    public function SearchOldComplaint(Request $request)
    {
        // Get the current user
        $uid = Auth::user()->id;
        $userid = User::findOrFail($uid);

        // Get common date filters
        $start_date = !empty($request->start) ? Carbon::createFromFormat('d M, Y', $request->start)->startOfDay() : null;
        $end_date = !empty($request->end) ? Carbon::createFromFormat('d M, Y', $request->end)->endOfDay() : null;

        // Get the base query based on user role
        $complaints = $this->getOldComplaintBaseQuery($userid, $request);

        // Apply filters to the query
        if (!empty($request->complaint_id)) {
            $complaints->where('old_complaints.complaint_id', $request->complaint_id);
        }

        if (!empty($request->region_id)) {
            $complaints->whereIn('region_id', $request->region_id);
        }

        if (!empty($request->complaint_status)) {
            $complaints->whereIn('old_complaints.complaint_status', $request->complaint_status);
        }

        if (!empty($request->warehouse_id)) {
            $complaints->whereIn('old_complaints.user_id', $request->warehouse_id);
        }

        if ($start_date && $end_date) {
            $complaints->whereBetween('old_complaints.created_at', [$start_date, $end_date]);
        }

        $complaints = $complaints->orderBy('old_complaints.created_at', 'DESC')->get([
            'old_complaints.id',
            'old_complaints.user_id',
            'old_complaints.complaint_id',
            'old_complaints.cat_id',
            'old_complaints.subject',
            'old_complaints.description',
            'old_complaints.complaint_status',
            'old_complaints.created_at',
            'old_complaints.resolved_date',
            'old_complaints.closed_date',
            'old_complaints.remark'
        ]);
        $complaints->loadMissing(['statusUpdates', 'uploadedFiles']);

        $warehouses = User::role('WarehouseManager')->get();
        $regions = Region::all();
        if ($userid->hasRole('RegionalManager')) {
            $warehouses = $warehouses->where('region_id', $userid->region_id);
        } elseif ($userid->hasRole('WarehouseManager')) {
            $warehouses = $warehouses->where('id', $userid->id);
        }
        
         $selectedStatus = $request->complaint_status ?? [];

        return view('admin.report.old_search', compact('complaints', 'warehouses', 'regions','selectedStatus'));
    }

    private function getOldComplaintBaseQuery($userid, $request)
    {
        $baseQuery = OldComplaint::join('users', 'users.id', '=', 'old_complaints.user_id');

        if ($userid->hasRole('SuperAdmin')) {
            return $baseQuery;
        } elseif ($userid->hasRole('RegionalManager')) {
            return $baseQuery->where('users.region_id', $userid->region_id);
        } elseif ($userid->hasRole('WarehouseManager')) {
            return $baseQuery->where('users.id', $userid->id);
        } elseif ($userid->hasRole('CorporateOffice') || $userid->hasRole('Railtel')) {
            return $baseQuery;
        }

        return $baseQuery; // Default case
    } // End Method
    
    public function SearchIncident(Request $request)
    {
        // Get the current user
        $uid = Auth::user()->id;
        $userid = User::findOrFail($uid);

        // Get common date filters
        $start_date = !empty($request->start) ? Carbon::createFromFormat('d M, Y', $request->start)->startOfDay() : null;
        $end_date = !empty($request->end) ? Carbon::createFromFormat('d M, Y', $request->end)->endOfDay() : null;

        // Get the base query based on user role
        $incidents = $this->getIncidentBaseQuery($userid, $request);

        // Apply filters to the query
        if (!empty($request->incident_id)) {
            $incidents->where('incidents.incident_id', $request->incident_id);
        }

        if (!empty($request->region_id)) {
            $incidents->whereIn('region_id', $request->region_id);
        }

        if (!empty($request->incident_status)) {
            $incidents->whereIn('incidents.incident_status', $request->incident_status);
        }

        if (!empty($request->warehouse_id)) {
            $incidents->whereIn('incidents.user_id', $request->warehouse_id);
        }

        if ($start_date && $end_date) {
            $incidents->whereBetween('incidents.created_at', [$start_date, $end_date]);
        }
       
        $incidents = $incidents->orderBy('incidents.created_at', 'DESC')->get([
            'incidents.id', 'incidents.user_id', 
            'incidents.incident_id', 'incidents.subject',
            'incidents.description', 'incidents.incident_status',
            'incidents.created_at', 'incidents.resolved_date',
            'incidents.closed_date', 'incidents.complaint_image'
        ]);
        $incidents->loadMissing(['statusUpdates', 'uploadedFiles']);

        $warehouses = User::role('WarehouseManager')->get();
        $regions = Region::all();
        if ($userid->hasRole('RegionalManager')) {
            $warehouses = $warehouses->where('region_id', $userid->region_id);
        } elseif ($userid->hasRole('WarehouseManager')) {
            $warehouses = $warehouses->where('id', $userid->id);
        }
        
        $selectedStatus = $request->incident_status ?? [];

        return view('admin.report.incident_search', compact('incidents', 'warehouses', 'regions','selectedStatus'));
    }

    private function getIncidentBaseQuery($userid, $request)
    {
        $baseQuery = Incident::join('users', 'users.id', '=', 'incidents.user_id');

        if ($userid->hasRole('SuperAdmin')) {
            return $baseQuery;
        } elseif ($userid->hasRole('RegionalManager')) {
            return $baseQuery->where('users.region_id', $userid->region_id);
        } elseif ($userid->hasRole('WarehouseManager')) {
            return $baseQuery->where('users.id', $userid->id);
        } elseif ($userid->hasRole('CorporateOffice') || $userid->hasRole('Railtel')) {
            return $baseQuery;
        }

        return $baseQuery; // Default case
    } // End Method
}
