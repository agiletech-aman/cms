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

                        <h4 class="card-title">Change Password</h4><br><br>
                        <form method="POST" action="{{ route('updateuser.password') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $user->id }}">
                            <div class="row mb-3">
                                <label for="password" class="col-sm-2 col-form-label">New Password</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="password" placeholder="Password" name="password" >
                                </div>
                            </div>
                            <!-- end row -->
                              <!-- Confirm Password Field -->
                            <div class="row mb-3">
                                <label for="password_confirmation" class="col-sm-2 col-form-label">Confirm Password</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="password" name="password_confirmation" placeholder="Confirm Password">
                                    
                                </div>
                            </div>


                        

                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Password">

                        </form>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

@endsection