@extends('admin.layouts.app')
@section('page', 'Daily Summary')
@section('content')

<section class="store-sec ">
  <div class="row">
      <div class="col-xl-12 order-2 order-xl-1">
          <div class="card search-card">
              <div class="card-body">
                  <div class="search__filter mb-5">
                      <div class="row align-items-center justify-content-between">
                          
                          <div class="col-md-12 mb-3">
                              <div class="search-filter-right">
                                  <div class="search-filter-right-el">
                                      <form class="row align-items-end justify-content-end" action="" method="GET">
                                          <div class="search-filter-right">
                                                <div class="search-filter-right-el">
                                                    <label for="zsm" class="text-muted small">ZSM</label>
                                                    <select name="zsm" id="zsm" class="form-control form-control-sm select2">
                                                        <option value="" selected>Select</option>
                                                       <option value="all" {{ request()->input('zsm') == 'all' ? 'selected' : '' }}>All</option>
                                                        @foreach ($zsmDetails as $item)
                                                         
                                                            <option value="{{$item->id}}" {{ request()->input('zsm') == $item->id ? 'selected' : '' }}>{{$item->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                 <div class="search-filter-right-el">
                                                    <label class="small text-muted">State</label>
                                                    <select class="form-control form-control-sm select2" name="state" >
                                                         <option value="" selected>Select</option>
                                                        
                                                        <option value="{{ $request->state_id }}">Select ZSM  first</option>
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">RSM</label>
                                                    <select class="form-control form-control-sm select2" name="rsm" >
                                                         <option value="" selected>Select</option>
                                                        
                                                        <option value="{{ $request->rsm_id }}">Select State  first</option>
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">SM</label>
                                                    <select class="form-control form-control-sm select2" name="sm" >
                                                        <option value="" selected>Select</option>
                                                        
                                                        <option value="{{ $request->sm_id }}">Select RSM first</option>
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">ASM</label>
                                                    <select class="form-control form-control-sm select2" name="asm" >
                                                        <option value="" selected>Select</option>
                                                       
                                                        <option value="{{ $request->asm_id }}">Select SM first</option>
                                                    </select>
                                                </div>
                                              <div class="search-filter-right-el">
                                                  <label for="date_from" class="text-muted small">Date</label>
                                                  <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{$date_from}}">
                                              </div>
                                              <div class="search-filter-right-el">
													<label for="ase" class="small text-muted">Status</label>
													<select class="form-select form-select-sm select2" id="status" name="status_id">
														 <option value="" >Select</option>
                                                        
															<option value="active" {{ request()->input('status_id') == 'active' ? 'selected' : '' }}>Active</option>
														  <option value="inactive" {{ request()->input('status_id') == 'inactive' ? 'selected' : '' }}>Inactive</option>
													</select>
                       						     </div>
                                              <div class="search-filter-right-el align-self-end">
                                                  <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">
                                                      Filter
                                                  </button>
                                              </div>
                                              
                                              <div class="search-filter-right-el align-self-end">
                                                    <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                                      <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                    </a>
                                              </div>
                                              
                                              <div class="search-filter-right-el align-self-end">
                                                 <div class="search-filter-right-el">
                                                  <a href="{{ route('admin.users.attendance.csv.download', ['zsm' => $request->zsm,'state' => $request->state,
                                                  'rsm' => $request->rsm,
                                                  'sm' => $request->sm,
                                                  'asm' => $request->asm,
                                                  'date_from' => $request->date_from,
                                                  'status_id'=>$request->status_id
                                                  ]) }}" id="btnExport" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                                      
                                                      <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                                  </a>
                                                </div> 
                                              </div>
                                              
                                              
                            
                                          </div>
                                          <div class="search-filter-right search-filter-right-store mt-3">
                                              
                                              
                                              <!--<div class="search-filter-right-el">-->
                                              <!--    <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">-->
                                              <!--        Filter-->
                                              <!--    </button>-->
                                              <!--    <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">-->
                                              <!--        <iconify-icon icon="basil:cross-outline"></iconify-icon>-->
                                              <!--    </a>-->
                                              <!--</div>-->
                                              
                                               <!--<div class="search-filter-right-el">
                                                  <a href="{{ route('admin.users.attendance.csv.download', ['zsm' => $request->zsm,
                                                  'rsm' => $request->rsm,
                                                  'sm' => $request->sm,
                                                  'asm' => $request->asm,
                                                  'date_from' => $request->date_from,
                                                  ]) }}" id="btnExport" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                                      
                                                      <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                                  </a>
                                                </div>-->
                                          </div>

                                          <p class="small text-end text-right mt-3">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} Entries</p>
                                           
                                      </form>
                                  </div>
                                  
                                  
                                  
                                 
                              </div>
                          </div>
                          
            
                      </div>
                  </div>
                  @if(!empty($data))
                    <table class="table big-sticky" >
                    <thead>
                        <tr>
                            <th>FieldUser Name</th>
                            <th>FieldUser Designation</th>
                            <th>FieldUser Status</th>
                            <th>Type</th>
                            <th>Login</th>
                            <th>First Call</th>
                            <th>Last Active</th> 
                            <th>SC</th>
                            <th>TC</th>
                            <th>PC</th>
                            <th>PC %</th>
                            <th>TO</th>
                            <th>Total Ord Qty</th>
                            <th>Selected Beat</th>
                            <th></th>
                        </tr>
                    </thead>
                <tbody>
                    @forelse ($data as $index => $item)
                    
                    @php
                       $daysCount=productivityCount($item->id,$date_from);
                       //$type=DB::select("select * from activities where user_id='$item->id' and (DATE_FORMAT(date,'%Y-%m-%d') = '$date_from') GROUP BY date ORDER BY time asc ");
                       $type= DB::table('activities')->where('user_id',$item->id)->whereDate('date', $date_from)->orderby('created_at','asc')->first();
                       
                       $productivityCount_pc_p=0;

                            if (!empty($daysCount)) { 
                              if($daysCount['pc']!=0 && $daysCount['tc']!=0){
                                 $productivityCount_pc_p= number_format((float)($daysCount['pc']/$daysCount['tc'])*100);
                                           }else{
                                           $productivityCount_pc_p=0;
                                           }}else{
                                           $productivityCount_pc_p=0;
                                           }
                    @endphp
                        <tr>
                            <td><a href="{{route('admin.users.daily.activity.index',['date_from'=>$date_from,'ase'=>$item->id])}}" target="_blank" >{{$item->name ?? ''}} </a></td>
                           <td>{{ $item->designation ? $item->designation : userTypeName($item->type) }} </td>
                            <td><span class="badge bg-{{($item->status == 1) ? 'success' : 'danger'}}">{{($item->status == 1) ? 'Active' : 'Inactive'}}</span></td>
                            @if(!empty($type))
                            @if($type->type=='distributor-visit' || $type->type=='distributor-visit-start')
                            <td> Distributor Visit </td> 
                            @elseif($type->type=='leave')
                            <td>Leave</td>
                            @elseif($type->type=='meeting')
                            <td>Meeting</td>
                            
                            
                            @elseif($type->type=='Visit Started' || $type->type=='Visit Ended' || $type->type=='Store Added' || $type->type=='No Order Placed' || $type->type=='Order Upload' || $type->type=='distributor-visit-end' || $type->type=='distributor-visit-start' || $type->type=='Order On Call')
                            <td>Retailing</td>
                            @elseif($type->type=='joint-work')
                            <td>Joint Work</td>
                            @else
                            <td></td>
                            @endif
                            @else
                            <td></td>
                            @endif
                            <td>{{$daysCount['login'] ?? ''}}</td>
                            <td>{{$daysCount['firstcall'] ?? ''}}</td>
                            <td>{{$daysCount['lastactive'] ?? ''}}</td>
                            <td> 
                                {{-- {{$daysCount['sc'] ?? ''}} --}}





                                {{-- updated sc count --}}
                                @php
                                    $userId = $item->id ? $item->id : 'all';
                                    $dateFrom = date('Y-m-d', strtotime($date_from));
                                    $dateTo = date('Y-m-d', strtotime($date_from));
                                @endphp
                                {{ updatedSCCount($userId, $dateFrom, $dateTo) }}
                            </td>
                            <td> {{$daysCount['tc'] ?? ''}} </td>
                            <td> {{$daysCount['pc']  ?? ''}} </td>
                            <td> {{$productivityCount_pc_p  ?? ''}} %</td>
                            <td> {{$daysCount['to']  ?? ''}} </td>
                            <td>
                                @if ($item->orderDetails)
                                    @php
                                        $ddate = date('Y-m-d', strtotime($date_from));
                                        $qrdQty = DB::select("SELECT count(op.id) AS total, sum(op.qty) AS qty FROM `orders` AS o 
                                        inner join order_products AS op on o.id = op.order_id 
                                        where o.user_id = '$item->id' AND DATE(o.created_at) = '$ddate'");
                                        echo number_format($qrdQty[0]->qty);
                                    @endphp
                                @else
                                    0
                                @endif
                            </td>
                            <td>{{$daysCount['beat'] ?? ''}}</td>
                            {{--<td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal{{$item->id}}">Show Activity</button></td>--}}
                            
                            <!-- Modal -->
                            {{--<div class="modal fade" id="exampleModal{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">{{$item->name ?? ''}} Activities</h5>
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
                            </div>--}}
                        </tr>
                    @empty
                        <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                    @endforelse
                </tbody>
             </table>
           
    <div class="d-flex justify-content-end">
        {{ $data->appends($_GET)->links() }}
    </div>
     @endif
</section>

@endsection

@section('script')
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<script>
   /* $('select[name="zsm"]').on('change', (event) => {
        var value = $('select[name="zsm"]').val();
        RSMChange(value);
    });

    @if (request()->input('zsm'))
        RSMChange({{request()->input('zsm')}})
    @endif

    function RSMChange(value) {
        $.ajax({
            url: '{{url("/")}}/admin/rsm/list/zsmwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="rsm"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    let selected = ``;
                    @if (request()->input('rsm'))
                        if({{request()->input('rsm')}} == value.rsm.id) {selected = 'selected';}
                    @endif
                    content += '<option value="'+value.rsm.id+'"'; content+=selected; content += '>'+value.rsm.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    }*/
    
    
    
    
    $('select[name="zsm"]').on('change', (event) => {
        var value = $('select[name="zsm"]').val();
        StateChange(value);
    });

    @if (request()->input('zsm'))
        StateChange({{request()->input('zsm')}})
    @endif

    function StateChange(value) {
        $.ajax({
            url: '{{url("/")}}/admin/state/list/zsmwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="state"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    let selected = ``;
                    @if (request()->input('state'))
                        if({{request()->input('state')}} == value.states.id) {selected = 'selected';}
                    @endif
                    content += '<option value="'+value.states.id+'"'; content+=selected; content += '>'+value.states.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    }
    
    
    $('select[name="state"]').on('change', (event) => {
        var value = $('select[name="state"]').val();
        RSMChange(value);
    });

    @if (request()->input('state'))
        RSMChange({{request()->input('state')}})
    @endif

    function RSMChange(value) {
        $.ajax({
            url: '{{url("/")}}/admin/rsm/list/statewise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="rsm"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    let selected = ``;
                    @if (request()->input('rsm'))
                        if({{request()->input('rsm')}} == value.rsm.id) {selected = 'selected';}
                    @endif
                    content += '<option value="'+value.rsm.id+'"'; content+=selected; content += '>'+value.rsm.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    }

    /*
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
    */
</script>

<script>
    $('select[name="rsm"]').on('change', (event) => {
        var value = $('select[name="rsm"]').val();
        SMChange(value);
    });

    @if (request()->input('rsm'))
        SMChange({{request()->input('rsm')}})
    @endif

    function SMChange(value) {
        $.ajax({
            url: '{{url("/")}}/admin/sm/list/rsmwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="sm"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    let selected = ``;
                    @if (request()->input('sm'))
                        if({{request()->input('sm')}} == value.sm.id) {selected = 'selected';}
                    @endif

                    content += '<option value="'+value.sm.id+'"'; content+=selected; content += '>'+value.sm.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    }

    /*
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
    */
</script>
<script>
    $('select[name="sm"]').on('change', (event) => {
        var value = $('select[name="sm"]').val();
        ASMChange(value);
    });

    @if (request()->input('sm'))
        ASMChange({{request()->input('sm')}})
    @endif

    function ASMChange(value) {
        $.ajax({
            url: '{{url("/")}}/admin/asm/list/smwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="asm"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    let selected = ``;
                    @if (request()->input('asm'))
                        if({{request()->input('asm')}} == value.asm.id) {selected = 'selected';}
                    @endif

                    content += '<option value="'+value.asm.id+'"'; content+=selected; content += '>'+value.asm.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    }


    /*
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
    */
</script>
<script>
    $('select[name="asm"]').on('change', (event) => {
        var value = $('select[name="asm"]').val();
        ASEChange(value);
    });

    @if (request()->input('asm'))
        ASEChange({{request()->input('asm')}})
    @endif

    function ASEChange(value) {
        $.ajax({
            url: '{{url("/")}}/admin/ase/list/asmwise/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="ase"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data, (key, value) => {
                    let selected = ``;
                    @if (request()->input('ase'))
                        if({{request()->input('ase')}} == value.ase.id) {selected = 'selected';}
                    @endif

                    content += '<option value="'+value.ase.id+'"'; content+=selected; content += '>'+value.ase.name+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    }


    /*
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
    */
</script>
<script>
    $(function() {
        /*
        $('#btnExport').click(function() {
            console.log("hello");
            //$('#tblHead').css("display","block");
            var url = 'data:application/vnd.ms-excel,' + encodeURIComponent($('#tableWrap').html())
            location.href = url
            return false
            $('#tblHead').css("display", "none");
        });
        */
    });
</script>
@endsection