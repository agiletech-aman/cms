@extends('admin.admin_master')
@section('admin')


<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Region</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('region.list'))
                        <a href="{{ route('all.region') }}" class="btn btn-info">View All Region</a>
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

                        <h4 class="card-title">Edit Region</h4><br><br>
                        <form method="POST" action="{{ route('update.region') }}" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="id" value="{{ $regions->id }}">
                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">Region</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="text" placeholder="Name" name="name" id="name" value="{{ $regions->name }}">
                                </div>
                            </div>
                            <!-- end row -->

                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">State</label>
                                <div class="col-sm-10">
                                    <select class="form-select" required name="state_id" id="state_id">

                                        <option value="" hidden>Select State</option>
                                        @foreach($state as $sta)
                                        <option value="{{ $sta->id }}" {{ $sta->id == $regions->state_id ? 'selected' : ''  }}>{{ $sta->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- end row -->


                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Region">

                        </form>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

@endsection