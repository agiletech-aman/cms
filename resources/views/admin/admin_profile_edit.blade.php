@extends('admin.admin_master')
@section('admin')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="card-title">Edit Profile Page</h4>
                            <form method="POST" action="{{ route('store.profile') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="row mb-3">
                                    <label for="name" class="col-sm-2 col-form-label">Name</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" placeholder="Name" name="name" id="name"
                                            value="{{ $editData->name }}">
                                    </div>
                                </div>
                                <!-- end row -->

                                <div class="row mb-3">
                                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="email" placeholder="Email" name="email" id="email"
                                            value="{{ $editData->email }}">
                                    </div>
                                </div>
                                <!-- end row -->


                                <div class="row mb-3">
                                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="text" placeholder="Username" name="username"
                                            id="username" value="{{ $editData->username }}">
                                    </div>
                                </div>
                                <!-- end row -->

                                <div class="row mb-3">
                                    <label for="image" class="col-sm-2 col-form-label">Profile Image</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="file" name="profile_image" id="image">
                                    </div>
                                </div>
                                <!-- end row -->

                                <div class="row mb-3">
                                    <label for="" class="col-sm-2"></label>
                                    <div class="col-sm-10">
                                        
                                         <img id='showImage'class="rounded-circle header-profile-user"
     src="{{ (!empty($adminData->profile_image) 
            && file_exists(public_path('upload/admin_images/'.$editData->profile_image)))
            ? asset('upload/admin_images/'.$editData->profile_image)
            : asset('upload/no_image.jpg') }}"
     alt="Header Avatar"
     onerror="this.src='{{ asset('upload/no_image.jpg') }}'">
                                    </div>
                                </div>

                                <!-- end row -->

                                {{-- 🔴 REMOVE IMAGE OPTION --}}
                                @if(!empty($editData->profile_image))
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label"></label>
                                        <div class="col-sm-10">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remove_profile_image"
                                                    id="remove_profile_image" value="1">
                                                <label class="form-check-label text-danger fw-bold" for="remove_profile_image">
                                                    Remove Profile Image
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Profile">

                            </form>

                        </div>
                    </div>
                </div>


            </div>

            <!-- end row -->
        </div>

    </div>
    <script>
        $(document).ready(function () {
            $('#image').change(function (e) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#showImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files['0']);
            });
        });
    </script>
@endsection