@extends('admin.layouts.app')

@section('page', 'Employee Hierarchy')
@section('content')



<section class="store-sec">
  <div class="row">
      <div class="col-xl-12 order-2 order-xl-1">
          <div class="card search-card">
              <div class="card-body">
                  <div class="search__filter mb-5">
                      <div class="row align-items-center justify-content-between">
                          <div class="col-md-12 mb-3">
                              
                                <div class="search-filter-right">
                                    <div class="search-filter-right-el">
                                        <form class="row align-items-end" action="" method="GET">
                                          
                                            <div class="search-filter-right search-filter-right-store mt-4">
                                              
                                              <div class="search-filter-right-el">
                                                  <input type="search" name="term" id="term" class="form-control form-control-sm" placeholder="Search by user name" value="{{app('request')->input('term')}}" autocomplete="off">
                                              </div>
                                              <div class="search-filter-right-el">
                                                  <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">
                                                      Filter
                                                  </button>
                                                  <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                                      <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                  </a>
                                              </div>
                                                
                                               
                                                
                                                <div class="search-filter-right-el align-self-end">
                                                  <a href="{{ route('admin.users.hiererchy.export', ['term'=>$request->term]) }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                                      
                                                      <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                                  </a>
													
                                                </div>
                                                
                                            </div>
                                            <div class="search-filter-right search-filter-right-store mt-3">
                                              <p class="small text-muted mt-1 mb-0">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} Entries</p>
                                              
                                              
                                            </div>
                                           
                              
                                        </form>
                                    </div>
                                  
                                </div>
                            </div>
                          
            
                      </div>
                  </div>
                  
                      <table class="table big-sticky ">
                          <thead>
                              <tr>
                                  <th>SR</th>
                                  <th>State</th>
                                  <th>Area</th>
                                  <th>ZSM</th>
                                  <th>RSM</th>
                                  <th>SM</th> 
                                  <th>ASM</th>
                                  <th>Employee</th>
                              </tr>
                          </thead>
                  
                          <tbody>
                              @forelse ($data as $index => $item)
                                    @php
                                         $findTeamDetails= findTeamDetails($item->id, $item->type);
							 
                                    @endphp
                          
                                  <tr>
                                      <td>{{$index + $data->firstItem()}}</td>
                                      <td> {{$findTeamDetails[0]['state']?? ''}} </td>
                                      <td> {{$findTeamDetails[0]['area']?? ''}} </td>
                                      <td> {{$findTeamDetails[0]['zsm']?? ''}} </td>
                                      <td> {{$findTeamDetails[0]['rsm']?? ''}} </td> 
                                      <td> {{$findTeamDetails[0]['sm']?? ''}} </td> 
                                      <td> {{$findTeamDetails[0]['asm']?? ''}} </td> 
                                      <td>
                                          {{ $item->name }}
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
