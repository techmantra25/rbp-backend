@extends('admin.layouts.app')

@section('page', 'Edit Headquarter')

@section('content')


<section class="inner-sec1">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.headquaters.update', $data->id) }}" enctype="multipart/form-data">@csrf @method('PUT')
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="title" name="name" placeholder="name@example.com" value="{{ $data->name }}">
                                       <label for="title">Name <span class="text-danger">*</span></label>
                                    </div>
                                    @error('name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
									<label for="title">State <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <select class="form-control form-control-sm select2" name="cat_id" id="cat_id">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($states as $index => $item)
                                            <option value="{{ $item->id }}" {{ ($data->state_id == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                       
                                    </div>
                                    @error('name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger">Save changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>








@endsection
