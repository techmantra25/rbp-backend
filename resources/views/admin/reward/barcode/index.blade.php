@extends('admin.layouts.app')

@section('page', 'Qrcode')

@section('content')
<section>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">

                    <div class="search__filter">
                        <div class="row align-items-center justify-content-between">
                            <div class="col">
                                <ul>
                                    <li class="active"><a href="{{ route('admin.reward.retailer.barcode.index') }}">All <span class="count">({{$data->count()}})</span></a></li>
                                    @php
                                        $activeCount = $inactiveCount = 0;
                                        foreach ($data as $catKey => $catVal) {
                                            if ($catVal->status == 1) $activeCount++;
                                            else $inactiveCount++;
                                        }
                                    @endphp
                                 {{--   <li><a href="{{ route('admin.reward.retailer.barcode.index', ['status' => 'active'])}}">Active <span class="count">({{$activeCount}})</span></a></li>
                                    <li><a href="{{ route('admin.reward.retailer.barcode.index', ['status' => 'inactive'])}}">Inactive <span class="count">({{$inactiveCount}})</span></a></li>--}}
                                </ul>
                            </div>
                            <div class="col-auto">
                                <form action="{{ route('admin.reward.retailer.barcode.index') }}" method="GET">
                                    <div class="row g-3 align-items-center">
										{{--<div class="col-auto">
													<label for="date_from" class="text-muted small">Date from</label>
													<input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from') ?? date('Y-m-01') }}">
												</div>
												<div class="col-auto">
													<label for="date_to" class="text-muted small">Date to</label>
													<input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to') ?? date('Y-m-d') }}">
												</div>--}}
                                        <div class="col-auto">
											
                                            <input type="search" name="term" class="form-control" placeholder="Search here.." id="term" value="{{app('request')->input('term')}}" autocomplete="off">
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Search</button>
                                        </div>
										<div class="col-auto">
											<a href="{{ route('admin.reward.retailer.barcode.create') }}" class="btn btn-sm btn-danger">Generate new Qrcodes</a>
										</div>
										<!--<div class="search-filter-right-el">-->
          <!--                                          <a href="#storelimitModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Qr update</a>-->
          <!--                                     </div>-->
										{{-- <div class="col-auto">
												<a href="{{ route('admin.reward.retailer.barcode.qr.details.csv.export',['date_from'=>request()->input('date_from'),'date_to'=>request()->input('date_to'),'term'=>request()->input('term')]) }}" type="submit" class="btn btn-sm btn-danger">CSV download for QR code scan</a>
										</div>--}}
                                    </div>
                                </form>
								
                                		
							   
                            </div>
                        </div>
                    </div>
                    
                        <div class="filter">
                            <div class="row align-items-center justify-content-between">
                            <div class="col">
                                <div class="row g-3 align-items-center">
                                   {{-- <div class="col-auto">
                                        <select name="bulk_action" class="form-control">
                                            <option value=" hidden selected">Bulk Action</option>
                                            <option value="delete">Delete</option>
                                        </select>
                                    </div>--}}
                                    <div class="col-auto">
                                       {{-- <button type="submit" class="btn btn-outline-danger btn-sm">Apply</button>--}}
										@if(Auth::guard('admin')->user()->email !='testprinter@gmail.com')
                                        
										 
											

													
												
										
										@endif
                                       {{-- <a href="{{ route('admin.reward.retailer.barcode.csv.export') }}" class="btn btn-sm btn-warning">Export all Qrcodes into CSV</a> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                @php
                                    if (!empty($_GET['status'])) {
                                        if ($_GET['status'] == 'active') {
                                            ($activeCount>1) ? $itemShow = 'Items' : $itemShow = 'Item';
                                            echo '<p>'.$activeCount.' '.$itemShow.'</p>';
                                        } elseif ($_GET['status'] == 'inactive') {
                                            ($inactiveCount>1) ? $itemShow = 'Items' : $itemShow = 'Item';
                                            echo '<p>'.$inactiveCount.' '.$itemShow.'</p>';
                                        }
                                    } else {
                                        ($data->count() > 1) ? $itemShow = 'Items' : $itemShow = 'Item';
                                        echo '<p>'.$data->count().' '.$itemShow.'</p>';
                                    }
                                @endphp
                            </div>
                            </div>
                        </div>

                        <table class="table">
                            <thead>
                                <tr>
                                   <th>#</th>
                                    <th>Details</th>
                                    <th>State</th>
                                    <th>Points</th>
                                    <th> Qrcodes</th>
									 <!--<th>Used Qrcodes</th>-->
                                    <th>Validity</th>
                                    <th>Date</th>
                                   {{-- <th>Status</th> --}}
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
                                    <td>
                                    {{$item->name}}
                                    <div class="row__action">
                                        <a href="{{ route('admin.reward.retailer.barcode.view', $item->slug) }}">View</a>
                                       {{-- <a href="{{ route('admin.reward.retailer.barcode.status', $item->id) }}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</a> --}}
                                       
                                    </div>
                                    </td>
                                     <td>{{$item->state->name ??''}}</td>
                                    <td>{{$item->type == 1 ? $item->amount. '' : ''.$item->amount}}</td>
                                    <td>
                                        @php
                                            $couponsCount = \DB::table('retailer_barcodes')->where('slug', $item->slug)->count();
                                        @endphp
                                        <div class="btn-group">
                                            <a href="{{ route('admin.reward.retailer.barcode.view', $item->slug) }}" class="btn btn-sm btn-primary">{{$couponsCount}}</a>
                                            {{-- <a href="{{ route('admin.reward.retailer.barcode.detail.csv.export', $item->slug) }}" class="btn btn-sm btn-warning">Export generated Qrcodes</a> --}}
                                        </div>
                                    </td>
                                    @php
                                            $usedcouponsCount = \DB::table('retailer_barcodes')->where('slug', $item->slug)->where('no_of_usage','!=',0)->count();
                                        @endphp
									<!--<td>-->
                                        
                                        <!--<div class="btn-group">-->
                                        <!--    <a href="{{ route('admin.reward.retailer.barcode.useqrcode', $item->slug) }}" class="btn btn-sm btn-primary">{{$usedcouponsCount}}</a>-->
                                        <!--    {{-- <a href="{{ route('admin.reward.retailer.barcode.detail.csv.export', $item->slug) }}" class="btn btn-sm btn-warning">Export generated Qrcodes</a> --}}-->
                                        <!--</div>-->
         <!--                           </td>-->
                                    <td>{{date('d M Y', strtotime($item->start_date))}} - {{date('d M Y', strtotime($item->end_date))}}</td>
                                    <td>Published<br/>{{date('d M Y', strtotime($item->created_at))}}</td>
                                    {{--<td><span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span></td> --}}
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


<div class="modal fade" id="storelimitModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                bulk upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.qr.sequence.csv.upload') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
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