@extends('admin.layouts.app')
@section('page', 'Color detail')

@section('content')
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                   

                    <h5 class="display-6">{{ $data->name }}</h5>

                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="{{$data->code}} " stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-circle"><circle cx="12" cy="12" r="10"></circle></svg>
                    {{ $data->code }}

                   
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
