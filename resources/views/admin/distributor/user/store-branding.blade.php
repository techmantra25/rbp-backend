@extends('admin.layouts.app')
@section('page', 'Distributor Branding Request for Retailer')
@section('content')
<section class="store-sec ">
    <div class="row">
        <div class="col-xl-12 order-2 order-xl-1">
            <div class="card search-card">
                <div class="card-body">
                    <div class="search__filter mb-5">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-md-12 mb-3">
                                <p class="text-muted mt-1 mb-0">Showing {{$loginCountWiseReport->count()}} out of {{$loginCountWiseReport->total()}} Entries</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="search-filter-right">
                                    <div class="search-filter-right-el">
                                        <form action="" method="GET">
                                          <div class="search-filter-right">
                                                  <div class="search-filter-right-el">
                                                    <label for="date_from" class="text-muted small">Date from</label>
                                                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from')}}">
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="date_to" class="text-muted small">Date to</label>
                                                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to')}}">
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="distributor" class="small text-muted">Distributor</label>
                                                    <select class="form-control form-control-sm select2" id="distributor" name="distributor_id">
                                                        <option value="" selected disabled>Select</option>
                                                        @foreach ($allDistributors as $item)
                                                            <option value="{{$item->id}}" {{ (request()->input('distributor_id') == $item->id) ? 'selected' : '' }}>{{$item->name}} ({{$item->employee_id}}) ({{$item->state}})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="ase" class="small text-muted">ASE</label>
                                                    <select class="form-control form-control-sm select2" id="ase" name="ase_id">
                                                        <option value="" selected disabled>Select</option>
                                                        @foreach ($allASEs as $item)
                                                            <option value="{{$item->id}}" {{ (request()->input('ase_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="ase" class="small text-muted">ASM</label>
                                                    <select class="form-control form-control-sm select2" id="asm" name="asm_id">
                                                        <option value="" selected disabled>Select</option>
                                                        @foreach ($allASMs as $item)
                                                            <option value="{{$item->id}}" {{ (request()->input('asm_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="state" class="text-muted small">State</label>
                                                    <select name="state_id" id="state" class="form-control form-control-sm select2">
                                                        <option value="" disabled>Select</option>
                                                        <option value="" selected>All</option>
                                                        @foreach ($stateData as $state)
                                                            <option value="{{$state->id}}" {{ request()->input('state') == $state->id ? 'selected' : '' }}>{{$state->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                               
                                                
                                                
                                            </div>
											
                                            <div class="search-filter-right search-filter-right-store mt-4">
                                                
                                                <div class="search-filter-right-el">
                                                    <input type="search" name="keyword" id="term" class="form-control form-control-sm" placeholder="Search by name/ contact/uniquecode" value="{{app('request')->input('keyword')}}" autocomplete="off">
                                                </div>
										
                                                <div class="search-filter-right-el">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">
                                                        <iconify-icon icon="carbon:filter"></iconify-icon> Filter
                                                    </button>
                                                    <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                                        <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                    </a>
                                                </div>
                                               <div class="search-filter-right-el">
                                                    <a   href="{{ route('admin.distributor.branding.index.export.csv',['date_from'=>$request->date_from,'date_to'=>$request->date_to,$request->state,'ase'=>$request->ase_id,'asm'=>$request->asm_id,'distributor'=>$request->distributor_id,'keyword'=>$request->keyword]) }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                                        
                                                        <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                                    </a>
                                                </div> 
                                            </div>
                                             
                                        </form>
                                    </div>
                                    
                                    
                                    
                                   
                                </div>
                            </div>
                            
							
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table admin-table no-sticky">
                        <thead>
                            <tr>
                                <th>#SR</th>
                                <th>Distributor</th>
                                <th class="text-center"><i class="fi fi-br-picture"></i></th>
                                <th>Uniquecode</th> 
                                <th>Retailer Name</th>
                                <th>Retailer Contact</th>
                                <th>Retailer State</th>
                                <th>Retailer City</th>
                                <th>ASE/ASM</th>
                                
                                <th>Remarks</th>
                                <th>Date</th>
								
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($loginCountWiseReport as $index => $item)
							
                            @php
                            if (!empty($_GET['status'])) {
                            if ($_GET['status'] == 'active') {
                            if ($item->status == 0) continue;
                            } else {
                            if ($item->status == 1) continue;
                            }
                            }
                            $distName = \App\Models\Team::select('users.name')->join('users', 'users.id', 'teams.distributor_id')->where('store_id', $item->id)->first();
                            $storename = \App\Models\Team::where('store_id', $item->store_id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();
							//dd($storename->zsm);
                            @endphp
                            @if(!empty($item->stores))
                            <tr>
                                <td>{{ ($loginCountWiseReport->firstItem()) + $index }}</td>
                                <td>{{ $item->distributors->name ??''}}</td>
                                <td class="text-center column-thumb">
                                   @if(!empty($item->stores->image))
                                    <img src="{{ asset($item->stores->image) }}" style="max-width: 80px;max-height: 80px;">
                                    
                                   @endif
                                </td>
                                <td>{{ $item->stores->unique_code??'' }}</td>
                                <td>
                                    {{ ucwords($item->stores->name)}}
                                    
                                </td>
                                
                                <td>{{ $item->stores->contact }}</td>
                                <td>{{ $item->stores->states->name }}</td>
                                <td>{{ $item->stores->areas->name }}</td>
                                <td>{{ $item->stores->users->name }}</td>
                                
                                 <td>{{ $item->remarks }}</td>
                                
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y g:i:s A')}}
                                </td>
								 
                              
                            </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="100%" class="small text-muted">No data found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                    <div class="d-flex justify-content-end">
                        {{ $loginCountWiseReport->appends($_GET)->links() }}
                    </div> 
                </div>
            </div>
        </div>
    </div>
</section>


@endsection
@section('script')
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<script>
   
        var value = {{$state}};

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
    
</script>
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script>
    $(function() {
        $('#btnExport').click(function() {
            console.log("hello");
            //$('#tblHead').css("display","block");
            var url = 'data:application/vnd.ms-excel,' + encodeURIComponent($('#tableWrap').html())
            location.href = url
            return false
            $('#tblHead').css("display", "none");
        });
    });
</script>
@endsection
