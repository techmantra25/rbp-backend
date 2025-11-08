@extends('admin.layouts.app')
@section('page', 'Notification')

@section('content')

<section class="store-sec ">
  <div class="row">
      <div class="col-xl-12 order-2 order-xl-1">
          <div class="card search-card">
              <div class="card-body">
                  <div class="search__filter mb-5">
                      <div class="row align-items-center justify-content-between">
                          <div class="col-3">
                              <p class="small text-muted mt-1 mb-0">Showing {{$data->firstItem()}} - {{$data->lastItem()}} out of {{$data->total()}} entries</p> 
                          </div>
                          <div class="col-9">
                              <div class="search-filter-right search-filter-right-store notification-filter">
                                  <div class="search-filter-right-el">
                                      <form class="row align-items-end justify-content-end" action="" method="GET">
                                          <div class="search-filter-right">
                                              <div class="search-filter-right-el">
                                                  <label for="dateFrom" class="small text-muted">Date from</label>
                                                    <input type="date" name="from" id="dateFrom" class="form-control form-control-sm" value="{{ (request()->input('from')) ? request()->input('from') : '' }}">
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                                  <label for="dateTo" class="small text-muted">Date to</label>
                            <input type="date" name="to" id="dateTo" class="form-control form-control-sm" value="{{ (request()->input('to')) ? request()->input('to') : '' }}">
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                                <input type="search" name="keyword" id="keyword" class="form-control form-control-sm" placeholder="Search by keyword" value="{{app('request')->input('keyword')}}" autocomplete="off">
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                                  <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">
                                                      Filter
                                                  </button>
                                                  <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                                      <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                  </a>
                                              </div>
                                          </div>
                                          <div class="search-filter-right search-filter-right-store mt-2">
                                              
                                              
                                              
                                              
                                  <!--            <div class="search-filter-right-el">-->
                                  <!--    <a href="{{ route('admin.users.activity.csv.export') }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">-->
                                          
                                  <!--        <iconify-icon icon="material-symbols:download"></iconify-icon> CSV-->
                                  <!--    </a>-->
                                  <!--</div>-->
                                          </div>
                                           
                                      </form>
                                  </div>
                                  
                                  
                                  
                                 
                              </div>
                          </div>
                          
            
                      </div>
                  </div>
                  
            <table class="table table-sm admin-table" id="example5">
                <thead>
                    <tr>
                        <th>#SR</th>
        				<th>Type</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Title</th>
                        <th>Body</th>
                        <th>Created At</th>
        				 <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $index => $item)
                    @php
                        if (!empty($_GET['read_flag'])) {
                            if ($_GET['read_flag'] == 'read') {
                                if ($item->read_flag == 0) continue;
                            } else {
                                if ($item->read_flag == 1) continue;
                            }
                        }
                    @endphp
                        <tr>
                        <td>{{($data->firstItem()) + $index}}</td>
                            <td>
                                @if($item->type == "secondary-order-place")
                                    <span class="badge bg-success">Secondary Order Place</span>
                                @elseif($item->type == "primary-order-place")
                                <span class="badge bg-danger">Primary Order Place</span>
                                @elseif($item->type == "store-add")
                                    <span class="badge bg-primary">New Store Create</span>
                                @endif
                            </td>
                            <td>
                                @if($item->sender=='admin')
                                    <p class="mb-0 text-danger small"><span class="text-danger">System generated</p>
                                @else
                                    <p class="mb-0 text-muted small">{{$item->senderDetails->name ?? ''}}</p>
                                @endif
                            </td>
                            <td>
                                <p class="mb-0 text-muted small">admin</p>
                            </td>
                            <td>
                                <p class="mb-0 text-muted small">{{$item->title}}</p>
                            </td>
                            <td>
                                <p class="mb-0 text-muted small">{{$item->body}}</p>
                            </td>
                            <td>
                                <p class="mb-0 text-muted small">{{ date('j F, Y h:i A', strtotime($item->created_at)) }}</p>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge bg-{{($item->read_flag == 1) ? 'success' : 'danger'}}">{{($item->read_flag == 1) ? 'Read' : 'Unread'}}</span>
                            </td>
                           
                        </tr>
                    @empty
                        <tr><td colspan="100%" class="small text-muted text-center">No data found</td></tr>
                    @endforelse
                </tbody>
            </table>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

<script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <script>
    </script>
@endsection
