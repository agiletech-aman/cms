@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="{{ asset('backend/assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}"
    rel="stylesheet" />
<link href="{{ asset('backend/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<style>
    table.dataTable.nowrap th,
    table.dataTable.nowrap td {
        white-space: normal;
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Alert log </h4>



                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Alert log</h4><br><br>

                        <table id="example1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Complaint ID</th>
                                    <th>Complaint Type</th>
                                    <th>Complaint Status</th>
                                    <th>Status Change by</th>
                                    <th>View</th>
                                    <!--<th>Created At</th>-->

                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp

                                @foreach($complaintStatuses as $status)
                                <tr>
                                    <td>{{ $i++ }}</td>

                                    {{-- ✅ Link now uses complaint_unique_id --}}
                                    <td>
                                         @php
        $url = null;
        if (!is_null($status->complaintID)) {
            if ($status->complaint_type == 1) {
                $url = route('detail.complaint', $status->complaintID);
            } elseif ($status->complaint_type == 2) {
                $url = route('detail.old.complaint', $status->complaintID);
            } elseif ($status->complaint_type == 3) {
                $url = route('detail.incident', $status->complaintID);
            }
        }
    @endphp
                                       
                                        @if($url)
        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer">
            {{ $status->complaint_unique_id ?? 'N/A' }}
        </a>
    @else
        {{ $status->complaint_unique_id ?? 'N/A' }}
    @endif
                                    </td>

                                    <td>
                                        @php
                                        if ($status->complaint_type == 1) {
                                        $complaint_type = 'New Infrastructure Complaint';
                                        } elseif ($status->complaint_type == 2) {
                                        $complaint_type = 'Old Infrastructure Complaint';
                                        } elseif ($status->complaint_type == 3) {
                                        $complaint_type = 'Incident';
                                        }
                                        @endphp
                                        {{ $complaint_type }}
                                    </td>

                                    <td>
                                        @php
                                        if ($status->complaint_status == 0) {
                                        $complaint_status = 'Open';
                                        } elseif ($status->complaint_status == 1) {
                                        $complaint_status = 'Inprogress';
                                        } elseif ($status->complaint_status == 2) {
                                        $complaint_status = 'Resolved';
                                        } elseif ($status->complaint_status == 3) {
                                        $complaint_status = 'Duplicate';
                                        } elseif ($status->complaint_status == 4) {
                                        $complaint_status = 'Closed';
                                        }
                                        @endphp
                                        {{ $complaint_status }}
                                    </td>

                                    <td>{{ $status->username }}</td>

                                    <td>
                                        @if($status->view == '0')
                                        <a href="{{ route('read.alert.log', $status->id) }}"
                                            class="btn btn-primary rounded-pill waves-effect waves-light">Read</a>
                                        @else
                                        <a href="{{ route('unread.alert.log', $status->id) }}"
                                            class="btn btn-primary rounded-pill waves-effect waves-light">Unread</a>
                                        @endif
                                    </td>

                                   <!-- <td>{{ \Carbon\Carbon::parse($status->created_at)->format('d-m-Y H:i:s') }}</td>-->
                                </tr>
                                @endforeach




                            </tbody>
                        </table>

                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div>
</div>
<script src="{{ asset('backend/assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<script src="{{ asset('backend/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<script src="https://cdn.datatables.net/select/1.4.0/js/dataTables.select.min.js"></script>

<script type="text/javascript">
    
   /* $("#example1").DataTable({
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>",
            },
        },
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass(
                "pagination-rounded"
            );

        },
        "aaSorting": [],
        "scrollX": true, 
        "responsive": false, 
        "lengthChange": true,
        "autoWidth": false,
        "buttons": [{
            extend: "excel",
            text: "Excel",
            filename: "alert_logs",
            exportOptions: {
                columns: [0, 1, 2, 3, 4]
            }
        }, {
            extend: "pdfHtml5",
            text: "PDF",
            filename: "alert_logs",
            orientation: 'landscape', // Optional: set the orientation to landscape
            pageSize: 'A4', // Optional: set the page size to A4
            exportOptions: {
                columns: [0, 1, 2, 3, 4]
            }
        }],
        
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');*/
    $("#example1").DataTable({
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>",
            },
        },
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass(
                "pagination-rounded"
            );

        },
        "aaSorting": [],
        "responsive": false,
        "scrollX": true,
        "lengthChange": true,
        "autoWidth": false,

    });
</script>




@endsection