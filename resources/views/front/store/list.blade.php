@extends('layouts.app')

@section('page', 'Store List')

@section('content')
<div class="col-sm-12">
    <div class="profile-card">
        <h3 class="mb-0">Store List</h3>
        <section class="store_listing">
            <div class="row">
                <div class="col-12">
                    <div class="date-formatter">
                        <form action="" method="get" class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dateFrom"><h5 class="small text-muted mb-0">Date from</h5></label>
                                    <input type="date" name="date_from" id="dateFrom" class="form-control form-control-sm" value="{{request()->input('date_from')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dateTo"><h5 class="small text-muted mb-0">Date to</h5></label>
                                    <input type="date" name="date_to" id="dateTo" class="form-control form-control-sm" value="{{ (request()->input('date_to'))}}">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="distributor" class="small text-muted">Distributor</label>
                                        <select class="form-control form-control-sm select2" id="distributor" name="distributor_id">
                                            <option value="" selected disabled>Select</option>
                                            @foreach ($allDistributors as $item)
                                                <option value="{{$item->id}}" {{ (request()->input('distributor_id') == $item->name) ? 'selected' : '' }}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="ase" class="small text-muted">ASE</label>
                                <select class="form-control form-control-sm select2" id="ase" name="ase">
                                    <option value="" selected disabled>Select</option>
                                    @foreach ($allASEs as $item)
                                        <option value="{{$item->id}}" {{ (request()->input('ase_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label for="ase" class="small text-muted">ASM</label>
                                <select class="form-control form-control-sm select2" id="asm" name="asm_id">
                                    <option value="" selected disabled>Select</option>
                                    @foreach ($allASMs as $item)
                                        <option value="{{$item->id}}" {{ (request()->input('asm_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label for="state" class="text-muted small">State</label>
                                    <select name="state_id" id="state" class="form-control form-control-sm select2">
                                        <option value="" disabled>Select</option>
                                        <option value="" selected>All</option>
                                        @foreach ($state as $state)
                                            <option value="{{$state->id}}" {{ request()->input('state_id') == $state->id ? 'selected' : '' }}>{{$state->name}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="small text-muted">Area</label>
                                <select class="form-control form-control-sm select2" name="area_id" disabled>
                                    <option value="{{ $request->area_id }}">Select state first</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="small text-muted">Keyword</label>
                                <input type="search" name="keyword" id="term" class="form-control form-control-sm" placeholder="Search by name/ contact" value="{{app('request')->input('keyword')}}" autocomplete="off">
                            </div>
                            <div class="col-md-6"></div>
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

                    <hr>

                            <div class="row">
                                

                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>#SR</th>
                                            <th class="text-center"><i class="fi fi-br-picture"></i></th>
                                            <th>Uniquecode</th> 
                                            <th>Store</th>
                                            <th>Created by</th>
                                            <th>Contact</th>
                                            <th>Distributor</th>
                                            <th>Address</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($data as $index => $item)
            
                                        @php
                                        if (!empty($_GET['status'])) {
                                        if ($_GET['status'] == 'active') {
                                        if ($item->status == 0) continue;
                                        } else {
                                        if ($item->status == 1) continue;
                                        }
                                        }
                                          $distName = \App\Models\Team::select('users.name')->join('users', 'users.id', 'teams.distributor_id')->where('store_id', $item->id)->first();
                                        
                                        @endphp
                                        <tr>
                                            <td>{{ ($data->firstItem()) + $index }}</td>
                                            <td class="text-center column-thumb">
                                               @if(!empty($item->image))
                                                <img src="{{ asset($item->image) }}" style="max-width: 80px;max-height: 80px;">
                                                
                                               @endif
                                            </td>
                                            <td>{{ $item->unique_code }}</td>
                                            <td>
                                                {{ ucwords($item->name) }}
                                                <p class="small text-muted">- {{ ucwords($item->business_name) }}</p>
                                                <div class="row__action">
                                                   
                                                    <a class="btn-link" href="{{ route('front.store.detail', $item->id) }}">View</a>
													 <a class="btn-link" href="{{ route('front.store.edit', $item->id) }}">Edit</a>
                                                    <a href="{{ route('front.store.list.approve.status.update', $item->id) }}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</a>
                                                        
                                                </div>
                                            </td>
                                            <td>
                                                {{ $item ? $item->users->name : '' }}
                                                <p class="small text-muted">@if($item->users->type==5)<span>(ASM)</span>@elseif($item->users->type==6)<span>(ASE)</span>@endif</p>
                                            </td>
                                            <td>{{ $item->email }}<br>{{ $item->contact }}</td>
                                            <td>
                                                {{ $distName ? $distName->name : '' }}
                                            </td> 
                                            <td>{{ ucwords($item->address) }}<br>{{ $item->areas->name }}<br>{{ $item->areas->name }}<br>{{ $item->states->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y g:i:s A')}}
                                            </td>
                                            <td><span class="badge bg-{{($item->zsm_approval == 1) ? 'success' : 'danger'}}">{{($item->zsm_approval == 1) ? 'Active' : 'Inactive'}}</span></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="100%" class="small text-muted">No data found</td>
                                        </tr>
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
        </section>
    </div>
</div>
@endsection

@section('script')
    <script>
		$('select[name="collection"]').on('change', (event) => {
			var value = $('select[name="collection"]').val();

			$.ajax({
				url: '{{url("/")}}/api/category/product/collection/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[name="style_no"]';
					var displayCollection = (result.data.collection_name == "all") ? "All products" : "All "+result.data.collection_name+" products";

					content += '<option value="" selected>'+displayCollection+'</option>';
					$.each(result.data.product, (key, value) => {
						content += '<option value="'+value.product_style_no+'">'+value.product_style_no+'</option>';
					});
					$(slectTag).html(content).attr('disabled', false);
                }
			});
		});
    </script>
     <script>
		$('select[name="category"]').on('change', (event) => {
			var value = $('select[name="category"]').val();

			$.ajax({
				url: '{{url("/")}}/api/collection/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[name="collection"]';
					var displayCollection =  "All";

					content += '<option value="" selected>'+displayCollection+'</option>';
					$.each(result.data, (key, value) => {
						content += '<option value="'+value.id+'">'+value.name+'</option>';
					});
					$(slectTag).html(content).attr('disabled', false);
                }
			});
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

