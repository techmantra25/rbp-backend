@extends('admin.layouts.app')
@section('page', 'Products')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@section('content')
<section class="pro-sec">
    <div class="card card-body">
        <div class="search__filter mb-5">
            <!--<div class="col-md-3">-->
            <!--    <p class="text-muted mt-1 mb-2">Showing {{$data->count()}} out of {{$data->total()}} Entries</p>-->
            <!--</div>-->
            <div class="row align-items-center">
                <div class="col-3">
                    <p class="text-muted mt-0 mb-0">Showing {{$data->count()}} out of {{$data->total()}} Entries</p>
                </div>
                <div class="col-9">
                    <form class="" action="{{ route('admin.products.index') }}">
                        <div class="search-filter-right">
                            <div class="search-filter-right-el">
                                    <select class="form-select form-select-sm select2" aria-label="Default select example" name="cat_id" id="category">
                                    <option value=""  selected>Select Category</option>
                                     @foreach ($category as $index => $item)
                                                 <option value="{{$item->id}}" {{ (request()->input('cat_id') == $item->id) ? 'selected' :  '' }}>{{ $item->name }}</option>
                                     @endforeach
                                    </select>
                            </div>
                            
                            <div class="search-filter-right-el">
                                <select class="form-select form-select-sm select2" aria-label="Default select example" name="collection_id" id="collection">
                                    <option value="" selected disabled>Select Collection</option>
                                    <option value="{ (request()->input('collection_id')) ? 'selected' :  '' }}"></option>
                                </select>
                            </div>
                            
                            <div class="search-filter-right-el">
                                <input type="search" name="keyword" id="term" class="form-control form-control-sm" placeholder="Search by name/ style no." value="{{app('request')->input('keyword')}}" autocomplete="off">
                            </div>
                            <div class="search-filter-right-el">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <iconify-icon icon="carbon:filter"></iconify-icon> Filter
                                </button>
                                <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter" data-bs-toggle="tooltip" title="Clear Filter">
                                    <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                </a>
                            </div>
                            <div class="search-filter-right-el">
                                <a href="{{ route('admin.products.csv.export',['collection_id'=>$request->collection_id,'cat_id'=>$request->cat_id,'term'=>$request->term]) }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                    <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                </a>
                            </div>
                             <div class="search-filter-right-el">
                                <a href="{{ route('admin.products.create') }}" class="btn btn-outline-danger btn-sm">
                                    <iconify-icon icon="prime:plus-circle"></iconify-icon> Create
                                </a>
                            </div>
                             <div class="search-filter-right-el">
                                                    <a href="#namebulkTransferModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Bulk upload</a>
                             </div>
                        </div>
                        </form>
                    </div>
                    
                   
              
            </div>
        </div>
        
        <table class="table admin-table" id="example5">
            <thead>
                <tr>
                    <th>#SR</th>
                    <th class="text-center"><i class="fi fi-br-picture"></i></th>
                    <th>Name</th>
                    <th>Style No.</th>
                    <th>Category</th>
                    <th>Range</th>
                    <th>Price</th>
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
                    @endphp
                <tr>
                    <td>{{($data->firstItem()) + $index}}</td>
                     @if($item->image == "uploads/product/polo_tshirt_front.png" ||
                                                        !file_exists($item->image))
    					 <td class="text-center column-thumb">
    						<img src="{{asset('admin/images/default-placeholder-product-image.png')}}" />
    					</td>
                    
                    @else
    				   <td class="text-center column-thumb">
    						<img src="{{asset($item->image)}}" />
    					</td>
                    @endif
                    <td class="mb-3">
                        {{$item->name}}
                        <div class="row__action">
                            <form action="{{ route('admin.products.destroy',$item->id) }}" method="POST">
                                <a href="{{ route('admin.products.edit', $item->id) }}">Edit</a>
                                <a href="{{ route('admin.products.show', $item->id) }}">View</a>
                                <a href="{{ route('admin.products.status', $item->id) }}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</a>
                                @csrf
                                @method('DELETE')
                               <button type="submit" onclick="return confirm('Are you sure ?')" class="btn-link">Delete</button> 
                            </form>
                        </div>
                    </td>
                    <td>{{$item->style_no}}</td>
                    <td>
                        <a href="{{ route('admin.categories.show', $item->category->id) }}">{{$item->category ? $item->category->name : ''}}</a>
                    </td>
                    <td>
                        <a href="{{ route('admin.collections.show', $item->collection->id) }}">{{$item->collection ? $item->collection->name : ''}}</a>
                    </td>
                    
                    <td>
                        
    					Rs. {{$item->offer_price}}
                    </td>
    
                     <td>Published<br/>{{date('j M Y', strtotime($item->created_at))}}</td> 
                    <td><span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span></td>
                </tr>
                @empty
                <tr><td colspan="100%" class="small text-muted text-center">No data found</td></tr>
                @endforelse
            </tbody>
        </table>       
        
        
        
    </div>



    <div class="d-flex justify-content-end">
        {{ $data->appends($_GET)->links() }}
    </div>
</section>


<div class="modal fade" id="namebulkTransferModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.products.bulk.upload') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/product-sample.csv') }}">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
		$('select[id="category"]').on('change', (event) => {
			var value = $('select[id="category"]').val();

			$.ajax({
				url: '{{url("/")}}/api/collection/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[id="collection"]';
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

   
@endsection
