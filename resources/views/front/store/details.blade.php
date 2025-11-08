@extends('layouts.app')

@section('page', 'Store Detail')

@section('content')


<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="badge bg-primary" style="font-size: 26px;">Retailer</div>
                        </div>

                        <div class="col-md-6 text-end">
                            <a href="{{ route('front.store.list.approve') }}" class="btn btn-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                Return back to store list
                            </a>

                           
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12"><p class="text-dark">Store information</p></div>

                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Image</p>
                                <img src="{{ asset($data->stores->image) }}" alt="" class="w-100">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Store Name</p>
                                <h5>{{ $data->stores->name ? $data->stores->name : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Firm Name</p>
                                <h5> {{ $data->stores->business_name ? $data->stores->business_name : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">GST number</p>
                                <h5> {{ $data->stores->gst_no ? $data->stores->gst_no : 'NA' }}</h5>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12"><p class="text-dark">Manager information</p></div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Distributor</p>
                                <h5> {{ $data->team->distributors->name ? $data->team->distributors->name : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">NSM</p>
                                <h5> {{ $data->team->nsm->name ? $data->team->nsm->name : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">ZSM</p>
                                <h5> {{ $data->team->zsm->name ? $data->team->zsm->name : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">RSM</p>
                                <h5> {{ $data->team->rsm->name ? $data->team->rsm->name : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">ASM</p>
                                <h5> {{ $data->team->asm->name ? $data->team->asm->name : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">SM</p>
                                <h5> {{ $data->team->sm->name ? $data->team->sm->name : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">ASE/ Created by</p>
                                <h5> {{ $data->stores->users->name }}</h5>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12"><p class="text-dark">Owner information</p></div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Owner First Name</p>
                                <h5> {{ $data->stores->owner_fname ? $data->stores->owner_fname : 'NA' }}</h5>
                            </div>
                        </div>
						<div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Owner Last Name</p>
                                <h5> {{ $data->stores->owner_lname ? $data->stores->owner_lname : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Date of Birth</p>
                                <h5> {{ $data->stores->date_of_birth ? $data->stores->date_of_birth : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Date of Anniversary</p>
                                <h5> {{ $data->stores->date_of_anniversary ? $data->stores->date_of_anniversary : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Store OCC number</p>
                                <h5> {{ $data->stores->store_OCC_number ? $data->stores->store_OCC_number : 'NA' }}</h5>
                            </div>
                        </div> 
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12"><p class="text-dark">Contact information</p></div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Contact</p>
                                <h5> {{ $data->stores->contact ? $data->stores->contact : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Whatsapp</p>
                                <h5> {{ $data->stores->whatsapp ? $data->stores->whatsapp : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Email</p>
                                <h5> {{ $data->stores->email ? $data->stores->email : 'NA' }}</h5>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12"><p class="text-dark">Address information</p></div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Address</p>
                                <h5> {{ $data->stores->address ? $data->stores->address : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group position-relative">
                                <p class="small text-muted mb-1">Pincode</p>
                                <h5> {{ $data->stores->pin ? $data->stores->pin : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">State</p>
                                <h5> {{ $data->stores->states->name ? $data->stores->states->name : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Area</p>
                                <h5> {{ $data->stores->areas->name ? $data->stores->areas->name : 'NA' }}</h5>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12"><p class="text-dark">Contact person information</p></div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Full Name</p>
                                <h5> {{ $data->stores->contact_person_fname.' '.$data->stores->contact_person_lname ? $data->stores->contact_person_fname.' '.$data->stores->contact_person_lname : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Contact</p>
                                <h5> {{ $data->stores->contact_person_phone ? $data->stores->contact_person_phone : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Whatsapp</p>
                                <h5> {{ $data->stores->contact_person_whatsapp ? $data->stores->contact_person_whatsapp : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Date of birth</p>
                                <h5> {{ $data->stores->contact_person_date_of_birth ? $data->stores->contact_person_date_of_birth : 'NA' }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3">
                            <div class="form-group">
                                <p class="small text-muted mb-1">Date of anniversary</p>
                                <h5> {{ $data->stores->contact_person_date_of_anniversary ? $data->stores->contact_person_date_of_anniversary : 'NA' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
