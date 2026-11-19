@extends('admin.admin_master')
@section('admin')
<style>
    .select2-container {
        width: 100% !important;
    }


    .select2-container--default .select2-selection--single {
        height: 38px !important;
        padding: 5px 5px;
        border: 1px solid #ced4da;
    }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="{{ asset('backend/assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}"
    rel="stylesheet" />
<link href="{{ asset('backend/assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Quarterly Visit</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('quarterlyVisit.list'))
                            <a href="{{ route('all.quarterlyvisit') }}" class="btn btn-info">View All Quarterly Visit</a>
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

                        <h4 class="card-title">Add Quarterly Visit</h4><br><br>

                        <!-- Nav tabs -->
                        <ul class="nav nav-pills" role="tablist">
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link active" data-bs-toggle="tab" href="#ro" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">Regional Office</span>
                                </a>
                            </li>
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link" data-bs-toggle="tab" href="#co" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">Corporate Office</span>
                                </a>
                            </li>
                            <li class="nav-item waves-effect waves-light">
                                <a class="nav-link" data-bs-toggle="tab" href="#wh" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                    <span class="d-none d-sm-block">Warehouse</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content mt-3 p-3 text-muted">

                            <!-- Regional office -->
                            <div class="tab-pane active" id="ro" role="tabpanel">
                                <form method="POST" action="{{ route('store.quarterlyvisit') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="row mb-3">
                                        <label for="region_id" class="col-sm-2 col-form-label">Region</label>
                                        <div class="col-sm-10">
                                            <select class="select2 form-select" required name="region_id" id="region_id"
                                                data-placeholder="Select Region">
                                                <option value=""></option>
                                                @foreach($regions as $region)
                                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="region_id" class="col-sm-2 col-form-label">Choose Schedule
                                            Dates</label>
                                        <div class="col-sm-10">
                                            <div class="input-daterange input-group" id="datepicker-ro"
                                                data-date-format="dd M, yyyy" data-date-autoclose="true">
                                                <input type="text" class="form-control" name="start_date"
                                                    placeholder="Start Date" />
                                                <input type="text" class="form-control" name="end_date"
                                                    placeholder="End Date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="phase_id" class="col-sm-2 col-form-label">Quarterly Phase</label>
                                        <div class="col-sm-10">
                                            <select class="select2 form-select" required name="phase_id"
                                                id="ro_phase_id" data-placeholder="Select Phase">
                                                <option value=""></option>
                                                @for($i = 1; $i <= 20; $i++)
                                                    <option value="{{ $i }}">Phase {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <input type="submit" class="btn btn-info waves-effect waves-light"
                                        value="Add Quarterly Visit">
                                </form>
                            </div>


                            <!-- Corporate office -->
                            <div class="tab-pane" id="co" role="tabpanel">
                                <form method="POST" action="{{ route('store.quarterlyvisit') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="row mb-3">
                                        <label for="user_id" class="col-sm-2 col-form-label">Corporate Office</label>
                                        <div class="col-sm-10">
                                            <select class="select2 form-select" required name="user_id" id="co_user_id"
                                                data-placeholder="Select Corporate Office">
                                                <option value=""></option>
                                                @foreach($allcorporateoffices as $office)
                                                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="warehouse_id" class="col-sm-2 col-form-label">Choose Schedule
                                            Dates</label>
                                        <div class="col-sm-10">
                                            <div class="input-daterange input-group" id="datepicker-co"
                                                data-date-format="dd M, yyyy" data-date-autoclose="true">
                                                <input type="text" class="form-control" name="start_date"
                                                    placeholder="Start Date" id="co_start_date" />
                                                <input type="text" class="form-control" name="end_date"
                                                    placeholder="End Date" id="co_end_date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="phase_id" class="col-sm-2 col-form-label">Quarterly Phase</label>
                                        <div class="col-sm-10">
                                            <select class="select2 form-select" required name="phase_id"
                                                id="co_phase_id" data-placeholder="Select Phase">
                                                <option value=""></option>
                                                @for($i = 1; $i <= 20; $i++)
                                                    <option value="{{ $i }}">Phase {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <input type="submit" class="btn btn-info waves-effect waves-light"
                                        value="Add Quarterly Visit">
                                </form>
                            </div>


                            <!-- Warehouse -->
                            <div class="tab-pane" id="wh" role="tabpanel">
                                <form method="POST" action="{{ route('store.quarterlyvisit') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="row mb-3">
                                        <label for="region_id" class="col-sm-2 col-form-label">Region</label>
                                        <div class="col-sm-10">
                                            <select class="select2 form-select" required name="region_id"
                                                id="wh_region_id" data-placeholder="Select Region">
                                                <option value=""></option>
                                                @foreach($regions as $region)
                                                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="user_id" class="col-sm-2 col-form-label">Warehouse</label>
                                        <div class="col-sm-10">
                                            <select class="select2 form-select" required name="user_id"
                                                id="warehouse_id" data-placeholder="Select Warehouse">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="user_id" class="col-sm-2 col-form-label">Choose Schedule
                                            Dates</label>
                                        <div class="col-sm-10">
                                            <div class="input-daterange input-group" id="datepicker-wh"
                                                data-date-format="dd M, yyyy" data-date-autoclose="true">
                                                <input type="text" class="form-control" name="start_date"
                                                    placeholder="Start Date" id="wh_start_date" />
                                                <input type="text" class="form-control" name="end_date"
                                                    placeholder="End Date" id="wh_end_date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="phase_id" class="col-sm-2 col-form-label">Quarterly Phase</label>
                                        <div class="col-sm-10">
                                            <select class="select2 form-select" required name="phase_id" id="phase_id"
                                                data-placeholder="Select Phase">
                                                <option value=""></option>
                                                @for($i = 1; $i <= 20; $i++)
                                                    <option value="{{ $i }}">Phase {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <input type="submit" class="btn btn-info waves-effect waves-light"
                                        value="Add Quarterly Visit">
                                </form>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="{{ asset('backend/assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<script>
    $('.select2').select2();

    // Initialize datepickers for each tab
    $('#datepicker-ro, #datepicker-co, #datepicker-wh').datepicker({
        format: 'dd M, yyyy',
        autoclose: true
    });


    $('#region_id, #wh_region_id').on("change", function (e) {
        var selectedRegions = $(this).val();

        if (selectedRegions) {
            $.ajax({
                url: "{{ url('/get/warehouse') }}",
                method: 'GET',
                data: {
                    region_id: selectedRegions
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    $('#warehouse_id').html(data.warehouse_users);
                },
            });
        } else {
            alert('Please select a region.');
        }
    });
</script>
@endsection