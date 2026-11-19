@extends('admin.admin_master')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Quarterly Visit Details </h4>

                    <div class="page-title-right">

                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="card-title">Phase# {{ $visit->phase_id }}</h4>

                            </div>
                            <div class="col-6">
                                <a href="{{ route('all.quarterlyvisit') }}" class="float-end">Back to Quarterly
                                    Visit</a>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <h3><strong>Region: </strong>{{ $visit['region']['name'] }} </h3>
                                </div>
                                <div class="col-6">
                                    <h5><strong>Site:
                                        </strong>{{ $visit['user']['username'] ?? 'ro' . strtolower($visit['region']['name']) }}
                                    </h5>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <p><strong>Schedule Dates: </strong>{{ $visit->start_date }} to
                                        {{ $visit->end_date }}
                                    </p>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-4">
                                    <p><strong>Created At: </strong>{{ $visit->created_at }} </p>
                                </div>
                                @if(!empty($visit->remark))
                                    <div class="col-4">
                                        <p><strong>Remark: </strong> {{ $visit->remark }}</p>
                                    </div>
                                @endif
                                @if(!empty($visit->visit_date))
                                    <div class="col-4">

                                        <p><strong>Visit Date: </strong>{{ $visit->visit_date }}</p>

                                    </div>
                                @endif
                            </div>
                            <div class="row">



                                @if(!empty($visit->certificate_image))
                                    <div class="col-4">

                                        <a href="{{ asset($visit->certificate_image) }}" download target="_blank"
                                            rel="noopener noreferrer"><img src="{{ asset($visit->certificate_image) }}"
                                                class="img-thumbnail" alt="profile-image"></a>

                                    </div>
                                @endif
                                <div class="col-6">
                                    @if(empty($visit->visit_date))
                                        @if((Auth::user()->hasAnyRole(['Railtel', 'SuperAdmin'])))
                                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target=".update-quarterlyvisit-status">Update
                                                Quarterly Visit Status</button>
                                        @endif
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div>


    </div>

    <div class="modal fade update-quarterlyvisit-status" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Quarterly Visit Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('update.quarterlyvisit.status', $visit->id) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">

                            <div class="row" id="closed_div">
                                <div class="col-md-12 mb-3">
                                    <label>Visit Date</label>
                                    <input type="date" class="form-control" name="visit_date" id="visit_date" required>
                                </div>
                            </div>
                            <div class="col-md-10 mb-3">
                                <label>Certificate Image</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="certificate_image"
                                        id="certificate_image" required accept=".jpeg,.jpg,.png,.gif,.svg">
                                </div>

                            </div>
                            <div class="row" id="remark_div">
                                <div class="col-md-12 mb-3">
                                    <label>Remark</label>
                                    <textarea class="form-control" name="remark" id="remark" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <input type="submit" class="btn btn-info waves-effect waves-light"
                            value="Update Quarterly Visit">

                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->



    @endsection