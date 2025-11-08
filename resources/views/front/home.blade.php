@extends('layouts.app')

@section('page', 'Dashboard')

@section('content')
<section class="store_listing">
    <div class="container">
        <div class="row">
			<div class="col-12">
				<div class="alert alert-primary alert-dismissible fade show" role="alert">
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg> 
				  Your location is being tracked.
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				  </button>
				</div>
			</div>

            <div class="col-12 mb-4">
                <h5 class="display-4" style="font-size: 1.5rem;">Welcome, {{ auth()->guard('web')->user()->name }}</h5>
				{{-- <h5 class="small text-muted mb-0">{{$userTypeDetail}}</h5> --}}
				<h5 class="text-muted mb-0">
                    @php
                        $designation = auth()->guard('web')->user()->designation;
                    @endphp

                    {{$designation ? $designation : $userTypeDetail}}
                </h5>
            </div>
        </div>

        <div class="row">
            @if(\Auth::guard('web')->user()->user_type != 1 && \Auth::guard('web')->user()->user_type != 2 && \Auth::guard('web')->user()->user_type != 3)
            <div class="col-4">
                <a href="{{ route('front.store.order.call.index') }}">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-phone-call"><path d="M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94m-1 7.98v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                <div class="texts">
                                    <h5 class="card-title m-0">Order on call</h5>
                                    <p class="card-text">Order from store...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-4">
                <a href="{{ route('front.store.index') }}">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                                <div class="texts">
                                    <h5 class="card-title m-0">Store visit</h5>
                                    <p class="card-text">Visit stores from...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            <div class="col-4">
                <a href="{{ route('front.catalouge.download.index') }}">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                <div class="texts">
                                    <h5 class="card-title m-0">Catalogue</h5>
                                    <p class="card-text">Find catalogues...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-4">
                <a href="{{ route('front.sales.report.index') }}">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                                <div class="texts">
                                    <h5 class="card-title m-0">Sales report</h5>
                                    <p class="card-text">Sales report on...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        @php
            $stateReportNameArray = $stateReportValueArray = [];
        @endphp

        @if(\Auth::guard('web')->user()->user_type == 1)
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <canvas id="stateReportDiv" width="400" height="220"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">States report</h5>
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stateWiseReport as $item)
                                    @php
                                        $stateReportNameArray[] = ($item->name == null) ? 'NA' : $item->name;
                                        $stateReportValueArray[] = ($item->value == null) ? 0 : $item->value;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ ($item->name == null) ? 'NA' : $item->name }}
                                            {{-- <a href="{{ route('front.sales.report.detail', ['rsm' => ($item->name == null) ? 'NA' : $item->name, 'state' => $item->state]) }}">{{ ($item->name == null) ? 'NA' : $item->name }}</a> --}}
                                        </td>
                                        <td>Rs {{number_format($item->value)}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a href="{{ route('front.sales.report.index') }}" class="btn btn-sm btn-danger float-right">View complete report</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row mt-4">
            @php
                $regionWiseReportAreaArray = $regionWiseReportValueArray = [];
            @endphp
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Region report</h5>

                        <form action="" method="get" class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Select state</label>
                                    @php
                                        $loggedInUserType = \Auth::guard('web')->user()->user_type;
                                        $loggedInUserState = \Auth::guard('web')->user()->state;

                                        // (count($vp_states) != 0) ? $vp_states = $vp_states : $vp_states = $loggedInUserState;
                                    @endphp
                                    @if (count($vp_states) != 0)
                                        <select name="state" class="form-control form-control-sm">
                                            @foreach ($vp_states as $state)
                                                <option value="{{$state->state}}"
                                                @if (request()->input('state'))
                                                    @if ($state->state == request()->input('state'))
                                                        {{'selected'}}
                                                    @endif
                                                @else
                                                    @if ($state->state == $loggedInUserState)
                                                        {{'selected'}}
                                                    @endif
                                                @endif
                                                >{{$state->state}}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select name="state" class="form-control form-control-sm">
                                            <option value="{{$loggedInUserState}}">{{$loggedInUserState}}</option>
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Date from</label>
                                    <input type="date" name="from" class="form-control form-control-sm" value="{{ (request()->input('from')) ? request()->input('from') : date('Y-m-01') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Date to</label>
                                    <input type="date" name="to" class="form-control form-control-sm" value="{{ (request()->input('to')) ? request()->input('to') : date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="" style="visibility: hidden;">save</label>
                                    <br>
                                    <button type="submit" class="btn btn-sm btn-danger">Apply</button>
                                    <a href="{{route('front.dashboard.index')}}" class="btn btn-sm btn-light border" data-toggle="tooltip" data-placement="top" title="Remove filter">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    </a>
                                </div>
                            </div>
                        </form>

                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Region</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($regionWiseReport as $item)
                                    @php
                                        $regionWiseReportAreaArray[] = $item->area;
                                        $regionWiseReportValueArray[] = ($item->value == null) ? 0 : $item->value;
                                    @endphp
                                    <tr>
                                        <td>{{$item->area}}</td>
                                        <td>Rs {{number_format($item->value)}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a href="{{ route('front.sales.report.index') }}" class="btn btn-sm btn-danger float-right">View complete report</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <canvas id="regionReportDiv" width="400" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>

        @php
            $RSMwiseReportNameArray = $RSMwiseReportValueArray = [];
        @endphp

        @if(\Auth::guard('web')->user()->user_type != 4)
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <canvas id="rsmReportDiv" width="400" height="220"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Regional sales manager report</h5>
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($RSMwiseReport as $item)
                                    @php
                                        $RSMwiseReportNameArray[] = ($item->name == null) ? 'NA' : $item->name;
                                        $RSMwiseReportValueArray[] = ($item->value == null) ? 0 : $item->value;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{-- {{ ($item->name == null) ? 'NA' : $item->name }} --}}
                                            <a href="{{ route('front.sales.report.detail', ['rsm' => ($item->name == null) ? 'NA' : $item->name, 'state' => $item->state]) }}">{{ ($item->name == null) ? 'NA' : $item->name }}</a>
                                        </td>
                                        <td>Rs {{number_format($item->value)}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a href="{{ route('front.sales.report.index') }}" class="btn btn-sm btn-danger float-right">View complete report</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.8.0/dist/chart.min.js"></script>

    <script>
        @if(\Auth::guard('web')->user()->user_type == 1)
        // State report
        var labelValues0 = [];
        var dataValues0 = [];
        labelValues0 = <?php echo json_encode($stateReportNameArray); ?>;
        dataValues0 = <?php echo json_encode($stateReportValueArray); ?>;

        // console.log(labelValues0);

        const ctx0 = document.getElementById('stateReportDiv').getContext('2d');
        const stateReportDiv = new Chart(ctx0, {
            type: 'bar',
            data: {
                labels: labelValues0,
                datasets: [{
                    label: 'State report',
                    data: dataValues0,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        @endif

        // Region report
        var labelValues1 = [];
        var dataValues1 = [];
        labelValues1 = <?php echo json_encode($regionWiseReportAreaArray); ?>;
        dataValues1 = <?php echo json_encode($regionWiseReportValueArray); ?>;

        const ctx = document.getElementById('regionReportDiv').getContext('2d');
        const regionReportDiv = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelValues1,
                datasets: [{
                    label: '{{$loggedInUserState}} state report',
                    data: dataValues1,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // RSM report
        var labelValues2 = [];
        var dataValues2 = [];
        labelValues2 = <?php echo json_encode($RSMwiseReportNameArray); ?>;
        dataValues2 = <?php echo json_encode($RSMwiseReportValueArray); ?>;

        const ctx2 = document.getElementById('rsmReportDiv').getContext('2d');
        const rsmReportDiv = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: labelValues2,
                datasets: [{
                    label: 'Regional sales manager report',
                    data: dataValues2,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
