@extends('admin.layouts.app')
@section('page', 'Scheme detail')

@section('content')
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-{{($data->status == 1) ? 'success' : 'danger'}}">{{($data->status == 1) ? 'Current' : 'Past'}}</span>
                    </div>

                    <div class="w-100 mb-3">
                        <img src="{{ asset($data->image) }}" class="img-thumbnail" style="height: 200px">
                    </div>

                    <div class="mb-3">
                        <a href="{{ asset($data->pdf) }}" target="_blank" class="btn btn-sm btn-primary">View PDF</a>
                    </div>

                    <h5 class="display-6">{{ $data->name }}</h5>
                    <h5 class="display-6">{{ $data->state }}</h5>
                    <p class="text-muted mb-2">Validity</p>
                    <p class="text-dark mb-2">{{date('d M Y', strtotime($data->start_date))}} - {{date('d M Y', strtotime($data->end_date))}}</p>

                   
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
