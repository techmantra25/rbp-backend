@extends('admin.layouts.app')
@section('page', 'EARN HISTORY')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@section('content')
@php
$allASE=DB::table('users')->where('type',6)->orderby('name')->groupby('name')->get();

@endphp
<section>
    <div class="card card-body">
        <div class="search__filter mb-0">
            <div class="row align-items-center">
				
                <div class="col-md-12 text-end">
                    <form class="row align-items-end justify-content-end" action="{{ route('admin.reward.qrcode.redeem.index') }}">
						    <div class="col-auto">
                                <label for="date_from" class="text-muted small">Date from</label>
                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from') ?? date('Y-m-01') }}">
                            </div>
                            <div class="col-auto">
                                <label for="date_to" class="text-muted small">Date to</label>
                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to') ?? date('Y-m-d') }}">
                            </div>
                       
						
                        <div class="col-auto">
                            <input type="search" name="keyword" id="keyword" class="form-control form-control-sm" placeholder="Search by store name/ contact/qrcode" value="{{request()->input('keyword')}}" autocomplete="off">
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Filter
                                </button>

                                <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Clear Filter">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </a>

                                <a href="{{ route('admin.reward.qrcode.redeem.csv.export', ['date_from'=>$request->date_from,'date_to'=>$request->date_to,'distributor'=>$request->distributor,'keyword'=>$request->keyword]) }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Export data in CSV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                </a>
                                <!--<div class="search-filter-right-el">-->
                                <!--                    <a href="#reedemDelete" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Remove test redeem history</a>-->
                                <!--               </div>-->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    <section class="for-fixed-table">
       <div class="table-responsive" fffffff>
        <table class="table no-sticky"  id="example5">
            <thead>
                <tr>
                    <th>#SR</th> 
                    
                 
    			
    			
                    <th>Store Name</th>
                    <th>Contact</th>
                    <th>Area/State</th>
                    
    				<th>Date</th>
    				<th>Points</th>
    				<th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $index => $item)
                
    			  @php
    			    
    			    
    			    
    			    
    			    
    			    
    			    
    			    //$ase->ase=DB::table('users')->where('id',$ase->ase_id)->first();
    			    $state=DB::table('states')->where('id',$item->state_id)->first();
    			    $area=DB::table('areas')->where('id',$item->area_id)->first();
    			 @endphp
                  
    
                    <tr>
                         <td>{{ $index + $data->firstItem() }}</td> 
    					
    					
    					
    				
                        <td>
                            {{ ucwords($item->name)?? '' }}
                           
                            
                        </td>
    					
                       
                        <td>{{ $item->email }}<br>{{ $item->contact }}</td>
                        <td>
                            {{ $area->name ?? ''}},{{ $state->name ?? ''}}
                        </td> 
    					
                       
    					<td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y g:i:s A')}}
                        </td>
                         <td>
                            {{ $item->amount }}
                        </td> 
                         <td>
                            {{ $item->description }}
                         </td> 
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="small text-muted">No data found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    	</div>
    </section>
        
        
        
    </div>

    <div class="d-flex justify-content-end">
        {{ $data->appends($_GET)->links() }}
    </div>
</section>

<div class="modal fade" id="reedemDelete" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.reward.qrcode.redeem.remove') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/reedem.csv') }}">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
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
</script>
@endsection