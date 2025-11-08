@extends('admin.layouts.app')
@section('page', 'Create new HeadQuarter')

@section('content')
<style>
    input::file-selector-button {
        display: none;
    }
</style>

<section class="inner-sec1">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.headquaters.store') }}" enctype="multipart/form-data">@csrf
                        
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="title" name="name" placeholder="name@example.com" value="{{ old('name') }}">
                                       <label for="title">Name <span class="text-danger">*</span></label>
                                    </div>
                                    @error('name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
									<label for="title">State <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <select class="form-control form-control-sm select2" name="state_id" id="state_id">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($states as $index => $item)
                                            <option value="{{ $item->id }}" {{ ($request->state_id == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <!--<label for="title">State *</label>-->
                                    </div>
                                    @error('state_id') <p class="small text-danger">{{$message}}</p> @enderror
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

@section('script')
<script>
    
</script>
@endsection
