@extends('admin.admin_master')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link href="{{ asset('backend/assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
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

                        <h4 class="card-title">Edit Quarterly Visit</h4><br><br>
                        <form method="POST" action="{{ route('update.quarterlyvisit', $visits->id) }}" enctype="multipart/form-data">
                            @csrf


                            <div class="row mb-3">
                                <label for="region_id" class="col-sm-2 col-form-label">Region</label>
                                <div class="col-sm-10">
                                    <select class="select2 form-select" required name="region_id" id="region_id" data-placeholder="Select Region">
                                        <option value=""></option>
                                        @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ ($region->id==$visits->region_id) ? 'selected' : '' }}>{{ $region->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="warehouse_id" class="col-sm-2 col-form-label">Warehouse</label>
                                <div class="col-sm-10">
                                    <select class="select2 form-select" required name="warehouse_id" id="warehouse_id" data-placeholder="Select Warehouse">
                                        <option value=""></option>
                                        @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ ($warehouse->id==$visits->warehouse_id) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <label for="warehouse_id" class="col-sm-2 col-form-label">Choose Schedule Dates</label>
                                <div class="col-sm-10">
                                    <div class="input-daterange input-group" id="datepicker6" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container="#datepicker6">
                                        <input type="text" class="form-control" name="start_date" placeholder="Start Date" value="{{ $visits->start_date }}"/>
                                        <input type="text" class="form-control" name="end_date" placeholder="End Date" value="{{ $visits->end_date }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="phase_id" class="col-sm-2 col-form-label">Quarterly Phase</label>
                                <div class="col-sm-10">
                                    <select class="select2 form-select" required name="phase_id" id="phase_id" data-placeholder="Select Phase">
                                        <option value=""></option>
                                        @for($i = 1; $i <= 20; $i++) 
                                        <option value="{{ $i }}" {{ ($i==$visits->phase_id) ? 'selected' : '' }}>Phase {{ $i }}</option>
                                            @endfor
                                    </select>
                                </div>
                            </div>
                            <!-- end row -->

                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Quarterly Visit">

                        </form>

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

    $('#region_id').on("change", function(e) {

        var selectedRegions = $(this).val();

        if (selectedRegions) {
            $.ajax({
                url: "{{ url('/get/warehouse') }}",
                method: 'GET',
                data: {
                    region_id: selectedRegions
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    $('#warehouse_id').html(data.warehouse_users);
                },

            });
        } else {
            alert('danger');
        }
    });
</script>
@endsection