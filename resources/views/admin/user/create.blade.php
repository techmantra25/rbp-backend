@extends('admin.layouts.app')

@section('page', 'User')

@section('content')
<section class="inner-sec1">
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                    @csrf
                        <h4 class="page__subtitle">Add New</h4>
                        
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <div class="form-group ">
									<label>Type <span class="text-danger">*</span> </label>
                                    <div class="form-floating mb-3">
                                        
                                        <select id="user_type" name="type" class="form-control">
                                            <option value="" selected disabled>Select</option>
                                            <option value="1">NSM</option>
                                            <option value="2" >ZSM</option>
                                            <option value="3" >RSM</option>
                                            <option value="4" >SM</option>
                                            <option value="5" >ASM</option>
                                            <option value="6" >ASE</option>
                                            <option value="7" >Distributor</option>
                                        </select>
                                        
                                        @error('type') <p class="small text-danger">{{ $message }}</p> @enderror
                                
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
									<label for="designation">Designation <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="designation" name="designation" placeholder="name@example.com" value="{{ old('designation') ? old('designation') : '' }}">
                                        
                                    </div>
                                    @error('designation') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
										 <label for="employee_id">Employee ID<span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="employee_id" name="employee_id" placeholder="name@example.com" value="{{ old('employee_id') ? old('employee_id') : '' }}">
                                       
                                    </div>
                                    @error('employee_id') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <div class="form-group ">
									<label class="label-control">First Name <span class="text-danger">*</span> </label>
                                    <div class="form-floating mb-3">
                                            <input type="text" name="fname" placeholder="" class="form-control" value="{{old('fname')}}">
                                            @error('fname') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group ">
									<label class="label-control">Last Name <span class="text-danger">*</span> </label>
                                    <div class="form-floating mb-3">
                                        <input type="text" name="lname" placeholder="" class="form-control" value="{{old('lname')}}">
                                        @error('lname') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                       
                            <div class="col-md-4">
                                <div class="form-group ">
									 <label class="label-control">Full Name <span class="text-danger">*</span> </label>
                                    <div class="form-floating mb-3">
                                        <input type="text" name="name" placeholder="" class="form-control" value=" {{old('name')}}">
                                        @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <div class="form-group">
									 <label class="label-control">Email  </label>
                                    <div class="form-floating mb-3">
                                        <input type="email" name="email" placeholder="" class="form-control" value="{{old('email')}}">
                                        @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
									<label class="label-control">Contact <span class="text-danger">*</span> </label>
                                    <div class="form-floating mb-3">
                                        <input type="number" name="mobile" placeholder="" class="form-control" value="{{old('mobile')}}">
                                        @error('mobile') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
									<label class="label-control">WhatsApp Number </label>
                                    <div class="form-floating mb-3">
                                        <input type="number" name="whatsapp_no" placeholder="" class="form-control" value="{{old('whatsapp_no')}}">
                                        @error('whatsapp_no') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                      
                        
                        
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <div class="form-group">
									<label for="state">State <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <select class="form-select select2" id="state" name="state" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($stateDetails as $index => $item)
                                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                            
                                    </div>
                                    @error('state') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
									 <label for="area">City/ Area</label>
                                    <div class="form-floating mb-3">
                                        <select class="form-select select2" id="area" name="area" aria-label="Floating label select example" disabled>
                                            <option value="">Select State first</option>
                                        </select>
                                       
                                    </div>
                                    @error('area') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
							<div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">HQ </label>
                                    <div class="form-floating mb-3">
                                        <select class="form-select select2" id="headquater" name="headquater" aria-label="Floating label select example">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($hq as $index => $item)
                                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                           
                                    </div>
                                    @error('headquater') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                             <div class="col-md-4">
                                <div class="form-group">
									 <label class="label-control">Date of Joining <span class="text-danger">*</span> </label>
                                    <div class="form-floating mb-3">
                                        <input type="date" name="date_of_joining" placeholder="" class="form-control" value="{{old('date_of_joining')}}">
                                        @error('date_of_joining') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
									 <label class="label-control">Password <span class="text-danger">*</span> </label>
                                    <div class="form-floating mb-3">
                                        <input type="password" name="password" placeholder="" class="form-control" value="{{old('password')}}">
                                        @error('password') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
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
    $('select[name="state"]').on('change', (event) => {
        var value = $('select[name="state"]').val();
      
        $.ajax({
            url: '{{url("/")}}/admin/users/state/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="area"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data.area, (key, value) => {
                    content += '<option value="'+value.area+'">'+value.area+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
@endsection
