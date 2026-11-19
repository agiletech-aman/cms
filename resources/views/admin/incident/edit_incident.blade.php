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
                    <h4 class="mb-sm-0">Edit Incident</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('incident.list'))
                        <a href="{{ route('all.incident') }}" class="btn btn-info">View incident</a>
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

                        <h4 class="card-title">Edit Incident</h4><br><br>
                        <form method="POST" action="{{ route('update.incident', $incident->id) }}" enctype="multipart/form-data">
                            @csrf

                            <!-- end row -->
                            <div class="row mb-3">
                                <label for="subject" class="col-sm-2 col-form-label">Subject</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="text" placeholder="Subject" name="subject" id="subject" value="{{ $incident->subject }}">
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <label for="description" class="col-sm-2 col-form-label">Description</label>
                                <div class="col-sm-10">
                                    <textarea name="description" class="form-control" id="description" cols="30" rows="10">{{ $incident->description }}</textarea>
                                </div>
                            </div>
                            <!-- end row -->
                            @if(Auth::user()->hasRole('SuperAdmin'))
                            <div class="row mb-3">
                                <label for="subject" class="col-sm-2 col-form-label">Created At</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="text" placeholder="Created Date" name="created_at" id="created_at" value="{{ $incident->created_at }}">
                                </div>
                            </div>
                            @endif
                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Incident">

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