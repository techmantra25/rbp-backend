@extends('admin.layouts.app')

@section('page', 'User list')
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
                                              <div class="search-filter-right-el">
                                                  <label class="small text-muted">User type</label>
                                                  <select class="form-select form-select-sm select2" name="user_type" id="type">
                                                      <option value="" disabled>Select</option>
                                                      <option value="" selected>All</option>
                                                      <option value="1" {{ ($request->user_type == 1) ? 'selected' : '' }}>NSM</option>
                                                                      <option value="2" {{ ($request->user_type == 2) ? 'selected' : '' }}>ZSM</option>
                                                      <option value="3" {{ ($request->user_type == 3) ? 'selected' : '' }}>RSM</option>
                                                      <option value="4" {{ ($request->user_type == 4) ? 'selected' : '' }}>SM</option>
                                                      <option value="5" {{ ($request->user_type == 5) ? 'selected' : '' }}>ASM</option>
                                                      <option value="6" {{ ($request->user_type == 6) ? 'selected' : '' }}>ASE</option>
                                                      <option value="7" {{ ($request->user_type == 7) ? 'selected' : '' }}>Distributor</option>
                                                  </select>
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                                  <label for="state" class="text-muted small">State</label>
                                                  <select name="state" id="state" class="form-control form-control-sm select2">
                                                      <option value="" disabled>Select</option>
                                                      <option value="" selected>All</option>
                                                      @foreach ($state as $state)
                                                          <option value="{{$state->name}}" {{ request()->input('state') == $state->name ? 'selected' : '' }}>{{$state->name}}</option>
                                                      @endforeach
                                                  </select>
                                                  </div>
                                              
                                                <div class="search-filter-right-el">
                                                  <label class="small text-muted">Area</label>
                                                    <select class="form-control form-control-sm select2" name="area" disabled>
                                                    <option value="{{ $request->area }}">Select state first</option>
                                                    </select>
                                                </div>
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
                                                <div class="search-filter-right-el">
                                                    <a href="{{ route('admin.users.create') }}" class="btn btn-outline-danger btn-sm">
                                                        <iconify-icon icon="prime:plus-circle"></iconify-icon> Create
                                                    </a>
                                                </div>
                                                {{--<div class="search-filter-right-el">
                                                    <a href="{{ route('admin.users.create') }}" class="btn btn-outline-danger btn-sm">
                                                        <iconify-icon icon="prime:plus-circle"></iconify-icon>Distributor  Create
                                                    </a>
                                                </div>--}}
                                                <div class="search-filter-right-el">
                                                    <a href="#csvUploadModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Distributor Bulk upload</a>
                                               </div>
                                               <div class="search-filter-right-el">
                                                    <a href="#employeecsvUploadModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Employee detail update</a>
                                               </div>
                                               {{-- <div class="search-filter-right-el">
                                                    <a href="#distributorcsvUploadModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Distributor Password generate</a>
                                               </div>--}}
                                               <div class="search-filter-right-el">
                                                    <a href="#distributorPostcodecsvUploadModal" data-bs-toggle="modal" class="btn btn-danger"> <iconify-icon icon="prime:plus-circle"></iconify-icon>Distributor Pincode Bulk Upload</a>
                                               </div>
                                                <div class="search-filter-right-el">
                                                  <a href="{{ route('admin.users.csv.export', ['user_type'=>$request->user_type,'state'=>$request->state,'area'=>$request->area,'keyword'=>$request->keyword]) }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                                      
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
                                  <th>Working Area/HQ & State</th>
                                  <th style="min-width: 200px">Manager</th>
                                  <th>Status</th>
                                  <th>Password</th>
								  <th></th>
                              </tr>
                          </thead>
                  
                          <tbody>
                              @forelse ($data as $index => $item)
                                    @php
                                        $area ='';
                                        $areaDetail = DB::table('user_areas')->where('user_id','=',$item->id)->groupby('area_id')->get();
                                        
                                        if(!empty($areaDetail)) {
                                            foreach($areaDetail as $key => $obj) {
                                                $areaList=DB::table('areas')->where('id','=',$obj->area_id)->first();
                                                $area .= $areaList->name ??'';
                                                if((count($areaDetail) - 1) != $key) $area .= ', ';
                                            }
                                        }
										$login=DB::table('user_logins')->where('user_id',$item->id)->orderby('id','desc')->first();
							 
                                    @endphp
                          
                                  <tr>
                                      <td>{{$index + $data->firstItem()}}</td>
                                      <td>
                                          <p class="small text-dark mb-0">
                                              {{$item->title}} {{$item->name}}
                                          </p>
                                          <p class="small text-muted">{{$item->employee_id}}</p>
                                          <div class="row__action">
                                              <form action="{{ route('admin.users.destroy',$item->id) }}" method="POST">
                                                  <a href="{{ route('admin.users.edit', $item->id) }}">Edit</a>
                                                  <a href="{{ route('admin.users.show', $item->id) }}">View</a>
                                                  <a href="{{ route('admin.users.status', $item->id) }}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</a>
                                                  
                                                  @csrf
                                                  @method('DELETE')
                                              <!--<button type="submit" onclick="return confirm('Are you sure ?')" class="btn-link">Delete</button> -->
                                              </form>
                                          </div>
                                      </td>
                                      
                                      <td>
                                          {{ $item->designation ? $item->designation : userTypeName($item->type) }}
                                      </td>
                                     
                                      <td>
                                          <p class="small text-dark">{{$item->mobile}}</p>
                                          @if($item->alt_number1) <p class="small text-muted">{{$item->alt_number1}}</p> @endif
                                          @if($item->alt_number2) <p class="small text-muted">{{$item->alt_number2}}</p> @endif
                                          @if($item->alt_number3) <p class="small text-muted">{{$item->alt_number3}}</p> @endif
                                      </td>
                            
                                      <td>
                                          {{$item->city}}, {{$item->state}}
                                          @if($item->type == 6)
                                            <p class="small text-dark">{{$area}}</p>
                        				 @endif
                                          
                                      </td>
                                      <td>
                                          <p class="small text-muted">{!! findManagerDetails($item->id, $item->type) !!}</p>
                                      </td>
                                      <td><span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span></td>
                                      @if($item->type==7)
                                      <td>{{ $item->password }}</td>
                                      @else
                                      <td></td>
                                      @endif
									  @if(!empty($login))
									  @if($login->is_login==1)
									   <td><a type="button" class="btn btn-primary" href="{{route('admin.users.logout',$item->id)}}">Logout from Other Devices</a></td>
									  @else
									  <td></td>
									  @endif
									  @endif
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
<div class="modal fade" id="employeecsvUploadModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Employee details Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.user.detail.update') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/userdetail.csv') }}">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- bulk upload variation modal --}}
<div class="modal fade" id="csvUploadModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.users.csv.upload') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/user.csv') }}">Download Sample CSV</a>
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


{{-- bulk upload variation modal --}}
<div class="modal fade" id="distributorPostcodecsvUploadModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.distributor.postcode.update') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
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
