@extends('admin.layouts.app')
@section('page', 'QRCODE HISTORY')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@section('content')
@php
$allASE=DB::table('users')->where('type',6)->orderby('name')->groupby('name')->get();

@endphp
<section>
    <div class="card card-body">
        <div class="search__filter mb-0">
            <div class="row align-items-center">
				
                <div class="col-md-12 text-end">
                    
                </div>
            </div>
        </div>
        
    <section class="for-fixed-table">
       <div class="table-responsive" fffffff>
        <table class="table no-sticky"  id="example5">
            <thead>
                <tr>
                    
                    <th>QR Details</th>
                    <th>Code</th> 
                    <th>Points</th> 
    				<th>Warehouse Stock In</th>
    				<th>Warehouse Stock Out</th>
                    <th>Primary Order No</th>
                    <th>Distributor</th>
                    <th>Distributor Stock In</th>
                    <th>Distributor Stock Out</th>
                    <th>Secondary Order No</th>
    				
                </tr>
            </thead>
            <tbody>
                
                
    			  @php
    			    
    			    
    			    
    			    
    			    
    			    $stockOutW=\App\Models\WarehouseStock::where('qrcode_id',$data->id)->with('order')->first();
    			    $stockOutD=\App\Models\DistributorStock::where('qrcode_id',$data->id)->with('distributor')->first();
    			   // dd($data);
    			    
    			 @endphp
                  
    
                    <tr>
                        
    					<td>
                            {{ $data ? $data->name : '' }}
                        </td>
    					 <td>
                            {{ $data ? $data->code : '' }}
                        </td>
                        <td>
                            {{ $data ? $data->amount : '' }}
                        </td>
                        @if($data->stock_in_date)
    					 <td>
                            {{ \Carbon\Carbon::parse($data->stock_in_date)->format('d/m/Y')  ??''}}<br> by <br>{{ $data->user->name  ??''}}
                        </td>
                        @else
                        <td></td>
                        @endif
                        @if($stockOutW)
    					<td>
                            {{ \Carbon\Carbon::parse($stockOutW->created_at)->format('d/m/Y')}} <br> 
                        </td>
                        @endif
                        <td>
                            {{ $stockOutW->order->order_no ??''}}
                           
                            
                        </td>
    					
                         <td>
                            {{ $stockOutW->distributor->name ??''}}
                           
                            
                        </td>
                        @if($stockOutD->stock_in_date)
                       <td>{{ \Carbon\Carbon::parse($stockOutD->stock_in_date)->format('d/m/Y')  ??''}}</td>
                       @else
                       <td></td>
                        @endif 
                        @if($stockOutD->stock_out_date)
                       <td>{{ \Carbon\Carbon::parse($stockOutD->stock_out_date)->format('d/m/Y')  ??''}}</td>
                       @else
                       <td></td>
                       @endif
                       <td>{{ $stockOutD->order->order_no ??''}}</td>
                    </tr>
                
            </tbody>
        </table>
    	</div>
    </section>
        
        
        
    </div>

    
</section>

<div class="modal fade" id="reedemDelete" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.reward.qrcode.redeem.remove') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/reedem.csv') }}">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

	
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
 <script>
    function stateWiseArea(value) {
        $.ajax({
            url: '{{url("/")}}/state-wise-area/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="area"]';
                var displayCollection = (result.data.state == "all") ? "All Area" : "All "+" area";
                content += '<option value="" selected>'+displayCollection+'</option>';
                
                let cat = "{{ app('request')->input('area') }}";

                $.each(result.data.area, (key, value) => {
                    if(value.area == '') return;
                    if (value.area == cat) {
                        content += '<option value="'+value.area+'" selected>'+value.area+'</option>';
                    } else {
                        content += '<option value="'+value.area+'">'+value.area+'</option>';
                    }
                    //content += '<option value="'+value.area+'">'+value.area+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    }

    $('select[name="state"]').on('change', (event) => {
        var value = $('select[name="state"]').val();
        stateWiseArea(value);
    });

    @if(request()->input('state'))
        stateWiseArea("{{ request()->input('state') }}");
    @endif
</script>
@endsection