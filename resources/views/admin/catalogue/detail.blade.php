@extends('admin.layouts.app')

@section('page', 'Catalogue detail')

@section('content')
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>{{ $data->name }}</h3>
                            <p class="small">Start Date: {{ date('d M Y', strtotime($data->start_date ))}}</p>
                            <p class="small">End Date : {{ date('d M Y', strtotime($data->end_date ))}}</p>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <p class="text-muted">Image</p>
                            <img src="{{ asset($data->image) }}" alt="" style="height: 50px">
                        </div>
                        <div class="col-md-3">
                            <p class="text-muted">Pdf</p>
                            <a href="{{ asset($data->pdf) }}" target="_blank"><i class="app-menu__icon fa fa-download"></i>Pdf</a>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
