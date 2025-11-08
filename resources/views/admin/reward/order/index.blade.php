@extends('admin.layouts.app')

@section('page', 'Gift Order')
<style>
    .chat_box {
        width: 300px;
        height: 100%;
        position: fixed;
        top: 0;
        right: 0;
        z-index: 999;
        display: flex;
        background: #fff;
        transform: translateX(100%);
        transition: all ease-in-out 0.5s;
    }
    .chat_box.active {
        transform: translateX(0%);
        box-shadow: 10px 10px 100px 10px rgb(0 0 0 / 30%);
    }
    .chat_box .card {
        width: 100%;
        margin: 0;
    }
    .chat_box .card-body {
        overflow: auto;
        margin-bottom: 42px;
        display: flex;
        flex-direction: column-reverse;
    }
    .chat_box .card-footer {
        position: fixed;
        bottom: 0;
    }
    .text-body {
        border-radius: 10px 10px 0 10px;
    }
    .text-body p {
        white-space: normal;
        text-align: right;
        color: #fff;
        line-height: 1.25;
    }
</style>
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<section>
    <div class="card card-body">
        <div class="row">
            <div class="col-md-12">
                <form action="" method="GET">
                    <div class="row g-3 align-items-end mb-4">
                        <div class="col-auto">
                            <label for="date_from" class="text-muted small">Date from</label>
                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from') ?? date('Y-m-01') }}">
                        </div>
                        <div class="col-auto">
                            <label for="date_to" class="text-muted small">Date to</label>
                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to') ?? date('Y-m-d') }}">
                        </div>

                        
                        <div class="col-auto">
                            <label for="product" class="small text-muted">Product</label>
                            <select name="product" class="form-control select2" id="product">
                                <option value="" disabled>Select</option>
                                <option value="" {{request()->input('product') == 'all' ? 'selected' : ''}}>All</option>
                                @foreach ($products as $product)
                                    <option value="{{$product->id}}" {{request()->input('product') == $product->id ? 'selected' : ''}}>{{$product->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <label for="product" class="small text-muted">Status</label>
                            <select name="status" class="form-control select2" id="status">
                                <option value="" >Select</option>
                                <option value="6" {{ request()->input('status') == 6 ? 'selected' : '' }}>Waiting for NOC</option>
                                <option value="1" {{ request()->input('status') == 1 ? 'selected' : '' }}>NOC Approved</option>
                                <option value="2" {{ request()->input('status') == 2 ? 'selected' : '' }}>Address Confirmed</option>
                                <option value="3" {{ request()->input('status') == 3 ? 'selected' : '' }}>Gift Ordered</option>
                                <option value="7" {{ request()->input('status') == 7 ? 'selected' : '' }}>Gift Dispatched</option>
                                <option value="4" {{ request()->input('status') == 4 ? 'selected' : '' }}>Gift Delivered</option>
                                <option value="5" {{ request()->input('status') == 5 ? 'selected' : '' }}>Cancelled</option>
                                
                            </select>
                        </div>
                        <div class="col-auto">
                            <label for="" class="small text-muted">Search for Order No/store name/store contact</label>
                            <input type="search" name="term" id="orderNo" class="form-control" placeholder="Search here.." value="{{app('request')->input('term')}}" autocomplete="off">
                        </div>

                        <div class="col-auto">
                            {{-- <button type="submit" class="btn btn-outline-danger btn-sm">Search</button> --}}
                            <div class="btn-group">
                                <button type="submit" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-danger" data-bs-original-title="Search"> <i class="fi fi-br-search"></i> </button>

                                <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="" data-bs-original-title="Clear search"> <i class="fi fi-br-x"></i> </a>

                                <a href="{{route('admin.reward.retailer.order.export.csv',['date_from'=>$request->date_from,'date_to'=>$request->date_to,'distributor'=>$request->distributor,'state'=>$request->state,'product'=>$request->product,'term'=>$request->term])}}" data-bs-toggle="tooltip" class="btn btn-sm btn-danger" title="" data-bs-original-title="Export"> <i class="fi fi-br-download"></i> </a>
                            </div>
                            <!--<div class="search-filter-right-el">-->
                            <!--        <a href="#storelimitModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Order Status Update</a>-->
                            <!--</div>-->
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        
        <div class="row">
            <div class="col-12">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#SR</th>
                            <th>Date</th>
                            <th>Qty</th>
                            <th>Redeemption Points</th>
                            <th>Redeemption No</th>
                            <th>Retailer Name</th>
                            <th>Retailer Code</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Current Balance</th>
                            
                            
                            <th>Delivery Status</th>
                            
        					<th></th>
        					
                        </tr>
                        </thead>
                        <tbody>
                                @php
                                    $all_orders_total_amount = 0;
                                @endphp
                                            
                                @forelse ($data as $index => $item)
                                            						
                                    @php
                                        
                                       
                                    
                                        $all_orders_total_amount += ($item->qty);
                                    
                                        if (!empty($item->id)) {
                                            $distributor = DB::table('users')->where('id', $item->distributor_id)->first();
                                            $state = DB::table('states')->where('id', $item->state_id)->first();
                                            $transactionH = DB::table('retailer_wallet_txns')->where('user_id', $item->user_id)->get();
                                            
                                            $qr = [];
                                            
                                            foreach ($transactionH as $rec) {
                                                $qr[] = $rec->barcode;
                                            }
                                            //dd($qr);
                                            // Fetch distributor IDs and count their occurrences
                                        
                                            $distributorIdCounts = DB::table('retailer_barcodes')
                                                ->whereIn('code', $qr)
                                                ->select('distributor_id', DB::raw('COUNT(*) as count'))
                                                ->groupBy('distributor_id')
                                                ->orderByDesc('count')
                                                ->get();
                                                $distributorIdCounts = $distributorIdCounts->filter(function($item) {
                                                    return $item->distributor_id !== null;
                                                });
                                            if (isset($distributorIdCounts[1])) {
                                            //dd($distributorIdCounts[1]);
                                                if ($distributorIdCounts[1]->distributor_id) {
                                                
                                                    $maxDistributorId = $distributorIdCounts[1]->distributor_id;
                                                    $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->first();
                                                    $maxCount = $distributorIdCounts[1]->count;
                                                }else{
                                                   
                                        			      $distributorIds = explode(',', $item->distributor_id);
                                                          $teamDistributorIds = DB::table('teams')->where('area_id', $item->area_id)->where('state_id', $item->state_id)->where('ase_id',$item->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                           // dd($teamDistributorIds);
                                                            // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                            $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                        			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                        			    
                                                   $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                                }
                                            } elseif (isset($distributorIdCounts[0])) {
                                               if ($distributorIdCounts[0]->distributor_id) {
                                                
                                                    $maxDistributorId = $distributorIdCounts[0]->distributor_id;
                                                    $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->first();
                                                    $maxCount = $distributorIdCounts[0]->count;
                                                }else{
                                                   
                                        			      $distributorIds = explode(',', $item->distributor_id);
                                                          $teamDistributorIds = DB::table('teams')->where('area_id', $item->area_id)->where('state_id', $item->state_id)->where('ase_id',$item->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                           // dd($teamDistributorIds);
                                                            // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                            $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                        			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                        			    
                                                   $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                                }
                                            }else{
                                                   $distributorIds = explode(',', $item->distributor_id);
                                                     
                                                      $teamDistributorIds = DB::table('teams')->where('area_id', $item->area_id)->where('state_id', $item->state_id)->where('ase_id',$item->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                        //dd($teamDistributorIds);
                                                        // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                        $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                                         
                                    			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                               $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                            }
                                            $walletTran = DB::table('retailer_user_txn_histories')->where('order_id', $item->id)->first();
                                            
                                            // Check if wallet transaction exists
                                            if ($walletTran) {
                                                $walletBal = DB::table('retailer_wallet_txns')
                                                    ->where('amount', $walletTran->amount)
                                                    ->where('user_id', $walletTran->user_id)
                                                    ->where('created_at', '=', $walletTran->created_at)
                                                    ->first();
                                    
                                                // Check if wallet balance exists
                                                if ($walletBal) {
                                                    $finalBal = DB::table('retailer_wallet_txns')
                                                        ->where('user_id', $walletTran->user_id)
                                                        ->where('id', '<', $walletBal->id)
                                                        ->orderBy('id', 'desc')
                                                        ->first();
                                                }
                                            }
                                        }
                                    @endphp
                                <tr id="row_{{$item->id}}">
                                    <td>
                                        {{ $index + 1 }}
                                    </td>
                                    <td>
                                        <div class="order-time">
                                            <p class="small text-muted mb-0">
                                                <span class="text-dark font-weight-bold mb-2">
                                                    {{date('j M Y g:i A', strtotime($item->created_at))}}
                                                </span>
                                            </p>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <p class="text-dark mb-1">{{$item->qty}}</p>
                                    </td>
                                    <td>
                                        <p class="text-dark mb-1">{{$item->finalamount}}</p>
                                    </td>
                                    <td>
                                        <p class="small text-dark mb-1">#{{$item->order_no}}</p>
        								<div class="row__action">
                                                    <a href="{{ route('admin.reward.retailer.order.view', $item->id) }}">View</a>
                                                    
                                                   
                                        </div>
                                    </td>
        
                                    <td>
                                        <p class="small text-dark mb-1">{{$item->shop_name ?? ''}}</p>
                                    </td>
                                    <td>
                                        <p class="small text-dark mb-1">{{$item->unique_code ?? ''}}</p>
                                    </td>
        
                                    <td>
                                        <p class="small text-dark mb-1">{{$item->storeemail ?? '' }}</p>
                                    </td>
                                    <td>
                                        <p class="small text-dark mb-1">{{$item->mobile ?? ''}}</p>
                                    </td>
                                     <td>
                                        <p class="small text-dark mb-1">{{$item->wallet ?? ''}}</p>
                                    </td>
                                    
                                   
        							 <td>
        							        @if($item->status== 6) <span class="badge bg-primary ">Waiting for NOC </span>
                                            @elseif($item->status == 1)<span class="badge bg-primary ">NOC Approved </span>
                                            @elseif($item->status == 2)<span class="badge bg-primary ">Address Confirmed </span>
                                            @elseif($item->status == 3) <span class="badge bg-primary ">Gift Ordered </span>
                                            @elseif($item->status == 7) <span class="badge bg-success ">Gift Dispatched </span>
        								    @elseif($item->status == 4) <span class="badge bg-success ">Gift Delivered </span>
        								    @elseif($item->status == 5) <span class="badge bg-danger ">Cancelled </span>
        								   
        								    @endif
        							</td>
        						
                                  
                                </tr>
                               
                            @empty
                                <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                            @endforelse
                            <tr>
                               
                                
                                <td>
                                    <p class="small text-dark mb-1 fw-bold">TOTAL</p>
                                </td>
                                <td>
                                    <p class="small text-dark mb-1 fw-bold">{{ number_format($all_orders_total_amount) }}</p>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                     <div class="d-flex justify-content-end">
                      {{$data->appends($_GET)->links()}}
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
                <form method="post" action="{{ route('admin.order.status.csv.upload') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
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