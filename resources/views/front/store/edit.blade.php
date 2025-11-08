
@extends('layouts.app')

@section('page', 'Edit Store details')

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
                    <form method="POST" action="{{ route('front.store.update', $data->stores->id) }}" enctype="multipart/form-data">@csrf 
                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Manager details</p>
                            </div>
                            <div class="col-md-4">
								
                                <div class="form-group">
                                     <label for="distributor_name">Distributor  <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <p class="small text-danger">({{$data->team->distributors->name}})</p>
                                        <select class="form-select select2" id="distributor_name"  name="distributor_id" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($data->allDistributors as $item)
                                            
                                                <option value="{{$item->id}}" {{($item->id== $data->team->distributors->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('distributor_id') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                          
                            <div class="col-md-4">
                                <div class="form-group">
									<label for="ase">Sales Person(ASE/ASM)  <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
										<p class="small text-danger">({{$data->stores->users->name}})</p>
                                        <select class="form-select select2" id="ase" name="ase_id"  aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($data->users as $item)
											@php
                                                $user = explode(",", $data->stores->user_id);
                                                $isSelected = in_array($item->id,$user) ? "selected='selected'" : "";
                                            @endphp
                                                <option value="{{$item->id}}" {{is_array($user) && in_array($item->id, $user) ? 'selected' : '' }}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        
                                    </div>
                                    <p class="small text-danger">ASE/ASM depends on Distributor</p>
                                </div>
                            </div>
                            
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Store information</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="store_name" name="name" placeholder="Distributor name" value="{{ old('name') ? old('name') : $data->stores->name }}">
                                        <label for="store_name">Store name  <span class="text-danger">*</span></label>
                                    </div>
                                    @error('name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="bussiness_name" name="business_name" placeholder="Distributor name" value="{{ old('business_name') ? old('business_name') : $data->stores->business_name }}">
                                        <label for="bussiness_name">Firm name  <span class="text-danger">*</span></label>
                                    </div>
                                    @error('business_name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="gst_no" name="gst_no" placeholder="Distributor name" value="{{ old('gst_no') ? old('gst_no') : $data->stores->gst_no }}">
                                        <label for="gst_no">GST number</label>
                                    </div>
                                    @error('gst_no') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="d-flex">
                                        @if (!empty($data->stores->image) || file_exists($data->stores->image))
                                            <img src="{{ asset($data->stores->image) }}" alt="" class="img-thumbnail" style="height: 52px;margin-right: 10px;">
                                        @endif
                                        <div class="form-floating mb-3">
                                            <input type="file" class="form-control" id="image" name="image" placeholder="Distributor name" value="">
                                            <label for="image">Image  <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    @error('image') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Owner information</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="owner_name" name="owner_fname" placeholder="name" value="{{ old('owner_fname') ? old('owner_fname') : $data->stores->owner_fname }}">
                                        <label for="owner_name">Owner first name  <span class="text-danger">*</span></label>
                                    </div>
                                    @error('owner_name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
							<div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="owner_lname" name="owner_lname" placeholder="name" value="{{ old('owner_lname') ? old('owner_lname') : $data->stores->owner_lname }}">
                                        <label for="owner_lname">Owner last name  <span class="text-danger">*</span></label>
                                    </div>
                                    @error('owner_lname') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" placeholder="name@example.com" value="{{ old('date_of_birth') ? old('date_of_birth') : $data->stores->date_of_birth }}">
                                        <label for="date_of_birth">Date of Birth</label>
                                    </div>
                                    @error('date_of_birth') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="date_of_anniversary" name="date_of_anniversary" placeholder="name@example.com" value="{{ old('date_of_anniversary') ? old('date_of_anniversary') : $data->stores->date_of_anniversary }}">
                                        <label for="date_of_anniversary">Date of Anniversary</label>
                                    </div>
                                    @error('date_of_anniversary') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Contact information</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="contact" name="contact" placeholder="name@example.com" value="{{ old('contact') ? old('contact') : $data->stores->contact }}">
                                        <label for="contact">Contact  <span class="text-danger">*</span></label>
                                    </div>
                                    @error('contact') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="whatsapp" name="whatsapp" placeholder="name@example.com" value="{{ old('whatsapp') ? old('whatsapp') : $data->stores->whatsapp }}">
                                        <label for="whatsapp">Whatsapp</label>
                                    </div>
                                    @error('whatsapp') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="{{ old('email') ? old('email') : $data->stores->email }}">
                                        <label for="email">Email</label>
                                    </div>
                                    @error('email') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Location details</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="address" name="address" placeholder="name@example.com" value="{{ old('address') ? old('address') : $data->stores->address }}">
                                        <label for="address">Address  <span class="text-danger">*</span></label>
                                    </div>
                                    @error('address') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
							<div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="city" name="city" placeholder="" value="{{ old('city') ? old('city') : $data->stores->city }}">
                                        <label for="pin">Town/City Name  <span class="text-danger">*</span></label>
                                    </div>
                                    @error('pin') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="pin" name="pin" placeholder="name@example.com" value="{{ old('pin') ? old('pin') : $data->stores->pin }}">
                                        <label for="pin">Pincode  <span class="text-danger">*</span></label>
                                    </div>
                                    @error('pin') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
									<label for="state">State <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <select class="form-select select2" id="state" name="state_id" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($data->states as $index => $item)
                                                <option value="{{ $item->id }}" {{ ($data->stores->state_id == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <!--<label for="state">State *</label>-->
                                    </div>
                                    <p class="small text-danger">State depends on Distributor</p>
                                    @error('state') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
									<label for="area">Area <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <select class="form-select select2" id="area" name="area_id" aria-label="Floating label select example" readonly>
                                            <option value="{{$data->stores->area_id}}" selected>{{$data->stores->areas->name}}</option>
                                        </select>
                                        <!--<label for="area">City/ Area *</label>-->
                                    </div>
                                    <p class="small text-danger">Area depends on Distributor</p>
                                    @error('area') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Contact person information</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="contact_person" name="contact_person_fname" placeholder="name@example.com" value="{{ old('contact_person_fname') ? old('contact_person_fname') : $data->stores->contact_person_fname }}">
                                        <label for="contact_person">First name </label>
                                    </div>
                                    @error('contact_person') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
							<div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="contact_person_lname" name="contact_person_lname" placeholder="name" value="{{ old('contact_person_lname') ? old('contact_person_lname') : $data->stores->contact_person_lname }}">
                                        <label for="contact_person_lname">Last name </label>
                                    </div>
                                    @error('contact_person_lname') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="contact_person_phone" name="contact_person_phone" placeholder="name@example.com" value="{{ old('contact_person_phone') ? old('contact_person_phone') : $data->stores->contact_person_phone }}">
                                        <label for="contact_person_phone">Contact </label>
                                    </div>
                                    @error('contact_person_phone') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="contact_person_whatsapp" name="contact_person_whatsapp" placeholder="name@example.com" value="{{ old('contact_person_whatsapp') ? old('contact_person_whatsapp') : $data->stores->contact_person_whatsapp }}">
                                        <label for="contact_person_whatsapp">Whatsapp</label>
                                    </div>
                                    @error('contact_person_whatsapp') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="contact_person_date_of_birth" name="contact_person_date_of_birth" placeholder="name@example.com" value="{{ old('contact_person_date_of_birth') ? old('contact_person_date_of_birth') : $data->stores->contact_person_date_of_birth }}">
                                        <label for="contact_person_date_of_birth">Date of Birth</label>
                                    </div>
                                    @error('contact_person_date_of_birth') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="contact_person_date_of_anniversary" name="contact_person_date_of_anniversary" placeholder="name@example.com" value="{{ old('contact_person_date_of_anniversary') ? old('contact_person_date_of_anniversary') : $data->stores->contact_person_date_of_anniversary }}">
                                        <label for="contact_person_date_of_anniversary">Date of Anniversary</label>
                                    </div>
                                    @error('contact_person_date_of_anniversary') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">

                                <button type="submit" class="btn btn-danger">Update changes</button>
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
    $('select[name="state_id"]').on('change', (event) => {
        var value = $('select[name="state_id"]').val();

        $.ajax({
            url: '{{url("/")}}/admin/state-wise-area/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="area_id"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data.area, (key, value) => {
                    content += '<option value="'+value.area_id+'">'+value.area+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
@endsection