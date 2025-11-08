

@extends('admin.layouts.app')
@section('page', 'Employee Productivity')

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
                                        <form class="row align-items-end" action="{{ route('admin.employee.productive.call') }}" method="GET">
                                            <div class="search-filter-right">
                                                <div class="search-filter-right-el">
                                                    <label for="state" class="text-muted small">ASM/ASE</label>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    
                                                    <select name="asm" id="asm" class="form-control form-control-sm select2">
                                                        <option value="" disabled>Select</option>
                                                        <option value="all" selected>All</option>
                                                        @foreach ($zsmDetails as $item)
                                                            <option value="{{$item->id}}" {{ request()->input('asm') == $item->id ? 'selected' : '' }}>{{$item->name}}({{$item->designation}})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                
                                                {{--<div class="search-filter-right-el">
                                                    <label class="small text-muted">ASM</label>
                                                    <select class="form-control form-control-sm select2" id= "asm" name="asm" disabled>
                                                        <option value="{{ $request->asm_id }}">Select state first</option>
                                                    </select>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label class="small text-muted">ASE</label>
                                                    <select class="form-control form-control-sm select2" id="ase" name="ase" disabled>
                                                        <option value="{{ $request->ase_id }}">Select state first</option>
                                                    </select>
                                                </div>--}}
                                                <div class="search-filter-right-el">
                                                    <label for="month" class="text-muted small">Month</label>
                                                </div>
                                                 <div class="search-filter-right-el">
                                                    <input type="month" name="month" id="month" class="form-control form-control-sm" aria-label="Default select example" value="{{$month}}">
                                                </div>
                                               {{-- <div class="search-filter-right-el">
                                                    <label for="date_from" class="text-muted small">Date from</label>
                                                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from') ?? date('Y-m-01') }}">
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <label for="date_to" class="text-muted small">Date to</label>
                                                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to') ?? date('Y-m-d') }}">
                                                </div>--}}
                                                
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
                                                    <a id="btnExport" href="javascript:void(0);" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                                        
                                                        <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                                    </a>
                                                </div>
                                                
                                            </div>
                                            
                                            <!--<div class="search-filter-right search-filter-right-store mt-4">
                                                <div class="search-filter-right-el">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">
                                                        Filter
                                                    </button>
                                                    <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                                        <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                    </a>
                                                </div>
                                                <div class="search-filter-right-el">
                                                    <a id="btnExport" href="javascript:void(0);" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                                        
                                                        <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                                    </a>
                                                </div> 
                                            </div>-->
                                             
                                        </form>
                                    </div>
                                    
                                    
                                    
                                   
                                </div>
                            </div>
                            
							
                        </div>
                    </div>
                     @php
                        $my_month =  explode("-",$month);
                        $year_val = $my_month[0];
                        $month_val = $my_month[1];
                        $dates_month=dates_month($month_val,$year_val);
                        $month_names = $dates_month['month_names'];
                        $date_values = $dates_month['date_values'];
                        $totaldays=count($dates_month['date_values']);
                        
                        
                        $days = $dates_month['date_values'];
                        
                        
                        
                        
                    @endphp
                        <div id="tableWrap">
                        <table class="table" >
                            <thead>
                                <tr>
                                    <th>Day</th>
                                   {{-- <th>BH</th>
                                    <th>ZSM</th>
                                    <th>ASM</th> --}}
                                    <th>Employee</th>
                                    <th>Employee Designation</th>
                                    <th>SC</th>
                                    <th>TC</th>
                                    <th>PC</th>
                                    <th>PC %</th>
                                    {{-- <th>MUB</th> --}}
                                    <th>Total Ord Qty</th>
                                </tr>
                            </thead>
                        <tbody>
                        @if(!empty($data))
                        @php
                        $all_sc_total_amount = 0;
                        $all_tc_total_amount=0;
                        $all_pc_total_amount=0;
                        $all_mub_total_amount=0;
                                        $totalMonthlyOrders=0;
                        @endphp
                        
                             @forelse ($data as $index => $item)
                                   @foreach ($days as $index22 => $months)
                        
                                        @php
                                            $getMonthName = getMonthName($months);
                                            
                                        @endphp
                                    
                                        @php
                                        $productivityCount_sc=0;
                                        $productivityCount_tc='';
                                        $productivityCount_pc='';
                                       $productivityCount_pc_p=0;
                                       $productivityCount_mub='';
                                          $findTeamDetails= findTeamDetails($item->id, $item->type);
                                         
                                       
                                          $productivityCount=productivityCount($item->id, $months);
                                         
                                           $productivityCount_sc= $productivityCount['sc'] ?? '';
                                           $productivityCount_tc= $productivityCount['tc'] ?? '';
                                           $productivityCount_pc= $productivityCount['pc'] ?? '';
                                           $productivityCount_mub= $productivityCount['mub'] ?? '';
                                           if($productivityCount_pc!=0 && $productivityCount_tc!=0){
                                           $productivityCount_pc_p= number_format((float)($productivityCount_pc/$productivityCount_tc)*100);
                                           }else{
                                           $productivityCount_pc_p=0;
                                           }
                                           $all_sc_total_amount += ($productivityCount['sc']);
                                           $all_tc_total_amount += ($productivityCount_tc);
                                           $all_pc_total_amount += ($productivityCount_pc);
                                            $all_mub_total_amount += ($productivityCount_mub);
                                        @endphp
                                        
                                     
                                            {{--<tr>
                                                        <th>{{$getMonthName}}</th>
                                            </tr>--}}
                                         
                                <tr>
                                    
                                    <td>{{$getMonthName}}</td>
                                    {{--<td> {{$findTeamDetails[0]['nsm'] ?? ''}} </td> 
                                    <td> {{$findTeamDetails[0]['zsm']?? ''}} </td>
                                    <td> {{$findTeamDetails[0]['asm']?? ''}} </td> --}}
                                    <td> {{$item->name}} </td>
                                    <td>{{ $item->designation ? $item->designation : userTypeName($item->type) }} </td>

                                    <td>
                                        {{-- {{$productivityCount_sc ?? ''}} --}}



                                        {{-- updated sc count --}}
                                        @php
                                            $userId = request()->input('asm') ? request()->input('asm') : 'all';
                                            $dateFrom = date('Y-m-d', strtotime($month.'-'.$index22));
                                            $dateTo = date('Y-m-d', strtotime($month.'-'.$index22));
                                        @endphp
                                        {{ updatedSCCount($userId, $dateFrom, $dateTo) }}
                                    </td>



                                    <td> {{$productivityCount_tc ?? ''}} </td>
                                    <td> {{$productivityCount_pc ?? ''}} </td>
                                    <td>{{$productivityCount_pc_p ??''}} % </td>
                                    {{-- <td></td> --}}
                                    <td>
                                        @if ($item->orderDetails)
                                            @php
                                                $ddate = date('Y-m-d', strtotime($month.'-'.$index22));
                                                $qrdQty = DB::select("SELECT count(op.id) AS total, sum(op.qty) AS qty FROM `orders` AS o 
                                                inner join order_products AS op on o.id = op.order_id 
                                                where o.user_id = '$item->id' AND DATE(o.created_at) = '$ddate'");
                                                $totalMonthlyOrders += $qrdQty[0]->qty;
                                                echo number_format($qrdQty[0]->qty);
                                            @endphp
                                        @else
                                            @php
                                                $totalMonthlyOrders += 0;
                                                echo 0;
                                            @endphp
                                        @endif
                                    </td>
                                    
                                
                                  
                                </tr>
                                
                                @endforeach 
                               
                               {{-- <tr>
                                    <td></td>
                                    <td></td>
                                   
                                    <td>
                                        <p class="small text-dark mb-1 fw-bold">TOTAL</p>
                                    </td>
                                    <td>
                                        <p class="small text-dark mb-1 fw-bold">{{ number_format($all_sc_total_amount) }}</p>
                                    </td>
                                     <td>
                                        <p class="small text-dark mb-1 fw-bold">{{ number_format($all_tc_total_amount) }}</p>
                                    </td>
                                     <td>
                                        <p class="small text-dark mb-1 fw-bold">{{ number_format($all_pc_total_amount) }}</p>
                                    </td>
                                    <td></td>
                                    <td><p class="small text-dark mb-1 fw-bold">{{ number_format($productivityCount_mub) ??''}}</p></td>
                                </tr>--}}
                                
                            
                            
                           
                          @empty
                            <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                             
                          @endforelse
                        
                                <tr>
                                    <td></td>
                                    <td></td>
                                   
                                    <td>
                                        <p class="small text-dark mb-1 fw-bold">TOTAL</p>
                                    </td>
                                    <td>
                                        <p class="small text-dark mb-1 fw-bold">
                                            {{-- {{ number_format($all_sc_total_amount) }} --}}



                                            {{-- updated sc count --}}
                                            @php
                                                $userId = request()->input('asm') ? request()->input('asm') : 'all';
                                                $dateFrom = getFirstAndLastDayOfMonth($month)[0];
                                                $dateTo = getFirstAndLastDayOfMonth($month)[1];
                                            @endphp
                                            {{ updatedSCCount($userId, $dateFrom, $dateTo) }}
                                        </p>
                                    </td>
                                     <td>
                                        <p class="small text-dark mb-1 fw-bold">{{ number_format($all_tc_total_amount) }}</p>
                                    </td>
                                     <td>
                                        <p class="small text-dark mb-1 fw-bold">{{ number_format($all_pc_total_amount) }}</p>
                                    </td>
                                    <td></td>
                                    {{-- <td><p class="small text-dark mb-1 fw-bold">{{ number_format($productivityCount_mub) ??''}}</p></td> --}}
                                    <td><p class="small text-dark mb-1 fw-bold">{{ number_format($totalMonthlyOrders)}}</p></td>
                                </tr>
                       
                        @endif
                    </tbody>
                </table>
            </div>
       
                <div class="d-flex justify-content-end">
                    {{ $data->appends($_GET)->links() }}
                </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('script')
<script>
        $('select[name="zsm"]').on('change', (event) => {
            var value = $('select[name="zsm"]').val();
    
            $.ajax({
                url: '{{url("/")}}/admin/asm/list/zsmwise/'+value,
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
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script>
    $(function() {
        $('#btnExport').click(function() {
            console.log("hello");
            //$('#tblHead').css("display","block");
            var url = 'data:application/vnd.ms-excel,' + encodeURIComponent($('#tableWrap').html())
            location.href = url
            return false
            $('#tblHead').css("display", "none");
        });
    });
</script>
@endsection