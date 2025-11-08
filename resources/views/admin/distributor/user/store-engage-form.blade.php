@extends('admin.layouts.app')
@section('page', 'Distributor Engagement Activity Form')
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
                                                    <a   href="{{ route('admin.distributor.program.index.export.csv',['date_from'=>$request->date_from,'date_to'=>$request->date_to,'distributor'=>$request->distributor_id,'keyword'=>$request->keyword]) }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                                        
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
                                <th>Distributor Name</th>
                                
                                <th>Distributor Code</th>
                                 <th>Distributor Contact </th>
                                 <th>Email</th>
                                 <th>City</th>
                                  <th>State</th>
                                <th>Target</th>
                                <th>Date</th>
                                <th>Video Downloaded</th>
								
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
                            $storename = \App\Models\Team::where('store_id', $item->retailer_id)->with('distributors','rsm','zsm','nsm','asm','sm','ase')->first();
							//dd($storename->zsm);
                            @endphp
                            @if(!empty($item->distributors))
                            <tr>
                                <td>{{ ($loginCountWiseReport->firstItem()) + $index }}</td>
                                
                               
                                <td>
                                    {{ $item->distributors->name ??''}}
                                    
                                </td>
                                
                                <td>{{ $item->distributors->employee_id ??'' }}</td>
                                <td>
                                    {{  $item->distributors->mobile ??''  }}
                                </td> 
                                <td>{{ $item->distributors->email ??''}}</td>
                                
                                 <td>{{ $item->distributors->city ??''}}</td>
                                <td>{{ $item->distributors->state ??''}}</td>
                                <td>{{ $item->target ??'' }}</td>
                                
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y g:i:s A')}}
                                </td>
								 <td><span class="badge bg-{{ $item->is_download == 1 ? 'success' : 'danger' }}">{{ ($item->is_download==1)? 'yes' : 'no' }}</span></td>
                              
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

@endsection
