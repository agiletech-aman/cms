@extends('admin.admin_master')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Add Roles in Permission</h4>

                    <div class="page-title-right">
                        <a href="{{ route('all.roles.permission') }}" class="btn btn-info">View All Roles in Permission</a>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Add Roles in Permission</h4><br><br>
                        <form method="POST" action="{{ route('role.permission.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <label for="role_id" class="col-sm-2 col-form-label">All Roles</label>
                                <div class="col-sm-10">
                                    <select class="form-select" required name="role_id" id="role_id">

                                        <option value="">Select One Role</option>
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <!-- end row -->

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="allpermission">
                                <label class="form-check-label" for="allpermission">
                                    Permission All
                                </label>
                            </div>
                            <hr>

                            @foreach($permission_groups as $group)
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox"  id="formCheck1">
                                        <label class="form-check-label" for="formCheck1">
                                            {{ $group-> group_name }}
                                        </label>
                                    </div>
                                </div><!-- End Column 3 -->
                                <div class="col-md-9">

                                    @php
                                    $permissions = App\Models\User::getpermissionByGroupName($group-> group_name);
                                    @endphp

                                    @foreach($permissions as $permission)
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="permission[]" id="customcheck{{ $permission->id }}" value="{{ $permission->id }}">
                                        <label class="form-check-label" for="customcheck{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                    <br>
                                </div><!-- End Column 9 -->
                            </div> <!-- End Row -->
                            @endforeach


                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Add Roles In Permission">

                        </form>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
<script>
    $('#allpermission').click(function() {
        if ($(this).is(':checked')) {
            $('input[type = checkbox]').prop('checked', true);
        } else {
            $('input[type = checkbox]').prop('checked', false);
        }
    });
</script>
@endsection