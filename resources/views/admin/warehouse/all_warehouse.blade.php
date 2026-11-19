@extends('admin.admin_master')
@section('admin')


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="{{ asset('backend/assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">All Warehouse</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('warehouse.add'))
                        <a href="{{ route('add.warehouse') }}" class="btn btn-info">Add Warehouse</a>
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

                        <h4 class="card-title">All Warehouse</h4><br><br>

                        <table id="example1" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    
                                    <th>Region</th>
                                    <th>Status</th>
                                    <th>Role</th>
                                    @if(Auth::user()->can('warehouse.edit') or Auth::user()->can('warehouse.delete') or Auth::user()->can('warehouse.status'))
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>

                            @php($i=1)
                            @foreach($allwarehouse as $item)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->username }}</td>
                                <td>{{ $item->email }}</td>
                                
                                <td>{{ $item->region->name }}</td>
                                <td>
                                    @if($item->status=='active')
                                    <span class="badge badge-pill bg-success">Active</span>
                                    @else
                                    <span class="badge badge-pill bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($item->roles as $role)
                                    <span class="badge badge-pill bg-danger">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                @if(Auth::user()->can('warehouse.edit') or Auth::user()->can('warehouse.delete') or Auth::user()->can('warehouse.status'))
                                <td>
                                    @if(Auth::user()->can('warehouse.edit'))
                                    <a href="{{ route('edit.warehouse',$item->id) }}" class="btn btn-info sm" title="Edit Data"><i class="fas fa-edit"></i></a>
                                    @endif
                                    @if(Auth::user()->can('warehouse.delete'))
                                    <a href="{{ route('delete.warehouse',$item->id) }}" class="btn btn-danger sm" id="delete" title="Delete Data"><i class="fas fa-trash-alt"></i></a>
                                    @endif
                                    @if(Auth::user()->can('warehouse.status'))
                                    @if($item->status=='active')
                                    <a href="{{ route('inactive.warehouse',$item->id ) }}" class="btn btn-primary rounded-pill waves-effect waves-light"><i class="fa-solid fas fa-thumbs-down" title="Inactive"></i></a>
                                    @else
                                    <a href="{{ route('active.warehouse',$item->id ) }}" class="btn btn-primary rounded-pill waves-effect waves-light"><i class="fa-solid fas fa-thumbs-up" title="Active"></i></a>
                                    @endif
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
            filename: "Warehouse_User_Excel",
            exportOptions: {
                columns: [0, 1,2,3,4]
            }
        }, ],

    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
@endsection