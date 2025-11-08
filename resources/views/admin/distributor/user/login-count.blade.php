@extends('admin.layouts.app')
@section('page', 'Store Login Count State Wise')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@section('content')
<section>
    <div class="card card-body">
        <div class="search__filter mb-0">
            <div class="row">
                <div class="col-md-3">
                    
                </div>
                <div class="col-md-9 text-end">
                    <form class="row align-items-end" action="">
                        
						
						<div class="col">
							<label class="small text-muted">State</label>
							<select class="form-control form-control-sm select2" name="state_id">
								<option value="" disabled>Select</option>
								<option value="" selected>All</option>
								@foreach ($state as $index => $item)
								<option value="{{ $item->id }}" {{ ($request->state_id == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
								@endforeach
							</select>
						</div>
						
                        
                        <div class="col">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    
                                    Filter
                                </button>

                                <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Clear Filter">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </a>

                                <a href="{{ route('admin.reward.retailer.user.login.count.export.csv', ['state_id'=>$request->state_id,'area'=>$request->area]) }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Export data in CSV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                </a>
                                <a href="{{ route('admin.reward.retailer.user.login.store.export.csv.data.all') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Export data in CSV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                Export all data
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <table class="table no-sticky" id="example5">
            <thead>
                <tr>
                   
                    <th>State</th>
    				<th>Count</th>
    				
                </tr>
            </thead>
            <tbody>
    					@php
                            $all_orders_total_amount = 0;
    			          
                        @endphp
                @foreach ($loginCountWiseReport as $aseKey => $item)
    			
    							   @php
    									  $stateData=DB::table('states')->where('id',$item->state_id)->first();
                                		$all_orders_total_amount += ($item->count);
    					        
                            		@endphp
                                        <tr>
    
                                            <td>
    
                                                {{$stateData->name ??''}}
                                            </td>
                                            <td> {{number_format($item->count)}}</td>
                                            @if(!empty($stateData))
                                             <td> <a href="{{route('admin.reward.retailer.user.login.store.count',$stateData->id)}}" class="btn btn-primary">View</a></td>
                                             @endif
                                        </tr>
                                        
                                    @endforeach
    						<tr>
    							
    
    							<td>
    								<p class="small text-dark mb-1 fw-bold">TOTAL</p>
    							</td>
    							<td>
    								<p class="small text-dark mb-1 fw-bold">{{ number_format($all_orders_total_amount) }}</p>
    							</td>
    							
                        </tr>
            </tbody>
        </table>     
        
        
    </div>



    
</section>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <script>
		function stateWiseArea(value) {
			$.ajax({
				url: '{{url("/")}}/state-wise-area/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[name="area"]';
					var displayCollection = (result.data.state == "all") ? "All Area" : "All "+" area";
					content += '<option value="" selected>'+displayCollection+'</option>';
					
					let cat = "{{ app('request')->input('area') }}";

					$.each(result.data.area, (key, value) => {
						if(value.area == '') return;
						if (value.area == cat) {
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

		@if(request()->input('state'))
		stateWiseArea("{{ request()->input('state') }}");
		@endif

		function typeWiseUser(value){
			$.ajax({
				url: '{{url("/")}}/type-wise-name/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[id="name"]';
					var displayCollection = (result.data.type == "all") ? "All " : "All "+" name";
					content += '<option value="" selected>'+displayCollection+'</option>';
					let type = "{{ app('request')->input('name') }}";

					$.each(result.data.name, (key, value) => {
						if(value.name == '') return;
						if (value.name == type) {
                            content += '<option value="'+value.name+'" selected>'+value.name+'</option>';
                        } else {
                            content += '<option value="'+value.name+'">'+value.name+'</option>';
                        }
						// content += '<option value="'+value.name+'">'+value.name+'</option>';
					});
					$(slectTag).html(content).attr('disabled', false);
                }
			});
		}

		$('select[id="type"]').on('change', (event) => {
			var value = $('select[id="type"]').val();
			typeWiseUser(value);
		});
		
		@if(request()->input('user_type'))
		typeWiseUser("{{ request()->input('user_type') }}");
		@endif
   </script>
@endsection
