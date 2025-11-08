
@extends('admin.layouts.app')

@section('page', 'Primary Order')
@section('content')

<style>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
    }

    .loading-spinner {
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 4px solid #ffffff;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<section class="store-sec ">
    @if (request()->input('store'))
        <p class="text-muted">{{request()->input('namee')}}</p>
    @endif
  <div class="row">
      <div class="col-xl-12 order-2 order-xl-1">
          <div class="card search-card">
              <div class="card-body">
                  <div class="search__filter mb-5">
                      <div class="row align-items-center justify-content-between">
                          <div class="col-md-12 mb-3">
                              <p class="small text-muted mb-0">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} Orders</p>
                          </div>
                          <div class="col-md-12 mb-3">
                              <div class="search-filter-right">
                                  <div class="search-filter-right-el">
                                      <form class="row align-items-end justify-content-end" action="" method="GET">
                                          <div class="search-filter-right">
                                              <div class="search-filter-right-el">
                                                  <label for="date_from" class="text-muted small">Date from</label>
                                                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{ (request()->input('date_from')) ? request()->input('date_from') : '' }}">
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                                  <label for="date_to" class="text-muted small">Date to</label>
                                                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{ (request()->input('date_to')) ? request()->input('date_to') : '' }}">
                                              </div>
                                              
                                              
                                              <div class="search-filter-right-el">
                                                  <label for="ase" class="small text-muted">Distributor</label>
                                                    <select class="form-select form-select-sm select2" id="dis" name="distributor_id">
                                                        <option value="" selected disabled>Select</option>
                                                        @foreach ($distributor as $item)
                                                            <option value="{{$item->id}}" {{ (request()->input('distributor_id') == $item->id) ? 'selected' : '' }}>{{$item->name}} ({{$item->employee_id}}) ({{$item->state}})</option>
                                                        @endforeach
                                                    </select>
                                              </div>
                                              
                                              
                                          </div>
                                          <div class="search-filter-right search-filter-right-store mt-3">
                                              
                                              <div class="search-filter-right-el">
                                                  
                                                <input type="search" name="term" class="form-control form-control-sm" placeholder="Search order no/store contact/store name" id="term" value="{{app('request')->input('term')}}" autocomplete="off">
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
                                                 
                                              </div>
                                          </div>
                                           
                                      </form>
                                  </div>
                                  
                                  
                                  
                                 
                              </div>
                          </div>
                          
            
                      </div>
                  </div>
                  
                  <div class="table-responsive">
                      <table class="table table-sm admin-table no-sticky" id="example5">
        <thead>
        <tr>
            <th>#SR</th>
            <th>Order No</th>
           
            <th>Distributor</th>
             
            
			
			
		
		    <th>Qty(in Box)</th>
            <th>Order Type</th>
            <th>Order time</th>
            <th>Note</th>
        </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $item)
                
			    @php
                  $validOrder=DB::table('order_products')->where('order_id',$item->id)->get();
                  $user=DB::table('teams')->where('store_id',$item->store_id)->first();
                  if(!empty($user)){
                  $userName=DB::table('users')->where('id',$user->distributor_id)->first();
                  }
                @endphp
            <tr>
                <td>
                   {{($data->firstItem()) + $index}}
                </td>
                <td>
                    <p class="small text-dark mb-1">{{$item->order_no}}</p>
                   
                </td>
                
               
                
               
                
                <td>
                    <p class="small text-muted mb-1"> {{ $item->distributor_name }}</p>
                </td>
                
			
			      <td>
                    <p class="small text-muted mb-1"> {{ $item->qty }}</p>
                </td>
			
			
                <td>
                    <p class="small text-muted mb-1"> {{$item->order_type}}</p>
                </td>
                <td>
                    <p class="small">{{date('j M Y g:i A', strtotime($item->created_at))}}</p>
                </td>
                <td>
                    <p class="small text-muted mb-1"> {{$item->comment}}</p>
                </td>
                
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
              </div>
          </div>
      </div>
  </div>
</section>


<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>


@endsection

@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2();
    });

    document.getElementById('redirectButton').addEventListener('click', function() {
        // Show loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';

        // Simulate a delay with setTimeout
        setTimeout(function() {
            // Redirect to desired page
            window.location.href = '{{ route('admin.orders.csv.export',['date_from'=>$request->date_from,'date_to'=>$request->date_to,'user_id'=>$request->user_id,'store_id'=>$request->store_id,'distributor_id'=>$request->distributor_id,'state_id'=>$request->state_id,'term'=>$request->term]) }}'; // Change this URL to your desired page
        }, 2000); // Change 2000 to the delay time in milliseconds

        setTimeout(() => {
            document.getElementById('loadingOverlay').style.display = 'none';
        }, 3000);
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
