@extends('admin.layouts.app')

@section('page', 'Qrcode detail')

@section('content')
<style>
	.d-btn {
		white-space: nowrap;
		border: none;
		background: transparent;
		color: #0d6efd;
	}
</style>
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">    
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="text-muted">{{ $data->name }}</h3>
                            {{-- <h6>{{ $data->name }}</h6> --}}
                        </div>
                        <div class="col-md-4 text-end">
                            @if ($data->end_date < \Carbon\Carbon::now() )
                            <h3 class="text-danger mt-3 fw-bold">EXPIRED</h3>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="small text-muted mt-4 mb-2">Details</p>
                            <table class="">
                                <tr>
                                    <td class="text-muted">No of Qrcodes: </td>
                                    <td>{{count($coupons)}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Points: </td>
                                    <td>{{$data->type == 1 ? $data->amount.' ' : ' '. $data->amount}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Max time usage : </td>
                                    <td>{{$data->max_time_of_use}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Max time usage for single user :  </td>
                                    <td>{{$data->max_time_one_can_use}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No of usage : </td>
                                    <td>{{$data->no_of_usage}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Start date: </td>
                                    <td>{{ date('j M Y h:m A', strtotime($data->start_date)) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">End date: </td>
                                    <td>{{ date('j M Y h:m A', strtotime($data->end_date)) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Distributor: </td>
                                    <td>{{ $data->distributor->name ??'' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">State: </td>
                                    <td>{{ $data->state->name ??'' }}</td>
                                </tr>
                            </table>

                            <hr>

                            <p class="small text-muted mt-4 mb-2">Qrcodes</p>
							{{--<div class="col-auto">
                                <a type="button" id="basic" class="btn btn-outline-danger btn-sm" download>Download pdf</a>
                            </div>--}}
											<div class="col-md-12 text-end">
                   				 <form class="row align-items-end justify-content-end" action="">
						
                        <div class="col-auto">
                            <input type="search" name="keyword" id="keyword" class="form-control form-control-sm" placeholder="Search by QRcode" value="{{request()->input('keyword')}}" autocomplete="off">
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Filter
                                </button>

                                <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Clear Filter">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </a>

                                <a type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"  id="basic">
                                   Download pdf
                                </a>
								<a href="{{ route('admin.reward.retailer.barcode.csv.export',['slug'=>$data->slug,'keyword'=>$request->keyword]) }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="">
                                    Download CSV
                                </a>
                                 <a type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"  id="basicPdf">
                                   Print pdf
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
							 
                            <table class="table table-sm print-code" >
                                <tr>
                                    <th>#SR</th>
									{{--<th>Unique code</th>--}}
                                    <th>Qrcode</th>
									<th>Status</th>
                                    {{--<th>Usage</th>--}}
									{{--<th>Action</th>--}}
                                </tr>
                                @forelse ($coupons as $couponKey => $coupon)
								@php
								   $finalCode='Code : '.$coupon->code.' Note :'.$coupon->note;
								@endphp
                                <tr>
                                    <td>{{$couponKey+1}}</td>
									{{-- <td>{{$coupon->code}}</td>--}}
                                   <td>
									  <div style="width: 120px;" class="text-center">
										  @if($data->name=='Test By Koushik')
										  <img src="https://bwipjs-api.metafloor.com/?bcid=qrcode&text={{$coupon->code}}&height=6&textsize=10&scale=6&includetext" alt="" style="height: 105px;width:105px" id="{{$coupon->code}}">
										  @endif
										  <img src="https://bwipjs-api.metafloor.com/?bcid=qrcode&text={{$coupon->code}}&height=6&textsize=10&scale=6&includetext" alt="" style="height: 105px;width:105px" id="{{$coupon->code}}">
									   <p class="text-center my-3">{{$coupon->code}}</p>
									   {{--<a type="button" class="save-btn" href="javascript:void(0);" qr-id="{{$coupon->code}}">save</a>--}}
									  {{-- <button data-href='https://bwipjs-api.metafloor.com/?bcid=qrcode&text={{$coupon->code}}&height=16&textsize=10&scale=6&includetext' class="d-btn" onclick="downloadFile('{{$coupon->code}}.png', '{{$couponKey+1}}')">Download Image</button> | --}}
									   <a href="javascript:void(0);"  class="qr-txt" val="{{$coupon->code}}">Copy Text</a>
									   <a href="{{ route('admin.reward.retailer.barcode.qr.history', $coupon->id) }}"  class="" >Reedem History</a>

									   </div>
									   @if(Auth::guard('admin')->user()->email !='testprinter@gmail.com')
									<div class="row__action">
                                            <a href="{{ route('admin.reward.retailer.barcode.show', $coupon->id) }}">View</a>
                                            <a href="{{ route('admin.reward.retailer.barcode.edit', $coupon->id) }}">Edit</a>
                                            <a href="{{ route('admin.reward.retailer.barcode.status', $coupon->id) }}">{{($coupon->status == 1) ? 'Active' : 'Inactive'}}</a>
                                           
                                        </div>
									   @endif
									</td> 
									 <td><span class="badge bg-{{($coupon->status == 1) ? 'success' : 'danger'}}">{{($coupon->status == 1) ? 'Active' : 'Inactive'}}</span></td>
                                   {{-- <td>
                                        @if($coupon->no_of_usage >= $coupon->max_time_use)
                                            {{$coupon->no_of_usage}}
                                                
                                        @else
                                            <p class="small text-danger">Not used yet</p>
                                        @endif
                                    </td>--}}
									{{-- <td>
                        <div class="btn-group">
                            <a href="{{ route('admin.reward.retailer.barcode.show', $coupon->id) }}" class="btn btn-sm btn-dark" data-bs-toggle="tooltip" title="View details">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </a>
							<a href="{{ route('admin.reward.retailer.barcode.edit', $coupon->id) }}" class="btn btn-sm btn-dark" data-bs-toggle="tooltip" title="Edit details">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
							@if ($coupon->status == 1)
							<a href="{{ route('admin.reward.retailer.barcode.status', $coupon->id) }}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="This qrcode is ACTIVE. Tap to make INACTIVE">
							 <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-check"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg></a> 
							@else
                                <a href="{{ route('admin.reward.retailer.barcode.status', $coupon->id) }}" data-bs-toggle="tooltip" title="This qrcode is INACTIVE. Tap to ACTIVATE" class="btn btn-sm btn-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-x"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg>
                                </a>
                            @endif
							</div>
							</td> --}}
                                </tr>
                                @empty
                                <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                                @endforelse
                            </table>
                          
                          
                          
                          
                          <table class="table table-sm " id="print-qr-code" style="display: none;">
                                <tr>
                                    <th>#SR</th>
								
                                    <th>Qrcode</th>
									
                                </tr>
                                @forelse ($coupons as $couponKey => $coupon)
								@php
								   $finalCode='Code : '.$coupon->code.' Note :'.$coupon->note;
								@endphp
                                <tr>
                                    <td>{{$couponKey+1}}</td>
								
                                   <td>
									  <div style="width: 120px;" class="text-center">
										  @if($data->name=='Test By Koushik')
										  <img src="https://bwipjs-api.metafloor.com/?bcid=qrcode&text={{$coupon->code}}&height=6&textsize=10&scale=6&includetext" alt="" style="height: 105px;width:105px" id="{{$coupon->code}}">
										  @endif
										  <img src="https://bwipjs-api.metafloor.com/?bcid=qrcode&text={{$coupon->code}}&height=6&textsize=10&scale=6&includetext" alt="" style="height: 50px;width:50px" id="{{$coupon->code}}">
									     <p class="text-center my-3">{{$coupon->code}}</p>
									  

									   </div>
									  
									</td> 
								
                                  
									
                                </tr>
                                @empty
                                <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                                @endforelse
                            </table>
                          
                        </div>
                    </div>
                </div>
            </div>
        </div>

      
    </div>

</section>
@endsection
@section('script')
<script src="{{ asset('admin/js/printThis.js') }}"></script>
<script>
 $('#basic').on("click", function () {
      $('.print-code').printThis();
    });
    
    
    //  $('#basicPdf').on("click", function () {
    //   $('.print-qr-code').printThis();
    // });

	/*$('.save-btn').on("click", function () {
      var attr_val = '#'+$(this).attr("qr-id");
	  console.log(attr_val);
		
		let dataUrl = document.querySelector("#4AB06ADOE3").querySelector('img').src;
        downloadURI(dataUrl, attr_val+'.png');
    });
	
	function downloadURI(uri, name) {
	  var link = document.createElement("a");
	  link.download = name;
	  link.href = uri;
	  document.body.appendChild(link);
	  link.click();
	  document.body.removeChild(link);
	  delete link;
	};*/
	
	
	
	
	
	function downloadFile(fileName, i) {
        var div1 = document.getElementsByClassName("d-btn");
        console.log(div1);
		console.log(i);
        var imageurl = div1[i-1].getAttribute("data-href");
    $.ajax({
        url: imageurl,
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function (data) {
            var a = document.createElement('a');
            var url = window.URL.createObjectURL(data);
            a.href = url;
            a.download = fileName;
            document.body.append(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        }
    });
    }

	
	$(document).ready(function(){
    $('.qr-txt').on("click", function(){
        value = $(this).attr('val'); //Upto this I am getting value
		console.log(value);
		toastFire("success", "Copy text successfully");
        var $temp = $("<input>");
          $("body").append($temp);
          $temp.val(value).select();
          document.execCommand("copy");
          $temp.remove();
    })
})
</script>
<script>
    document.getElementById('basicPdf').addEventListener('click', function() {
  var table = document.getElementById('print-qr-code');
  
  table.style.display = 'table'; // Show the table when the button is clicked
   // Trigger the print dialog
   $('#print-qr-code').printThis({
       printDelay: 12000
   });
   
});
</script>
@endsection
