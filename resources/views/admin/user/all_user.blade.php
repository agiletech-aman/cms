@extends('admin.admin_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">All User</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('user.add'))
                        <a href="{{ route('add.user') }}" class="btn btn-info">Add User</a>
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

                        <h4 class="card-title">All User</h4><br><br>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Role</th>
                                    @if(Auth::user()->can('user.edit') or Auth::user()->can('user.delete') or Auth::user()->can('user.status'))
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>

                            @php($i=1)
                            @foreach($alluser as $item)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->username }}</td>
                                <td>{{ $item->email }}</td>
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
                                @if(Auth::user()->can('user.edit') or Auth::user()->can('user.delete') or Auth::user()->can('user.status') or  Auth::user()->can('user.ChangePassword'))
                                <td>
                                    @if(Auth::user()->can('user.edit'))
                                    <a href="{{ route('edit.user',$item->id) }}" class="btn btn-info sm" title="Edit Data"><i class="fas fa-edit"></i></a>
                                    @endif
                                    @if(Auth::user()->can('user.delete'))
                                    <a href="{{ route('delete.user',$item->id) }}" class="btn btn-danger sm" id="delete" title="Delete Data"><i class="fas fa-trash-alt"></i></a>
                                    @endif
                                    @if(Auth::user()->can('user.status'))
                                    @if($item->status=='active')
                                    <a href="{{ route('inactive.user',$item->id ) }}" class="btn btn-primary rounded-pill waves-effect waves-light"><i class="fa-solid fas fa-thumbs-down" title="Inactive"></i></a>
                                    @else
                                    <a href="{{ route('active.user',$item->id ) }}" class="btn btn-primary rounded-pill waves-effect waves-light"><i class="fa-solid fas fa-thumbs-up" title="Active"></i></a>
                                    @endif
                                    @endif
                                     @if(Auth::user()->can('user.ChangePassword'))
                                    <a href="{{ route('user.passwordget',$item->id) }}" class="btn btn-danger sm" id="" title="Change Password"><i class="fas fa-key"></i></a>
                                    
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
@endsection