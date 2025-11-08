@extends('admin.layouts.app')
@section('page', 'Area detail')

@section('content')
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    
                    <p class="text-muted mb-2">Name</p>
                    <h5 class="display-6">{{ $data->name }}</h5>

                    <p class="text-muted mb-2">State</p>
                    <p class="text-dark mb-2">{{$data->states->name ?? ''}}</p>

                   
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
