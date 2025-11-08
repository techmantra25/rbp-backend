@extends('admin.layouts.app')
@section('page', 'State detail')

@section('content')
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    

                    <h5 class="display-6">Name - {{ $data->name }}</h5>

                    <p>Code - {{ $data->code }}</p>

                   
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
