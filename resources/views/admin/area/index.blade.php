@extends('admin.layouts.app')
@section('page', 'Area')

@section('content')
<section class="pro-sec">
    <div class="card card-body mb-4">
        <div class="search__filter mb-0">
            
            <div class="row align-items-center justify-content-between">
                            <!--<div class="col-md-4">-->
                            <!--   <p class="small text-muted mt-1 mb-0">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} Entries</p>-->
                            <!--</div>-->
                            <div class="col-3"><p class="small text-muted mt-0 mb-0">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} Entries</p></div>
                            <div class="col-9">
                                
                                <form action="{{ route('admin.areas.index')}}" method="GET">
                                    <div class="search-filter-right">
                                        <div class="search-filter-right-el">
                                             <input type="search" name="term" id="term" class="form-control" placeholder="Search here.." value="{{app('request')->input('term')}}" autocomplete="off">
                                        </div>
                                        <div class="search-filter-right-el">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                    <iconify-icon icon="carbon:filter"></iconify-icon> Filter
                                </button>

                                <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter" data-bs-toggle="tooltip" title="Clear Filter">
                                    <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                </a>
                                 {{-- <a href="#csvUploadModal" data-bs-toggle="modal" class="btn btn-danger mt-2">Bulk upload</a>--}}
                                        </div>
                                        <div class="search-filter-right-el">
                                             <a href="{{ route('admin.areas.create') }}" class="btn btn-danger btn-sm">
                       <iconify-icon icon="prime:plus-circle"></iconify-icon> Create New Area
                    </a>
                                        </div>
                                        
                                            
                                      
                                    </div>
                                </form>
                            </div>
                            
							
                            </div>
            
        </div>
        
        
    <table class="table admin-table">
        <thead>
            <tr>
                <th>#SR</th>
                <th>Title</th>
                <th>State</th>
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
                    <td>{{ $index + $data->firstItem() }}</td>
                   
                    <td>
                        <p class="text-dark mb-0">{{$item->name ?? ''}}</p>
                        <div class="row__action">
                            <form action="{{ route('admin.areas.destroy',$item->id) }}" method="POST">
                                <a href="{{ route('admin.areas.edit', $item->id) }}">Edit</a>
                                <a href="{{ route('admin.areas.show', $item->id) }}">View</a>
                                <a href="{{ route('admin.areas.status', $item->id) }}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</a>
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure ?')" class="btn-link">Delete</button>
                            </form>
                        </div>
                    </td>
                    <td>
                        <p class="text-dark mb-0">{{$item->states->name ?? ''}}</p>
                    </td>
                    <td>
                        <span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
            @endforelse
        </tbody>
    </table>    
        
        
        
        
        
    </div>



    <div class="d-flex justify-content-end">
        {{$data->appends($_GET)->links()}}
    </div>

</section>
{{-- bulk upload variation modal --}}
<div class="modal fade" id="csvUploadModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.areas.csv.upload') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/static/product-variation-sample.csv') }}">Download Sample CSV</a>
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
<script>

</script>
@endsection
