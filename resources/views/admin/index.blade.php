@extends('admin.admin_master')
@section('admin')
    @php
        $id = Auth::user()->id;
        $userid = App\Models\User::findOrFail($id);
        $status = $userid->status;
        $username = $userid->username;
    @endphp
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="{{ asset('backend/assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}"
        rel="stylesheet" />
    <script src="{{ asset('backend/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

    <style>
        .highcharts-credits {
            display: none;
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">

                    @if(auth()->user()->hasRole(['SuperAdmin', 'Railtel']))
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Complaint Management Tools</h3>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('complaints.check-unresolved') }}" class="btn btn-warning">
                                    <i class="fas fa-clock"></i> Check & Notify Unresolved Complaints (48h+)
                                </a>
                            </div>
                        </div>
                    @endif


                    @if($status == 'inactive')
                        
                        <div class="page-title-box align-items-center justify-content-between">
                            <h4 class="mb-sm-0">{{ $username }} Account Is <span class="text-danger">Inactive</span></h4>
                            <p class="text-danger"><strong>Plz wait admin will check and approve your account</strong></p>
                            
                        </div>
                    @endif



                </div>
            </div>

             <!-- end page title -->
            @if($status == 'active')
                    

                                    <!-- ROW 1 : OPEN -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-body d-flex align-items-center">
                                        <!-- <i class="ri-ticket-2-line font-size-36 text-primary me-3"></i> -->
                                        <div>
                                            <h5 class="mb-1">New CCTV Complaints</h5>
                                            <hr>

                                            <div class="row">
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-grow-1">
                                                                @php
                                                                    if (Auth::user()->hasRole('SuperAdmin')) {
                                                                        $openComplaint = App\Models\Complaint::where('complaint_status', 0)->orderBy('created_at', 'DESC')->get();
                                                                    } elseif (Auth::user()->hasRole('RegionalManager')) {
                                                                        $openComplaint = App\Models\User::role(['WarehouseManager', 'RegionalManager'])->join('complaints', 'users.id', '=', 'complaints.user_id')->where('users.region_id', $userid->region_id)->where('complaints.complaint_status', 0)->orderBy('complaints.created_at', 'DESC')->get();
                                                                    } elseif (Auth::user()->hasRole(['WarehouseManager',])) {
                                                                        $openComplaint = App\Models\Complaint::where('complaint_status', 0)->where('user_id', $userid->id)->orderBy('created_at', 'DESC')->get();

                                                                    } else {
                                                                        $openComplaint = App\Models\Complaint::where('complaint_status', 0)->orderBy('created_at', 'DESC')->get();
                                                                    }

                                                                    $openCount = $openComplaint->count();
                                                                @endphp
                                                                <p class="text-truncate font-size-14 mb-2">Open New Complaints</p>
                                                                <h4 class="mb-2">{{ $openCount }}</h4>

                                                                <a href="{{ route('complaint', 0) }}" class="card-link">View All</a>
                                                            </div>
                                                            <div class="avatar-sm">
                                                                <span class="avatar-title bg-light text-primary rounded-3">
                                                                    <i class="ri-ticket-2-line font-size-24"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div><!-- end cardbody -->
                                                </div><!-- end card -->
                                            </div><!-- end col -->

                                            <!-- inprogress complaint -->
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-grow-1">
                                                                @php
                                                                    if (Auth::user()->hasRole('SuperAdmin')) {
                                                                        $inprogressComplaint = App\Models\Complaint::where('complaint_status', 1)->orderBy('created_at', 'DESC')->get();
                                                                    } elseif (Auth::user()->hasRole('RegionalManager')) {
                                                                        $inprogressComplaint = App\Models\User::role(['WarehouseManager', 'RegionalManager'])->join('complaints', 'users.id', '=', 'complaints.user_id')->where('users.region_id', $userid->region_id)->where('complaints.complaint_status', 1)->orderBy('complaints.created_at', 'DESC')->get();
                                                                    } elseif (Auth::user()->hasRole(['WarehouseManager',])) {
                                                                        $inprogressComplaint = App\Models\Complaint::where('complaint_status', 1)->where('user_id', $userid->id)->orderBy('created_at', 'DESC')->get();

                                                                    } else {
                                                                        $inprogressComplaint = App\Models\Complaint::where('complaint_status', 1)->get();
                                                                    }
                                                                    $inprogressCount = $inprogressComplaint->count();
                                                                @endphp
                                                                <p class="text-truncate font-size-14 mb-2"> Inprogress New
                                                                    Complaints</p>
                                                                <h4 class="mb-2">{{ $inprogressCount }}</h4>

                                                                <a href="{{ route('complaint', 1) }}" class="card-link">View All</a>
                                                            </div>
                                                            <div class="avatar-sm">
                                                                <span class="avatar-title bg-light text-success rounded-3">
                                                                    <i class="mdi mdi-ticket font-size-24"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div><!-- end cardbody -->
                                                </div><!-- end card -->
                                            </div><!-- end col -->


                                            <div class="col-xl-3 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-grow-1">
                                                                @php
                                                                    if (Auth::user()->hasRole('SuperAdmin')) {
                                                                        $resolvedComplaint = App\Models\Complaint::where('complaint_status', 2)->orderBy('created_at', 'DESC')->get();
                                                                    } elseif (Auth::user()->hasRole('RegionalManager')) {
                                                                        $resolvedComplaint = App\Models\User::role(['WarehouseManager', 'RegionalManager'])->join('complaints', 'users.id', '=', 'complaints.user_id')->where('users.region_id', $userid->region_id)->where('complaints.complaint_status', 2)->orderBy('complaints.created_at', 'DESC')->get();
                                                                    } elseif (Auth::user()->hasRole(['WarehouseManager',])) {
                                                                        $resolvedComplaint = App\Models\Complaint::where('complaint_status', 2)->where('user_id', $userid->id)->orderBy('created_at', 'DESC')->get();

                                                                    } else {
                                                                        $resolvedComplaint = App\Models\Complaint::where('complaint_status', 2)->get();
                                                                    }
                                                                    $resolvedCount = $resolvedComplaint->count();
                                                                @endphp
                                                                <p class="text-truncate font-size-14 mb-2">Resolved New
                                                                    Complaints</p>
                                                                <h4 class="mb-2">{{ $resolvedCount }}</h4>

                                                                <a href="{{ route('complaint', 2) }}" class="card-link">View All</a>
                                                            </div>
                                                            <div class="avatar-sm">
                                                                <span class="avatar-title bg-light text-success rounded-3">
                                                                   <i class="mdi mdi-ticket-confirmation-outline font-size-24"></i>                                        </span>
                                                            </div>
                                                        </div>
                                                    </div><!-- end cardbody -->
                                                </div><!-- end card -->
                                            </div><!-- end col -->

                                            <div class="col-xl-3 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-grow-1">
                                                                @php
                                                                    if (Auth::user()->hasRole('SuperAdmin')) {
                                                                        $closedComplaint = App\Models\Complaint::where('complaint_status', 4)->orderBy('created_at', 'DESC')->get();
                                                                    } elseif (Auth::user()->hasRole('RegionalManager')) {
                                                                        $closedComplaint = App\Models\User::role(['WarehouseManager', 'RegionalManager'])->join('complaints', 'users.id', '=', 'complaints.user_id')->where('users.region_id', $userid->region_id)->where('complaints.complaint_status', 4)->orderBy('complaints.created_at', 'DESC')->get();
                                                                    } elseif (Auth::user()->hasRole(['WarehouseManager',])) {
                                                                        $closedComplaint = App\Models\Complaint::where('complaint_status', 4)->where('user_id', $userid->id)->orderBy('created_at', 'DESC')->get();

                                                                    } else {
                                                                        $closedComplaint = App\Models\Complaint::where('complaint_status', 4)->get();
                                                                    }
                                                                    $closedCount = $closedComplaint->count();
                                                                @endphp
                                                                <p class="text-truncate font-size-14 mb-2">Closed New
                                                                    Complaints

                                                                </p>
                                                                <h4 class="mb-2">{{ $closedCount }}</h4>
                                                                <a href="{{ route('complaint', 4) }}" class="card-link">View All</a>
                                                            </div>
                                                            <div class="avatar-sm">
                                                                <span class="avatar-title bg-light text-success rounded-3">
                                                                    <i class="mdi mdi-close-octagon-outline font-size-24"></i> <!-- force-closed -->

                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div><!-- end cardbody -->
                                                </div><!-- end card -->
                                            </div><!-- end col -->




                                        </div>
                                        </div>

                                        <!-- end row -->

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ROW 2 : IN PROGRESS -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-body d-flex align-items-center">
                                        <!-- <i class="mdi mdi-ticket font-size-36 text-warning me-3"></i> -->
                                        <div>
                                            <h5 class="mb-1">Old CCTV Complaints</h5>
                                            <hr>


                                        <!-- old complaints -->
                                        <div class="row">
                                            <!-- Open Old Complaints -->
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-grow-1">
                                                                @php
                                                                    if (Auth::user()->hasRole('SuperAdmin')) {
                                                                        $openOldComplaint = App\Models\OldComplaint::where('complaint_status', 0)->get();
                                                                    } elseif (Auth::user()->hasRole('RegionalManager')) {
                                                                        $openOldComplaint = App\Models\User::role(['WarehouseManager', 'RegionalManager'])
                                                                            ->join('old_complaints', 'users.id', '=', 'old_complaints.user_id')
                                                                            ->where('users.region_id', $userid->region_id)
                                                                            ->where('old_complaints.complaint_status', 0)
                                                                            ->get();
                                                                    } elseif (Auth::user()->hasRole(['WarehouseManager',])) {
                                                                        $openOldComplaint = App\Models\OldComplaint::where('complaint_status', 0)
                                                                            ->where('user_id', $userid->id)
                                                                            ->get();
                                                                    } else {
                                                                        $openOldComplaint = App\Models\OldComplaint::where('complaint_status', 0)->get();
                                                                    }
                                                                    $openOldCount = $openOldComplaint->count();
                                                                @endphp
                                                                <p class="text-truncate font-size-14 mb-2">Open Old Complaints</p>
                                                                <h4 class="mb-2">{{ $openOldCount }}</h4>
                                                                <a href="{{ route('old.complaint', 0) }}" class="card-link">View All</a>
                                                            </div>
                                                            <div class="avatar-sm">
                                                                <span class="avatar-title bg-light text-primary rounded-3">
                                                                    <i class="ri-ticket-2-line font-size-24"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div><!-- end cardbody -->
                                                </div><!-- end card -->
                                            </div><!-- end col -->

                                            <!-- Inprogress Old Complaints -->
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-grow-1">
                                                                @php
                                                                    if (Auth::user()->hasRole('SuperAdmin')) {
                                                                        $inporgressOldComplaint = App\Models\OldComplaint::where('complaint_status', 1)->get();
                                                                    } elseif (Auth::user()->hasRole('RegionalManager')) {
                                                                        $inporgressOldComplaint = App\Models\User::role(['WarehouseManager', 'RegionalManager'])
                                                                            ->join('old_complaints', 'users.id', '=', 'old_complaints.user_id')
                                                                            ->where('users.region_id', $userid->region_id)
                                                                            ->where('old_complaints.complaint_status', 1)
                                                                            ->get();
                                                                    } elseif (Auth::user()->hasRole(['WarehouseManager',])) {
                                                                        $inporgressOldComplaint = App\Models\OldComplaint::where('complaint_status', 1)
                                                                            ->where('user_id', $userid->id)
                                                                            ->get();
                                                                    } else {
                                                                        $inporgressOldComplaint = App\Models\OldComplaint::where('complaint_status', 1)->get();
                                                                    }
                                                                    $inporgressOldCount = $inporgressOldComplaint->count();
                                                                @endphp
                                                                <p class="text-truncate font-size-14 mb-2">Inprogress Old Complaints</p>
                                                                <h4 class="mb-2">{{ $inporgressOldCount }}</h4>
                                                                <a href="{{ route('old.complaint', 1) }}" class="card-link">View All</a>
                                                            </div>
                                                            <div class="avatar-sm">
                                                                <span class="avatar-title bg-light text-success rounded-3">
                                                                    <i class="mdi mdi-ticket font-size-24"></i> </span>
                                                            </div>
                                                        </div>
                                                    </div><!-- end cardbody -->
                                                </div><!-- end card -->
                                            </div><!-- end col -->

                                            <!-- Resolved Old Complaints -->
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-grow-1">
                                                                @php
                                                                    if (Auth::user()->hasRole('SuperAdmin')) {
                                                                        $resolvedOldComplaint = App\Models\OldComplaint::where('complaint_status', 2)->get();
                                                                    } elseif (Auth::user()->hasRole('RegionalManager')) {
                                                                        $resolvedOldComplaint = App\Models\User::role(['WarehouseManager', 'RegionalManager'])
                                                                            ->join('old_complaints', 'users.id', '=', 'old_complaints.user_id')
                                                                            ->where('users.region_id', $userid->region_id)
                                                                            ->where('old_complaints.complaint_status', 2)
                                                                            ->get();
                                                                    } elseif (Auth::user()->hasRole(['WarehouseManager',])) {
                                                                        $resolvedOldComplaint = App\Models\OldComplaint::where('complaint_status', 2)
                                                                            ->where('user_id', $userid->id)
                                                                            ->get();
                                                                    } else {
                                                                        $resolvedOldComplaint = App\Models\OldComplaint::where('complaint_status', 2)->get();
                                                                    }
                                                                    $resolvedOldCount = $resolvedOldComplaint->count();
                                                                @endphp
                                                                <p class="text-truncate font-size-14 mb-2">Resolved Old Complaints</p>
                                                                <h4 class="mb-2">{{ $resolvedOldCount }}</h4>
                                                                <a href="{{ route('old.complaint', 2) }}" class="card-link">View All</a>
                                                            </div>
                                                            <div class="avatar-sm">
                                                                <span class="avatar-title bg-light text-success rounded-3">
                        <i class="mdi mdi-ticket-confirmation-outline font-size-24"></i>                                        </span>
                                                            </div>
                                                        </div>
                                                    </div><!-- end cardbody -->
                                                </div><!-- end card -->
                                            </div><!-- end col -->

                                            <!-- Closed Old Complaints -->
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-grow-1">
                                                                @php
                                                                    if (Auth::user()->hasRole('SuperAdmin')) {
                                                                        $closedOldComplaint = App\Models\OldComplaint::where('complaint_status', 4)->get();
                                                                    } elseif (Auth::user()->hasRole('RegionalManager')) {
                                                                        $closedOldComplaint = App\Models\User::role(['WarehouseManager', 'RegionalManager'])
                                                                            ->join('old_complaints', 'users.id', '=', 'old_complaints.user_id')
                                                                            ->where('users.region_id', $userid->region_id)
                                                                            ->where('old_complaints.complaint_status', 4)
                                                                            ->get();
                                                                    } elseif (Auth::user()->hasRole(['WarehouseManager',])) {
                                                                        $closedOldComplaint = App\Models\OldComplaint::where('complaint_status', 4)
                                                                            ->where('user_id', $userid->id)
                                                                            ->get();
                                                                    } else {
                                                                        $closedOldComplaint = App\Models\OldComplaint::where('complaint_status', 4)->get();
                                                                    }
                                                                    $closedOldCount = $closedOldComplaint->count();
                                                                @endphp
                                                                <p class="text-truncate font-size-14 mb-2">Closed Old Complaints</p>
                                                                <h4 class="mb-2">{{ $closedOldCount }}</h4>
                                                                <a href="{{ route('old.complaint', 4) }}" class="card-link">View All</a>
                                                            </div>
                                                            <div class="avatar-sm">
                                                                <span class="avatar-title bg-light text-success rounded-3">
                                                                    <i class="mdi mdi-close-octagon-outline font-size-24"></i> <!-- force-closed -->
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div><!-- end cardbody -->
                                                </div><!-- end card -->
                                            </div><!-- end col -->





                                        </div><!-- end row -->

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ROW 3 : CLOSED -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-body d-flex align-items-center">
                                        <!-- <i class="mdi mdi-ticket-check font-size-36 text-success me-3"></i> -->
                                        <div>
                                            <h5 class="mb-1">Incidents</h5>
                                            <hr>
                                       <div class="row">
                                            <!-- open incidents -->
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-grow-1">
                                                                @php
                                                                    if (Auth::user()->hasRole('SuperAdmin')) {
                                                                        $openincidents = App\Models\Incident::where('incident_status', 0)->orderBy(
                                                                            'created_at',
                                                                            'DESC'
                                                                        )->get();
                                                                    } elseif (Auth::user()->hasRole('RegionalManager')) {
                                                                        $openincidents = App\Models\User::role(['WarehouseManager', 'RegionalManager'])->join(
                                                                            'incidents',
                                                                            'users.id',
                                                                            '=',
                                                                            'incidents.user_id'
                                                                        )->where(
                                                                                'users.region_id',
                                                                                $userid->region_id
                                                                            )->where('incidents.incident_status', 0)->orderBy('incidents.created_at', 'DESC')->get();
                                                                    } elseif (Auth::user()->hasRole(['WarehouseManager',])) {
                                                                        $openincidents = App\Models\Incident::where('incident_status', 0)->where('user_id', $userid->id)->orderBy(
                                                                            'created_at',
                                                                            'DESC'
                                                                        )->get();

                                                                    } else {
                                                                        $openincidents = App\Models\Incident::where('incident_status', 0)->orderBy('created_at', 'DESC')->get();
                                                                    }

                                                                    $countincidents = $openincidents->count();
                                                                @endphp
                                                                <p class="text-truncate font-size-14 mb-2">Open Incidents</p>
                                                                <h4 class="mb-2">{{ $countincidents }}</h4>

                                                                <a href="{{ route('view.incident', 0) }}" class="card-link">View All</a>
                                                            </div>
                                                            <div class="avatar-sm">
                                                                <span class="avatar-title bg-light text-primary rounded-3">
                                                                    <i class="ri-ticket-2-line font-size-24"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div><!-- end cardbody -->
                                                </div><!-- end card -->
                                            </div>

                                            <!-- inprogress incidents -->
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-grow-1">
                                                                @php
                                                                    if (Auth::user()->hasRole('SuperAdmin')) {
                                                                        $inprogressincidents = App\Models\Incident::where('incident_status', 1)->orderBy(
                                                                            'created_at',
                                                                            'DESC'
                                                                        )->get();
                                                                    } elseif (Auth::user()->hasRole('RegionalManager')) {
                                                                        $inprogressincidents = App\Models\User::role(['WarehouseManager', 'RegionalManager'])->join(
                                                                            'incidents',
                                                                            'users.id',
                                                                            '=',
                                                                            'incidents.user_id'
                                                                        )->where(
                                                                                'users.region_id',
                                                                                $userid->region_id
                                                                            )->where('incidents.incident_status', 1)->orderBy('incidents.created_at', 'DESC')->get();
                                                                    } elseif (Auth::user()->hasRole(['WarehouseManager',])) {
                                                                        $inprogressincidents = App\Models\Incident::where('incident_status', 1)->where('user_id', $userid->id)->orderBy(
                                                                            'created_at',
                                                                            'DESC'
                                                                        )->get();

                                                                    } else {
                                                                        $inprogressincidents = App\Models\Incident::where('incident_status', 1)->orderBy('created_at', 'DESC')->get();
                                                                    }

                                                                    $countincidents = $inprogressincidents->count();
                                                                @endphp
                                                                <p class="text-truncate font-size-14 mb-2">Inprogress Incidents</p>
                                                                <h4 class="mb-2">{{ $countincidents }}</h4>

                                                                <a href="{{ route('view.incident', 1) }}" class="card-link">View All</a>
                                                            </div>
                                                            <div class="avatar-sm">
                                                                <span class="avatar-title bg-light text-success rounded-3">
                                                          <i class="mdi mdi-ticket font-size-24"></i></span>
                                                            </div>
                                                        </div>
                                                    </div><!-- end cardbody -->
                                                </div><!-- end card -->
                                            </div>

                                            <!-- resolved incidents -->
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-grow-1">
                                                                @php
                                                                    if (Auth::user()->hasRole('SuperAdmin')) {
                                                                        $resolvedincidents = App\Models\Incident::where('incident_status', 2)->orderBy(
                                                                            'created_at',
                                                                            'DESC'
                                                                        )->get();
                                                                    } elseif (Auth::user()->hasRole('RegionalManager')) {
                                                                        $resolvedincidents = App\Models\User::role(['WarehouseManager', 'RegionalManager'])->join(
                                                                            'incidents',
                                                                            'users.id',
                                                                            '=',
                                                                            'incidents.user_id'
                                                                        )->where(
                                                                                'users.region_id',
                                                                                $userid->region_id
                                                                            )->where('incidents.incident_status', 2)->orderBy('incidents.created_at', 'DESC')->get();
                                                                    } elseif (Auth::user()->hasRole(['WarehouseManager',])) {
                                                                        $resolvedincidents = App\Models\Incident::where('incident_status', 2)->where('user_id', $userid->id)->orderBy(
                                                                            'created_at',
                                                                            'DESC'
                                                                        )->get();

                                                                    } else {
                                                                        $resolvedincidents = App\Models\Incident::where('incident_status', 2)->orderBy('created_at', 'DESC')->get();
                                                                    }

                                                                    $countincidents = $resolvedincidents->count();
                                                                @endphp
                                                                <p class="text-truncate font-size-14 mb-2">Resolved Incidents</p>
                                                                <h4 class="mb-2">{{ $countincidents }}</h4>

                                                                <a href="{{ route('view.incident', 2) }}" class="card-link">View All</a>
                                                            </div>
                                                            <div class="avatar-sm">
                                                                <span class="avatar-title bg-light text-success rounded-3">
                                            <i class="mdi mdi-ticket-confirmation-outline font-size-24"></i> </span>
                                                            </div>
                                                        </div>
                                                    </div><!-- end cardbody -->
                                                </div><!-- end card -->
                                            </div>

                                            <!-- closed incidents -->
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex">
                                                            <div class="flex-grow-1">
                                                                @php
                                                                    if (Auth::user()->hasRole('SuperAdmin')) {
                                                                        $closedincidents = App\Models\Incident::where('incident_status', 4)->orderBy(
                                                                            'created_at',
                                                                            'DESC'
                                                                        )->get();
                                                                    } elseif (Auth::user()->hasRole('RegionalManager')) {
                                                                        $closedincidents = App\Models\User::role(['WarehouseManager', 'RegionalManager'])->join(
                                                                            'incidents',
                                                                            'users.id',
                                                                            '=',
                                                                            'incidents.user_id'
                                                                        )->where(
                                                                                'users.region_id',
                                                                                $userid->region_id
                                                                            )->where('incidents.incident_status', 4)->orderBy('incidents.created_at', 'DESC')->get();
                                                                    } elseif (Auth::user()->hasRole(['WarehouseManager',])) {
                                                                        $closedincidents = App\Models\Incident::where('incident_status', 4)->where('user_id', $userid->id)->orderBy(
                                                                            'created_at',
                                                                            'DESC'
                                                                        )->get();

                                                                    } else {
                                                                        $closedincidents = App\Models\Incident::where('incident_status', 4)->orderBy('created_at', 'DESC')->get();
                                                                    }

                                                                    $countincidents = $closedincidents->count();
                                                                @endphp


                                                                <p class="text-truncate font-size-14 mb-2">Closed Incidents</p>
                                                                <h4 class="mb-2">{{ $countincidents }}</h4>

                                                                <a href="{{ route('view.incident', 4) }}" class="card-link">View All</a>
                                                            </div>
                                                            <div class="avatar-sm">
                                                                <span class="avatar-title bg-light text-success rounded-3">
                                                                    <i class="mdi mdi-close-octagon-outline font-size-24"></i> <!-- force-closed -->
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div><!-- end cardbody -->
                                                </div><!-- end card -->
                                            </div>



                                        </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                   
                    

                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card">
                                  <div class="card-header d-flex justify-content-between align-items-center py-2">
                            <h5 class="mb-0">New CCTV Monthly Complaint Status</h5>

                            <select id="complaint-year-new" class="form-select form-select-sm w-auto">
                                @for ($y = date('Y'); $y >= 2025; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                                <div class="card-body py-0 px-2">
                                    <div id="histogram-container"></div>
                                </div>
                            </div><!-- end card -->
                        </div>
                        <!-- end col -->

                         <div class="col-xl-6">
                            <div class="card">
                                 <div class="card-header d-flex justify-content-between align-items-center py-2">
                            <h5 class="mb-0">Old CCTV Monthly Complaint Status</h5>

                            <select id="complaint-year-old" class="form-select form-select-sm w-auto">
                                @for ($y = date('Y'); $y >= 2025; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                                <div class="card-body py-0 px-2">
                                    <div id="histogram-container-old"></div>
                                </div>
                            </div><!-- end card -->
                        </div>
                      

                    </div>

                    <!-- ch -->

                    <div class="row">

    {{-- REGION WISE NEW COMPLAINT --}}
    @if (!Auth::user()->hasRole(['WarehouseManager', 'RegionalManager']))
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body py-0 px-2">
                <div id="regionwise-histogram-container"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- REGION WISE OLD COMPLAINT --}}
    @if (!Auth::user()->hasRole([ 'RegionalManager', 'WarehouseManager']))
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body py-0 px-2">
                <div id="regionwise-histogram-container-old"></div>
            </div>
        </div>
    </div>
    @endif

</div>



                  <div class='row'>
<!-- ----------------------------- WAREHOUSE WISE NEW COMPLAINT------------------------------ -->
                     
                           @if (Auth::user()->hasRole('RegionalManager')) 
                    
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-body py-0 px-2">
                                    <div id="warehousewise-histogram-container"></div>
                                </div>
                            </div><!-- end card -->
                        </div>
                        @endif
                        <!-- end col -->
                       
<!-- ----------------------------- WAREHOUSE WISE OLD COMPLAINT------------------------------ -->

                        @if (Auth::user()->hasRole('RegionalManager')) 
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-body py-0 px-2">
                                    <div id="warehousewise-histogram-container-old"></div>
                                </div>
                            </div><!-- end card -->
                        </div>
                        @endif
</div>

                    <!-- end row -->


                    @php
                        if (Auth::user()->hasRole('CorporateOffice')) {
                    @endphp
                    <div class="row mb-3">
                        <div class="col-xl-6">
                            <div id="chart-container"></div>
                        </div>

                        <!-- <div class="col-xl-6">
                             <div class="card">
                                 <div class="card-body px-2">
                                     <h4 class="text-center">Uptime Percentage</h4><br>

                                     <form method="POST" action="#" name="uptime">
                                         @csrf
                                         <div class="row mb-3">
                                             <div class="col-xl-8">
                                                 <label for="">Choose Dates</label>
                                                 <div class="input-daterange input-group" id="datepicker6"
                                                     data-date-format="dd M, yyyy" data-date-autoclose="true"
                                                     data-provide="datepicker" data-date-container="#datepicker6">
                                                     <input type="text" class="form-control" name="start" placeholder="Start Date"
                                                         autocomplete="off" />
                                                     <input type="text" class="form-control" name="end" placeholder="End Date"
                                                         autocomplete="off" />
                                                 </div>
                                             </div>
                                             <div class="col-xl-4">
                                                 <label for=""></label>
                                                 <input type="submit" class="form-control btn btn-info waves-effect waves-light"
                                                     value="Search Uptime">
                                             </div>
                                         </div>
                                     </form>

                                     <div id="uptime-container"></div>
                                 </div>
                             </div>
                         </div>-->
                         <div class="col-md-6">
                            <div id="chart-container-oldcomplaint"></div>
                        </div>
                    </div>
                    <!-- end col -->
                    @php
                    } else { @endphp

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div id="chart-container"></div>
                        </div>
                        <div class="col-md-6">
                            <div id="chart-container-oldcomplaint"></div>
                        </div>
                    </div>
                    @php }
                    @endphp
            @endif
            </div>
        </div>
@endsection