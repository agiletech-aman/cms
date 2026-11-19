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
                    <h4 class="mb-sm-0">Edit Complaint</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('complaint.list'))
                        <a href="{{ route('all.new.complaint') }}" class="btn btn-info">View New Infra Complaints</a>
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

                        <h4 class="card-title">Edit Complaint</h4><br><br>
                        <form method="POST" action="{{ route('update.complaint', $complaint->id) }}" enctype="multipart/form-data">
                            @csrf



                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">Item/Category</label>
                                <div class="col-sm-5">
                                    <select class="form-select" required name="cat_id" id="cat_id">
                                        @foreach($category as $cat)
                                        <option value="{{ $cat->id }}" {{ $cat->id == $complaint->cat_id ? 'selected' : ''  }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-5" id="itemDiv" style="{{ count($items) == 0 ? 'display: none;' : '' }}">
                                    <select class="form-select select2" required name="item_id" id="item_id" data-placeholder="Select Item" {{ count($items) == 0 ? 'disabled' : 'required' }}>
                                        <option value="">Select Item</option>
                                        @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ ($item->id==$complaint->item_id) ? 'selected' : '' }}>{{ $item->name }}</option>
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
                                <label for="created_at" class="col-sm-2 col-form-label">Created At</label>
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
    $(document).ready(function(){
        $('select[name="cat_id"]').on('change', function(){
            var cat_id = $(this).val();
            if(cat_id){
                $.ajax({
                    url: "{{ url('/item/ajax') }}/"+cat_id, 
                    type: "GET",
                    dataType: "json",
                    success: function(data){
                        $('select[name="item_id"]').html('');
                        var d = $('select[name="item_id"]').empty();
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
            }  else {
                $('#itemDiv').hide(); // Hide the item selection div if no category selected
            $('select[name="item_id"]').prop('disabled', true); // Disable the dropdown
            $('select[name="item_id"]').prop('required', false); // Make it not required
               
            }
        });
    });
</script>
@endsection