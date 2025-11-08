@extends('admin.layouts.app')
@section('page', 'Size detail')

@section('content')
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                   

                    <h5 class="display-6">{{ $data->name }}</h5>

                    

                   
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
