@php
    $id = Auth::user()->id;
    $userid = App\Models\User::findOrFail($id);
    $status = $userid->status;
@endphp

<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>

                <li>
                    <a href="{{ route('dashboard') }}" class="waves-effect">
                        <i class="ri-dashboard-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @if($status == 'active')


                    @if(Auth::user()->can('complaint.menu'))
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-ticket-2-line"></i>
                                <span>Complaints</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Auth::user()->can('complaint.list'))
                                    <li><a href="{{ route('all.new.complaint') }}">New CCTV Complaint</a></li>
                                @endif
                                @if(Auth::user()->can('oldcomplaint.list'))
                                    <li><a href="{{ route('all.old.complaint') }}">Old CCTV Complaint</a></li>
                                @endif
                                @if(Auth::user()->can('incident.list'))
                                    <li><a href="{{ route('all.incident') }}">Incident</a></li>
                                @endif
                                <!-- @if(Auth::user()->can('complaint.add'))
                                    <li><a href="{{ route('add.complaint') }}">Add Complaint</a></li>
                                @endif -->
                               <!-- @if(Auth::user()->can('complaint.open'))
                                    <li><a href="{{ route('complaint', 0) }}">Open Complaint</a></li>
                                @endif-->
                            </ul>
                        </li>
                    @endif


                    <!-- <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-ticket-2-line"></i>
                                <span>Complaint Visit</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">

                                @if(Auth::user()->can('complaint.visit'))
                                    <li><a href="{{route('new.complaint.visit')}}">New Infra Complaint Visit</a></li>
                                @endif
                                @if(Auth::user()->can('oldcomplaint.visit'))

                                    <li><a href="{{route('old.complaint.visit')}}">Old Infra Complaint Visit</a></li>
                                @endif
                                @if(Auth::user()->can('incident.visit'))
                                    <li><a href="{{route('incident.visit')}}">Incident</a></li>
                                @endif
                            </ul>
                        </li> 
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="ri-ticket-2-line"></i>
                            <span>Complaint Visit</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            @if(Auth::user()->hasRole(['Railtel', 'SuperAdmin', 'RegionalManager', 'WarehouseManager']))
                                @if(Auth::user()->can('complaint.visit'))
                                    <li><a href="{{ route('new.complaint.visit') }}">New Infra Complaint Visit</a></li>
                                @endif
                                @if(Auth::user()->can('oldcomplaint.visit'))
                                    <li><a href="{{ route('old.complaint.visit') }}">Old Infra Complaint Visit</a></li>
                                @endif
                                @if(Auth::user()->can('incident.visit'))
                                    <li><a href="{{ route('incident.visit') }}">Incident</a></li>
                                @endif
                            @endif
                        </ul>
                    </li> -->

                    @if(
    Auth::user()->hasRole(['Railtel', 'SuperAdmin', 'RegionalManager', 'WarehouseManager']) &&
    (
        Auth::user()->can('complaint.visit') ||
        Auth::user()->can('oldcomplaint.visit') ||
        Auth::user()->can('incident.visit')
    )
)
<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect">
        <i class="ri-ticket-2-line"></i>
        <span>Complaint Visit</span>
    </a>
    <ul class="sub-menu" aria-expanded="false">
        @if(Auth::user()->can('complaint.visit'))
            <li><a href="{{ route('new.complaint.visit') }}">New Infra Complaint Visit</a></li>
        @endif
        @if(Auth::user()->can('oldcomplaint.visit'))
            <li><a href="{{ route('old.complaint.visit') }}">Old Infra Complaint Visit</a></li>
        @endif
        @if(Auth::user()->can('incident.visit'))
            <li><a href="{{ route('incident.visit') }}">Incident</a></li>
        @endif
    </ul>
</li>
@endif


                     @if(Auth::user()->can('quarterlyVisit.menu'))
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-file-user-fill"></i>
                                <span>Quarterly Visit</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Auth::user()->can('quarterlyVisit.list'))
                                    <li><a href="{{ route('all.quarterlyvisit') }}">All Quarterly Visit</a></li>
                                @endif
                                @if(Auth::user()->can('quarterlyVisit.add'))
                                    <li><a href="{{ route('add.quarterlyvisit') }}">Add Quarterly Visit</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                   
                    @if(Auth::user()->can('report.menu'))
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-file-user-fill"></i>
                                <span>Report</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Auth::user()->can('report.newlist'))
                                    <li><a href="{{ route('report.new.complaint') }}">New CCTV Complaint</a></li>
                                @endif
                                @if(Auth::user()->can('report.oldlist'))
                                    <li><a href="{{ route('report.old.complaint') }}">Old CCTV Complaint</a></li>
                                @endif
                                @if(Auth::user()->can('report.incidentlist'))
                                    <li><a href="{{ route('report.incident.complaint') }}">Incident</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif



                    @if(Auth::user()->can('warehouse.menu'))
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-file-user-fill"></i>
                                <span>WareHouse</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Auth::user()->can('warehouse.list'))
                                    <li><a href="{{ route('all.warehouse') }}">All WareHouse</a></li>
                                @endif
                                @if(Auth::user()->can('warehouse.add'))
                                    <li><a href="{{ route('add.warehouse') }}">Add WareHouse</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if(Auth::user()->can('regionalManager.menu'))
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-file-user-fill"></i>
                                <span>Regional Manager</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Auth::user()->can('regionalManager.list'))
                                    <li><a href="{{ route('all.regionalmanager') }}">All Regional Manager</a></li>
                                @endif
                                @if(Auth::user()->can('regionalManager.add'))
                                    <li><a href="{{ route('add.regionalmanager') }}">Add Regional Manager</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->can('corporateOffice.menu'))
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-file-user-fill"></i>
                                <span>Corporate Office</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Auth::user()->can('corporateOffice.list'))
                                    <li><a href="{{ route('all.corporateoffice') }}">All Corporate Office</a></li>
                                @endif
                                @if(Auth::user()->can('corporateOffice.add'))
                                    <li><a href="{{ route('add.corporateoffice') }}">Add Corporate Office</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->can('state.menu'))
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-map-pin-line"></i>
                                <span>State</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Auth::user()->can('state.list'))
                                    <li><a href="{{ route('all.state') }}">All State</a></li>
                                @endif
                                @if(Auth::user()->can('state.add'))
                                    <li><a href="{{ route('add.state') }}">Add State</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->can('region.menu'))
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-map-pin-line"></i>
                                <span>Region</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Auth::user()->can('region.list'))
                                    <li><a href="{{ route('all.region') }}">All Region</a></li>
                                @endif
                                @if(Auth::user()->can('region.add'))
                                    <li><a href="{{ route('add.region') }}">Add Region</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if(Auth::user()->hasRole('SuperAdmin'))
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-user-settings-line"></i>
                                <span>Engineers</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('all.engineer') }}">All Engineers</a></li>
                                <li><a href="{{ route('add.engineer') }}">Add Engineer</a></li>
                            </ul>
                        </li>
                    @endif

                    @if(Auth::user()->can('category.menu'))
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-file-user-fill"></i>
                                <span>Category</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Auth::user()->can('category.list'))
                                    <li><a href="{{ route('all.category') }}">All Category</a></li>
                                @endif
                                @if(Auth::user()->can('category.add'))
                                    <li><a href="{{ route('add.category') }}">Add Category</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->can('item.menu'))
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-file-user-fill"></i>
                                <span>Items</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Auth::user()->can('item.list'))
                                    <li><a href="{{ route('all.items') }}">All Item</a></li>
                                @endif
                                @if(Auth::user()->can('item.add'))
                                    <li><a href="{{ route('add.items') }}">Add Item</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if(Auth::user()->can('user.menu'))
                        <li class="menu-title">Settings</li>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-file-user-fill"></i>
                                <span>User</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @if(Auth::user()->can('user.list'))
                                    <li><a href="{{ route('all.user') }}">All User</a></li>
                                @endif
                                @if(Auth::user()->can('user.add'))
                                    <li><a href="{{ route('add.user') }}">Add User</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->hasRole('SuperAdmin'))
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="ri-map-pin-line"></i>
                                <span>Role and Permission</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('all.permission') }}">All Permission</a></li>
                                <li><a href="{{ route('all.roles') }}">All Roles</a></li>
                                <li><a href="{{ route('add.roles.permission') }}">Roles In Permission</a></li>
                                <li><a href="{{ route('all.roles.permission') }}">All Roles In Permission</a></li>
                            </ul>
                        </li>
                    @endif
                @else

                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>