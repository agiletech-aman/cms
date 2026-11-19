@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="{{ asset('backend/assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
<link href="{{ asset('backend/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<style>
    table.dataTable.nowrap th,
    table.dataTable.nowrap td {
        white-space: normal;
    }
    .dataTables_wrapper {
        overflow-x: auto;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Incident Visit</h4>
                  
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">incidents List</h4>
                        <div class="table-responsive">
                            <table id="incident-table" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">Sl</th>
                                        <th>Sites</th>
                                        <th>incident ID</th>
                                        <th>Subject</th>
                                        <th>Visit Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allincidents as $key => $item)
                                        @if($item->visit_date)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $item->user->username ?? 'N/A' }}</td>
                                                <td>{{ $item->incident_id }}</td>
                                                <td>{{ $item->subject }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->visit_date)->format('d-m-Y H:i:s') }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('backend/assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#incident-table').DataTable({
        "processing": true,
        "responsive": true,
        "ordering": true,
        "pageLength": 10,
        "language": {
            "paginate": {
                "previous": "<i class='mdi mdi-chevron-left'>",
                "next": "<i class='mdi mdi-chevron-right'>"
            }
        },
        "drawCallback": function() {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
        },
        "columnDefs": [
            { "orderable": false, "targets": [0] }
        ]
    });
});
</script>
@endsection