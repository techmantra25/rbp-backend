@extends('layouts.app')

@section('page', 'Dashboard')

@section('content')
<section class="store_listing">
    <div class="container">
          <div class="row mt-4">
			  <div class="col-12">
                    <div class="d-flex justify-content-between mb-3">
                        <ul class="nav nav-pills mt-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link" id="pills-current-tab" data-toggle="pill" href="#pills-current" role="tab" aria-controls="pills-current" aria-selected="true">ASM</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active " id="pills-past-tab" data-toggle="pill" href="#pills-past" role="tab" aria-controls="pills-past" aria-selected="false">ASE</a>
                            </li>
                        </ul>
                    </div>
            <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade" id="pills-current" role="tabpanel" aria-labelledby="pills-current-tab">
                            <div class="row">
                                <div class="col-12">
                                    
                                </div>

                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Designation</th>
											<th>Contact</th>
											<th>Working Area</th>
										    <th>Store Count</th>
											<th>Sales Count(Qty)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                          @foreach($asms as $asm)
										 @php
										$area ='';
										   $storeCount=DB::table('stores')->where('user_id',$asm->asm->id)->get();
										   $workArea=\App\Models\Team::select('area_id')->where('asm_id',$asm->asm->id)->groupby('area_id')->with('areas')->get();
										
										if(!empty($workArea)) {
											foreach($workArea as $key => $obj) {
												$area .= $obj->areas->name;
												if((count($workArea) - 1) != $key) $area .= ', ';
											}
										}
										 $orderCount=DB::select("SELECT SUM(op.qty) AS qty FROM `order_products` AS op
                                           
                                            INNER JOIN orders AS o ON o.id = op.order_id
                                             WHERE o.user_id = '".$asm->asm->id."'");
										@endphp
                                            <tr>
                                                <td>
                                                   {{$asm->asm->name}}
                                                    
                                                </td>
                                                <td>
                                                   
                                                   {{$asm->asm->designation}}
                                                </td>
												<td>
                                                   
                                                   {{$asm->asm->mobile}}
                                                </td>
												<td>
                                                   
                                                  {{$area}}
                                                </td>
												<td>
                                                   
                                                   {{ $storeCount->count()}}
                                                </td>
												<td>
                                                   
                                                   {{ $orderCount[0]->qty}}
                                                </td>
                                            </tr>
                                         @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade show active" id="pills-past" role="tabpanel" aria-labelledby="pills-past-tab">
                            <div class="row">
                                <div class="col-12">
                                  
                                   
                                </div>

                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Designation</th>
											<th>Contact</th>
											<th>Working Area</th>
										    <th>Store Count</th>
											<th>Sales Count(Qty)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @foreach($ases as $ase)
										 @php
										$area ='';
										   $storeCount=DB::table('stores')->where('user_id',$ase->ase->id)->get();
										   $workArea=\App\Models\UserArea::select('area_id')->where('user_id',$ase->ase->id)->groupby('area_id')->with('areas')->get();
										
										if(!empty($workArea)) {
											foreach($workArea as $key => $obj) {
												$area .= $obj->areas->name;
												if((count($workArea) - 1) != $key) $area .= ', ';
											}
										}
										 $orderCount=DB::select("SELECT SUM(op.qty) AS qty FROM `order_products` AS op
                                           
                                            INNER JOIN orders AS o ON o.id = op.order_id
                                             WHERE o.user_id = '".$ase->ase->id."'");
										@endphp
                                            <tr>
                                                <td>
                                                   {{$ase->ase->name}}
                                                    
                                                </td>
                                                <td>
                                                   
                                                   {{$ase->ase->designation}}
                                                </td>
												<td>
                                                   
                                                   {{$ase->ase->mobile}}
                                                </td>
												<td>
                                                   
                                                  {{$area}}
                                                </td>
												<td>
                                                   
                                                   {{ $storeCount->count()}}
                                                </td>
												<td>
                                                   
                                                   {{ $orderCount[0]->qty}}
                                                </td>
                                            </tr>
                                         @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


   
</section>
@endsection


