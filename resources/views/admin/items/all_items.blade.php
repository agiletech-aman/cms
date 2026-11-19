@extends('admin.admin_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">All Items</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('item.add'))
                        <a href="{{ route('add.items') }}" class="btn btn-info">Add Items</a>
                        @endif
                        <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target=".import-excel">Import Excel</a>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        @if(session('errors'))
                        <div class="alert alert-danger">
                            <ul>
                                @foreach(session('errors') as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(session('skipped'))
                        <div class="alert alert-warning">
                            {{ session('skipped') }}
                        </div>
                        @endif
                        <h4 class="card-title">All Items</h4><br><br>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    @if(Auth::user()->can('item.edit') or Auth::user()->can('item.delete'))
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>

                            @php($i=1)
                            @foreach($allitems as $item)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ optional($item['category'])['name'] ?? 'N/A' }}</td>
                                @if(Auth::user()->can('item.edit') or Auth::user()->can('item.delete'))
                                <td>
                                    @if(Auth::user()->can('item.edit'))
                                    <a href="{{ route('edit.items',$item->id) }}" class="btn btn-info sm" title="Edit Data"><i class="fas fa-edit"></i></a>
                                    @endif
                                    @if(Auth::user()->can('item.delete'))
                                    <a href="{{ route('delete.items',$item->id) }}" class="btn btn-danger sm" id="delete" title="Delete Data"><i class="fas fa-trash-alt"></i></a>
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


<div class="modal fade import-excel" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('import.excel') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="col-form-label">Category</label><code>*</code>

                            <select class="form-select" required name="cat_id" id="cat_id">

                                <option value="" hidden>Select Category</option>
                                @foreach($category as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label>Select Item Excel</label><code>*</code><a href="{{ asset('upload/item-sample.xlsx') }}" download="" target="_blank" rel="noopener noreferrer" class="float-end">(Sample File)</a>
                            <input type="file" class="form-control" name="item_excel" id="item_excel" required accept=".xls, .xlsx">

                        </div>
                    </div>


                    <input type="submit" class="btn btn-info waves-effect waves-light" value="Import Excel">

                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@endsection