@extends('layouts.app')

@section('page', 'Area wise sales report')

@section('content')

<section class="store-sec ">
          <div class="card search-card">
              <div class="card-body">
                      <div class="row">
                          <div class="col-12 mb-3">
                            <div class="date-formatter">
                                <form action="" method="get" class="row">
                                    <div class="row">
                                        <div class="col-12 col-md-3 mb-3">
                                            <label for="date_from" class="text-muted small">Date from</label>
                                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from') ?? date('Y-m-01') }}">
                                        </div>
                                              
                                        <div class="col-12 col-md-3 mb-3">
                                            <label for="date_to" class="text-muted small">Date to</label>
                                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to') ?? date('Y-m-d') }}">
                                        </div>
                                              
                                              
                                        <div class="col-12 col-md-3 mb-3">
                                            <label for="state" class="text-muted small">State</label>
                                            <select name="state_id" id="state" class="form-control select2">
                                                <option value="" disabled>Select</option>
                                                <option value="" selected>All</option>
                                                @foreach ($state as $row)
                                                    <option value="{{$row->states->id}}" {{ request()->input('state_id') == $row->states->id ? 'selected' : '' }}>{{$row->states->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-3 mb-3">
                                            <label class="small text-muted">Area</label>
                                            <select class="form-control select2" name="area_id" disabled>
                                                <option value="{{ $request->area_id }}">Select state first</option>
                                            </select>
                                              </div>
                                        </div>
                                        <div class="report-filter filter-btns">
                                            <div class="filter-btn-left">
                                                <button type="submit" data-bs-toggle="tooltip" title="" class="btn btn-outline-danger btn-sm store-filter-btn filter-btn search-filter" data-bs-original-title="Search"> Filter </button>
                                            
                                                <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times filter-btn" data-bs-toggle="tooltip" title="Clear Filter">
                                                    <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                </a>
                                            </div>
                                              
                                           
                                             <a href="{{route('front.zone.order.csv.download',['date_from'=>$request->date_from,'date_to'=>$request->date_to,'state'=>$request->state_id,'area'=>$request->area_id])}}" class="btn btn-outline-danger btn-sm filter-btn filter-csv" data-bs-toggle="tooltip" title="Export data in CSV">
                                          
                                                     <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                            </a>
                                            
                                        </div>
                                           
                                      </form>
                                  </div>
                                  
                                  
                                  
                                 
                              </div>
                          </div>
                          
            
                      </div>
                  </div>
            <div class="card card-search mt-4">
                  <div class="table-responsive">
                      <table class="table table-striped table-hovered">
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
                  </div>

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