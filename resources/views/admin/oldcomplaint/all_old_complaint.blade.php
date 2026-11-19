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
                        <h4 class="mb-sm-0">Old CCTV Complaints</h4>

                        <div class="page-title-right">
                            @if(Auth::user()->can('complaint.add'))
                                <a href="{{ route('add.old.complaint') }}" class="btn btn-info">Add Old Complaint</a>
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

                            <h4 class="card-title">Old CCTV Complaints</h4><br><br>

                            <table id="example1" class="table table-bordered dt-responsive"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Region</th>
                                        <th>Sites</th>
                                        <th>Complaint ID</th>
                                        <th>Category/Item</th>
                                        <th>Subject</th>
                                        <th>Description</th>
                                        <th>Complaint Status</th>
                                        <th>Created At</th>
                                        <th>Visit Date</th>
                                        <th>Resolved Date</th>
                                        <th>Closed Date</th>
                                        @if(Auth::user()->can('oldcomplaint.edit') or Auth::user()->can('oldcomplaint.delete') or Auth::user()->can('oldcomplaint.detail') or Auth::user()->can('oldcomplaint.closed'))
                                            <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>

                                @php
                                    $i = 1;
                                @endphp

                                @foreach($allcomplaints as $item)

                                                            @php
                                                                $created = $item->created_at;
                                                                $createdDate = \Carbon\Carbon::parse($created);
                                                                $isOlderThan48Hours = \Carbon\Carbon::now()->diffInHours($createdDate) > 48;

                                                                $bgColor = '';
                                                                if ($item->complaint_status == 0 && $isOlderThan48Hours) {
                                                                    $bgColor = "style=color:#f70707";
                                                                }
                                                            @endphp

                                                            <tr {{ $bgColor }}>
                                                                <td>{{ $i++ }}</td>
                                                                <td>{{ $item['region']['name']  }}</td>
                                                                <td>{{ $item['user']['username'] }}</td>

                                                                <td>{{ $item->complaint_id }}</td>
                                                                <td>
                                                                    {{ optional($item['category'])['name'] ?? 'N/A' }}
                                                                </td>

                                                                <td>{{ $item->subject }}</td>
                                                                <td>{{ $item->description }}</td>
                                                                <td>
                                                                    @php
                                                                        if ($item->complaint_status == 0) {
                                                                            $complaint_st = 'Open';
                                                                        } elseif ($item->complaint_status == 1) {
                                                                            $complaint_st = 'Inprogress';
                                                                        } elseif ($item->complaint_status == 2) {
                                                                            $complaint_st = 'Resolved';
                                                                        } elseif ($item->complaint_status == 3) {
                                                                            $complaint_st = 'Duplicate';
                                                                        } elseif ($item->complaint_status == 4) {
                                                                            $complaint_st = 'Closed';
                                                                        }


                                                                    @endphp
                                                                    {{ $complaint_st }}
                                                                </td>
                                                                <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                                                <td>
                                                                    {{ $item->visit_date ? \Carbon\Carbon::parse($item->visit_date)->format('d-m-Y H:i:s') : 'N/A' }}
                                                                </td>
                                                                <td>
                                                                    <!--{{ in_array($item->complaint_status, [2, 4]) ? \Carbon\Carbon::parse($item->resolved_date)->format('d-m-Y H:i:s') : "N/A"  }}-->
                                                                    {{ in_array($item->complaint_status, [2, 4])
    ? ($item->resolved_date || $item->closed_date
        ? \Carbon\Carbon::parse($item->resolved_date ?? $item->closed_date)->format('d-m-Y H:i:s')
        : 'N/A')
    : 'N/A'
}}
                                                                </td>
                                                                <td>
                                                                    {{ $item->complaint_status == 4 ? \Carbon\Carbon::parse($item->closed_date)->format('d-m-Y H:i:s') : "N/A"  }}
                                                                </td>

                                                                @if(Auth::user()->can('oldcomplaint.edit') or Auth::user()->can('oldcomplaint.delete') or Auth::user()->can('oldcomplaint.detail'))
                                                                    <td>
                                                                        @if(Auth::user()->can('oldcomplaint.edit'))


                                                                            @if(($item->complaint_status != 2 && $item->complaint_status != 4) && (Auth::user()->hasRole('SuperAdmin') || $item->user_id == Auth::user()->id))
                                                                                <a href="{{ route('edit.old.complaint', $item->id) }}" class="btn btn-info sm"
                                                                                    title="Edit Data">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </a>
                                                                            @endif
                                                                        @endif
                                                                        <!-- @if(Auth::user()->can('oldcomplaint.edit') && (Auth::user()->hasRole('SuperAdmin') || $item->user_id == Auth::user()->id))
                                                                                                                                                                                                        @if($item->complaint_status != 2 || $item->complaint_status != 4)
                                                                                                                                                                                                        <a href="{{ route('edit.complaint', $item->id) }}" class="btn btn-info sm" title="Edit Data"><i class="fas fa-edit"></i></a>
                                                                                                                                                                                                        @endif
                                                                                                                                                                                                        @endif -->
                                                                        @if(Auth::user()->can('oldcomplaint.detail'))

                                                                            <a href="{{ route('detail.old.complaint', $item->id) }}" class="btn btn-success sm"
                                                                                title="Detail Complaint"><i class="fas fa-eye"></i></a>

                                                                        @endif
                                                                        @if(Auth::user()->can('oldcomplaint.delete'))
                                                                            <a href="{{ route('delete.old.complaint', $item->id) }}" class="btn btn-danger sm"
                                                                                id="delete" title="Delete Data"><i class="fas fa-trash-alt"></i></a>
                                                                        @endif

                                                                    </td>
                                                                @endif
                                                            </tr>
                                @endforeach

                                <tbody>

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
        $("#example1").DataTable({
            pageLength: 100,          // ✅ show 100 rows by default
    lengthMenu: [10, 25, 50, 100, 200, 500], // optional dropdown options
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>",
                },
            },
            drawCallback: function () {
                $(".dataTables_paginate > .pagination").addClass(
                    "pagination-rounded"
                );

            },
            "aaSorting": [],
            "scrollX": true,
            "responsive": false,
            "lengthChange": true,
            "autoWidth": false,

            columnDefs: [{
                targets: [1, 6,],
                visible: false
            },],


        });
    </script>




@endsection