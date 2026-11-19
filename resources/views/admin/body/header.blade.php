<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('backend/assets/images/logo-cwc.png') }}" alt="logo-sm-light" height="22" width="40">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('backend/assets/images/logo-cwc.png') }}" alt="logo-light" height="50" width="200">
                    </span>
                </a>
            </div>


            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                <i class="ri-menu-2-line align-middle"></i>
            </button>

            <div class="d-flex align-items-center ms-3">
                <h3 class="mb-0 text-white fw-semibold">
                    Modern Video Surveillance System
                </h3>
            </div>


        </div>

        <div class="d-flex">

            <div class="dropdown d-none d-lg-inline-block ms-1">
                <!-- <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="ri-fullscreen-line"></i>
                </button> -->
            </div>
            @if(Auth::user()->can('alertLog.list'))
                @php
                    $user = Auth::user();

                    if ($user->hasRole(['SuperAdmin', 'Railtel', 'CorporateOffice'])) {
                        $alertCount = \App\Models\AlertLog::where('view', 0)->count();
                        $notifications = \App\Models\AlertLog::latest('created_at')->where('view', 0)->get();
                    } elseif ($user->hasRole('RegionalManager')) {
                        // RegionalManager sees alerts of users in their region regardless of type
                        $alertCount = \App\Models\AlertLog::whereHas('user', function ($query) use ($user) {
                            $query->where('region_id', $user->region_id);
                        })->where('view', 0)->count();

                        $notifications = \App\Models\AlertLog::latest('created_at')
                            ->whereHas('user', function ($query) use ($user) {
                                $query->where('region_id', $user->region_id);
                            })
                            ->where('view', 0)
                            ->get();
                    } elseif ($user->hasRole('WarehouseManager')) {
                        $alertCount = \App\Models\AlertLog::where('user_id', $user->id)->where('view', 0)->count();
                        $notifications = \App\Models\AlertLog::latest('created_at')
                            ->where('user_id', $user->id)
                            ->where('view', 0)
                            ->get();
                    } else {
                        $alertCount = 0;
                        $notifications = collect();
                    }
                @endphp

                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item noti-icon waves-effect"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-notification-3-line"></i>
                        @if($alertCount > 0)
                            <span class="noti-dot"></span>
                            <span class="badge rounded-pill bg-danger float-end">{{ $alertCount }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0"> Notifications </h6>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 230px;">
                            @foreach ($notifications as $notification)
                                @php
                                    $alertUser = $notification->user; // use relationship
                                    $userName = $alertUser ? $alertUser->username : 'Unknown User';

                                    $complaint_id = $notification->complaintNumber();
                                    $complaint_type = $notification->complaintTypeName();
                                    $complaint_status = $notification->complaintStatusName();
                                @endphp
                                @php
                                    // Generate route dynamically based on complaint_type
                                    $link = '#'; // default
                                    if ($notification->complaint_type == 1) {
                                        // New Complaint
                                        $link = route('detail.complaint', $notification->complaint_id);
                                    } elseif ($notification->complaint_type == 2) {
                                        // Old Complaint
                                        $link = route('detail.old.complaint', $notification->complaint_id);
                                    } elseif ($notification->complaint_type == 3) {
                                        // Incident
                                        $link = route('detail.incident', $notification->complaint_id);
                                    }
                                @endphp
                                <a href="{{ $link }}" target="_blank" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <div class="avatar-xs me-3">
                                            <span class="avatar-title bg-primary rounded-circle font-size-16">
                                                <i class="ri-heart-line"></i>
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <h6 class="mb-1 font-size-10">{{ $complaint_id }}</h6>
                                            <div class="font-size-12 text-muted">
                                                <p class="mb-1">
                                                    {{ $complaint_type }} status changed to
                                                    <strong>{{ $complaint_status }}</strong> by
                                                    <strong>{{ $userName }}</strong>
                                                </p>
                                                <p class="mb-0">
                                                    <i class="mdi mdi-clock-outline"></i>
                                                    {{ \Carbon\Carbon::parse($notification->created_at)->format('d-m-Y H:i:s') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="p-2 border-top">
                            <div class="d-grid">
                                <a class="btn btn-sm btn-link font-size-14 text-center" href="{{ route('alert.log') }}">
                                    <i class="mdi mdi-arrow-right-circle me-1"></i> View More..
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @php

                $id = Auth::user()->id;
                $adminData = App\Models\User::find($id);
            @endphp

            <div class="dropdown d-inline-block user-dropdown">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
     src="{{ (!empty($adminData->profile_image) 
            && file_exists(public_path('upload/admin_images/'.$adminData->profile_image)))
            ? asset('upload/admin_images/'.$adminData->profile_image)
            : asset('upload/no_image.jpg') }}"
     alt="Header Avatar"
     onerror="this.src='{{ asset('upload/no_image.jpg') }}'">

                    <!--<img class="rounded-circle header-profile-user"-->
                    <!--    src="{{ (!empty($adminData->profile_image)) ? url('upload/admin_images/' . $adminData->profile_image) : url('upload/no_image.jpg')   }}"-->
                    <!--    alt="Header Avatar">-->
                    <span class="d-none d-xl-inline-block ms-1">{{ $adminData->name }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('admin.profile') }}"><i
                            class="ri-user-line align-middle me-1"></i> Profile</a>

                    @if(Auth::user()->can('user.ChangePassword'))
                        <a class="dropdown-item" href="{{ route('change.password') }}"><i
                                class="ri-wallet-2-line align-middle me-1"></i> Change Password</a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ route('admin.logout') }}"><i
                            class="ri-shut-down-line align-middle me-1 text-danger"></i> Logout</a>
                </div>
            </div>



        </div>
    </div>
</header>