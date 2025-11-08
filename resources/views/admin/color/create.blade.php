@extends('admin.layouts.app')
@section('page', 'Create new Color')

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
                    <form method="POST" action="{{ route('admin.colors.store') }}" enctype="multipart/form-data">@csrf
                        
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="title" name="name" placeholder="name@example.com" value="{{ old('name') }}">
                                        <label for="title">Title <span class="text-danger">*</span></label>
                                    </div>
                                    @error('name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="color" class="form-control" id="code" name="code" placeholder="name@example.com" value="{{ old('code') }}">
                                        <label for="title">Code <span class="text-danger">*</span></label>
                                    </div>
                                    @error('code') <p class="small text-danger">{{$message}}</p> @enderror
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
