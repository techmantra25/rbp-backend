@extends('admin.layouts.app')

@section('page', 'Coupon Issue vs Scan Consumption Report')

{{-- Include Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@section('content')
<section>
    <div class="card card-body">
        <div class="search__filter mb-0">
            <div class="row">
                <div class="col-md-3">
                    <h5>Export Coupon Issue vs Scan Consumption Report</h5>
                </div>
                <div class="col-md-9 text-end">
                    <form action="{{ route('admin.scan.consumption.csv.export') }}" method="GET">
                        <div class="row g-3 align-items-end mb-4">
                        {{-- State Dropdown --}}
                            <div class="col-md-4">
                                <label for="date_from" class="text-muted small">Date from</label>
                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_from') ?? date('2024-10-21') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="date_to" class="text-muted small">Date to</label>
                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" aria-label="Default select example" value="{{request()->input('date_to') ?? date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted">Distributor</label>
                                <select class="form-control form-control-sm select2" name="distributor">
                                    <option value="" selected>All</option>
                                    @foreach ($distributorDetails as $index => $item)
                                    <option value="{{ $item->id }}" {{ ($request->distributor == $item->id) ? 'selected' : '' }}>
                                        {{ $item->name }} ({{ $item->employee_id }}) ({{ $item->state }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
    
                            {{-- Download Button --}}
                            <div class="col d-inline-block">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Download CSV
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download ms-1">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
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

		function typeWiseUser(value){
			$.ajax({
				url: '{{url("/")}}/type-wise-name/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[id="name"]';
					var displayCollection = (result.data.type == "all") ? "All " : "All "+" name";
					content += '<option value="" selected>'+displayCollection+'</option>';
					let type = "{{ app('request')->input('name') }}";

					$.each(result.data.name, (key, value) => {
						if(value.name == '') return;
						if (value.name == type) {
                            content += '<option value="'+value.name+'" selected>'+value.name+'</option>';
                        } else {
                            content += '<option value="'+value.name+'">'+value.name+'</option>';
                        }
						// content += '<option value="'+value.name+'">'+value.name+'</option>';
					});
					$(slectTag).html(content).attr('disabled', false);
                }
			});
		}

		$('select[id="type"]').on('change', (event) => {
			var value = $('select[id="type"]').val();
			typeWiseUser(value);
		});
		
		@if(request()->input('user_type'))
		typeWiseUser("{{ request()->input('user_type') }}");
		@endif
   </script>
@endsection
