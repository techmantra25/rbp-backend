@extends('admin.layouts.app')

@section('page', 'Distributor list')
@section('content')



<section class="store-sec">
  <div class="row">
      <div class="col-xl-12 order-2 order-xl-1">
          <div class="card search-card">
              <div class="card-body">
                  <div class="search__filter mb-5">
                      <div class="row align-items-center justify-content-between">
                          <div class="col-md-12 mb-3">
                              <p class="small text-muted mt-1 mb-0">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} Entries</p>
                          </div>
                          <div class="col-md-12 mb-3">
                              <div class="search-filter-right">
                                  <div class="search-filter-right-el">
                                      <form class="row align-items-end" action="" method="GET">
                                          <div class="search-filter-right">
                                              
                                              
                                              
                                              
                                                
                                            </div>
                                          <div class="search-filter-right search-filter-right-store mt-4">
                                              
                                              <div class="search-filter-right-el">
                                                  <input type="search" name="keyword" id="term" class="form-control form-control-sm" placeholder="Search by user name" value="{{app('request')->input('keyword')}}" autocomplete="off">
                                              </div>
                                              <div class="search-filter-right-el">
                                                  <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">
                                                      Filter
                                                  </button>
                                                  <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                                      <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                  </a>
                                              </div>
                                                
                                                {{--<div class="search-filter-right-el">
                                                    <a href="{{ route('admin.users.create') }}" class="btn btn-outline-danger btn-sm">
                                                        <iconify-icon icon="prime:plus-circle"></iconify-icon>Distributor  Create
                                                    </a>
                                                </div>--}}
                                               <div class="search-filter-right-el">
                                                    <a href="#csvUploadModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon> Coupon Count Upload </a>
                                               </div>
                                               <div class="search-filter-right-el">
                                                <a href="#videocsvUploadModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>video Bulk upload</a>
                                               </div>
                                               <div class="search-filter-right-el">
                                                <a href="#sequencecsvUploadModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Distributor sequence code CSV upload</a>
                                               </div>
                                               <div class="search-filter-right-el">
                                                <a href="#distributorcreateModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Distributor create</a>
                                               </div>
                                                <div class="search-filter-right-el">
                                                  <a href="{{ route('admin.distributor.index.export.csv', ['keyword'=>$request->keyword]) }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                                      
                                                      <iconify-icon icon="material-symbols:download"></iconify-icon> CSV Download
                                                  </a>
													
                                                </div>
                                                
                                          </div>
                                           
                                      </form>
                                  </div>
                                  
                              </div>
                          </div>
                          
            
                      </div>
                  </div>
                    <div class="table-responsive">
                      <table class="table table-sm admin-table mt-3 no-sticky fffff">
                          <thead>
                              <tr>
                                  <th>SR</th>
                                  <th>Name</th>
                                  <th>Designation</th>
                                  
                                  <th>Mobile</th> 
                                 
                                 <th>City/State</th>
                                 <th>Sequence no state wise</th> 
                                 
                                  <th>Status</th>
								  <th>Given Coupon</th>
								  <th>Retailer scan count</th>
								  <th>Rest coupon count</th>
								  <th width="50">Postal Code</th> 
								  <th width="50">Wallet Balance</th> 
                              </tr>
                          </thead>
                  
                          <tbody>
                              @forelse ($data as $index => $item)
                                    @php
                                        $area ='';
                                        $store_id=[];
                                        $areaDetail = DB::table('user_areas')->where('user_id','=',$item->id)->groupby('area_id')->get();
                                        
                                        if(!empty($areaDetail)) {
                                            foreach($areaDetail as $key => $obj) {
                                                $areaList=DB::table('areas')->where('id','=',$obj->area_id)->first();
                                                $area .= $areaList->name ??'';
                                                if((count($areaDetail) - 1) != $key) $area .= ', ';
                                            }
                                        }
										$login=DB::table('user_logins')->where('user_id',$item->id)->orderby('id','desc')->first();
										
							            //$store=DB::select("SELECT s.*  FROM `stores` s
                                			//	INNER JOIN teams t ON s.id = t.store_id
                                			//		INNER JOIN retailer_barcodes rb ON s.id = t.store_id
                                				
                                               // WHERE t.distributor_id=$item->id and s.status=1
                                               
                                               // ORDER BY s.name ASC");
                                                
                                               // foreach($store as $stores){
                                               //      array_push($store_id, $stores->id);
                                               // }
                                        //dd($store_id);
                                        //$reward=\App\Models\RetailerWalletTxn::whereIN('user_id',$store_id)->where('type',1)->where('barcode_id','!=',NULL)->whereBetween('created_at', ['2024-10-01', now()])->count();
                                        $reward = \App\Models\RetailerWalletTxn::join('stores', 'stores.id', 'retailer_wallet_txns.user_id')
                                            ->join('teams', 'stores.id', 'teams.store_id')
                                            ->join('retailer_barcodes', 'retailer_barcodes.id', 'retailer_wallet_txns.barcode_id')
                                            ->whereRaw("find_in_set('".$item->id."', retailer_barcodes.distributor_id)")
                                            ->where('retailer_wallet_txns.created_at', '>', '2024-10-01') // Filter after 1st Oct 2024
                                            ->latest('retailer_wallet_txns.id')
                                            ->count();
                                            $remainingAmount=(($item->given_coupon)-($reward));
                                    @endphp
                          
                                  <tr>
                                      <td>{{$index + $data->firstItem()}}</td>
                                      <td>
                                          <p class="small text-dark mb-0">
                                              {{$item->title}} {{$item->name}}
                                          </p>
                                          <p class="small text-muted">{{$item->employee_id}}</p>
                                          <div class="row__action">
                                              
                                                  <a href="{{ route('admin.users.edit', $item->id) }}">Edit</a>
                                                  <a href="{{ route('admin.users.show', $item->id) }}">View</a>
                                                  
                                                  
                                                  
                                              </form>
                                          </div>
                                      </td>
                                      
                                     <td>Distributor</td>
                                     
                                      <td>
                                          <p class="small text-dark">{{$item->mobile}}</p>
                                          @if($item->alt_number1) <p class="small text-muted">{{$item->alt_number1}}</p> @endif
                                          @if($item->alt_number2) <p class="small text-muted">{{$item->alt_number2}}</p> @endif
                                          @if($item->alt_number3) <p class="small text-muted">{{$item->alt_number3}}</p> @endif
                                      </td>
                            
                                      <td>
                                          {{$item->city}}, {{$item->state}}
                                          
                                          
                                      </td>
                                      <td>
                                          {{$item->distributor_position_code ??''}}
                                          
                                          
                                      </td>
                                      
                                      <td><span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span></td>
									  <td>{{$item->given_coupon ??''}}</td>
									  <td>{{$reward ??''}}</td>
									  <td>{{$remainingAmount ??''}}</td>
									  <td>
                                          {{$item->postal_code ??''}}
                                          
                                          
                                      </td>
                                      <td>
                                          {{$item->wallet ??''}}
                                          
                                          
                                      </td>
                                  </tr>
                                 
                              @endforeach
                          </tbody>
                      </table>
                      <div class="d-flex justify-content-end">
                      {{$data->appends($_GET)->links()}}
                    </div>
                </div>

                  
              </div>
          </div>
      </div>
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
                <form method="post" action="{{ route('admin.distributor.coupon.csv.upload') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/distributor-coupon.csv') }}">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="videocsvUploadModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.distributor.video.csv.upload') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/distributor-video.csv') }}">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- reset password modal --}}
<div class="modal fade" id="resetPassword" tabindex="-1" aria-labelledby="resetPasswordLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordLabel">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="resetPassBody"></div>
        </div>
    </div>
</div>



{{-- bulk upload variation modal --}}
<div class="modal fade" id="distributorcsvUploadModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.distributor.password.generate') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- distributor sequence upload variation modal --}}
<div class="modal fade" id="sequencecsvUploadModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.distributor.sequence.code') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                     <a href="{{ asset('admin/distributor-sequence.csv') }}">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="distributorcreateModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                distributor create
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.bulk.distributor.csv.create') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection



@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
<script>
    $('select[name="state"]').on('change', (event) => {
        var value = $('select[name="state"]').val();
      
        $.ajax({
            url: '{{url("/")}}/admin/users/state/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="area"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data.area, (key, value) => {
                    content += '<option value="'+value.area+'">'+value.area+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
<script>
    function ResetPasswordModal(userId) {
        $.ajax({
            url: "{{route('admin.users.password.generate')}}",
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
                userId: userId
            },
            success: function(resp) {
                // console.log(resp);
                var content = '';
                var url = "{{url('/')}}/admin/users/password/reset";

                if (resp.status == 200) {
                    content += `
                    <form method="post" action="${url}">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="password" name="password" placeholder="Password" value="${resp.data}">
                                <label for="password">Generated password *</label>
                            </div>
                        </div>
                        <p class="">Password generated</p>

                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" name="id" value="${userId}">
                        <button type="submit" class="btn btn-danger">Change password</button>
                    </form>
                    `;
                } else {
                    content += `
                    <p class="text-danger">${resp.message}</p>
                    <form method="post" action="${url}">
                        <div class="form-group">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="password" name="password" placeholder="Password" value="">
                                <label for="password">Generate password *</label>
                            </div>
                        </div>
                        <p class="">Suggested password: Firstname SHORT-COUNTRY-CODE EMPLOYEE-ID</p>

                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" name="id" value="${userId}">
                        <button type="submit" class="btn btn-danger">Change password</button>
                    </form>
                    `;
                }

                $('#resetPassBody').html(content);
                var resetPassword = new bootstrap.Modal(document.getElementById('resetPassword'));
                resetPassword.show();
            }
        })
    }
</script>

@endsection
