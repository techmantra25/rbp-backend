@extends('admin.layouts.app')
@section('page', 'Order Dump')
@section('content')

<section class="store-sec ">
  <div class="row">
      <div class="col-xl-12 order-2 order-xl-1">
          <div class="card search-card">
              <div class="card-body">
                  <div class="search__filter mb-5">
                      <div class="row align-items-center justify-content-between">
                          <div class="col-md-12 mb-3">
                              
                          </div>
                          <div class="col-md-12 mb-3">
                              <div class="search-filter-right">
                                  <div class="search-filter-right-el">
                                      <form class="row align-items-end justify-content-end" action="" method="GET">
                                          <div class="search-filter-right">
                                              <div class="search-filter-right-el">
                                                  <label for="date_from" class="text-muted small">Date from</label>
                                                  <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from') ?? date('Y-m-01') }}">
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                                  <label for="date_to" class="text-muted small">Date to</label>
                                                  <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to') ?? date('Y-m-d') }}">
                                              </div>
                                             <div class="search-filter-right-el">
                                                    <label for="zsm" class="text-muted small">ZSM</label>
                                                    <select name="zsm" id="zsm" class="form-control form-control-sm select2">
                                                        <option value="" selected>Select</option>
                                                       <option value="all"  {{ request()->input('zsm') == 'all' ? 'selected' : '' }}>All</option>
                                                        @foreach ($zsm as $item)
                                                         
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
                                                    <label class="small text-muted">ASE</label>
                                                    <select class="form-control form-control-sm select2" name="ase" >
                                                        <option value="" selected>Select</option>
                                                       
                                                        <option value="{{ $request->ase_id }}">Select ASM first</option>
                                                    </select>
                                                </div>
                                          <div class="search-filter-right search-filter-right-store mt-2">
                                              
                                              <!--<div class="search-filter-right-el">-->
                                              <!--    <input type="search" name="keyword" id="term" class="form-control form-control-sm" placeholder="Search by user name" value="{{app('request')->input('keyword')}}" autocomplete="off">-->
                                              <!--</div>-->
                                              <div class="search-filter-right-el">
                                                  <button type="submit" class="btn btn-outline-danger btn-sm store-filter-btn">
                                                      Filter
                                                  </button>
                                                  <a href="{{ url()->current() }}" class="btn btn-sm btn-light clear-filter store-filter-times" data-bs-toggle="tooltip" title="Clear Filter">
                                                      <iconify-icon icon="basil:cross-outline"></iconify-icon>
                                                  </a>
                                              </div>
                                              
                                              <div class="search-filter-right-el">
                                      <a href="{{ route('admin.orders.dump.csv.export',['date_from'=>$request->date_from,'date_to'=>$request->date_to,'zsm'=>$request->zsm,'state' => $request->state,'rsm'=>$request->rsm,'sm'=>$request->sm,'asm'=>$request->asm,'ase'=>$request->ase]) }}" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Export data in CSV">
                                          
                                          <iconify-icon icon="material-symbols:download"></iconify-icon> CSV
                                      </a>
                                  </div>
                                  <div class="search-filter-right-el">
                                                      <input type="checkbox" id="vehicle1" name="checkbox" value="checkbox"checked>
                                                            <label for="vehicle1"> Show only those columns which have data.</label><br>  
                                                </div> 
                                          </div>
                                           
                                      </form>
                                  </div>
                                  
                                  
                                  
                                 
                              </div>
                          </div>
                          
            
                      </div>
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
    
                    content += '<option value="all">'+displayCollection+'</option>';
                    
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
    
                    content += '<option value="all" >'+displayCollection+'</option>';
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
    
                    content += '<option value="all" >'+displayCollection+'</option>';
                    $.each(result.data, (key, value) => {
                        content += '<option value="'+value.ase.id+'">'+value.ase.name+'</option>';
                    });
                    $(slectTag).html(content).attr('disabled', false);
                }
            });
        });
        */
    </script>

@endsection
