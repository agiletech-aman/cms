@extends('admin.admin_master')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">User</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('user.list'))
                        <a href="{{ route('all.user') }}" class="btn btn-info">View All User</a>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Edit User</h4><br><br>
                        <form method="POST" action="{{ route('update.user') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $user->id }}">
                            <div class="row mb-3">
                                <label for="name" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="text" placeholder="Name" name="name" id="name" value="{{ $user->name }}">
                                </div>
                            </div>
                            <!-- end row -->

                            <div class="row mb-3">
                                <label for="username" class="col-sm-2 col-form-label">Username</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="text" placeholder="Username" name="username" id="username" value="{{ $user->username }}">
                                </div>
                            </div>
                            <!-- end row -->

                            <div class="row mb-3">
                                <label for="email" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="email" placeholder="Email" name="email" id="email" value="{{ $user->email }}">
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <label for="phone" class="col-sm-2 col-form-label">Phone</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="text" placeholder="Phone" name="phone" id="phone" value="{{ $user->phone }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="state_id" class="col-sm-2 col-form-label">State</label>
                                <div class="col-sm-10">
                                    <select class="form-select" required name="state_id" id="state_id">

                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ ($state->id==$user->state_id) ? 'selected' : '' }}>{{ $state->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <!-- end row -->

                            <div class="row mb-3">
                                <label for="region_id" class="col-sm-2 col-form-label">Region</label>
                                <div class="col-sm-10">
                                    <select class="form-select" required name="region_id" id="region_id">

                                        <option value="">Select Region</option>
                                        @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ ($region->id==$user->region_id) ? 'selected' : '' }}>{{ $region->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <label for="roles" class="col-sm-2 col-form-label">Assign Roles</label>
                                <div class="col-sm-10">
                                    <select class="form-select" required name="roles" id="roles">

                                        <option value="">Select One Role</option>
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <!-- end row -->

                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Update User">

                        </form>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('select[name="state_id"]').on('change', function(){
            var state_id = $(this).val();
           
            if(state_id){
                $.ajax({
                    url: "{{ url('/region/ajax') }}/"+state_id, 
                    type: "GET",
                    dataType: "json",
                    success: function(data){
                        $('select[name="region_id"]').html('');
                        var d = $('select[name="region_id"]').empty();
                        $.each(data, function(key,value){
                            $('select[name="region_id"]').append('<option value="'+value.id+'">'+value.name +'</option>' )
                        });
                    },

                });
            }else{
                alert('danger');
            }
        });
    });
</script>
@endsection