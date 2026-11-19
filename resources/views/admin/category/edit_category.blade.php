@extends('admin.admin_master')
@section('admin')


<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit Category</h4>

                    <div class="page-title-right">
                        @if(Auth::user()->can('category.list'))
                        <a href="{{ route('all.category') }}" class="btn btn-info">View All Category</a>
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

                        <h4 class="card-title">Edit Category</h4><br><br>
                        <form method="POST" action="{{ route('update.category', $category->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <label for="type" class="col-sm-2 col-form-label">Type</label>
                                <div class="col-sm-5">
                                    <select class="form-select" required name="type" id="type">
                                        <option value="" hidden>Select Type</option>
                                        <option value="1"  {{ $category->type == 1 ? 'selected' : ''  }}>New Infra</option>
                                        <option value="2" {{ $category->type == 2 ? 'selected' : ''  }}>Old Infra</option>
                                        <option value="3" {{ $category->type == 3 ? 'selected' : ''  }}>CCC</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">Category</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="text" placeholder="Name" name="name" id="name" value="{{ $category->name }}">
                                </div>
                            </div>
                            <!-- end row -->

                            @if(Auth::user()->hasRole('SuperAdmin'))
                            <div class="row mb-3">
                                <label for="ld_amount" class="col-sm-2 col-form-label">LD Amount</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="number" placeholder="LD Amount" name="ld_amount" id="ld_amount" value="{{ $category->ld_amount }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="ld_amount_7days" class="col-sm-2 col-form-label">LD Amount More than 7 Days</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="number" placeholder="LD Amount More than 7 Days" name="ld_amount_7days" id="ld_amount_7days" value="{{ $category->ld_amount_7days }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="paid_days" class="col-sm-2 col-form-label">Paid Days</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="number" placeholder="Paid Days" name="paid_days" id="paid_days" value="{{ $category->paid_days }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="milestone_amount" class="col-sm-2 col-form-label">10% of Milestone Amount</label>
                                <div class="col-sm-10">
                                    <input class="form-control" required type="number" placeholder="Milestone Amount" name="milestone_amount" id="milestone_amount" value="{{ $category->milestone_amount }}">
                                </div>
                            </div>
                            @endif
                           
                            <div class="row mb-3">
                                <label for="specification" class="col-sm-2 col-form-label">Specification</label>
                                <div class="col-sm-10">
                                    <textarea name="specification" class="form-control" id="specification" cols="30" rows="10">{{ $category->specification }}</textarea>

                                </div>
                            </div>
                            <!-- end row -->

                            <input type="submit" class="btn btn-info waves-effect waves-light" value="Update Category">

                        </form>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

@endsection