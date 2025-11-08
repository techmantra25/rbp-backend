@extends('admin.layouts.app')

@section('page', 'Edit Scheme')

@section('content')
<style>
    input::file-selector-button {
        display: none;
    }
    .veiwPDF{
        font-size: 12px;
        padding: 8px;
        width: 90px;
        display: flex;
        align-items: center;
        margin-right: 10px;
    }
</style>

<section class="inner-sec1">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.schemes.update', $data->id) }}" enctype="multipart/form-data">@csrf @method('PUT')
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="is_current" name="is_current">
                                            <option value="1" {{ ($data->status == 1) ? 'selected' : '' }}>Current</option>
                                            <option value="0" {{ ($data->status == 0) ? 'selected' : '' }}>Past</option>
                                        </select>
                                        <label for="is_current">Status *</label>
                                    </div>
                                    @error('is_current') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') ? old('start_date') : $data->start_date }}">
                                        <label for="start_date">Validity from *</label>
                                    </div>
                                    @error('start_date') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') ? old('end_date') : $data->end_date }}">
                                        <label for="end_date">Validity to *</label>
                                    </div>
                                    @error('end_date') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="title" name="name" value="{{ old('name') ? old('name') : $data->name }}">
                                        <label for="title">Title *</label>
                                    </div>
                                    @error('name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                                <div class="form-group">
									<label for="title">State <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
										 
										
                                        <select class="form-control form-control-sm select2" name="state[]" id="state" multiple>
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($states as $index => $item)
                                                @php
                                                    $cat = explode(",", $data->state_id);
                                                    $isSelected = in_array($item->id,$cat) ? "selected='selected'" : "";
                                                @endphp
                                                
                                                <option  value="{{$item->id}}" {{ (in_array($item->id, $cat)) ? 'selected' : '' }} >{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <!--<label for="title">State <span clas="textdanger">*</span></label>-->
                                    </div>
                                    @error('state') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="d-flex">
                                        @if (!empty($data->image) || file_exists($data->image))
                                            <img src="{{ asset($data->image) }}" alt="" class="img-thumbnail" style="height: 52px;margin-right: 10px;">
                                        @endif
                                        <div class="form-floating mb-3">
                                            <input type="file" class="form-control" id="image" name="image" value="">
                                            <label for="image">Preview Image *</label>
                                        </div>
                                    </div>
                                    @error('image') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="d-flex">
                                        @if (!empty($data->pdf) || file_exists($data->pdf))
                                            <a href="{{ asset($data->pdf) }}" class="btn veiwPDF btn-primary" target="_blank">VIEW PDF</a>
                                        @endif
                                        <div class="form-floating">
                                            <input type="file" class="form-control" id="pdf" name="pdf" value="">
                                            <label for="pdf">Scheme in PDF *</label>
                                        </div>
                                    </div>
                                    @error('pdf') <p class="small text-danger">{{$message}}</p> @enderror
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
