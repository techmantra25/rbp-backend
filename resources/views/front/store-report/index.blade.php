
@extends('layouts.app')

@section('page', 'Store wise Sales')
@section('content')

<section class="store-sec ">
    @if (request()->input('store'))
        <p class="text-muted">{{request()->input('name')}}</p>
    @endif
          <div class="card search-card">
              <div class="card-body">
       
                     <div class="row">
                          <div class="col-12 mb-3">
                                  <div class="date-formatter">
                        				<form action="" method="get" class="row">
        
                         
  	                                         <div class="row">
												<div class="col-12 col-md-3 mb-3">
													<div class="report-filter">
  
                                   
              
                                                  <label for="date_from" class="text-muted small">Date from</label>
                                                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{ (request()->input('date_from')) ? request()->input('date_from') : '' }}">
                                              </div>
												 </div>
                                              <div class="col-12 col-md-3 mb-3">
													<div class="report-filter">
                                              
                                                  <label for="date_to" class="text-muted small">Date to</label>
                                                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{ (request()->input('date_to')) ? request()->input('date_to') : '' }}">
                                              </div>
												 </div>
                                              <div class="col-12 col-md-3 mb-3">
													<div class="report-filter">
                                                  <label for="ase" class="small text-muted">Sales Person(ASE/ASM)</label>
                                                    <select class="form-select form-select-sm select2" id="ase" name="user_id">
                                                        <option value="" selected disabled>Select</option>
                                                        @foreach ($user as $item)
                                                            <option value="{{$item->id}}" {{ (request()->input('user_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                              </div>
												 </div>
                                              <div class="col-12 col-md-3 mb-3">
													<div class="report-filter">
                                                  <label for="ase" class="small text-muted">Distributor</label>
                                                    <select class="form-select form-select-sm select2" id="dis" name="distributor_id">
                                                        <option value="" selected disabled>Select</option>
                                                        @foreach ($distributor as $item)
                                                            <option value="{{$item->id}}" {{ (request()->input('user_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                              </div>
												 </div>
											</div>
											<div class="row">
                                          <div class="col-12 col-md-3 mb-3">
													<div class="report-filter">
                                                  <label for="store_id" class="small text-muted">Store</label>
                                                    <select class="form-select form-select-sm select2" id="store_id" name="store_id">
                                                        <option value="" selected disabled>Select</option>
                                                        @foreach ($stores as $item)
                                                            <option value="{{$item->id}}" {{ (request()->input('store_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                              </div>
												 </div>
                                              <div class="col-12 col-md-3 mb-3">
													<div class="report-filter">
                                                  <label for="state" class="text-muted small">State</label>
                                                    <select name="state_id" id="state" class="form-control select2">
                                                        <option value="" disabled>Select</option>
                                                        <option value="" selected>All</option>
                                                        @foreach ($state as $row)
                                                            <option value="{{$row->states->id}}" {{ request()->input('state_id') == $row->states->id ? 'selected' : '' }}>{{$row->states->name}}</option>
                                                        @endforeach
                                                    </select>
                                              </div>
												 </div>
												 <div class="col-12 col-md-3 mb-3">
													<div class="report-filter">
                                              
                                                  <label class="small text-muted">Area</label>
                        							<select class="form-control select2" name="area_id" disabled>
                        								<option value="{{ $request->area_id }}">Select state first</option>
                        							</select>
                                              </div>
                                          </div>
                                          
                                              
                                               <div class="col-12 col-md-3 mb-3">
													<div class="report-filter">
														<label class="text-muted small">Keyword</label>
													  
													  
													  
													 
                                                <input type="search" name="term" class="form-control form-control-sm" placeholder="Search order no" id="term" value="{{app('request')->input('term')}}" autocomplete="off">
                                                </div>
												</div>
											</div>
                                         <div class="col-12 col-md-3 mb-3">
													<div class="report-filter filter-btns">
														<div class="filter-btn-left">
                                                  <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn filter-btn search-filter">
                                                      Filter
                                                  </button>
                                                  <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                                      <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                  </a>
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                                  <a href="{{ route('front.order.csv.download',['date_from'=>$request->date_from,'date_to'=>$request->date_to,'user_id'=>$request->user_id,'store_id'=>$request->store_id,'term'=>$request->term,'state_id'=>$request->state_id,'area_id'=>$request->area_id]) }}" class="btn btn-outline-danger btn-sm filter-btn filter-csv" data-bs-toggle="tooltip" title="Export data in CSV">
                                                      
                                                      <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                                  </a>
                                              </div>
                                          </div>
											</div>
                                      </form>
                                  </div>
                                  
						 </div>
                                  
                                 
                              </div>
                          </div>
	                   </div>
            
                      
                  <div class="card card-search mt-4">
                  <div class="table-responsive">
                      <table class="table table-sm admin-table" id="example5">
        <thead>
        <tr>
            <th>#SR</th>
            <th>Order No</th>
            <th>Report</th>
            <th>Store</th>
            <th>Store State</th>
            <th>Store Area</th>
            <th>Distributor</th>
			<th>Sales Person</th>
			<th>Sales Person Mobile</th>
			<th>Sales Person WhatsApp Number</th>
			<th>Sales Person Pincode</th>
            <th>Order Type</th>
            <th>Order time</th>
            <th>Note</th>
        </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $item)
			    @php
                  $validOrder=DB::table('order_products')->where('order_id',$item->id)->get();
                  $user=DB::table('teams')->where('store_id',$item->store_id)->first();
                  $userName=DB::table('users')->where('id',$user->distributor_id)->first();
                @endphp
            <tr>
                <td>
                   {{($data->firstItem()) + $index}}
                </td>
                <td>
                    <p class="small text-dark mb-1">{{$item->order_no}}</p>
                   
                </td>
                
                @if(count($validOrder)==0)
                <td>
                   <p class="text-danger">Invalid Order</p>
                </td>
                @else
                <td>
                    <div class="btn-group">
                        <a href="{{ route('front.orders.pdf', $item->id) }}" class="btn btn-sm btn-primary">PDF</a>
                        <a href="{{ route('front.orders.report.csv', $item->id) }}" class="btn btn-sm btn-primary">CSV</a>
                    </div>
                </td>
                @endif
                <td>
                    <p class="small text-muted mb-1"> {{$item->stores ? $item->stores->name : ''}}</p>
                </td>
                <td>
                    <p class="small text-muted mb-1"> {{$item->stores ? $item->stores->states->name : ''}}</p>
                </td>
                <td>
                    <p class="small text-muted mb-1"> {{$item->stores ? $item->stores->areas->name : ''}}</p>
                </td>
                <td>
                    <p class="small text-muted mb-1"> {{$userName ? $userName->name : ''}}</p>
                </td>
				<td>
					@if(!empty($item->users))
                    <p class="small text-muted mb-1"> {{$item->users ? $item->users->name : ''}}</p>
					@else
					 <p class="small text-danger mb-1"> No ASE,Self order</p>
					@endif
                </td>
				<td>
                    <p class="small text-muted mb-1"> {{$item->users ? $item->users->mobile : ''}}</p>
                </td>
				<td>
                    <p class="small text-muted mb-1"> {{$item->users ? $item->users->whatsapp_no : ''}}</p>
                </td>
				<td>
                    <p class="small text-muted mb-1"> {{$item->users ? $item->users->pin : ''}}</p>
                </td>
                <td>
                    <p class="small text-muted mb-1"> {{$item->order_type}}</p>
                </td>
                <td>
                    <p class="small">{{date('j M Y g:i A', strtotime($item->created_at))}}</p>
                </td>
                <td>
                    <p class="small text-muted mb-1"> {{$item->comment}}</p>
                </td>
                
            </tr>
            @empty
            <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
            @endforelse
        </tbody>
    </table>
                  </div>

                   <div class="d-flex justify-content-end">
        {{ $data->appends($_GET)->links() }}
    </div>
              </div>
          </div>
      </div>
</div>
  </div>
</section>


@endsection

@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

<script>
        $(document).ready(function() {
            $('.select2').select2();
        });
</script>
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
