@extends('admin.layouts.app')
@section('page', 'User Attendance')
@section('content')

<section class="store-sec ">
  <div class="row">
      <div class="col-xl-12 order-2 order-xl-1">
          <div class="card search-card">
              <div class="card-body">
                  <div class="search__filter mb-5">
                      <div class="row align-items-center justify-content-between">
                          <!--<div class="col-md-12 mb-3">-->
                          <!--    <p class="small text-muted mt-1 mb-0">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} Entries</p>-->
                          <!--</div>-->
                          <div class="col-md-12 mb-3">
                              <div class="search-filter-right">
                                  <div class="search-filter-right-el">
                                      <form class="row align-items-end justify-content-end" action="" method="GET">
                                          <div class="search-filter-right">
                                            <div class="search-filter-right-el">
                                                    <label for="state" class="text-muted small">ZSM</label>
                                                    <select name="zsm" id="zsm" class="form-control form-control-sm select2">
                                                        <option value="" disabled>Select</option>
                                                        <option value="" selected>All</option>
                                                        @foreach ($zsmDetails as $item)
                                                            <option value="{{$item->id}}" {{ request()->input('zsm') == $item->id ? 'selected' : '' }}>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">RSM</label>
                                                    <select class="form-control form-control-sm select2" name="rsm" disabled>
                                                        <option value="{{ $request->rsm_id }}">Select ZSM first</option>
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">SM</label>
                                                    <select class="form-control form-control-sm select2" name="sm" disabled>
                                                        <option value="{{ $request->sm_id }}">Select RSM first</option>
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">ASM</label>
                                                    <select class="form-control form-control-sm select2" name="asm" disabled>
                                                        <option value="{{ $request->asm_id }}">Select SM first</option>
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">ASE</label>
                                                    <select class="form-control form-control-sm select2" name="ase" disabled>
                                                        <option value="{{ $request->ase_id }}">Select ASM first</option>
                                                    </select>
                                                </div>
                                              <div class="search-filter-right-el">
                                                  <label for="date_from" class="text-muted small">Date from</label>
                                                  <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{ (request()->input('date_from')) ? request()->input('date_from') : '' }}">
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                                  <label for="date_to" class="text-muted small">Date to</label>
                                                  <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{ (request()->input('date_to')) ? request()->input('date_to') : '' }}">
                                              </div>
                                              
                            
                                          </div>
                                          <div class="search-filter-right search-filter-right-store mt-3">
                                              
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
                                      <a href="{{ route('admin.users.attendance.csv.download') }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                          
                                          <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
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
                    <table class="table" >
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>NSM</th>
                            <th>ZSM</th>
                            <th>RSM</th>
                            <th>SM</th>
                            <th>ASM</th> 
                            <th>Employee</th>
                            <th>Employee Id</th>
                            <th>Employee Status</th>
                            <th>Employee Designation</th>
                            <th>Employee Date of Joining</th>
                            <th>Employee HQ</th>
                            <th>Employee Contact No</th>
                            <th>Attendance</th>
                            <th>Time In</th>
                            <th>Total Hours</th>
                            <th></th>
                        </tr>
                    </thead>
                <tbody>
                    @forelse ($data as $index => $item)
                    @php
                        $activity=\App\Models\Activity::where('user_id',$item->user_id)->where('date',$item->entry_date)->get();
                        $findTeamDetails= findTeamDetails($item->users->id, $item->users->type);
                    @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->entry_date)->format('d/m/Y') }}</td>
                            <td> {{$findTeamDetails[0]['nsm'] ?? ''}} </td> 
                            <td> {{$findTeamDetails[0]['zsm']?? ''}} </td> 
                            <td> {{$findTeamDetails[0]['rsm']?? ''}} </td> 
                            <td> {{$findTeamDetails[0]['sm']?? ''}} </td> 
                            <td> {{$findTeamDetails[0]['asm']?? ''}} </td> 
                            <td>
                                {{$item->users ? $item->users->name : ''}}
                            </td>
                            <td> {{$item->users->employee_id ?? ''}} </td>
                            <td> <span class="badge bg-{{($item->users->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span> </td>
                            <td> {{$item->users->designation?? ''}} </td>
                            <td> {{$item->users->date_of_joining?? ''}} </td>
                            <td> {{$item->users->headquater?? ''}} </td>
                            <td> {{$item->users->mobile}} </td>
                            
                            <td> {{$item->users->employee_id}} </td>
                            @if($item->type=='leave')
                            <td> Leave 
                            <p>{{$item->otheractivity->reason}}</p>
                            </td>
                            {{-- @elseif($item->type=='distributor-visit')
                            <td> Distributor Visit 
                            <p>{{$item->otheractivity->reason}}</p>
                            </td>
                            @elseif($item->type=='meeting')
                            <td> Meeting
                            <p>{{$item->otheractivity->reason}}</p>
                            </td> --}}
                            
                            @else
                            <td> Present </td>
                            @endif
                            @if($item->type!='leave')
                            <td> {{ $item->start_time }} </td>
                            @endif
                            @if(!empty($item->end_time))
                            <td> {{\Carbon\Carbon::parse($item->start_time)->diffInHours($item->end_time) }} Hours</td>
                            @else
                            <td></td>
                            @endif
                            @if($item->type=='leave')
                            <td></td>
                            @else
                            <td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal{{$item->user_id}}">Show Activity</button></td>
                            @endif
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal{{$item->user_id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Daily Activity</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="container my-5">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="timeline-wrapper">
                                                            <ul class="list-unstyled p-0 m-0">
                                                                @foreach($activity as $item)
                                                                <li>
                                                                    <div class="left">
                                                                        <div class="time">
                                                                        <span>{{$item->time}}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="right">
                                                                        <div class="info">
                                                                            <p>
                                                                                {{$item->comment}}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                
                                    </div>
                                </div>
                            </div>
                        </tr>
                    @empty
                        <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                    @endforelse
                </tbody>
             </table>
            </div>

    <div class="d-flex justify-content-end">
        {{ $data->appends($_GET)->links() }}
    </div>
</section>

@endsection

@section('script')
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<script>
    $('select[name="zsm"]').on('change', (event) => {
        var value = $('select[name="zsm"]').val();

        $.ajax({
            url: '{{url("/")}}/admin/rsm/list/zsmwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="rsm"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    content += '<option value="'+value.rsm.id+'">'+value.rsm.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>

<script>
    $('select[name="rsm"]').on('change', (event) => {
        var value = $('select[name="rsm"]').val();

        $.ajax({
            url: '{{url("/")}}/admin/sm/list/rsmwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="sm"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    content += '<option value="'+value.sm.id+'">'+value.sm.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
<script>
    $('select[name="sm"]').on('change', (event) => {
        var value = $('select[name="sm"]').val();

        $.ajax({
            url: '{{url("/")}}/admin/asm/list/smwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="asm"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    content += '<option value="'+value.asm.id+'">'+value.asm.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
<script>
    $('select[name="asm"]').on('change', (event) => {
        var value = $('select[name="asm"]').val();

        $.ajax({
            url: '{{url("/")}}/admin/ase/list/asmwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="ase"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    content += '<option value="'+value.ase.id+'">'+value.ase.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
@endsection