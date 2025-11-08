

@extends('admin.layouts.app')
@section('page', 'Monthly Employee Productivity')

@section('content')

<section class="store-sec ">
    <div class="row">
        <div class="col-xl-12 order-2 order-xl-1">
            <div class="card search-card">
                <div class="card-body">
                    <div class="search__filter mb-5">
                        <div class="row align-items-center justify-content-between">
                            
                            <div class="col-md-12 mb-3">
                                    <div class="search-filter-right-el">
                                        <form class="row align-items-end" action="{{ route('admin.employee.productivity.monthly') }}" method="GET">
                                            <div class="search-filter-right">
                                                
                                                <div class="search-filter-right-el"><label for="date_from" class="text-muted small">Date from</label></div>
                                                <div class="search-filter-right-el">
                                                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from') ?? date('Y-m-01') }}">
                                                </div>
                                                <div class="search-filter-right-el"><label for="date_to" class="text-muted small">Date to</label></div>
                                                <div class="search-filter-right-el">
                                                    
                                                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to') ?? date('Y-m-d') }}">
                                                </div>
                                                 <div class="search-filter-right-el"><label for="date_to" class="text-muted small">Status</label></div>
                                                  <div class="search-filter-right-el">
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
                                                    <a id="btnExport" href="javascript:void(0);" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                                        
                                                        <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                                    </a>
                                                </div> 
                                                
                                            </div>
                                            
                                            <!--<div class="search-filter-right search-filter-right-store mt-4">-->
                                            <!--    <div class="search-filter-right-el">-->
                                            <!--        <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">-->
                                            <!--            Filter-->
                                            <!--        </button>-->
                                            <!--        <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">-->
                                            <!--            <iconify-icon icon="basil:cross-outline"></iconify-icon>-->
                                            <!--        </a>-->
                                            <!--    </div>-->
                                            <!--    <div class="search-filter-right-el">-->
                                            <!--        <a id="btnExport" href="javascript:void(0);" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">-->
                                                        
                                            <!--            <iconify-icon icon="material-symbols:download"></iconify-icon> CSV-->
                                            <!--        </a>-->
                                            <!--    </div> -->
                                            <!--</div>-->
                                            
                                             
                                        </form>
                                    </div>

                            </div>
                            
							
                        </div>
                    </div>
                    <div id="tableWrap">
                        <table class="table" >
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Employee Id</th>
                                    <th>Employee Designation</th>
                                    <th>Total SC</th>
                                    <th>Total TC</th>
                                    <th>Total PC</th>
                                    <th>Total PC %</th>
                                    <th>Total Ord Qty</th>
                                </tr>
                            </thead>
                        <tbody>
                        
                        @forelse ($data as $index => $item)
    
                            @php
                              $findTeamDetails= findTeamDetails($item->id, $item->type);
                              $daysCount=daysProductivityCount($date_from,$date_to,$item->id);
                              $productivityCount_pc_p=0;
                              if($daysCount['total_pc']!=0 && $daysCount['total_tc']!=0){
                              $productivityCount_pc_p= number_format((float)($daysCount['total_pc']/$daysCount['total_tc'])*100);
                                           }else{
                                           $productivityCount_pc_p=0;
                                           }
                            @endphp
                        
                            <tr>
                                
                                <td> {{$item->name}} </td>
                                <td> {{$item->employee_id}} </td>
                                <td> {{ $item->designation ? $item->designation : userTypeName($item->type) }} </td>
                                <td> 
                                    {{-- {{$daysCount['total_sc'] ?? ''}}  --}}



                                    {{-- updated sc count --}}
                                    @php
                                        $userId = $item->id ? $item->id : 'all';
                                        $dateFrom = date('Y-m-d', strtotime($date_from));
                                        $dateTo = date('Y-m-d', strtotime($date_to));
                                    @endphp
                                    {{ updatedSCCount($userId, $dateFrom, $dateTo) }}
                                
                                
                                </td>
                                <td> {{$daysCount['total_tc'] ?? ''}} </td>
                                <td> {{$daysCount['total_pc']  ?? ''}}</td>
                                <td> {{$productivityCount_pc_p  ?? ''}} %</td>
                                <td>
                                    @if ($item->orderDetails)
                                        @php
                                            $dateFrom = request()->input('date_from') ? request()->input('date_from') : date('Y-m-01');
                                            $dateTo = request()->input('date_to') ? request()->input('date_to') : date('Y-m-d');

                                            $ddate = date('Y-m-d', strtotime($date_from));
                                            $qrdQty = DB::select("SELECT count(op.id) AS total, sum(op.qty) AS qty FROM `orders` AS o 
                                            inner join order_products AS op on o.id = op.order_id 
                                            where o.user_id = '$item->id' AND DATE(o.created_at) >= '$dateFrom' AND DATE(o.created_at) <= '$dateTo'");
                                            echo number_format($qrdQty[0]->qty);
                                        @endphp
                                    @else
                                        0
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                        @endforelse
                    </tbody>
                </table>
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