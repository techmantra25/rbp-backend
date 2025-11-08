@extends('admin.layouts.app')
@section('page', 'Catalogue')

@section('content')
<section class="pro-sec">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="search__filter">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-md-4">
                                <p class="text-muted mt-1 mb-0">Showing {{$data->count()}} out of {{$data->total()}} Entries</p>
                            </div>
                            <div class="col-12 col-md-8">
                                <form action="{{ route('admin.catalogues.index')}}">
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
                                            <a href="{{ route('admin.catalogues.create') }}" class="btn btn-outline-danger btn-sm">
                                                    <iconify-icon icon="prime:plus-circle"></iconify-icon> Create New Catalogue
                                                </a>
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
                                    <th class="text-center"> PDF</th>
                                    <th>Title</th>
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
                                    <td>{{ $index+1 }}</td>
                                    <td class="text-center column-thumb">
                                        <img src="{{ asset($item->image) }}" style="max-width: 80px;max-height: 80px;">
                                    </td>
                                    <td class="text-center column-thumb">
                                        
										  <a class="btn btn-sm btn-info" href="{{ asset($item->pdf) }}"  download><i class="app-menu__icon fa fa-download"></i> Download</a>
                                    </td>
                                    <td>
                                        <h3 class="text-dark">{{$item->name}}</h3>
                                        <div class="row__action">
                                            <form action="{{ route('admin.catalogues.destroy',$item->id) }}" method="POST">
                                                <a href="{{ route('admin.catalogues.edit', $item->id) }}">Edit</a>
                                                <a href="{{ route('admin.catalogues.show', $item->id) }}">View</a>
                                                <a href="{{ route('admin.catalogues.status', $item->id) }}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure ?')" class="btn btn-link" style="padding: 0;margin: 0;font-size: 14px;line-height: 1;text-decoration: none;color: #dc3545;">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>{{date('d M Y', strtotime($item->start_date))}} - {{ date('d M Y', strtotime($item->end_date))}}</td>
                                    {{-- <td>Published<br/>{{date('d M Y', strtotime($item->created_at))}}</td> --}}
                                    <td>
                                        <span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end">
                            {{ $data->appends($_GET)->links() }}
                        </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

@endsection

@section('script')

@endsection
