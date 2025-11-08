@extends('admin.layouts.app')

@section('title','Profile')

@section('content')
   <section>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                        <h4 class="page__subtitle">Profile</h4>
                        <div class="form-group mb-3">
                             <label class="control-label" for="site_name">Name</label>
                           <input
                    class="form-control"
                    type="text"
                    placeholder="Enter name"
                    id="name"
                    name="name"
                    value="{{ $profile->name }}"
                />
                            @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                           <label class="control-label" for="site_title">Email</label>
                           <input
                    class="form-control"
                    type="text"
                    placeholder="Enter Email ID"
                    id="email"
                    name="email"
                    value="{{ $profile->email }}"
                    readonly
                />
                            @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="control-label" for="site_title">State</label>
                            <textarea class="form-control" type="text" placeholder="Enter State" id="email" name="state" value="" readonly>{{ $profile->state }}</textarea>
                             @error('state') <p class="small text-danger">{{ $message }}</p> @enderror
                         </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-danger">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
          <div class="col-sm-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reset.password.post') }}" enctype="multipart/form-data">
                    @csrf
                        <h4 class="page__subtitle">Change Password</h4>
                        <div class="form-group mb-3">
                              <label class="control-label" for="site_name">Current Password</label>
                <input
                    class="form-control"
                    type="password"
                    placeholder="Enter current password"
                    id="current_password"
                    name="current_password"
                    value=""
                />
                            @error('current_password') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="control-label" for="site_title">New Password</label>
                <input
                    class="form-control"
                    type="password"
                    placeholder="Enter new password"
                    id="new_password"
                    name="new_password"
                    value=""
                />
                            @error('new_password') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label class="control-label" for="site_title">Confirm Password</label>
                <input
                    class="form-control"
                    type="password"
                    placeholder="Enter confirm password"
                    id="new_confirm_password"
                    name="new_confirm_password"
                    value=""
                />
                            @error('new_confirm_password') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                       
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-danger">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>

@endsection