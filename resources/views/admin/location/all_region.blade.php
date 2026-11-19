@extends('admin.admin_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">All Region</h4>

                    <div class="page-title-right">
                    @if(Auth::user()->can('region.add'))
                        <a href="{{ route('add.region') }}" class="btn btn-info">Add Region</a>
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

                        <h4 class="card-title">All Region</h4><br><br>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Region</th>
                                    <th>State</th>
                                    @if(Auth::user()->can('region.edit') or Auth::user()->can('region.delete'))
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>

                            @php($i=1)
                            @foreach($allregion as $item)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item['state']['name'] }}</td>
                                @if(Auth::user()->can('region.edit') or Auth::user()->can('region.delete'))
                               
                                <td>
                                    @if(Auth::user()->can('region.edit'))
                                    <a href="{{ route('edit.region',$item->id) }}" class="btn btn-info sm" title="Edit Data"><i class="fas fa-edit"></i></a>
                                    @endif
                                    @if(Auth::user()->can('region.delete'))
                                    <a href="{{ route('delete.region',$item->id) }}" class="btn btn-danger sm" id="delete" title="Delete Data"><i class="fas fa-trash-alt"></i></a>
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