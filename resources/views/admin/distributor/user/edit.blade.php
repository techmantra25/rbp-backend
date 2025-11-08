@php
    $state = DB::select("SELECT ro.state AS state FROM `retailer_list_of_occ` AS ro GROUP BY ro.state ORDER BY ro.state");
@endphp

@extends('admin.layouts.app')
@section('page', 'Edit User')

@section('content')
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.user.update', $data->id) }}" enctype="multipart/form-data">@csrf
                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">TYPE</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="user_type" name="user_type">
                                            <option value="" selected disabled>Select</option>
                                            <option value="1" {{ ($data->user_type == 1) ? 'selected' : '' }}>VP</option>
                                            <option value="2" {{ ($data->user_type == 2) ? 'selected' : '' }}>RSM</option>
                                            <option value="3" {{ ($data->user_type == 3) ? 'selected' : '' }}>ASM</option>
                                            <option value="4" {{ ($data->user_type == 4) ? 'selected' : '' }}>ASE</option>
                                            <option value="5" {{ ($data->user_type == 5) ? 'selected' : '' }}>Distributor</option>
                                            <option value="6" {{ ($data->user_type == 6) ? 'selected' : '' }}>Retailer</option>
                                        </select>
                                        <label for="mobile">Type *</label>
                                    </div>
                                    @error('mobile') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="designation" name="designation" placeholder="name@example.com" value="{{ old('designation') ? old('designation') : $data->designation }}">
                                        <label for="designation">Designation *</label>
                                    </div>
                                    @error('designation') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="employee_id" name="employee_id" placeholder="name@example.com" value="{{ old('employee_id') ? old('employee_id') : $data->employee_id }}">
                                        <label for="employee_id">Employee ID</label>
                                    </div>
                                    @error('employee_id') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Name details</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="title" name="title" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            <option value="" selected>NA</option>
                                            <option value="Mr" {{ ($data->title == "Mr") ? 'selected' : '' }}>Mr</option>
                                            <option value="Miss" {{ ($data->title == "Miss") ? 'selected' : '' }}>Miss</option>
                                            <option value="Mrs" {{ ($data->title == "Mrs") ? 'selected' : '' }}>Mrs</option>
                                            <option value="Dr" {{ ($data->title == "Dr") ? 'selected' : '' }}>Dr</option>
                                            <option value="CA" {{ ($data->title == "CA") ? 'selected' : '' }}>CA</option>
                                            <option value="Prof" {{ ($data->title == "Prof") ? 'selected' : '' }}>Prof</option>

                                            {{-- <option value="Mr" {{ (old('title') == "Mr" || old('title') == "") ? 'selected' : '' }}>Mr</option>
                                            <option value="Miss" {{ (old('title') == "Miss") ? 'selected' : '' }}>Miss</option>
                                            <option value="Mrs" {{ (old('title') == "Mrs") ? 'selected' : '' }}>Mrs</option>
                                            <option value="Dr" {{ (old('title') == "Dr") ? 'selected' : '' }}>Dr</option>
                                            <option value="CA" {{ (old('title') == "CA") ? 'selected' : '' }}>CA</option>
                                            <option value="Prof" {{ (old('title') == "Prof") ? 'selected' : '' }}>Prof</option> --}}
                                        </select>
                                        <label for="title">Name Prefix</label>
                                    </div>
                                    @error('title') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="name@example.com" value="{{ old('name') ? old('name') : $data->name }}">
                                        <label for="name">Full name *</label>
                                    </div>
                                    @error('name') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="fname" name="fname" placeholder="name@example.com" value="{{ old('fname') ? old('fname') : $data->fname }}">
                                        <label for="fname">First name *</label>
                                    </div>
                                    @error('fname') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="lname" name="lname" placeholder="name@example.com" value="{{ old('lname') ? old('lname') : $data->lname }}">
                                        <label for="lname">Last name *</label>
                                    </div>
                                    @error('lname') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-muted mb-2">Contact details</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="mobile" name="mobile" placeholder="name@example.com" value="{{ old('mobile') ? old('mobile') : $data->mobile }}">
                                        <label for="mobile">Mobile number *</label>
                                    </div>
                                    @error('mobile') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="{{ old('email') ? old('email') : $data->email }}">
                                        <label for="email">Email ID</label>
                                    </div>
                                    @error('email') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <p class="small text-danger mb-2">Update Password</p>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="password" name="password" placeholder="name@example.com" value="">
                                        <label for="password">Password</label>
                                    </div>
                                    @error('password') <p class="small text-danger">{{$message}}</p> @enderror
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
                                        <select class="form-select" id="state" name="state" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($state as $index => $item)
                                                <option value="{{ $item->state }}" {{ (strtolower($data->state) == strtolower($item->state)) ? 'selected' : '' }}>{{ $item->state }}</option>
                                            @endforeach
                                        </select>
                                        <label for="state">State *</label>
                                    </div>
                                    @error('state') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="area" name="area" aria-label="Floating label select example" disabled>
                                            <option value="">Select State first</option>
                                        </select>
                                        <label for="area">City/ Area *</label>
                                    </div>
                                    @error('area') <p class="small text-danger">{{$message}}</p> @enderror
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



































{{-- <section>
    @php
        $state_area = DB::select('SELECT state, area FROM retailer_list_of_occ GROUP BY area ORDER BY state ASC, area ASC');
    @endphp
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.user.update', $data->id) }}" enctype="multipart/form-data">
                    @csrf
                        <h4 class="page__subtitle">Edit</h4>
                        <div class="form-group">
                            <label class="label-control">Prefix<span class="text-danger">*</span> </label>
                            <select class="form-control" name="title">
                                <option value="" hidden selected>Select...</option>
                                <option value="Mr" {{ ($data->title == "Mr") ? 'selected' : '' }}>Mr</option>
                                <option value="Mrs" {{ ($data->title == "Mrs") ? 'selected' : '' }}>Mrs</option>
                                <option value="Miss" {{ ($data->title == "Miss") ? 'selected' : '' }}>Miss</option>
                                <option value="Dr" {{ ($data->title == "Dr") ? 'selected' : '' }}>Dr</option>
                                <option value="CA" {{ ($data->title == "CA") ? 'selected' : '' }}>CA</option>
                                <option value="Prof" {{ ($data->title == "Prof") ? 'selected' : '' }}>Prof</option>
                            </select>
                            @error('title') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">First Name <span class="text-danger">*</span> </label>
                            <input type="text" name="fname" placeholder="" class="form-control" value="{{$data->fname}}">
                            @error('fname') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Last Name <span class="text-danger">*</span> </label>
                            <input type="text" name="lname" placeholder="" class="form-control" value="{{$data->lname}}">
                            @error('lname') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group ">
                            <label class="label-control"> Name <span class="text-danger">*</span> </label>
                            <input type="text" name="name" placeholder="" class="form-control" value=" {{$data->name}}">
                            @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="label-control">Contact <span class="text-danger">*</span> </label>
                            <input type="number" name="mobile" placeholder="" class="form-control" value="{{$data->mobile}}">
                            @error('mobile') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">WhatsApp Number <span class="text-danger">*</span> </label>
                            <input type="number" name="whatsapp_no" placeholder="" class="form-control" value="{{$data->whatsapp_no}}">
                            @error('whatsapp_no') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Date of Birth <span class="text-danger">*</span> </label>
                            <input type="date" name="dob" placeholder="" class="form-control" value="{{$data->dob}}">
                            @error('dob') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Gender <span class="text-danger">*</span> </label>
                            <select class="form-control" name="gender">
                                <option value="" hidden selected>Select...</option>
                                <option value="male" {{ ($data->gender == "male") ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ ($data->gender == "female") ? 'selected' : '' }}>Female</option>
                                <option value="trans" {{ ($data->gender == "trans") ? 'selected' : '' }}>Trans</option>
                                <option value="other" {{ ($data->gender == "other") ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="label-control">User Type <span class="text-danger">*</span> </label>
                            <select class="form-control" name="user_type">
                                <option value="" hidden selected>Select...</option>
                                <option value="1" {{ ($data->user_type == "1") ? 'selected' : '' }}>VP</option>
                                <option value="2" {{ ($data->user_type == "2") ? 'selected' : '' }}>RSM</option>
                                <option value="3" {{ ($data->user_type == "3") ? 'selected' : '' }}>ASM</option>
                                <option value="4" {{ ($data->user_type == "4") ? 'selected' : '' }}>ASE</option>
                            </select>
                            @error('type') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Employee Id <span class="text-danger">*</span> </label>
                            <input type="text" name="employee_id" placeholder="" class="form-control" value="{{$data->employee_id}}">
                            @error('employee_id') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Address <span class="text-danger">*</span> </label>
                            <input type="text" name="address" placeholder="" class="form-control" value="{{$data->address}}">
                            @error('address') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="floating-label">Landmark <span class="text-danger">*</span></label>
                            <input type="text" class="form-control"
                                value="{{$data->landmark}}" name="landmark">
                            @error('landmark')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="floating-label">State</label>
                                @php
                                    $statesOnly = [];
                                    foreach ($state_area as $stateKey => $stateValue) {
                                        if (!in_array($stateValue->state, $statesOnly)) {
                                            array_push($statesOnly, $stateValue->state);
                                        }
                                    }
                                    $selectedState =  $statesOnly[0];
                                @endphp
                                <select name="state" class="form-control" onchange="cityGenerate($(this).val())">
                                    @foreach ($statesOnly as $statesOnlyvalue)
                                        <option value="{{ $statesOnlyvalue }}"
                                            {{ $selectedState == $statesOnlyvalue ? 'selected' : '' }}>
                                            {{ $statesOnlyvalue }}</option>
                                    @endforeach
                                </select>
                                @error('state')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="floating-label">Area</label>
                                <select name="city" class="form-control"></select>
                                @error('city')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Pincode <span class="text-danger">*</span> </label>
                            <input type="text" name="pin" placeholder="" class="form-control" value="{{$data->pin}}">
                            @error('pin') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Aadhar Number <span class="text-danger">*</span> </label>
                            <input type="text" name="aadhar_no" placeholder="" class="form-control" value="{{$data->aadhar_no}}">
                            @error('aadhar_no') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="label-control">Pan Number <span class="text-danger">*</span> </label>
                            <input type="text" name="pan_no" placeholder="" class="form-control" value="{{$data->pan_no}}">
                            @error('pan_no') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="card">
                            <div class="card-header p-0 mb-3">Image <span class="text-danger">*</span></div>
                            <div class="card-body p-0">
                                <div class="w-100 product__thumb">
                                    <label for="thumbnail"><img id="output" src="{{ asset($data->image) }}" /></label>
                                </div>
                                <input type="file" name="image" id="thumbnail" accept="image/*" onchange="loadFile(event)" class="d-none">
                                <script>
                                    var loadFile = function(event) {
                                        var output = document.getElementById('output');
                                        output.src = URL.createObjectURL(event.target.files[0]);
                                        output.onload = function() {
                                            URL.revokeObjectURL(output.src) // free memory
                                        }
                                    };
                                </script>
                            </div>
                            @error('image') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-danger">Update</button>
                            <a type="submit" href="{{ route('admin.user.index') }}" class="btn btn-sm btn-danger">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section> --}}

@endsection
@section('script')
    <script>
        function stateWiseArea(value) {
			$.ajax({
				url: '{{url("/")}}/state-wise-area/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[name="area"]';
					// var displayCollection = (result.data.state == "all") ? "All Area" : "All "+" area";
					// content += '<option value="" selected>'+displayCollection+'</option>';

					let cat = "{{ strtolower($data->city) }}";

					$.each(result.data.area, (key, value) => {
						if(value.area == '') return;
						if (value.area.toLowerCase() == cat) {
                            content += '<option value="'+value.area+'" selected>'+value.area+'</option>';
                        } else {
                            content += '<option value="'+value.area+'">'+value.area+'</option>';
                        }
						//content += '<option value="'+value.area+'">'+value.area+'</option>';
					});
					$(slectTag).html(content).attr('disabled', false);
                }
			});
		}

		$('select[name="state"]').on('change', (event) => {
			var value = $('select[name="state"]').val();
			stateWiseArea(value);
		});

        @if(!empty($data->state))
            stateWiseArea('{{$data->state}}')
        @endif
    </script>
@endsection
