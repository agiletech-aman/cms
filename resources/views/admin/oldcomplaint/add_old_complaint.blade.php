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
                    <h4 class="mb-sm-0">Add Old CCTV Complaint</h4>

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
                       
                                <form method="POST" action="{{ route('store.old.complaint') }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="infra" id="infra" value="old">
                                    @php
                                    $uid = Auth::user()->id;
                                    $userid = App\Models\User::findOrFail($uid);
                                    if ($userid->hasRole('RegionalManager')) {
                                    $complaint_for = App\Models\User::where('region_id', $userid->region_id)->where('id', '!=', $uid)->get();
                                    @endphp
                                    <div class="row mb-3">
                                        <label for="title" class="col-sm-2 col-form-label">Complaint For</label>
                                        <div class="col-sm-10">
                                            <select class="form-select" required name="complaint_for">
                                                <option value="{{ $uid }}">Regional Office</option>
                                                @foreach($complaint_for as $complaintfor)
                                                <option value="{{ $complaintfor->id }}">{{ $complaintfor->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>


                                    </div>
                                    @php
                                    }
                                    @endphp

                                    <div class="row mb-3">
                                        <label for="title" class="col-sm-2 col-form-label">Category/Item</label>
                                        <div class="col-sm-10">
                                            <select class="form-select" required name="oldcat_id" id="oldcat_id">
                                                <option value="" hidden>Select Category</option>
                                                @foreach($oldcategory as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>


                                    </div>
                                    <div class="row mb-3">
                                        <label for="subject" class="col-sm-2 col-form-label">Subject</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" required type="text" placeholder="Subject" name="subject" id="subject" value="">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="description" class="col-sm-2 col-form-label">Description</label>
                                        <div class="col-sm-10">
                                            <textarea name="description" class="form-control" id="description" cols="30" rows="10"></textarea>
                                        </div>
                                    </div>
<!-- 
                                    <div class="row mb-3">

                                        <label class="col-sm-2 col-form-label">Complaint Date <code>*</code></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="oldcomplaint_date" id="oldcomplaint_date" required placeholder="DD-MM-YYYY H:M:S">
                                        </div>
                                    </div> -->

                                    <input type="submit" class="btn btn-info waves-effect waves-light" value="Add Complaint">

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
    $("#complaint_date").datetimepicker({
        format: 'd-m-Y H:i:s',
        minDate: new Date(new Date().setDate(new Date().getDate() - 3)),
        maxDate: new Date()
    });
    $("#oldcomplaint_date").datetimepicker({
        format: 'd-m-Y H:i:s',
        minDate: new Date(new Date().setDate(new Date().getDate() - 3)),
        maxDate: new Date()
    });
    $("#incident_date").datetimepicker({
        format: 'd-m-Y H:i:s',
        minDate: new Date(new Date().setDate(new Date().getDate() - 3)),
        maxDate: new Date()
    });

    $('.select2').select2();
    $(document).ready(function() {
        $('select[name="cat_id"]').on('change', function() {
            var cat_id = $(this).val();
            if (cat_id) {
                $.ajax({
                    url: "{{ url('/item/ajax') }}/" + cat_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('select[name="item_id"]').html(''); // Clear previous items
                        if (data.length > 0) { // Check if there are any items
                            var d = $('select[name="item_id"]').empty();
                            $.each(data, function(key, value) {
                                $('select[name="item_id"]').append('<option value="' + value.id + '">' + value.name + '</option>');
                            });
                            $('#itemDiv').show(); // Show the item selection div
                            $('select[name="item_id"]').prop('disabled', false); // Enable the dropdown
                            $('select[name="item_id"]').prop('required', true); // Make it required
                        } else {
                            $('#itemDiv').hide(); // Hide the item selection div if no items
                            $('select[name="item_id"]').prop('disabled', true); // Disable the dropdown
                            $('select[name="item_id"]').prop('required', false); // Make it not required
                        }
                    },
                });
            } else {
                $('#itemDiv').hide(); // Hide the item selection div if no category selected
                $('select[name="item_id"]').prop('disabled', true); // Disable the dropdown
                $('select[name="item_id"]').prop('required', false); // Make it not required

            }
        });
    });
</script>
@endsection

