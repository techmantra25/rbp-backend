@extends('admin.layouts.app')
@section('page', 'Scheme')

@section('content')
<section class="pro-sec">
    <div class="card card-body">
        <div class="search__filter mb-0">
            <div class="row align-items-center justify-content-between">
                            <div class="col-md-4">
                                <p class="small text-muted mt-1 mb-0">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} Entries</p>
                            </div>
                            <div class="col-12 col-md-8">
                                <form action="{{ route('admin.schemes.index')}}" method="GET">
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
                                        </div>
                                        <div class="search-filter-right-el">
                                             <a href="{{ route('admin.schemes.create') }}" class="btn btn-danger btn-sm">
                        <iconify-icon icon="prime:plus-circle"></iconify-icon> Create New Scheme
                    </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
							
                            </div>
            <!--<div class="row align-items-center">-->
            <!--    <div class="col-12 text-end mb-3">-->
            <!--        <a href="{{ route('admin.schemes.create') }}" class="btn btn-danger btn-sm">-->
            <!--            Create New Scheme-->
            <!--        </a>-->
            <!--    </div>-->
            <!--    <div class="col-md-3">-->
            <!--        <p class="small text-muted mt-1 mb-0">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} Entries</p>-->
            <!--    </div>-->

            <!--    <div class="col-md-9 text-end">-->
            <!--        <form class="row align-items-end justify-content-end" action="{{ route('admin.schemes.index')}}" method="GET">-->
            <!--            <div class="col-auto">-->
            <!--                <input type="search" name="term" id="term" class="form-control" placeholder="Search here.." value="{{app('request')->input('term')}}" autocomplete="off">-->
            <!--            </div>-->
            <!--            <div class="col-auto">-->
            <!--                <div class="btn-group">-->
            <!--                    <button type="submit" class="btn btn-danger btn-sm">-->
            <!--                        Filter-->
            <!--                    </button>-->

            <!--                    <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Clear Filter">-->
            <!--                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>-->
            <!--                    </a>-->

            <!--                    {{-- <a href="" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Export data in CSV">-->
            <!--                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>-->
            <!--                    </a> --}}-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </form>-->
            <!--    </div>-->
            <!--</div>-->
        </div>
        
        
        
        <table class="table admin-table">
            <thead>
                <tr>
                    <th>#SR</th>
                    <th>Image</th>
                    <th>PDF</th>
                    <th>Title</th>
                    <th>Validity</th>
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
                            <img src="{{ asset($item->image) }}" style="height: 80px;">
                        </td>
                        <td>
                            <a href="{{ asset($item->pdf) }}" target="_blank" download class="btn btn-sm btn-primary">Download</a>
                        </td>
                        <td>
                            <p class="text-dark mb-0">{{$item->name}}</p>
                            <div class="row__action">
                                <form action="{{ route('admin.schemes.destroy',$item->id) }}" method="POST">
                                    <a href="{{ route('admin.schemes.edit', $item->id) }}">Edit</a>
                                    <a href="{{ route('admin.schemes.show', $item->id) }}">View</a>
                                    <a href="{{ route('admin.schemes.status', $item->id) }}">{{($item->status == 1) ? 'Current' : 'Past'}}</a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure ?')" class="btn-link">Delete</button>
                                </form>
                            </div>
                        </td>
                        <td>
                            <p class="small text-muted mb-0">{{date('d M Y', strtotime($item->start_date))}} - {{date('d M Y', strtotime($item->end_date))}}</p>
                        </td>
                        <td>
                            <p class="small text-muted mb-0">{{$item->state ??''}}</p>
                        </td>
                        <td>
                            <span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Current' : 'Past'}}</span>
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
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

@endsection

@section('script')
<script>

</script>
@endsection
