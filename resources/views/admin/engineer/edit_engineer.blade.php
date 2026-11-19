@extends('admin.admin_master')
@section('admin')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit Engineer</h4>
                    <div class="page-title-right">
                        <a href="{{ route('all.engineer') }}" class="btn btn-info">View All Engineers</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Engineer</h4><br><br>
                        <form method="POST" action="{{ route('update.engineer', $engineer->id) }}">
                            @include('admin.engineer._form')
                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Engineer">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
