@extends('admin.admin_master')
@section('admin')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <center> <br><br>
                    <img class="rounded-circle header-profile-user"
     src="{{ (!empty($adminData->profile_image) 
            && file_exists(public_path('upload/admin_images/'.$adminData->profile_image)))
            ? asset('upload/admin_images/'.$adminData->profile_image)
            : asset('upload/no_image.jpg') }}"
     alt="Header Avatar"
     onerror="this.src='{{ asset('upload/no_image.jpg') }}'">
                    </center>
                    <div class="card-body">
                        <h4 class="card-title">Name : {{ $adminData->name }} </h4><hr>
                        <h4 class="card-title">Email : {{ $adminData->email }} </h4><hr>
                        <h4 class="card-title">User Name : {{ $adminData->username }} </h4><hr>
                        <a href="{{ route('edit.profile') }}" class="btn btn-info btn-rounded waves-effect waves-light">Edit Profile</a>
                       
                    </div>
                </div>
            </div>


        </div>

        <!-- end row -->
    </div>

</div>
@endsection