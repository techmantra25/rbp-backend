@extends('admin.layouts.app')

@section('page', 'QRcode detail')

@section('content')
<section>
    <div class="row">
        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.reward.retailer.barcode.update', $data->id) }}" enctype="multipart/form-data">
                                    @csrf
                                        <h4 class="page__subtitle">Edit</h4>
                                        <div class="form-group mb-3">
                                            <label class="label-control">Name <span class="text-danger">*</span> </label>
                                            <input type="text" name="name" placeholder="" class="form-control" value="{{ $data->name }}">
                                            @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="label-control">State <span class="text-danger">*</span> </label>
                                                <select name="state_id" id="state" class="form-control form-control-sm select2">
                                                    <option value="" disabled>Select</option>
                                                    <option value="" selected>All</option>
                                                    @foreach ($state as $state)
                                                        <option value="{{$state->id}}" {{ request()->input('state_id') == $state->id ? 'selected' : '' }}>{{$state->name}}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                    </div>
                                        <!--<div class="col-md-4">-->
                                        <!--    <div class="form-group mb-3">-->
                                        <!--        <label class="label-control">State <span class="text-danger">*</span> </label>-->
                                        <!--            <select name="distributor_id" id="distributor_id" class="form-control form-control-sm select2">-->
                                        <!--                <option value="" disabled>Select</option>-->
                                        <!--                <option value="" selected>All</option>-->
                                        <!--                @foreach ($allDistributors as $dist)-->
                                        <!--                    <option value="{{$dist->id}}" {{ request()->input('distributor_id') == $dist->id ? 'selected' : '' }}>{{$dist->name}}</option>-->
                                        <!--                @endforeach-->
                                        <!--            </select>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                         <div class="form-group mb-3">
                                            <label class="label-control"> code <span class="text-danger">*</span> </label>
                                            <input type="text" name="code" placeholder="" class="form-control" value="{{ $data->code }}" disabled>
                                            @error('code') <p class="small text-danger">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="label-control">Amount <span class="text-danger">*</span> </label>
                                            <input type="number" name="amount" placeholder="" class="form-control" value="{{ $data->amount }}">
                                            @error('amount') <p class="small text-danger">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="label-control">Max time of use <span class="text-danger">*</span> </label>
                                            <input type="number" name="max_time_of_use" placeholder="" class="form-control" value="{{ $data->max_time_of_use }}">
                                            @error('max_time_of_use') <p class="small text-danger">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="label-control">Max time one can use <span class="text-danger">*</span> </label>
                                            <input type="number" name="max_time_one_can_use" placeholder="" class="form-control" value="{{ $data->max_time_one_can_use }}">
                                            @error('max_time_one_can_use') <p class="small text-danger">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="label-control">Start date <span class="text-danger">*</span> </label>
                                            <input type="datetime-local" name="start_date" placeholder="" class="form-control" value="{{ date('Y-m-d h:i:s', strtotime($data->start_date)) }}">
                                            @error('start_date') <p class="small text-danger">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="label-control">End date <span class="text-danger">*</span> </label>
                                            <input type="datetime-local" name="end_date" placeholder="" class="form-control" value="{{ date('Y-m-d h:i:s', strtotime($data->end_date)) }}">
                                            @error('end_date') <p class="small text-danger">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-sm btn-danger">Update</button>
                                            <a type="submit" class="btn btn-sm btn-secondary" href="{{route('admin.reward.retailer.barcode.index')}}">Cancel</a>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
        </div>
    </div>
</section>
@endsection
