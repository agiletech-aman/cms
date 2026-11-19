@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="{{ asset('backend/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit Old Infrastructure Complaint</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('oldcomplaint.list'))
                        <a href="{{ route('all.old.complaint') }}" class="btn btn-info">View Old Infra Complaint</a>
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

                        <h4 class="card-title">Edit Old Infrastructure Complaint</h4><br><br>
                        <form method="POST" action="{{ route('update.old.complaint', $complaint->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <label for="oldcat_id" class="col-sm-2 col-form-label">Item/Category</label>
                                <div class="col-sm-10">
                                    <select class="form-select" required name="oldcat_id" id="oldcat_id">
                                        @foreach($category as $cat)
                                        <option value="{{ $cat->id }}" {{ $cat->id == $complaint->cat_id ? 'selected' : ''  }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                               

                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <label for="subject" class="col-sm-2 col-form-label">Subject</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="text" placeholder="Subject" name="subject" id="subject" value="{{ $complaint->subject }}">
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <label for="description" class="col-sm-2 col-form-label">Description</label>
                                <div class="col-sm-10">
                                    <textarea name="description" class="form-control" id="description" cols="30" rows="10">{{ $complaint->description }}</textarea>
                                </div>
                            </div>
                            <!-- end row -->
                            @if(Auth::user()->hasRole('SuperAdmin'))
                            <div class="row mb-3">
                                <label for="subject" class="col-sm-2 col-form-label">Created At</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="text" placeholder="Created Date" name="created_at" id="created_at" value="{{ $complaint->created_at }}">
                                </div>
                            </div>
                            @endif
                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Complaint">

                        </form>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
<script src="{{ asset('backend/assets/libs/select2/js/select2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js">
</script>
<script>
     $('.select2').select2();
    $('#created_at').datetimepicker({
        format: 'Y-m-d H:i:s',
        maxDate: new Date(), // Disable future dates and times                          
    });
   
</script>
@endsection