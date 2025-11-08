@extends('admin.layouts.app')
@section('page', 'User Daily Activity')
@section('content')

<section class="store-sec ">
  <div class="row">
      <div class="col-xl-12 order-2 order-xl-1">
          <div class="card search-card">
              <div class="card-body">
                  <div class="search__filter mb-5">
                      <div class="row align-items-center justify-content-between">
                          <div class="col-md-12 mb-3">
                              <p class="small text-muted mt-1 mb-0"></p>
                          </div>
                          <div class="col-md-12 mb-3">
                              <div class="search-filter-right">
                                  <div class="search-filter-right-el">
                                      <form class="row align-items-end justify-content-end" action="" method="GET">
                                          <div class="search-filter-right">
                                              <div class="search-filter-right-el">
                                                  <label for="date_from" class="text-muted small">Date</label>
                                                  <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{ (request()->input('date_from')) ? request()->input('date_from') : '' }}">
                                              </div>
                                              
                                              
                                              <div class="search-filter-right-el">
                                                <label for="state" class="text-muted small">Sales Person</label>
                                                <select name="ase" id="ase" class="form-control form-control-sm select2">
                                                    <option value="" disabled>Select</option>
                                                    <option value="" selected>All</option>
                                                    @foreach ($user as $item)
                                                        <option value="{{$item->id}}" {{ request()->input('ase') == $item->id ? 'selected' : '' }}>{{$item->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                             
                                          <div class="search-filter-right search-filter-right-store align-self-end">
                                              
                                              
                                              <div class="search-filter-right-el">
                                                  <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">
                                                      Filter
                                                  </button>
                                                  <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                                      <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                  </a>
                                              </div>
                                              
                                             
                                          </div>
                                           
                                      </form>
                                  </div>
                                  
                                  
                                  
                                 
                              </div>
                          </div>
                          
            
                      </div>
                  </div>
                @if(!empty($data))
                 @php
                  $color='';
                 @endphp
                             
                    <div class="row">
                        <div class="col-12">
                            <div class="timeline-wrapper">
                                <ul class="list-unstyled p-0 m-0">
                                    @foreach($data as $item)
                                   
                                    @php
                                    $ordqty='';
                                                      if ($item->type =='Store Added') 
                                                           $color = "#1815c7";
                                                       elseif ($item->type =='Visit Started' ) 
                                                           $color = "#03990f";
                                                            elseif ($item->type =='Visit Ended') 
                                                           $color = "#ff2f18";
                                                        elseif ($item->type =='Order Upload') 
                                                           $color = "#03afcb";
                                                        elseif ($item->type =='Order On Call') 
                                                           $color = "#ff00bc";
                                                        elseif ($item->type =='Start Visit') 
                                                           $color = "#999403";
                                                        elseif ($item->type =='No Order Placed') 
                                                           $color = "#ff6868";
                                                        elseif ($item->type =='meeting') 
                                                           $color = "#ffab2e";
                                                           elseif ($item->type =='distributor-visit-start') 
                                                           $color = "#fe7096";
                                                           elseif ($item->type =='distributor-visit-end') 
                                                           $color = "#ffbf96";
                                                           elseif ($item->type =='distributor-visit') 
                                                           $color = "#868e96";
                                                           if($item->type =='Order Upload' || $item->type =='Order On Call'){
                                                           $timeInAMPM = date('h:i A', strtotime($item->created_at));
                                                            $ordqty = \App\Models\OrderProduct::select(DB::raw("(SUM(order_products.qty)) as qty"))->join('orders', 'orders.id', 'order_products.order_id')->where('orders.created_at', 'like','%' . date('Y-m-d H:i', strtotime($item->created_at)). '%')->where('orders.user_id',$item->user_id)->get();  
                                                         
                                                          }   
                                                          
                                        @endphp
                                        <li>
                                            <div class="left">
                                                <div class="time">
                                                  <span>{{$item->time}}</span>
                                                </div>
                                            </div>
                                            <div class="right">
                                                <div class="info">
                                                    <h6 style="color:{{$color}}">{{$item->type}}</h6>
                                                     <p style="bold"> {{$ordqty[0]->qty ?? ''}}</p>
                                                    <p>{{$item->comment}}</p>
                                                    <p>{{$item->location}}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                                            
                                
            @endif
          </div>
      </div>
  </div>
</section>



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
