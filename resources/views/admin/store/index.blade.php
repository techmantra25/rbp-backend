@extends('admin.layouts.app')
@section('page', 'Store')
@section('content')
<section class="store-sec ">
    <div class="row">
        <div class="col-xl-12 order-2 order-xl-1">
            <div class="card search-card">
                <div class="card-body">
                    <div class="search__filter mb-5">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-md-12 mb-3">
                                <p class="text-muted mt-1 mb-0">Showing {{$data->count()}} out of {{$data->total()}} Entries</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="search-filter-right">
                                    <div class="search-filter-right-el">
                                        <form action="{{ route('admin.stores.index') }}" method="GET">
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
                                                    <label for="ase" class="small text-muted">ASE</label>
                                                    <select class="form-control form-control-sm select2" id="ase" name="ase_id">
                                                        <option value="" selected disabled>Select</option>
                                                        @foreach ($allASEs as $item)
                                                            <option value="{{$item->id}}" {{ (request()->input('ase_id') == $item->id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                <div class="search-filter-right-el">
                                                    <label for="state" class="text-muted small">State</label>
                                                    <select name="state_id" id="state" class="form-control form-control-sm select2">
                                                        <option value="" disabled>Select</option>
                                                        <option value="" selected>All</option>
                                                        @foreach ($state as $state)
                                                            <option value="{{$state->id}}" {{ request()->input('state_id') == $state->id ? 'selected' : '' }}>{{$state->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">Area</label>
                                                    <select class="form-control form-control-sm select2" name="area_id" disabled>
                                                        <option value="{{ $request->area_id }}">Select state first</option>
                                                    </select>
                                                </div>
                                                <!--<div class="search-filter-right-el">-->
                                                <!--    <label class="small text-muted">Area</label>-->
                                                <!--    <select class="form-control form-control-sm select2" name="area_id" disabled>-->
                                                <!--        <option value="{{ $request->area_id }}">Select state first</option>-->
                                                <!--    </select>-->
                                                <!--</div>-->
                                                <!--<div class="search-filter-right-el">-->
                                                <!--    <input type="search" name="keyword" id="term" class="form-control form-control-sm" placeholder="Search by name/ contact" value="{{app('request')->input('keyword')}}" autocomplete="off">-->
                                                <!--</div>-->
                                                <!--<div class="search-filter-right-el">-->
                                                <!--    <button type="submit" class="btn btn-outline-danger btn-sm">-->
                                                <!--        <iconify-icon icon="carbon:filter"></iconify-icon> Filter-->
                                                <!--    </button>-->
                                                <!--    <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter" data-bs-toggle="tooltip" title="Clear Filter">-->
                                                <!--        <iconify-icon icon="basil:cross-outline"></iconify-icon>-->
                                                <!--    </a>-->
                                                <!--</div>-->
                                                
                                            </div>
											
                                            <div class="search-filter-right search-filter-right-store mt-4">
                                                
                                                <div class="search-filter-right-el">
                                                    <input type="search" name="keyword" id="term" class="form-control form-control-sm" placeholder="Search by name/ contact" value="{{app('request')->input('keyword')}}" autocomplete="off">
                                                </div>
												<div class="search-filter-right-el">
													<label for="ase" class="small text-muted">Status</label>
													<select class="form-select form-select-sm select2" id="status" name="status_id">
														 <option value="" >Select</option>
                                                        
															<option value="active" {{ request()->input('status_id') == 'active' ? 'selected' : '' }}>Active</option>
														  <option value="inactive" {{ request()->input('status_id') == 'inactive' ? 'selected' : '' }}>Inactive</option>
													</select>
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
                                        <a href="{{ route('admin.stores.csv.export',['date_from'=>$request->date_from,'date_to'=>$request->date_to,'ase'=>$request->ase_id,'asm'=>$request->asm_id,'distributor_id'=>$request->distributor_id,'state_id'=>$request->state_id,'area_id'=>$request->area_id,'keyword'=>$request->keyword,'status_id'=>$request->status_id,'zsm_approval_id'=>$request->zsm_approval_id]) }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                            
                                            <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                        </a>
                                        
                                    </div>
                                            <div class="search-filter-right-el">
                                                <a href="#csvUploadModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Bulk stock data upload</a>
                                            </div>
                                            {{--<div class="search-filter-right-el">
                                                    <a href="#panUploadModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Pincode Bulk update</a>
                                               </div>
                                               <div class="search-filter-right-el">
                                                    <a href="#bulkTransferModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon> Bulk transfer to distributor</a>
                                               </div>
                                               <div class="search-filter-right-el">
                                                    <a href="#namebulkTransferModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Store details Bulk update</a>
                                               </div>
                                                <div class="search-filter-right-el">
                                                    <a href="#multipledisbulkTransferModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Multiple distributor Bulk update</a>
                                               </div>
                                               <div class="search-filter-right-el">
                                                    <a href="#storelimitModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Store Scan Limit update</a>
                                               </div>
                                    </div>--}}
                                             
                                        </form>
                                    </div>
                                    
                                    
                                           
                                    
                                    {{--<div class="search-filter-right-el">
                                        <a href="{{ route('admin.stores.inactive') }}" class="btn btn-outline-danger btn-sm">
                                            <iconify-icon icon="prime:plus-circle"></iconify-icon> Inactive Store List
                                        </a>
                                    </div>--}}
                                    
                                   
                                </div>
                            </div>
                                            <div class="mb-3">
                                                   <button class="btn btn-primary import-btn" data-url="{{ url('/admin/stores/import/state')}}">Step 1: State List Save</button>
                                                    <button class="btn btn-primary import-btn" data-url="{{ url('/admin/stores/import/beat')}}">Step 2: Beat List Save</button>
                                                    <button class="btn btn-primary import-btn" data-url="{{ url('/admin/stores/import/employee')}}">Step 3: Employee List Save</button>
                                                    <button class="btn btn-primary import-btn" data-url="{{ url('/admin/stores/import/retailer')}}">Step 4: Retailer List Save</button>
                                            </div>
                                                <div id="resultBox" class="alert d-none"></div>
                                                    <!-- Failed Modal -->
                                                <div class="modal fade" id="failedModal" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Failed Records</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>API ID</th>
                                                                            <th>Name</th>
                                                                            <th>Error</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="failedList"></tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <a href="{{ route('admin.import.downloadFailed') }}" class="btn btn-success">Download Failed List</a>
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
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
                                <!--<th class="text-center"><i class="fi fi-br-picture"></i></th>-->
                                <th>Uniquecode</th> 
                                <th>Store</th>
                                <th>Created by</th>
                                <th>Contact</th>
                               
                                <th>Address</th>
                                <th>Date</th>
							
                                <th>Status</th>
                                 <th>Wallet Balance</th>
                                 <th></th>
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
                              
                            @endphp
                            <tr>
                                <td>{{ ($data->firstItem()) + $index }}</td>
                                <!--<td class="text-center column-thumb">-->
                                <!--   @if(!empty($item->image))-->
                                <!--    <img src="{{ asset($item->image) }}" style="max-width: 80px;max-height: 80px;">-->
                                    
                                <!--   @endif-->
                                <!--</td>-->
                                <td>{{ $item->unique_code }}</td>
                                <td>
                                    {{ ucwords($item->name) }}
                                    
                                    <div class="row__action">
                                        <form action="{{ route('admin.stores.destroy',$item->id) }}" method="POST">
                                            <a href="{{ route('admin.stores.edit', $item->id) }}">Edit</a>
                                            <a href="{{ route('admin.stores.show', $item->id) }}">View</a>
											 
                                            @csrf
                                            @method('DELETE')
                                            @if($item->status == 0)
                                           <!--<button type="submit" onclick="return confirm('Are you sure ?')" class="btn-link" style="">Delete</button>-->
                                           @endif
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    {{  $item->users->name ?? '' }}
                                    @if(!empty($item->users))<p class="small text-muted">@if($item->users->type==5)<span>(ASM)</span>@elseif($item->users->type==6)<span>(ASE)</span>@endif</p>@endif
                                </td>
                                <td>{{ $item->email }}<br>{{ $item->contact }}</td>
                                
                                
                               
                                <td>{{ ucwords($item->address) }}<br>{{ $item->areas->name ??'' }}<br>{{ $item->areas->name ??''}}<br>{{ $item->states->name ??''}}</td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y g:i:s A')}}
                                </td>
								
                                <td><span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span></td>
                                 <td>{{ $item->wallet ??'' }}</td>
                                 <td>
                                        <td>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#manualAdjustment-{{ $item->id }}">
                                                Manual Adjustment
                                            </button>
                                        </td>
                                        
                                        <!-- Modal OUTSIDE the table row -->
                                        <div class="modal fade" id="manualAdjustment-{{ $item->id }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" >
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('admin.stores.adjustment.save', $item->id) }}">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <label>Balance</label>
                                                            <input type="number" name="amount" class="form-control" required />
                                        
                                                            <label>Type</label>
                                                            <select name="status" class="form-control" required>
                                                                <option value="increment">Credit</option>
                                                                <option value="decrement">Debit</option>
                                                            </select>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-success">Save</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                   

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
    
    
     <!-- Modal -->
                                   
</section>

<div class="modal fade" id="csvUploadModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.stores.stock.save') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/stock.csv') }}">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="panUploadModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.store.pan.update') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="bulkTransferModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.store.bulk.transfer') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/distributor.csv') }}">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="namebulkTransferModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.name.csv.upload') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/name update_Sheet1.csv') }}">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="multipledisbulkTransferModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Multiple distributor Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.store.bulk.distributor.csv.upload') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/bulk_distributor.csv') }}">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="storelimitModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                bulk upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.store.limit.csv.upload') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

document.querySelectorAll('.modal').forEach(function(modal) {
  document.body.appendChild(modal);
});
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.import-btn').click(function(e) {
        e.preventDefault();
        let url = $(this).data('url');

        let box = $('#resultBox');
        box.removeClass('alert-success alert-danger').addClass('alert-info').text('Processing...').removeClass('d-none');

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    box.removeClass('alert-info alert-success').addClass('alert-danger').text(data.error);
                } else {
                    box.removeClass('alert-info alert-danger').addClass('alert-success')
                        .text(`Success: ${data.successCount}, Failed: ${data.failedCount}`);

                    let failedList = $('#failedList');
                    failedList.empty();

                    if (data.failedList && data.failedList.length > 0) {
                        $.each(data.failedList, function(i, item) {
                            failedList.append(`<tr>
                                <td>${item.api_id}</td>
                                <td>${item.name}</td>
                                <td>${item.error}</td>
                            </tr>`);
                        });
                        $('#failedModal').modal('show');
                    }
                }
            },
            error: function(xhr, status, error) {
                box.removeClass('alert-info alert-success').addClass('alert-danger')
                    .text('AJAX Error: ' + error);
            }
        });
    });
});
</script>
@endsection
