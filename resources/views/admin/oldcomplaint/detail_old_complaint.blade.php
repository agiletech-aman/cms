@extends('admin.admin_master')
@section('admin')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />

    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Old CCTV Complaint Details </h4>

                        <div class="page-title-right">

                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">

                            <!-- ch -->

                            @if ($errors->any())
                                    <div class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif



                            <!-- ch -->
                                <div class="col-6">
                                    <h4 class="card-title">COMPLAINT_ID# {{ $complaint->complaint_id }}</h4>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('all.old.complaint') }}" class="float-end">Back to Complaint</a>
                                </div>
                                <div class="row mt-3">
                                    @if ($errors->has('comment_files.*') || $errors->has('comment'))
                                        <div class="alert alert-danger">
                                            @foreach (array_merge($errors->get('comment'), \Illuminate\Support\Arr::flatten($errors->get('comment_files.*'))) as $message)
                                                <p>{{ $message }}</p>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if ($errors->has('complaint_image.*'))
                                        <div class="alert alert-danger">
                                            @foreach ($errors->get('complaint_image.*') as $messages)
                                                @foreach ($messages as $message)
                                                    <p>{{ $message }}</p>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="col-6">
                                        <h6><strong>Subject: </strong>{{ $complaint->subject }} </h6>
                                    </div>
                                    <div class="col-6">
                                        <h6><strong>Category/Item: </strong>
                                            {{ optional($complaint['category'])['name'] ?? 'N/A' }}
                                        </h6>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <p><strong>Description: </strong>{{ $complaint->description }} </p>
                                    </div>
                                </div>
                                <div class="row">


                                    <div class="col-4">
                                        <p><strong>Raised By: </strong>
                                            @if($complaint->raisedBy)
                                                <a href="#">{{ $complaint->raisedBy->name }}</a>
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-4">
                                        <p><strong>Created At: </strong>{{ $complaint->created_at->format('d-m-Y H:i:s') }}
                                        </p>
                                    </div>

                                </div>
                                <div class="row">

                                    <div class="col-4">
                                        <p><strong>Status: </strong>
                                            @if($complaint->complaint_status == 0)
                                                Open
                                            @elseif($complaint->complaint_status == 1)
                                                Inprogress
                                            @elseif($complaint->complaint_status == 2)
                                                Resolved
                                            @elseif($complaint->complaint_status == 3)
                                                Duplicate
                                            @elseif($complaint->complaint_status == 4)
                                                Closed
                                            @endif
                                        </p>
                                    </div>
                                    @if($complaint->complaint_status == 1)
                                        @if(!empty($complaint->visit_date))
                                            <div class="col-4">
                                                <p><strong>Visit Date: </strong>
                                                    {{ \Carbon\Carbon::parse($complaint->visit_date)->format('d-m-Y H:i:s') }}
                                                </p>
                                            </div>
                                        @endif
                                    @elseif($complaint->complaint_status == 2)
                                        @if(!empty($complaint->resolved_date))
                                            <div class="col-4">
                                                <p><strong>Resolved Date: </strong>
                                                    {{ \Carbon\Carbon::parse($complaint->resolved_date)->format('d-m-Y H:i:s') }}
                                                </p>
                                            </div>
                                        @endif
                                    @elseif($complaint->complaint_status == 4)
                                        @if(!empty($complaint->closed_date))
                                            <div class="col-4">
                                                <p><strong>Closed Date: </strong>
                                                    {{ \Carbon\Carbon::parse($complaint->closed_date)->format('d-m-Y H:i:s') }}
                                                </p>
                                            </div>
                                        @endif
                                    @endif

                                    @if(!empty($complaint->remark))
                                        <div class="col-4">
                                            <p><strong>Remark: </strong> {{ $complaint->remark }}</p>
                                        </div>
                                    @endif
                                    <div class="col-4">
                                        @if(!empty($complaint->complaint_image))
                                            <a href="{{ asset($visit->certificate_image) }}" download target="_blank"
                                                rel="noopener noreferrer"><img src="{{ asset($complaint->complaint_image) }}"
                                                    class="img-thumbnail" alt="profile-image"></a>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        @if($complaint->complaint_status != 4)
                                            @if(($complaint->user_id == Auth::user()->id) || (Auth::user()->hasAnyRole(['Railtel', 'SuperAdmin', 'RegionalManager', 'Vendor']) && $complaint->complaint_status != 2))

                                                <button type="button" class="btn btn-primary waves-effect waves-light"
                                                    data-bs-toggle="modal" data-bs-target=".update-complaint-status">Update
                                                    Complaint Status</button>
                                            @endif
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <h3 class="card-title">Comments</h4>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="float-end btn btn-primary waves-effect waves-light"
                                        data-bs-toggle="modal" data-bs-target=".add-comment">Add Comment</button>
                                </div>
                            </div>
                            <hr>
                            <div class="row mt-3">


                                @php
                                    $comment = App\Models\OldComment::where('complaint_id', $complaint->id)->latest()->get();

                                @endphp
                                @if($comment->count() > 0)
                                    @foreach($comment as $item)
                                        <div class="text-reset notification-item">
                                            <div class="d-flex">
                                                <img src="{{ (!empty($adminData->profile_image)) ? url('upload/admin_images/' . $adminData->profile_image) : url('upload/no_image.jpg')   }}"
                                                    class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                <div class="flex-1">
                                                     @if($item->user->hasRole(['SuperAdmin','Railtel', 'Vendor']))
                                                        <!--<p class="mb-1">{{ $item->user->name }}</p>-->
                                                        <p class="mb-1">Railtel Team</p>
                                                    @else
                                                   <p class="mb-1">{{ $item->user->name }}</p>
                                                   @endif

                                                    <div class="font-size-12 text-muted">
                                                        <h6 class="mb-1">{{ $item->comment }}</h6>
                                                       <p class="text-muted mb-1">
    @if($item->comment === 'This complaint is Resolved' && !empty($complaint->resolved_date))
        {{ \Carbon\Carbon::parse($complaint->resolved_date)->format('d-m-Y H:i:s') }}
    @elseif($item->comment === 'This complaint is Closed' && !empty($complaint->closed_date))
        {{ \Carbon\Carbon::parse($complaint->closed_date)->format('d-m-Y H:i:s') }}
    @else
        {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i:s') }}
    @endif
</p>
                                                        @if(!empty($item->remark))
                                                            <h5><strong>Remark: </strong> {{ $item->remark }}</h5>
                                                        @endif
                                                        @php
                                                            $files = App\Models\OldComplaintImage::where('comment_id', $item->id)->latest()->get();
                                                        @endphp

                                                          @if($files->count() > 0)
                                                           
                                                        @foreach($files as $file)
    @php
        $fileUrl = url($file->complaint_image);
        $extension = strtolower(pathinfo($file->complaint_image, PATHINFO_EXTENSION));
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    @endphp

    @if(in_array($extension, $imageExtensions))
        <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="d-inline-block me-2 mb-2 text-center">
            <img src="{{ $fileUrl }}" alt="Image" class="rounded" style="width: 120px;">
            <p class="mb-0"><i class="fas fa-eye"></i> View Image</p>
        </a>
    @elseif($extension === 'pdf')
        <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="d-block mb-2">
            <i class="fas fa-file-pdf fa-3x text-danger"></i>
            <p class="mb-0">View PDF</p>
        </a>
    @else
        <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="d-block mb-2">
            <i class="fas fa-file fa-3x text-secondary"></i>
            <p class="mb-0">Download File</p>
        </a>
    @endif
@endforeach

                                                        
                                                        
                                                        
                                                            @endif



                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <hr>
                                    @endforeach
                                @else
                                    <div style="padding: 15px;max-height: 35vh;overflow-y: auto;">
                                        <p>No Comment</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div>
        </div>

        <div class="modal fade update-complaint-status" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Complaint Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('update.old.complaint.status', $complaint->id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @if($complaint->user_id == Auth::user()->hasAnyRole(['Railtel', 'Vendor']))

                                <div class="row">
                                    <div class="col-md-10 mb-3">
                                        <label>Complaint Status</label> <code>*</code>
                                        <select class="form-select" name="complaint_status" id="complaint_status" required
                                            data-minDate="{{ $complaint->complaint_status != 2 ? \Carbon\Carbon::parse($complaint->created_at)->format('m/d/Y') : \Carbon\Carbon::parse($complaint->resolved_date)->format('m/d/Y') }}">
                                            <option value="" selected="" hidden>Select Complaint Status</option>
                                            <option value="1">In Progress</option>
                                            <option value="2">Resolved</option>
                                        </select>
                                    </div>
                                    <div class="row" id="visit_div"  style="display:none;">
                                        <div class="col-md-12 mb-3">
                                            <label>Visit Date</label><code>*</code>
                                            <input type="text" class="form-control" name="visit_date" id="visit_date"
                                                autocomplete="off" required>
                                        </div>
                                    </div>

                                    <div class="row" id="resolved_div"  style="display:none;">
                                        <div class="col-md-12 mb-3">
                                            <label>Resolved Date</label><code>*</code>
                                            <input type="text" class="form-control" name="resolved_date" id="resolved_date"
                                                required autocomplete="off">
                                        </div>
                                    </div>

                                    <!-- <div class="col-md-10 mb-3" id="complaint_image_div">
                                                <label>Complaint Image</label>
                                                <div class="input-group">
                                                    <input type="file" class="form-control" name="complaint_image[]" multiple=""
                                                        id="complaint_image" accept=".jpeg,.jpg,.png,.gif,.svg">
                                                </div>
                                            </div> -->


                                    <div class="col-md-10 mb-3" id="complaint_files_div">
                                        <label>Complaint Files (Images/PDFs)</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control" name="complaint_files[]" multiple=""
                                                id="complaint_files" accept=".jpeg,.jpg,.png,.gif,.svg,.pdf">
                                        </div>
                                        <small class="text-muted">Allowed file types: JPEG, JPG, PNG, GIF, SVG, PDF (Max size:
                                            1MB)</small>
                                    </div>


                                    <div class="row" id="remark_div">
                                        <div class="col-md-12 mb-3">
                                            <label>Remark</label>
                                            <textarea class="form-control" name="remark" id="remark" rows="3"
                                                disabled></textarea>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-10 mb-3">
                                        <label>Complaint Status</label> <code>*</code>
                                        <select class="form-select" name="complaint_status" id="complaint_status" required
                                            data-minDate="{{ $complaint->complaint_status != 2 ? \Carbon\Carbon::parse($complaint->created_at)->format('Y-m-d\TH:i:s') : \Carbon\Carbon::parse($complaint->resolved_date)->format('Y-m-d\TH:i:s') }}">
                                            <option value="" selected="" hidden>Select Complaint Status</option>
                                            <option value="4">Closed</option>
                                            <option value="0">ReOpen</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="row" id="closed_div">
                                    <div class="col-md-12 mb-3">
                                        <label>Closed Date</label><code>*</code>
                                        <input type="text" class="form-control" name="closed_date" id="closed_date" required
                                            disabled>
                                    </div>
                                </div>
                                <div class="row" id="remark_div">
                                    <div class="col-md-12 mb-3">
                                        <label>Remark</label>
                                        <textarea class="form-control" name="remark" id="remark" rows="3" disabled></textarea>
                                    </div>
                                </div>
                            @endif
                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Complaint">

                        </form>


                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->



        <div class="modal fade add-comment" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('store.old.comment', $complaint->id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label>Message</label>
                                    <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>
                                </div>
                            </div>
                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Add Comment">

                        </form>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js">
        </script>


        <script>
            var now = new Date();

            // Function to parse date from the data attribute
            function parseDate(dateString) {
                if (dateString) {
                    return new Date(dateString);
                }
                return null;
            }
            // Initialize datetimepicker for closed_date
            $("#closed_date").datetimepicker({
                format: 'd-m-Y H:i:s',
                onShow: function (ct) {
                    var minDateTime = parseDate($('#complaint_status').data('mindate'));
                    if (minDateTime) {
                        var minDate = new Date(minDateTime.getFullYear(), minDateTime.getMonth(), minDateTime.getDate());
                        this.setOptions({
                            minDate: minDate,
                            maxDate: now
                        });
                    }
                },
                onChangeDateTime: function (dp, $input) {
                    var minDateTime = parseDate($('#complaint_status').data('mindate'));
                    if (minDateTime) {
                        // If selected date matches minDate, apply minTime restriction
                        var selectedDate = new Date(dp.getFullYear(), dp.getMonth(), dp.getDate());
                        var minDate = new Date(minDateTime.getFullYear(), minDateTime.getMonth(), minDateTime.getDate());

                        if (selectedDate.getTime() === minDate.getTime()) {
                            this.setOptions({
                                minTime: minDateTime.toTimeString().split(' ')[0] // Set minTime as HH:mm:ss
                            });
                        } else {
                            this.setOptions({
                                minTime: false // Remove minTime restriction for other dates
                            });
                        }
                    }
                }
            });

            $("#visit_date").datetimepicker({
                format: 'd-m-Y H:i:s',
                onShow: function (ct) {
                    var minDateTime = parseDate($('#complaint_status').data('mindate'));
                    if (minDateTime) {
                        var minDate = new Date(minDateTime.getFullYear(), minDateTime.getMonth(), minDateTime.getDate());
                        this.setOptions({
                            minDate: minDate,
                            maxDate: now
                        });
                    }
                },
                onChangeDateTime: function (dp, $input) {
                    var minDateTime = parseDate($('#complaint_status').data('mindate'));
                    if (minDateTime) {
                        var selectedDate = new Date(dp.getFullYear(), dp.getMonth(), dp.getDate());
                        var minDate = new Date(minDateTime.getFullYear(), minDateTime.getMonth(), minDateTime.getDate());

                        if (selectedDate.getTime() === minDate.getTime()) {
                            this.setOptions({
                                minTime: minDateTime.toTimeString().split(' ')[0]
                            });
                        } else {
                            this.setOptions({
                                minTime: false
                            });
                        }
                    }
                }
            });


            // Initialize datetimepicker for resolved_date
            $("#resolved_date").datetimepicker({
                format: 'd-m-Y H:i:s',
                onShow: function (ct) {
                    var minDateTime = parseDate($('#complaint_status').data('mindate'));
                    if (minDateTime) {
                        var minDate = new Date(minDateTime.getFullYear(), minDateTime.getMonth(), minDateTime.getDate());
                        this.setOptions({
                            minDate: minDate,
                            maxDate: now
                        });
                    }
                },
                onChangeDateTime: function (dp, $input) {
                    var minDateTime = parseDate($('#complaint_status').data('mindate'));
                    if (minDateTime) {
                        // If selected date matches minDate, apply minTime restriction
                        var selectedDate = new Date(dp.getFullYear(), dp.getMonth(), dp.getDate());
                        var minDate = new Date(minDateTime.getFullYear(), minDateTime.getMonth(), minDateTime.getDate());

                        if (selectedDate.getTime() === minDate.getTime()) {
                            this.setOptions({
                                minTime: minDateTime.toTimeString().split(' ')[0] // Set minTime as HH:mm:ss
                            });
                        } else {
                            this.setOptions({
                                minTime: false // Remove minTime restriction for other dates
                            });
                        }
                    }
                }
            });
            $(document).ready(function () {
                // Update the datepicker when the complaint status changes
                $('#complaint_status').change(function () {
                    var selectedStatus = $(this).val();
                    var minDate = parseDate($('#complaint_status').data('mindate'));
                    if (selectedStatus == "4") { // Assuming 4 is the status for 'Closed'
                        $("#closed_date").datetimepicker('reset');
                        $("#closed_date").datetimepicker('setOptions', {
                            minDate: minDate,
                            maxDate: now
                        });
                        $("#closed_date").prop('disabled', false); // Enable the date input
                    } else {
                        $("#closed_date").prop('disabled', true); // Disable the date input for other statuses
                    }
                });

            });


            $('#complaint_status').change(function () {
                var selectedStatus = $(this).val();

                $('#closed_div, #resolved_div, #visit_div, #remark_div, #complaint_image_div').hide();
                $('#closed_date, #resolved_date, #visit_date, #remark').prop('disabled', true);

                if (selectedStatus == '4') { // Closed
                    $('#closed_div').show();
                    $('#closed_date').prop('disabled', false);
                    $('#remark_div').show();
                    $('#remark').prop('disabled', false);
                } else if (selectedStatus == '2') { // Resolved
                    $('#resolved_div').show();
                    $('#resolved_date').prop('disabled', false);
                    $('#complaint_image_div').show();
                    $('#remark_div').show();
                    $('#remark').prop('disabled', false);
                } else if (selectedStatus == '1') { // In Progress
                    $('#visit_div').show();
                    $('#visit_date').prop('disabled', false);
                    $('#remark_div').show();
                    $('#remark').prop('disabled', false);
                    $('#resolved_div, #complaint_image_div').hide();
                }
            });
        </script>

@endsection