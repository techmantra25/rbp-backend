@extends('layouts.app')

@section('page', 'Store Wise Sales Count')

@section('content')
<div class="col-sm-12">
    <div class="profile-card">
        <h3 class="mb-0">Store Wise Sales Count</h3>
        <section class="store_listing">
            <div class="row">
                <div class="col-12">
                    <div class="date-formatter">
                        <form action="" method="get" class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dateFrom"><h5 class="small text-muted mb-0">Date from</h5></label>
                                    <input type="date" name="from" id="dateFrom" class="form-control form-control-sm" value="{{ (request()->input('from')) ? request()->input('from') : date('Y-m-01') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dateTo"><h5 class="small text-muted mb-0">Date to</h5></label>
                                    <input type="date" name="to" id="dateTo" class="form-control form-control-sm" value="{{ (request()->input('to')) ? request()->input('to') : date('Y-m-d') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category"><h5 class="small text-muted mb-0">Category</h5></label>
                                    <select class="form-control form-control-sm" name="category">
                                        <option value="" disabled>Select</option>
                                        <option value="all" selected>All</option>
                                        @foreach ($category as $index => $item)
                                            <option value="{{ $item->id }}" {{ ($request->category == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="collection"><h5 class="small text-muted mb-0">Collection <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm select2" aria-label="Default select example" name="collection" id="collection" disabled>
                                    <option value="" selected disabled>Select Collection</option>
                                    <option value="{{ (request()->input('collection')) ? 'selected' :  '' }}"></option>
                                </select>
                                @error('collection') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="col-md-3"></div>
                            <div class="col-md-3 text-right">
                                <div class="form-group pt-4">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="submit" class="btn btn-sm btn-danger">Apply</button>

                                        <a type="button" href="{{ url()->current() }}" class="btn btn-sm btn-light border" data-toggle="tooltip" data-placement="top" title="Remove filter">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <hr>

                            <div class="row">
                                <div class="col-12">
                                    @if (request()->input('from') || request()->input('to'))
                                        <p class="text-dark">Store wise report from <strong>{{ date('j F, Y', strtotime(request()->input('from'))) }}</strong> - <strong>{{ date('j F, Y', strtotime(request()->input('to'))) }}</strong></p>
                                    @else
                                        <p class="text-dark">Store wise report of <strong>{{ date('1 F, Y') }}</strong> - <strong>{{ date('j F, Y') }}</strong></p>
                                    @endif
                                </div>

                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Store Name</th>
                                            <th>Created By</th>
                                            <th>Sales Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($store as $item)
                                        @php
                                        if ( request()->input('from') || request()->input('to') ) {
                                            // date from
                                            if (!empty(request()->input('from'))) {
                                                $from = request()->input('from');
                                            } else {
                                                $from = date('Y-m-01');
                                            }

                                            // date to
                                            if (!empty(request()->input('to'))) {
                                                $to = date('Y-m-d', strtotime(request()->input('to')));
                                            } else {
                                                $to = date('Y-m-d', strtotime('+1 day'));
                                            }


                                            // collection
                                            if (!isset($request->collection) || $request->collection == '10000') {
                                                $collectionQuery = "";
                                            } else {
                                                $collectionQuery = " AND p.collection_id = ".$request->collection;
                                            }

                                            // category
                                            if (!isset($request->category) || $request->category == 'all') {
                                                $categoryQuery = "";
                                            } else {
                                                $categoryQuery = " AND p.cat_id = ".$request->category;
                                            }


                                            $report = DB::select("SELECT SUM(op.qty) AS qty FROM `orders` AS o
										   
                                            INNER JOIN order_products AS op ON op.order_id = o.id
                                            INNER JOIN products p ON p.id = op.product_id
										    INNER JOIN stores s ON FIND_IN_SET(s.id,o.store_id)
                                            WHERE o.store_id = '".$item->id."'
                                            ".$collectionQuery."
                                            ".$categoryQuery."
                                            AND (date(o.created_at) BETWEEN '".$from."' AND '".$to."')
                                          ");
                                            
                                        } else {
                                            
                                            $report = DB::select("SELECT SUM(op.qty) AS qty FROM `orders` AS o  INNER JOIN order_products AS op ON op.order_id = o.id INNER JOIN stores s ON FIND_IN_SET(s.id,o.store_id) WHERE o.store_id = '".$item->id."' AND (date(o.created_at) BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-d', strtotime('+1 day'))."')");
                                        }
                                        @endphp
                                            <tr>
                                                <td>
                                                    <a href="">
                                                        {{ ($item->name == null) ? 'NA' : $item->name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ ($item->users->name == null) ? 'NA' : $item->users->name }}
                                                </td>
                                                <td>
                                                    <p class="qty">
                                                        {{ ($report[0]->qty == null) ? 0 : $report[0]->qty }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('script')
    <script>
		$('select[name="collection"]').on('change', (event) => {
			var value = $('select[name="collection"]').val();

			$.ajax({
				url: '{{url("/")}}/api/category/product/collection/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[name="style_no"]';
					var displayCollection = (result.data.collection_name == "all") ? "All products" : "All "+result.data.collection_name+" products";

					content += '<option value="" selected>'+displayCollection+'</option>';
					$.each(result.data.product, (key, value) => {
						content += '<option value="'+value.product_style_no+'">'+value.product_style_no+'</option>';
					});
					$(slectTag).html(content).attr('disabled', false);
                }
			});
		});
    </script>
     <script>
		$('select[name="category"]').on('change', (event) => {
			var value = $('select[name="category"]').val();

			$.ajax({
				url: '{{url("/")}}/api/collection/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[name="collection"]';
					var displayCollection =  "All";

					content += '<option value="" selected>'+displayCollection+'</option>';
					$.each(result.data, (key, value) => {
						content += '<option value="'+value.id+'">'+value.name+'</option>';
					});
					$(slectTag).html(content).attr('disabled', false);
                }
			});
		});
    </script>
@endsection

