@extends('admin.layouts.app')

@section('page', 'Area wise sales')

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
                                              
                                              <div class="search-filter-right-el"><label for="date_from" class="text-muted small">Date from</label></div>
                                              <div class="search-filter-right-el">
                                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from') ?? date('Y-m-01') }}">
                                              </div>
                                              
                                              <div class="search-filter-right-el"><label for="date_to" class="text-muted small">Date to</label></div>
                                              <div class="search-filter-right-el">
                                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to') ?? date('Y-m-d') }}">
                                              </div>
                                              
                                              <div class="search-filter-right-el"><label for="state" class="text-muted small">State</label></div>
                                              <div class="search-filter-right-el">
                                                   
                                                    <select name="state_id" id="state" class="form-control select2">
                                                        <option value="" disabled>Select</option>
                                                        <option value="" selected>All</option>
                                                        @foreach ($state as $row)
                                                            <option value="{{$row->id}}" {{ request()->input('state_id') == $row->id ? 'selected' : '' }}>{{$row->name}}</option>
                                                        @endforeach
                                                    </select>
                                              </div>
                                              
                                              <div class="search-filter-right-el"><label class="small text-muted">Area</label></div>
                                              <div class="search-filter-right-el">
                        							<select class="form-control select2" name="area_id" disabled>
                        								<option value="{{ $request->area_id }}">Select state first</option>
                        							</select>
                                              </div>
                                          </div>
                                          <div class="search-filter-right search-filter-right-store mt-3">
                                              
                                               
                                              <div class="search-filter-right-el">
                                                  <button type="submit" data-bs-toggle="tooltip" title="" class="btn btn-outline-danger btn-sm store-filter-btn" data-bs-original-title="Search"> <i class="fi fi-br-search"></i> </button>
                                                  
                                                  <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                                      <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                  </a>
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                      <a href="{{route('admin.orders.area.csv.export',['date_from'=>$request->date_from,'date_to'=>$request->date_to,'state'=>$request->state_id,'area'=>$request->area_id])}}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                          
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
                  
                    <table class="table table-sm admin-table big-sticky">
                        <thead>
                        <tr>
                            <th>#SR</th>
                            <th>Area</th>
                            <th>State</th>
                            <th>Qty</th>
        					
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $all_orders_total_amount = 0;
                            @endphp
        
                            @forelse ($data->all_orders as $index => $item)
                                @php
                                    $all_orders_total_amount += ($item->qty);
                                @endphp
                                <tr id="row_{{$item->id}}">
                                    <td>
                                        {{ $index + 1 }}
                                    </td>
                                    <td>
                                        <p class="small text-dark mb-1">{{$item->area ?? ''}}</p>
                                    </td>
                                    <td>
                                        <p class="small text-dark mb-1">{{$item->state ?? ''}}</p>
                                    </td>
                                    
                                    <td>
                                        <p class="text-dark mb-1">{{$item->qty}}</p>
                                    </td>
                                   
                                   
                                    
        							
                                </tr>
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
                                    <p class="small text-dark mb-1 fw-bold">{{ number_format($all_orders_total_amount) }}</p>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                  	<div class="d-flex justify-content-end">
               {{ $data->all_orders->appends($_GET)->links() }}
            </div>
              </div>
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
    $('select[name="state_id"]').on('change', (event) => {
        var value = $('select[name="state_id"]').val();

        $.ajax({
            url: '{{url("/")}}/admin/state-wise-area/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="area_id"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data.area, (key, value) => {
                    content += '<option value="'+value.area_id+'">'+value.area+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
@endsection