@extends('layouts.app')

@section('page', 'Team Activity')
<style>
    .date-formatter .form-group h5.day {
        transform: translateY(7px);
        font-size: 16px;
    }
    .date-comment {

        max-width: 800px;
        margin: 20px auto;
        position: relative;
    }
    
    .date-comment::before {
        content:'';
        position: absolute;
        top: 0;
        left: 15%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 2px;
        height: 100%;
        border: 1px dashed #ccc;
    }
    .date-comment li {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 25px;
        position: relative;
    }
    .date-comment li::before {
        content: '';
        position: absolute;
        top: -.2px;
        left:13.8%;
        width: 20px;
        height: 20px;
        background: #ea1c2c;
        border-radius: 50%;
    }
    .date-comment .time {
        
    }
    .comment-content {
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,.1);
        flex: 1;
        margin-left: 115px;
    }
    .profile-card .activity-pagination {
        margin-top: 20px;
    }
    .profile-card .activity-pagination nav {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }
    .profile-card .activity-pagination .page-item.active .page-link {
        background: #ea1c2c;
        border-color: #ea1c2c;
   
    }
    .profile-card .activity-pagination .page-link {
        color: #111;
    }
</style>
@section('content')
<div class="col-sm-12">
    <div class="profile-card">
        <h3>Activity Log</h3>
                <div class="d-flex justify-content-between mb-3">
                        <ul class="nav nav-pills mt-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-current-tab" data-toggle="pill" href="#pills-current" role="tab" aria-controls="pills-current" aria-selected="true">ASM</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link  " id="pills-past-tab" data-toggle="pill" href="#pills-past" role="tab" aria-controls="pills-past" aria-selected="false">ASE</a>
                            </li>
                        </ul>
                    </div>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade" id="pills-past" role="tabpanel" aria-labelledby="pills-past-tab">
                <div class="date-formatter">
                    <form action="" method="get" class="row align-items-center">
                        <div class="col-md-3">
                            <div class="form-group">
                               <h5 class="small day text-muted mb-0">{{ now()->format('l') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="dateTo"><h5 class="small text-muted mb-0">Date</h5></label>
                                <input type="date" name="to" id="dateTo" class="form-control form-control-sm" value="{{ (request()->input('to')) ? request()->input('to') : date('Y-m-d') }}">
                            </div>
                        </div>
                       
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="collection"><h5 class="small text-muted mb-0">ASE</h5></label>
                                <select class="form-control form-control-sm" name="user" id="user">
                                    <option value="" selected>Select</option>
                                  
                                    @foreach ($aseData  as $index => $item)
                                    <option value="{{ $item->ase_id }}" {{ ($request->user == $item->ase_id) ? 'selected' : '' }}>{{ $item->ase->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3 text-right">
                            <div class="form-group pt-4">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="submit" class="btn btn-sm btn-danger">Apply</button>
        
                                    <a type="button" href="{{ url()->current() }}" class="btn btn-sm btn-light border" data-toggle="tooltip" data-placement="top" title="Remove filter">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
        		<div class="row">
                    <div class="col-12">
                        @if (request()->input('from') || request()->input('to'))
        				  @if (request()->input('user'))
        				    @php
        				  		$userDetails=\App\Models\User::where('id',request()->input('user'))->first();
        				    @endphp
        				<p class="text-dark">Activity log for <strong>{{$userDetails->name}}</strong> date <strong>{{ date('j F, Y', strtotime(request()->input('to'))) }}</strong>
        					@else
        					<p class="text-dark">Activity log for <strong>{{Auth::guard('web')->user()->name}}</strong> date <strong>{{ date('j F, Y', strtotime(request()->input('to'))) }}</strong>
        						@endif
                        @else
                            <p class="text-dark">Activity log for <strong>Team</strong>  date <strong>{{date('j F, Y')}}</strong></p>
                        @endif
                    </div>
                </div>
        		
        
                <div class="row">
                    <div class="col-12">
                        <ul class="date-comment list-unstyled">
                            @forelse($activity as $orderKey => $orderValue)
                            <li>
                                <div class="time">
                                    {{date('h:i A', strtotime($orderValue->time))}}
                                </div>
                                <div class="comment-content">
                                    <p>{{$orderValue->comment}}</p>
                                </div>
                            </li>
        					@empty
        					<p>No Activity Log found</p>
                            @endforelse
                            
                        </ul>
                    </div>
                </div>
                                 
                    <div class="activity-pagination">
                        {{ $activity->appends($_GET)->links() }}
                    </div>
            </div>
      
        
        
        
       
           <div class="tab-pane fade show active" id="pills-current" role="tabpanel" aria-labelledby="pills-current-tab">
                <div class="date-formatter">
                    <form action="" method="get" class="row align-items-center">
                        <div class="col-md-3">
                            <div class="form-group">
                               <h5 class="small day text-muted mb-0">{{ now()->format('l') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="dateTo"><h5 class="small text-muted mb-0">Date</h5></label>
                                <input type="date" name="to" id="dateTo" class="form-control form-control-sm" value="{{ (request()->input('to')) ? request()->input('to') : date('Y-m-d') }}">
                            </div>
                        </div>
                       
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="collection"><h5 class="small text-muted mb-0">ASM</h5></label>
                                <select class="form-control form-control-sm" name="user" id="user">
                                    <option value="" selected>Select</option>
                                  
                                    @foreach ($asmData  as $index => $item)
                                    <option value="{{ $item->asm_id }}" {{ ($request->user == $item->asm_id) ? 'selected' : '' }}>{{ $item->asm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3 text-right">
                            <div class="form-group pt-4">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <button type="submit" class="btn btn-sm btn-danger">Apply</button>
        
                                    <a type="button" href="{{ url()->current() }}" class="btn btn-sm btn-light border" data-toggle="tooltip" data-placement="top" title="Remove filter">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
        		<div class="row">
                    <div class="col-12">
                        @if (request()->input('from') || request()->input('to'))
        				  @if (request()->input('user'))
        				    @php
        				  		$userDetails=\App\Models\User::where('id',request()->input('user'))->first();
        				    @endphp
        				<p class="text-dark">Activity log for <strong>{{$userDetails->name}}</strong> date <strong>{{ date('j F, Y', strtotime(request()->input('to'))) }}</strong>
        					@else
        					<p class="text-dark">Activity log for <strong>{{Auth::guard('web')->user()->name}}</strong> date <strong>{{ date('j F, Y', strtotime(request()->input('to'))) }}</strong>
        						@endif
                        @else
                            <p class="text-dark">Activity log for <strong>Team</strong>  date <strong>{{date('j F, Y')}}</strong></p>
                        @endif
                    </div>
                </div>
        		
        
                <div class="row">
                    <div class="col-12">
                        <ul class="date-comment list-unstyled">
                            @forelse($asmactivity as $orderKey => $orderValue)
                            <li>
                                <div class="time">
                                    {{date('h:i A', strtotime($orderValue->time))}}
                                </div>
                                <div class="comment-content">
                                    <p>{{$orderValue->comment}}</p>
                                </div>
                            </li>
        					@empty
        					<p>No Activity Log found</p>
                            @endforelse
                            
                        </ul>
                    </div>
                </div>
                                 
                    <div class="activity-pagination">
                        {{ $activity->appends($_GET)->links() }}
                    </div>
            </div>
        </div>
        
        
    </div>
</div>
@endsection
@section('script')
    
@endsection
