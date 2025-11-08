@extends('admin.layouts.app')
@section('page', 'User detail')

@section('content')
<style>
    .working_area {
        display: inline-flex;
        vertical-align: top;
        padding: 6px 12px;
        align-items: center;
        background: #f7f7f7;
        border-radius: 6px;
        text-decoration: none;
        color: #000;
    }
    .working_area svg {
        margin-right: 10px;
    }
</style>
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="badge bg-primary" style="font-size: 26px;">{{$data->user->designation}}</div>

                            @if ($data->user->status == 1)
                                <a href="{{ route('admin.users.status', $data->user->id) }}" data-bs-toggle="tooltip" title="This user is ACTIVE. Tap to make INACTIVE" class="badge bg-success">Active</a>
                            @else
                                <a href="{{ route('admin.users.status', $data->user->id) }}" data-bs-toggle="tooltip" title="This user is INACTIVE. Tap to ACTIVATE" class="badge bg-danger">Inactive</a>
                            @endif
                        </div>

                        <div class="col-md-6 text-end">
                            <a href="{{ url()->previous() }}" class="btn btn-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                Go back
                            </a>

                            <a href="{{ route('admin.users.edit', $data->user->id) }}" class="btn btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                Edit
                            </a>
                            <a href="javascript: void(0)" onclick="ResetPasswordModal({{$data->user->id}})" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                Reset Password
                            </a>
                         
                               <a href="#newRangeModal" data-bs-toggle="modal" class="btn btn-secondary">Add area</a>
                                
                              
                          
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <p class="text-dark">Primary information</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Name</p>
                            <h5>{{$data->user->name}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Mobile number</p>
                            <h5>{{$data->user->mobile}}</h5>
                        </div>
						<div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Whatsapp number</p>
                            <h5>{{$data->user->whatsapp_no}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Email address</p>
                            <h5>{{$data->user->email}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Code</p>
                            <h5>{{$data->user->employee_id}}</h5>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <p class="text-dark">Location information</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">State</p>
                            <h5>{{$data->user->state}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Area</p>
                            <h5>{{$data->user->city}}</h5>
                        </div>
						 <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">HQ</p>
                            <h5>{{$data->user->headquater}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Working Area List</p>
                            <h5>
                            @foreach ($data->workAreaList as $item)
                               <a href="{{route('admin.users.area.delete',$item->id)}}" class="working_area" title="Delete area/bit" onclick="return confirm('Are you sure ?')"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> <span>{{$item->areas->name}}@if(!$loop->last), @endif</span></a>
                            @endforeach
                            </h5>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <p class="text-dark">Team information</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">NSM</p>
                            <h5>{{$data->team->nsm->name ??''}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">ZSM</p>
                            <h5>{{$data->team->zsm->name??''}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">RSM</p>
                            <h5>{{$data->team->rsm->name??''}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">SM</p>
                            <h5>{{$data->team->sm->name??''}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">ASM</p>
                            <h5>{{$data->team->asm->name??''}}</h5>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <p class="text-dark">Optional information</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Date of Joining</p>
                            <h5>{{$data->user->date_of_joining ? $data->user->date_of_joining : 'NA'}}</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p class="small text-muted mb-1">Date of Leaving</p>
                            <h5>{{$data->user->date_of_leaving ? $data->user->date_of_leaving : 'NA'}}</h5>
                        </div>
                        
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <p class="text-dark">Distributor information</p>
                        </div>
                      
                        @forelse ($data->distributorList as $item)
                          @if(!empty($item->distributors))
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.users.show', $item->distributors->id) }}"><h5>{{$item->distributors->name}}</h5></a>
                            </div>
                             @endif
                        @empty
                            <div class="col-md-4 mb-3">
                                <p class="small text-muted mb-1">No Distributor found</p>
                            </div>
                           
                        @endforelse
                        
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <p class="text-dark">Store information</p>
                        </div>
                        @forelse ($data->storeList as $item)
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('admin.stores.show', $item->id) }}"><h5>{{$item->name}}</h5></a>
                            </div>
                        @empty
                            <div class="col-md-4 mb-3">
                                <p class="small text-muted mb-1">No Stores found</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
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
{{-- add area ---}}
<div class="modal fade" id="newRangeModal" tabindex="-1"  aria-labelledby="newRangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newRangeModalLabel">Add new Area</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.users.area.store')}}" method="POST">@csrf
                    <input type="hidden" name="user_id" value="{{$data->user->id}}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-floating mb-3">
                                    <select class="form-select select2" id="area_id" data-width="100%"  name="area_id" aria-label="Floating label select example">
                                        <option value="" selected disabled>Select</option>
                    
                                      @foreach ($data->areaDetail as $areaItem)
                                        <option value="{{$areaItem->id}}" >{{$areaItem->name}}</option>
                                      @endforeach
                                    </select>
                                    <label for="state">Area *</label>
                                </div>
                                @error('area_id') <p class="small text-danger">{{$message}}</p> @enderror
                            </div>
                        </div> 
                    </div>

                    <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-sm btn-danger">Add Area</button>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')
    <script>
		$('select[name="stateDetail"]').on('change', (event) => {
        var value = $('select[name="stateDetail"]').val();

        $.ajax({
            url: '{{url("/")}}/state-wise-area/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="areaDetail"]';
                var displayCollection = (result.data.state == "all") ? "All State" : " Select";

                content += '<option value="" selected>' + displayCollection + '</option>';
                $.each(result.data.area, (key, value) => {
                    content += '<option value="' + value.area + '">' + value
                        .area + '</option>';
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
                        <p class="">Suggested password: Firstname EMPLOYEE-ID</p>

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
    
    
$(document).ready(function() {
  //$("select").select2({
    //dropdownParent: $('.rrrr' '.distributor-edit')
	//dropdownParent: $('.distributor-edit')
  //});
$('.modal select').each(function() {  
   var $p = $(this).parent(); 
   $(this).select2({  
     dropdownParent: $p
     
   });  
});
});
</script>
@endsection