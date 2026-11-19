@extends('admin.admin_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">All Role In Permission</h4>

                    <div class="page-title-right">
                        <a href="{{ route('add.roles.permission') }}" class="btn btn-info">Add Role In Permission</a>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">All Role In Permission</h4><br><br>

                        <table  class="table table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Roles Name</th>
                                    <th>Permission</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            @foreach($roles as $key=> $item)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>
                                    @foreach($item->permissions as $perm)
                                    <span class="badge rounded-pill bg-danger">{{ $perm->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('admin.edit.roles',$item->id) }}" class="btn btn-info sm" title="Edit Data"><i class="fas fa-edit"></i></a>
                                    <!-- <a href="{{ route('admin.delete.roles',$item->id) }}" class="btn btn-danger sm" id="delete" title="Delete Data"><i class="fas fa-trash-alt"></i></a> -->
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
@endsection