@extends('admin.admin_master')
@section('admin')


<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">State</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('state.list'))
                        <a href="{{ route('all.state') }}" class="btn btn-info">View All State</a>
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

                        <h4 class="card-title">Add State</h4><br><br>
                        <form method="POST" action="{{ route('store.state') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">State</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="text" placeholder="Name" name="name" id="name" value="">
                                </div>
                            </div>
                            <!-- end row -->



                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Add State">

                        </form>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

@endsection