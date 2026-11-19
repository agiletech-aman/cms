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
                    <h4 class="mb-sm-0">Edit Roles in Permission</h4>

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

                        <h4 class="card-title">Edit Roles in Permission</h4><br><br>
                        <form method="POST" action="{{ route('role.permission.update',$role->id) }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <label for="role_id" class="col-sm-2 col-form-label">All Roles</label>
                                <div class="col-sm-10">
                                    {{ $role->name }}
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
                                <div class="col-3">
                                    @php
                                    $permissions = App\Models\User::getpermissionByGroupName($group->group_name);
                                    @endphp
                                    <div class="form-check mb-2 form-check-primary">
                                        <input class="form-check-input" type="checkbox" value="" id="customckeck1" {{ App\Models\User::roleHasPermissions($role, $permissions) ? 'checked' : ''  }}>
                                        <label class="form-check-label" for="customckeck1">{{ $group->group_name }}</label>
                                    </div>

                                </div><!--  // End col 3  -->
                                <div class="col-md-9">



                                    @foreach($permissions as $permission)
                                    <div class="form-check mb-2 form-check-primary">
                                        <input class="form-check-input" name="permission[]" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }} type="checkbox" value="{{ $permission->id }}" id="customckeck{{ $permission->id }}">
                                        <label class="form-check-label" for="customckeck1">{{ $permission->name }}</label>
                                    </div>
                                    @endforeach
                                    <br>
                                </div><!-- End Column 9 -->
                            </div> <!-- End Row -->
                            @endforeach


                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Roles In Permission">

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