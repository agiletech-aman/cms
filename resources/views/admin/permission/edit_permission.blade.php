@extends('admin.admin_master')
@section('admin')


<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit Permission</h4>

                    <div class="page-title-right">
                        <a href="{{ route('all.permission') }}" class="btn btn-info">View All Permission</a>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Edit Permission</h4><br><br>
                        <form method="POST" action="{{ route('update.permission') }}" enctype="multipart/form-data">
                            @csrf
<input type="hidden" name="id" value="{{ $permission->id }}">
                            <div class="row mb-3">
                                <label for="name" class="col-sm-2 col-form-label">Permission Name</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="text" placeholder="Name" name="name" id="name" value="{{ $permission->name }}">
                                </div>
                            </div>
                            <!-- end row -->


                            <div class="row mb-3">
                                <label for="group_name" class="col-sm-2 col-form-label">Group Name</label>
                                <div class="col-sm-10">
                                    <select class="form-select" required name="group_name" id="group_name">

                                        <option value="">Select One Permission</option>
                                        <option value="complaint" {{ $permission->group_name=='complaint' ? 'selected' : '' }}>Complaint</option>
                                        
                                        <option value="oldcomplaint" {{ $permission->group_name=='oldcomplaint' ? 'selected' : '' }}>OldComplaint</option>
                                        <option value="incident" {{ $permission->group_name=='incident' ? 'selected' : '' }}>Incident</option>
                                        <option value="quarterlyVisit" {{ $permission->group_name=='quarterlyVisit' ? 'selected' : '' }}>Quarterly Visit</option>
                                        <option value="report" {{ $permission->group_name=='report' ? 'selected' : '' }}>Report</option>
                                        <option value="state" {{ $permission->group_name=='state' ? 'selected' : '' }}>State</option>
                                        <option value="region" {{ $permission->group_name=='region' ? 'selected' : '' }}>Region</option>
                                        <option value="warehouse" {{ $permission->group_name=='warehouse' ? 'selected' : '' }}>WareHouse</option>
                                        <option value="item" {{ $permission->group_name=='item' ? 'selected' : '' }}>Item</option>
                                        <option value="regionalManager" {{ $permission->group_name=='regionalManager' ? 'selected' : '' }}>Regional Manager</option>
                                        <option value="corporateOffice" {{ $permission->group_name=='corporateOffice' ? 'selected' : '' }}>Corporate Office</option>
                                        <option value="alertLog" {{ $permission->group_name=='alertLog' ? 'selected' : '' }}>AlertLog</option>
                                        <option value="user" {{ $permission->group_name=='user' ? 'selected' : '' }}>User</option>
                                    </select>
                                </div>
                            </div>
                            <!-- end row -->


                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Permission">

                        </form>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

@endsection