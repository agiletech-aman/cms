@extends('admin.admin_master')
@section('title', 'Report Download')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="{{ asset('backend/assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
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
                    <h4 class="mb-sm-0">Search Old InfrastructureComplaints</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('incident.all'))
                        <a href="{{ route('all.incident') }}" class="btn btn-info">All Incident</a>
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
                        <h4 class="card-title">Search Incident</h4><br>
                        <form method="POST" action="{{ route('search.incident') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="">Choose Dates</label>
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control" name="start" placeholder="Start Date" />
                                        <input type="text" class="form-control" name="end" placeholder="End Date" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="">Status</label>
                                    <select class="select2 form-control" multiple="multiple" name="incident_status[]" id="incident_status" data-placeholder="Select Incident Status">
                                        <option value="0">Open</option>
                                        <option value="1">Inprogess</option>
                                        <option value="2">Resolved</option>
                                        <option value="4">Closed</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="">Enter Incident ID</label>
                                    <input type="text" class="form-control" name="incident_id" id="incident_id" placeholder="Enter Incident ID">
                                </div>
                            </div>
                            <div class="row mb-3">

                                @if(!Auth::user()->hasRole(['WarehouseManager','RegionalManager']))
                                <div class="col-md-4">
                                    <label for="">Region</label>
                                    <select class="select2 form-control" multiple="multiple" name="region_id[]" id="region_id" data-placeholder="Select Region">

                                        @foreach($regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                @if(!Auth::user()->hasRole(['WarehouseManager']))
                                <div class="col-md-4">
                                    <label for="">Warehouse</label>
                                    <select class="select2 form-control" multiple="multiple" name="warehouse_id[]" id="warehouse_id" data-placeholder="Select Warehouse">
                                        @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->username }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                            <div class="text-center">
                                <input type="submit" class="btn btn-info waves-effect waves-light" value="Search Incident">
                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">Incidents</h4><br><br>

                        <table id="example1" class="table table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Region</th>
                                    <th>Sites</th>
                                    <th>Incident ID</th>
                                    <th>Subject</th>
                                    <th>Description</th>
                                    <th>Incident Status</th>
                                    <th>In Progress Remark</th>
                                    <th>In Progress File</th>
                                    <th>Resolved Remark</th>
                                    <th>Resolved File</th>
                                    <th>Closed Remark</th>
                                    <th>Closed File</th>
                                    <th>Created At</th>
                                    <th>Resolved Date</th>
                                    <th>Closed Date</th>
                                    
                                </tr>
                            </thead>

                            @php
                            $i = 1;
                            @endphp

                            @foreach($incidents as $item)
                            @php
                            $created = $item->created_at;
                            $createdDate = \Carbon\Carbon::parse($created);
                            $isOlderThan48Hours = \Carbon\Carbon::now()->diffInHours($createdDate) > 48;

                            $bgColor = '';
                            if($item->incident_status == 0 && $isOlderThan48Hours) {
                            $bgColor = "style=color:#f70707";
                            } 
                            @endphp

                            <tr {{ $bgColor }}>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item['region']['name']  }}</td>
                                <td>{{ $item['user']['username'] }}</td>
                                <td>{{ $item->incident_id }}</td>
                                
                                <td>{{ $item->subject }}</td>
                                <td>{{ $item->description }}</td>
                                <td>
                                    @php
                                    if($item->incident_status==0)
                                    { $incident_st='Open';
                                    }
                                    elseif($item->incident_status==1)
                                    { $incident_st='Inprogess';
                                    }
                                    elseif($item->incident_status==2)
                                    { $incident_st='Resolved';
                                    }
                                    elseif($item->incident_status==3)
                                    { $incident_st='Duplicate'; }
                                    elseif($item->incident_status==4)
                                    { $incident_st='Closed';
                                    }


                                    @endphp
                                    {{ $incident_st }}
                                </td>
                                @include('admin.report.partials.complaint_remarks_and_files', [
                                    'item' => $item,
                                    'historyFileColumn' => 'incident_image'
                                ])
                                <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                <td>
                                       {{ in_array($item->incident_status, [2, 4])
    ? ($item->resolved_date || $item->closed_date
        ? \Carbon\Carbon::parse($item->resolved_date ?? $item->closed_date)->format('d-m-Y H:i:s')
        : 'N/A')
    : 'N/A'
}}
                               
                              
                                </td>

                                <td>
                                    {{ $item->incident_status == 4 ? \Carbon\Carbon::parse($item->closed_date)->format('d-m-Y H:i:s') : "N/A" }}
                                </td>
                                
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
@php
    $includeRemarkAndFileColumnsInExport = Auth::user()->hasRole('Railtel');
    $reportExportColumns = $includeRemarkAndFileColumnsInExport
        ? range(0, 15)
        : array_merge(range(0, 6), range(13, 15));
@endphp
@include('admin.report.partials.complaint_excel_link_helper', [
    'fileColumnIndexes' => $includeRemarkAndFileColumnsInExport ? [8, 10, 12] : []
])
@if(Auth::user()->can('report.download'))
<script type="text/javascript">
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
        "scrollX": true, 
        "responsive": false, 
        "lengthChange": true,
        "autoWidth": false,
        "buttons": [{
            extend: "excel",
            text: "Excel",
            customize: customizeComplaintExcelLinks,
            filename: function () {

    let statusText = "All";

    let statusMap = {
        "0": "Open",
        "1": "Inprogress",
        "2": "Resolved",
        "4": "Closed"
    };

    let allStatuses = ["0","1","2","4"];

    if (Array.isArray(selectedStatusFromBackend) && selectedStatusFromBackend.length > 0) {

        // convert all to string (important)
        let selected = selectedStatusFromBackend.map(String).sort();
        let all = allStatuses.sort();

        // check if all selected
        let isAllSelected = JSON.stringify(selected) === JSON.stringify(all);

        if (!isAllSelected) {
            let names = selected.map(s => statusMap[s] || "Unknown");
            statusText = names.join("_");
        }
    }

    return "Incident_" + statusText + "_Report_Excel";
},
            // filename: "Incident_Report_Excel",
            exportOptions: {
                columns: @json($reportExportColumns),
                format: {
                    body: complaintReportExcelBody
                }
            }
        }, {
            extend: "pdfHtml5",
            text: "PDF",
            filename: function () {

    let statusText = "All";

    let statusMap = {
        "0": "Open",
        "1": "Inprogress",
        "2": "Resolved",
        "4": "Closed"
    };

    let allStatuses = ["0","1","2","4"];

    if (Array.isArray(selectedStatusFromBackend) && selectedStatusFromBackend.length > 0) {

        // convert all to string (important)
        let selected = selectedStatusFromBackend.map(String).sort();
        let all = allStatuses.sort();

        // check if all selected
        let isAllSelected = JSON.stringify(selected) === JSON.stringify(all);

        if (!isAllSelected) {
            let names = selected.map(s => statusMap[s] || "Unknown");
            statusText = names.join("_");
        }
    }

    return "Incident_" + statusText + "_Report_Pdf";
},
            // filename: "Incident_Report_PDF",
            orientation: 'landscape', // Optional: set the orientation to landscape
            pageSize: 'A4', // Optional: set the page size to A4
            exportOptions: {
                columns: @json($reportExportColumns)
            }
        }],
        columnDefs: [{
            targets: [1, 7, 8, 9, 10, 11, 12, 14, 15],
            visible: false
        }, ],


    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    /*  $("#example1").DataTable({
         scrollY: '280px',
         scrollCollapse: true,
         paging: false,
         "aaSorting": [],
         "scrollX": true, 
        "responsive": false, 
         "lengthChange": false,
         "autoWidth": false,
         "buttons": [{
             extend: "excel",
             text: "Export Complaint Report",
             exportOptions: {
                 columns: @json($reportExportColumns)
             }
         }],
         columnDefs: [{
                 targets: [6, 7, 8, 9, 10, 11, 12, 13, 15],
                 visible: false
             },
             {
                 orderable: false,
                 className: 'select-checkbox',
                 targets: 0
             }
         ],
         select: {
             style: 'os',
             selector: 'td:first-child',
             style: 'multi'
         }
     }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)'); */
</script>
@else
<script type="text/javascript">
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
        "scrollX": true, 
        "responsive": false, 
        "lengthChange": true,
        "autoWidth": false,

        columnDefs: [{
            targets: [1, 6, 7, 8, 9, 10, 11, 12],
            visible: false
        }, ],


    });
</script>
@endif

<script type="text/javascript">
    $('.select2').select2();
    $('#region_id').on("change", function(e) {

        var selectedRegions = $(this).val();

        if (selectedRegions) {
            $.ajax({
                url: "{{ url('/get/user') }}",
                method: 'GET',
                data: {
                    region_id: selectedRegions
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    $('#warehouse_id').html(data.warehouse_users);
                },

            });
        } else {
            alert('danger');
        }
    });
</script>
<script>
    var selectedStatusFromBackend = {!! json_encode($selectedStatus ?? []) !!};
</script>
@endsection
