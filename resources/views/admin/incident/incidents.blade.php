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
                    <h4 class="mb-sm-0">Incidents</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('incident.add'))
                            <a href="{{ route('add.complaint') }}" class="btn btn-info">Add Incident</a>
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

                        <h4 class="card-title">Incidents</h4><br><br>

                        <table id="example1" class="table table-bordered dt-responsive"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Region</th>
                                    <th>Sites</th>
                                    <th>Incident ID</th>
                                    <th>Subject</th>
                                    <th>Description</th>
                                    <th>Incident Status</th>
                                    <th>Created At</th>
                                    
                                    @if(Auth::user()->can('incident.edit') or Auth::user()->can('incident.delete') or Auth::user()->can('incident.detail') or Auth::user()->can('incident.closed'))
                                        <th>Action</th>
                                    @endif
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
                                                            if ($item->incident_status == 0 && $isOlderThan48Hours) {
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
                                                                    if ($item->incident_status == 0) {
                                                                        $incident_st = 'Open';
                                                                    } elseif ($item->incident_status == 1) {
                                                                        $incident_st = 'Cancelled';
                                                                    } elseif ($item->incident_status == 2) {
                                                                        $incident_st = 'Resolved';
                                                                    } elseif ($item->incident_status == 3) {
                                                                        $incident_st = 'Duplicate';
                                                                    } elseif ($item->incident_status == 4) {
                                                                        $incident_st = 'Closed';
                                                                    }


                                                                @endphp
                                                                {{ $incident_st }}
                                                            </td>
                                                            <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                                            
                                                            @if(Auth::user()->can('incident.edit') or Auth::user()->can('incident.delete') or Auth::user()->can('incident.detail'))
                                                                <td>
                                                                    @if(Auth::user()->can('incident.edit'))


                                                                        @if(($item->incident_status != 2 && $item->incident_status != 4) && (Auth::user()->hasRole('SuperAdmin') || $item->user_id == Auth::user()->id))
                                                                            <a href="{{ route('edit.incident', $item->id) }}" class="btn btn-info sm"
                                                                                title="Edit Data"><i class="fas fa-edit"></i></a>
                                                                        @endif
                                                                    @endif
                                                                    <!-- @if(Auth::user()->can('incident.edit') && (Auth::user()->hasRole('SuperAdmin') || $item->user_id == Auth::user()->id))
                                                                    @if($item->incident_status != 2 || $item->incident_status != 4)
                                                                    <a href="{{ route('edit.incident', $item->id) }}" class="btn btn-info sm" title="Edit Data"><i class="fas fa-edit"></i></a>
                                                                    @endif
                                                                    @endif -->

                                                                    @if(Auth::user()->can('incident.detail'))

                                                                     <a href="{{ route('detail.incident', $item->id) }}" class="btn btn-success sm" title="Detail Incident"><i class="fas fa-eye"></i></a>

                                                                    @endif
                                                                    @if(Auth::user()->can('incident.delete'))
                                                                        <a href="{{ route('delete.incident', $item->id) }}" class="btn btn-danger sm"
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