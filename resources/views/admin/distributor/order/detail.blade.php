@extends('admin.layouts.app')

@section('page', 'Order detail')

@section('content')
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
<section>
    <div class="row justify-content-end">
        <div class="col-auto">
            <a type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"  id="basic">
                 Download pdf
            </a>
        </div>
        @if((Auth()->guard('admin')->user()->email=='admin@admin.com'))
        <div class="col-auto">
            <a href="#storelimitModal" data-bs-toggle="modal" class="btn btn-danger"> Update Shipping Address</a>
        </div>
        @if($data->status==6)
        <div class="col-auto">
            <a href="{{route('admin.reward.retailer.order.mail.sent',$data->id)}}"  class="btn btn-danger">Sent Noc Mail to Distributor</a>
        </div>
        @endif
        @endif
    </div>
</section>
<section>
    <div class="row print-code">
        <div class="col-sm-5">
            <div class="card shadow-sm">
                <div class="card-header">Ordered Products ({{count($data->orderProduct)}})</div>
                <div class="card-body pt-0">
                    @forelse($data->orderProduct as $productKey => $productValue)
                    <div class="admin__content">
                        <aside>
                            <a href="{{ route('admin.reward.retailer.product.view', $productValue->product_id) }}" target="_blank">
                                <nav>{{$productValue->product_name}}</nav>
                                <img src="{{ asset($productValue->product_image) }}" class="mt-2" style="width: 80%;">
                            </a>
                        </aside>
                        <content>
                            
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <label for="inputPassword6" class="col-form-label text-muted">Qty :</label>
                                </div>
                                <div class="col-auto">
                                    {{$productValue->qty}}
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <label for="inputPassword6" class="col-form-label text-muted">Points :</label>
                                </div>
                                <div class="col-auto">
                                    {{$productValue->price}}
                                </div>
                            </div>
                           
                            
                        </content>
                    </div>
                   {{-- <div class="card card-body mb-0 p-0 pt-3">
                        <h5>Product status</h5>
                        <p class="small text-muted">Update status for this Product only</p>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.reward.retailer.order.product.status', [$productValue->id, 6]) }}" type="button" class="status_1 btn btn-outline-primary btn-sm {{($productValue->status == 6) ? 'active' : ''}}">Waiting for NOC</a>
                            <a href="{{ route('admin.reward.retailer.order.product.status', [$productValue->id, 1]) }}" type="button" class="status_1 btn btn-outline-primary btn-sm {{($productValue->status == 1) ? 'active' : ''}}">NOC Approved</a>

                            <a href="{{ route('admin.reward.retailer.order.product.status', [$productValue->id, 2]) }}" type="button" class="status_2 btn btn-outline-primary btn-sm {{($productValue->status == 2) ? 'active' : ''}}">Address Confirmed</a>

                            <a href="{{ route('admin.reward.retailer.order.product.status', [$productValue->id, 3]) }}" type="button" class="status_3 btn btn-outline-primary btn-sm {{($productValue->status == 3) ? 'active' : ''}}">Gift Ordered</a>

                            <a href="{{ route('admin.reward.retailer.order.product.status', [$productValue->id, 4]) }}" type="button" class="status_4 btn btn-outline-success btn-sm {{($productValue->status == 4) ? 'active' : ''}}">Gift Delivered</a>
							
                            <a href="{{ route('admin.reward.retailer.order.product.status', [$productValue->id, 5]) }}" type="button" class="status_5 btn btn-outline-danger btn-sm {{($productValue->status == 5) ? 'active' : ''}}">Cancelled</a>
                        </div>
					</div>--}}
                      @empty
                        <h5 class="display-6 text-danger">Invalid Order</h5>
                        <p class="text-muted">Customer refreshed Order success page</p>
                    @endforelse
                      
                        <br>

                        
                        <br>

                    </div>    
            </div>
        </div>
        <div class="col-sm-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="btn-group" role="group" aria-label="Basic outlined example">
                        	@if((Auth()->guard('admin')->user()->email=='admin@admin.com'))
                            <a href="{{ route('admin.reward.retailer.order.status', [$data->id, 6]) }}" type="button" class="btn btn-outline-secondary btn-sm {{($data->status == 6) ? 'active' : ''}}">Waiting for NOC</a>
                            <a href="{{ route('admin.reward.retailer.order.status', [$data->id, 1]) }}" type="button" class="btn btn-outline-primary btn-sm {{($data->status == 1) ? 'active' : ''}}">NOC Approved</a>
                            <a href="{{ route('admin.reward.retailer.order.status', [$data->id, 2]) }}" type="button" class="btn btn-outline-primary btn-sm {{($data->status == 2) ? 'active' : ''}}">Address Confirmed</a>
                           
                            <a href="{{ route('admin.reward.retailer.order.status', [$data->id, 3]) }}" type="button" class="btn btn-outline-primary btn-sm {{($data->status == 3) ? 'active' : ''}}">Gift Ordered</a>
                            <a href="{{ route('admin.reward.retailer.order.status', [$data->id, 4]) }}" type="button" class="btn btn-outline-success btn-sm {{($data->status == 4) ? 'active' : ''}}">Gift Delivered</a>
                            
                            
                            
                            <a href="{{ route('admin.reward.retailer.order.status', [$data->id, 5]) }}" type="button" class="btn btn-outline-danger btn-sm {{($data->status == 5) ? 'active' : ''}}">Cancelled</a>
                             @endif
                            
                             @if( (Auth()->guard('admin')->user()->email=='jyoti.singh@luxcozi.com'))
                            <a href="{{ route('admin.reward.retailer.order.status', [$data->id, 3]) }}" type="button" class="btn btn-outline-primary btn-sm {{($data->status == 3) ? 'active' : ''}}">Gift Ordered</a>
                            <a href="{{ route('admin.reward.retailer.order.status', [$data->id, 4]) }}" type="button" class="btn btn-outline-success btn-sm {{($data->status == 4) ? 'active' : ''}}">Gift Delivered</a>
                            @endif
                        </div>
                </div>
            </div>
                @php
                  $distributorData=DB::table('teams')->where('store_id',$data->user->id)->first();
                 
                  $distributorName=DB::table('users')->where('id',$distributorData->distributor_id)->first();
                  $transactionH = DB::table('retailer_wallet_txns')->where('user_id', $data->user->id)->get();
                                         
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
                                                //dd($distributorIdCounts);
                                               $distributorIdCounts = $distributorIdCounts->filter(function($item) {
                                                    return $item->distributor_id !== null;
                                                });
                                             
                                            //if ($distributorIdCounts->distributor_id) {
                                              //  $maxDistributorId = $distributorIdCounts->distributor_id;
                                              //  $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->first();
                                              //  $maxCount = $distributorIdCounts->count;
                                            //}else{
                                             //      $distributorIds = explode(',', $distributorData->distributor_id);
                                                      
                                            //          $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                        
                                                        // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                             //           $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                    			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                              // $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                           // }
                                            
                                            
                                            if (isset($distributorIdCounts[1])) {
                                            //dd($distributorIdCounts[1]);
                                                if ($distributorIdCounts[1]->distributor_id) {
                                                
                                                    $maxDistributorId = $distributorIdCounts[1]->distributor_id;
                                                    $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->first();
                                                    $maxCount = $distributorIdCounts[1]->count;
                                                }else{
                                                   
                                        			      $distributorIds = explode(',', $distributorData->distributor_id);
                                                          $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
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
                                                   
                                        			      $distributorIds = explode(',', $distributorData->distributor_id);
                                                          $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                           // dd($teamDistributorIds);
                                                            // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                            $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                        			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                        			    
                                                   $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                                }
                                            }else{
                                                   $distributorIds = explode(',', $distributorData->distributor_id);
                                                      
                                                      $teamDistributorIds = DB::table('teams')->where('area_id', $distributorData->area_id)->where('state_id', $distributorData->state_id)->where('ase_id',$distributorData->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                        
                                                        // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                        $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                    			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                               $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                            }
                @endphp
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="form-group mb-3">
                        <p class="small">Order Time : {{date('j M Y g:i A', strtotime($data->created_at))}}</p>
                        <p>Order No : {{$data->order_no}}</p>
                        <h2>{{$data->user->name}}</h2>
						 <p class="small text-dark mb-0"> <span class="text-muted">Owner Name :</span> {{$data->user->owner_fname.' '.$data->user->owner_lname}} </p>
                        <p class="small text-dark mb-0"> <span class="text-muted">Email : </span> {{$data->user->email}}</p>
                        <p class="small text-dark mb-0"> <span class="text-muted">Mobile : </span> {{$data->user->contact}}</p>
                        <p class="small text-dark mb-0"> <span class="text-muted">Distributor : </span> {{$distributorDetails->name ?? ''}}</p>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="small text-dark mb-0"> <span class="text-muted">Actual address : </span> {{$data->billing_address}}</p>
                            <p class="small text-dark mb-0"> {{$data->user->city.', '.$data->user->pin.', '.$data->user->states->name ??''}}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="small text-dark mb-0"> <span class="text-muted">Shipping address : </p>
                            @if(!empty($data->shipping_address) || !empty($data->shipping_city) || !empty($data->shipping_pin) || !empty($data->shipping_state) || !empty($data->shipping_landmark))
                            <p class="small text-dark mb-0">{{$data->shipping_address.', '.$data->shipping_city.', '.$data->shipping_pin.', '.$data->shipping_state.', '.$data->shipping_landmark}}</p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3 justify-content-end">
                        <div class="col-md-4 text-end">
                            <p class="small text-muted mb-2"></p>
                            <table class="w-100">
                                <tr>
                                    <td><p class="small text-muted mb-0">Currency : </p></td>
                                    <td><p class="small text-dark mb-0 text-end"> {{$data->final_amount}}</p></td>
                                </tr>
                                <tr class="border-top">
                                    <td><p class="small text-muted mb-0">Final Currency : </p></td>
                                    <td><p class="small text-dark mb-0 text-end"> {{$data->final_amount}}</p></td>
                                </tr>
                            </table>
                        </div>
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
                Shipping Address
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.reward.retailer.order.address.update',$data->id) }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
               
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="shipping_address" name="shipping_address" placeholder="Shipping Address" value="{{ old('shipping_address') ? old('shipping_address') : $data->shipping_address }}">
                                <label for="shipping_address">Street/Area  </label>
                            </div>
                            @error('shipping_address') <p class="small text-danger">{{$message}}</p> @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="shipping_city" name="shipping_city" placeholder="Shipping City" value="{{ old('shipping_city') ? old('shipping_city') : $data->shipping_city }}">
                                <label for="shipping_city">Shipping City  </label>
                            </div>
                            @error('shipping_city') <p class="small text-danger">{{$message}}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="shipping_state" name="shipping_state" placeholder="Shipping State" value="{{ old('shipping_state') ? old('shipping_state') : $data->shipping_state }}">
                                <label for="shipping_state">Shipping State  </label>
                            </div>
                            @error('shipping_state') <p class="small text-danger">{{$message}}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="shipping_country" name="shipping_country" placeholder="Shipping Country" value="{{ old('shipping_country') ? old('shipping_country') : $data->shipping_country }}">
                                <label for="shipping_country">Shipping Country  </label>
                            </div>
                            @error('shipping_country') <p class="small text-danger">{{$message}}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="shipping_pin" name="shipping_pin" placeholder="Shipping Pincode" value="{{ old('shipping_pin') ? old('shipping_pin') : $data->shipping_pin }}">
                                <label for="shipping_pin">Shipping Pincode  </label>
                            </div>
                            @error('shipping_pin') <p class="small text-danger">{{$message}}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="shipping_landmark" name="shipping_landmark" placeholder="Shipping Landmark" value="{{ old('shipping_landmark') ? old('shipping_landmark') : $data->shipping_landmark }}">
                                <label for="shipping_landmark">Shipping Landmark  </label>
                            </div>
                            @error('shipping_landmark') <p class="small text-danger">{{$message}}</p> @enderror
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Save <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="{{ asset('admin/js/printThis.js') }}"></script>
<script>
 $('#basic').on("click", function () {
      $('.print-code').printThis();
    });
        
    </script>
@endsection