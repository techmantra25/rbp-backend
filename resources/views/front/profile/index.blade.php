@extends('layouts.app')

@section('page', 'Profile')

@section('content')
<div class="col-sm-12">
    <div class="profile-card">
        <h3>Profile</h3>

        <section class="store_listing">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8">
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="text-muted">Name</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-dark font-weight-bold">{{ auth()->guard('web')->user()->title }} {{ auth()->guard('web')->user()->name }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="text-muted">Mobile number</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-dark font-weight-bold">{{ auth()->guard('web')->user()->mobile }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="text-muted">State</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-dark font-weight-bold">{{ auth()->guard('web')->user()->state }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="text-muted">Area</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-dark font-weight-bold">{{ auth()->guard('web')->user()->city }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <div>
<div>
@endsection

@section('script')
    <script>

    </script>
@endsection