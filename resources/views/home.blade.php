@extends('layouts.app')

@section('page', 'Dashboard')

@section('content')
<section class="store_listing">
    <div class="container">
        <div class="row mb-4">
			<div class="col-12">
				
			</div>

            <div class="col-6">
                <h5 class="display-4" style="font-size: 1.5rem;">Welcome, {{ auth()->guard('web')->user()->name }}</h5>
				{{-- <h5 class="small text-muted mb-0">{{$userTypeDetail}}</h5> --}}
				<h5 class="text-muted mb-0">
                    @php
                        $designation = auth()->guard('web')->user()->designation;
                    @endphp

                    {{$designation ? $designation : $userTypeDetail}}
                </h5>
            </div>
            <div class="col-6 text-right">
                <form action="" method="get" class="row justify-content-end">
                    {{-- <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Select state</label>
                            @php
                                $loggedInUserType = \Auth::guard('web')->user()->type;
                                $loggedInUserState = \Auth::guard('web')->user()->state;
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
                    </div> --}}
                    {{-- <div class="col-md-4">
                        <div class="form-group">
                            <label for="dateFrom"><h5 class="small text-muted mb-0">Date from</h5></label>
                            <input type="date" name="from" id="dateFrom" class="form-control form-control-sm" value="{{ (request()->input('from')) ? request()->input('from') : date('Y-m-01') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dateTo"><h5 class="small text-muted mb-0">Date to</h5></label>
                            <input type="date" name="to" id="dateTo" class="form-control form-control-sm" value="{{ (request()->input('to')) ? request()->input('to') : date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="" style="visibility: hidden;">save</label>
                            <br>
                            <button type="submit" class="btn btn-sm btn-danger">Apply</button>
                            <a href="{{route('home')}}" class="btn btn-sm btn-light border" data-toggle="tooltip" data-placement="top" title="Remove filter">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            </a>
                        </div>
                    </div> --}}
                </form>
            </div>
        </div>

       
            
          <div class="row mt-4">
            <div class="col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Inactive ASE report</h5>
						<div class="table-responsive">
							<table class="table table-sm table-hover">
								<thead>
									<tr>
										<th>Name</th>
										<th>Contact</th>
										<th>Area</th>
										<th>State</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($inactiveASE as $item)

										<tr>
											<td>

													{{ ($item->name == null) ? 'NA' : $item->name }}

											</td>
											<td>{{ ($item->mobile == null) ? 'NA' : $item->mobile }}</td>
											<td>{{ ($item->city == null) ? 'NA' : $item->city }}</td>
											<td>{{ ($item->state == null) ? 'NA' : $item->state }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div> 

        </div>


    </div>
</section>
@endsection


