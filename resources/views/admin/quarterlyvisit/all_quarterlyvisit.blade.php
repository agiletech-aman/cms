@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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
                    <h4 class="mb-sm-0">All Quarterly Visit</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('quarterlyVisit.add'))
                            <a href="{{ route('add.quarterlyvisit') }}" class="btn btn-info">Add Quarterly Visit</a>
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

                        <h4 class="card-title">All Quarterly Visits</h4><br><br>

                        <table id="example1" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Region</th>
                                    <th>Sites</th>
                                    <th>Schedule Dates</th>
                                    <th>Visit Phase</th>
                                    <th>Visit Date</th>
                                    <th>Created At</th>
                                    @if(Auth::user()->can('quarterlyVisit.edit') or Auth::user()->can('quarterlyVisit.delete') or Auth::user()->can('quarterlyVisit.detail') or Auth::user()->can('quarterlyVisit.closed'))
                                        <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    $i = 1;
                                @endphp

                                @foreach($allvisits as $item)

                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item['region']['name']  }}</td>
                                        <td>{{ $item['user']['username'] ?? 'ro'.strtolower($item['region']['name']) }}</td>
                                        <td>{{ $item->start_date }} to {{ $item->end_date }}</td>

                                        <td>Phase {{ $item->phase_id }}</td>
                                        <td>{{ !empty($item->visit_date) ? $item->visit_date : 'N/A' }}</td>

                                        <td>{{ $item->created_at->format('d-m-Y H:i:s') }}</td>
                                        @if(Auth::user()->can('quarterlyVisit.edit') or Auth::user()->can('quarterlyVisit.delete') or Auth::user()->can('quarterlyVisit.detail'))
                                            <td>
                                                @if(Auth::user()->can('quarterlyVisit.edit'))

                                                    @if(empty($item->visit_date))

                                                        <a href="{{ route('edit.quarterlyvisit', $item->id) }}" class="btn btn-info sm"
                                                            title="Edit Data"><i class="fas fa-edit"></i></a>
                                                    @endif
                                                @endif
                                                @if(Auth::user()->can('quarterlyVisit.detail'))

                                                    <a href="{{ route('detail.quarterlyvisit', $item->id) }}"
                                                        class="btn btn-success sm" title="Detail Visit"><i
                                                            class="fas fa-eye"></i></a>

                                                @endif
                                                @if(Auth::user()->can('quarterlyVisit.delete'))
                                                    <a href="{{ route('delete.quarterlyvisit', $item->id) }}"
                                                        class="btn btn-danger sm" id="delete" title="Delete Data"><i
                                                            class="fas fa-trash-alt"></i></a>
                                                @endif

                                            </td>
                                        @endif
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
            targets: [],
            visible: false
        },],


    });
</script>



@endsection