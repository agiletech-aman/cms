@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="{{ asset('backend/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">


<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Add Item</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('item.list'))
                        <a href="{{ route('all.items') }}" class="btn btn-info">View All Items</a>
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
                        <h4 class="card-title">Add Item</h4><br><br>
                        <form method="POST" action="{{ route('store.items') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">Item</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="text" placeholder="Name" name="name" id="name" value="">
                                </div>
                            </div>
                            <!-- end row -->

                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">Category</label>
                                <div class="col-sm-10">
                                    <select class="form-select select2" required name="cat_id" id="cat_id">

                                        <option value="" hidden>Select Category</option>
                                        @foreach($category as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- end row -->



                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Add Item">

                        </form>

                    </div>
                </div>
            </div>


        </div>
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



<script src="{{ asset('backend/assets/libs/select2/js/select2.min.js') }}"></script>
<script>
    $('.select2').select2();
</script>
@endsection