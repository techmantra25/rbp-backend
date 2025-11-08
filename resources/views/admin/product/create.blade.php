@extends('admin.layouts.app')

@section('page', 'Create Product')

@section('content')
<section>
    <form method="post" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">@csrf
        <div class="row">
        <div class="col-sm-9">

                <div class="row mb-3">
                    <div class="col-sm-4">
						<label for="" class="col-form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm select2" aria-label="Default select example" name="cat_id" id="category">
                            <option value=""  selected>Select Category</option>
                            @foreach ($category as $index => $item)
                                        <option value="{{$item->id}}" {{ (request()->input('cat_id') == $item->id) ? 'selected' :  '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('cat_id') <p class="small text-danger">{{ $message }}</p> @enderror
                    </div>
           
                    <div class="col-sm-4">
						<label for="" class="col-form-label">Collection <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm select2" aria-label="Default select example" name="collection_id" id="collection">
                            <option value="" selected disabled>Select Collection</option>
                            <option value="{{ (request()->input('collection_id')) ? 'selected' :  '' }}"></option>
                        </select>
                        @error('collection_id') <p class="small text-danger">{{ $message }}</p> @enderror
                    </div>
                </div>

            <div class="form-group mb-3">
				<label for="" class="col-form-label">Title/Name <span class="text-danger">*</span></label>
                <input type="text" name="name" placeholder="Add Product Title" class="form-control" value="{{old('name')}}">
                @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    Short Description
                </div>
                <div class="card-body">
                    <textarea id="product_short_des" name="short_desc">{{old('short_desc')}}</textarea>
                    @error('short_desc') <p class="small text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    Description
                </div>
                <div class="card-body">
                    <textarea id="product_des" name="desc">{{old('desc')}}</textarea>
                    @error('desc') <p class="small text-danger">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    Product data
                </div>
                <div class="card-body pt-0">
                    <div class="admin__content">
                    <aside>
                        <nav>Price</nav>
                    </aside>
                    <content>
                        <div class="row mb-2 align-items-center">
                        <div class="col-3">
                            <label for="inputPassword6" class="col-form-label">Regular Price</label>
                        </div>
                        <div class="col-auto">
                            <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="price" value="{{old('price')}}">
                            @error('price') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="col-auto">
                            <span id="priceHelpInline" class="form-text">
                            Must be 8-20 characters long.
                            </span>
                        </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                        <div class="col-3">
                            <label for="inputprice6" class="col-form-label">Offer Price</label>
                        </div>
                        <div class="col-auto">
                            <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="offer_price" value="{{old('offer_price')}}">
                            @error('offer_price') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div class="col-auto">
                            <span id="passwordHelpInline" class="form-text">
                            Must be 8-20 characters long.
                            </span>
                        </div>
                        </div>
                    </content>
                    </div>
                    <div class="admin__content">
                        <aside>
                            <nav>Meta</nav>
                        </aside>
                        <content>
                            <div class="row mb-2 align-items-center">
                                <div class="col-3">
                                    <label for="inputPassword6" class="col-form-label">Title</label>
                                </div>
                                <div class="col-auto">
                                    <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="meta_title" value="{{old('meta_title')}}">
                                    @error('meta_title') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                                <div class="col-auto">
                                    <span id="priceHelpInline" class="form-text">
                                    Must be 8-20 characters long.
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-2 align-items-center">
                                <div class="col-3">
                                    <label for="inputprice6" class="col-form-label">Description</label>
                                </div>
                                <div class="col-auto">
                                    <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="meta_desc" value="{{old('meta_desc')}}">
                                    @error('meta_desc') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                                <div class="col-auto">
                                    <span id="passwordHelpInline" class="form-text">
                                    Must be 8-20 characters long.
                                    </span>
                                </div>
                            </div>
                            <div class="row mb-2 align-items-center">
                                <div class="col-3">
                                    <label for="inputprice6" class="col-form-label">Keyword</label>
                                </div>
                                <div class="col-auto">
                                    <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="meta_keyword" value="{{old('meta_keyword')}}">
                                    @error('meta_keyword') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                                <div class="col-auto">
                                    <span id="passwordHelpInline" class="form-text">
                                    Must be 8-20 characters long.
                                    </span>
                                </div>
                            </div>
                        </content>
                    </div>
                    <div class="admin__content">
                        <aside>
                            <nav>Data</nav>
                        </aside>
                        <content>
                            <div class="row mb-2 align-items-center">
                            <div class="col-3">
                                <label for="inputPassword6" class="col-form-label">Style No</label>
                            </div>
                            <div class="col-auto">
                                <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="style_no" value="{{old('style_no')}}">
                                @error('style_no') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div class="col-auto">
                                <span id="priceHelpInline" class="form-text">
                                Must be 8-20 characters long.
                                </span>
                            </div>
                            </div>
                        </content>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm" id="timePriceTable">
                                <thead>
                                    <tr>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-control select2" name="color_id[]">
                                                <option value="" disabled hidden selected>Select...</option>
                                                @foreach($colors as $colorIndex => $colorValue)
                                                    <option value="{{$colorValue->id}}" @if (old('color') && in_array($colorValue,old('color'))){{('selected')}}@endif>{{$colorValue->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control select2" name="size_id[]">
                                                <option value="" disabled hidden selected>Select...</option>
                                                @foreach($sizes as $sizeIndex => $sizeValue)
                                                    <option value="{{$sizeValue->id}}">{{$sizeValue->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><a class="btn btn-sm btn-success actionTimebtn addNewTime">+</a></td>
                                    </tr>
                                </tbody>
                            </table>
                            @error('color')<p class="text-danger">{{$message}}</p>@enderror
                            @error('size')<p class="text-danger">{{$message}}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-sm-3">
			<div class="card shadow-sm">
                <div class="card-header">
                    Product Main Image <span class="text-danger">*</span>
                </div>
                <div class="card-body">
                    <div class="w-100 product__thumb">
                    <label for="thumbnail"><img id="output" src="{{ asset('admin/images/placeholder-image.jpg') }}"/></label>
                    @error('image') <p class="small text-danger">{{ $message }}</p> @enderror
                    </div>
                    <input type="file" id="thumbnail" accept="image/*" name="image" onchange="loadFile(event)" class="d-none">
                    <script>
                    var loadFile = function(event) {
                        var output = document.getElementById('output');
                        output.src = URL.createObjectURL(event.target.files[0]);
                        output.onload = function() {
                        URL.revokeObjectURL(output.src) // free memory
                        }
                    };
                    </script>
                </div>
            </div>
            <div class="card shadow-sm">
            <div class="card-header">
                Publish
            </div>
            <div class="card-body text-end">
                <button type="submit" class="btn btn-sm btn-danger">Publish </button>
            </div>
            </div>
            
           
        </div>
        </div>
    </form>
</section>
@endsection

@section('script')
<script>
    ClassicEditor
    .create( document.querySelector( '#product_des' ) )
    .catch( error => {
        console.error( error );
    });
    ClassicEditor
    .create( document.querySelector( '#product_short_des' ) )
    .catch( error => {
        console.error( error );
    });

    $(document).on('click','.addNewTime',function(){
		var thisClickedBtn = $(this);
		thisClickedBtn.removeClass(['addNewTime','btn-success']);
		thisClickedBtn.addClass(['removeTimePrice','btn-danger']).text('X');

		var toAppend = `
        <tr>
            <td>
                <select class="form-control select2" name="color_id[]">
                    <option value="" hidden selected>Select...</option>
                    @foreach($colors as $colorIndex => $colorValue)
                        <option value="{{$colorValue->id}}" @if (old('color') && in_array($colorValue,old('color'))){{('selected')}}@endif>{{$colorValue->name}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select class="form-control select2" name="size_id[]">
                    <option value="" hidden selected>Select...</option>
                    @foreach($sizes as $sizeIndex => $sizeValue)
                        <option value="{{$sizeValue->id}}">{{$sizeValue->name}}</option>
                    @endforeach
                </select>
            </td>
            <td><a class="btn btn-sm btn-success actionTimebtn addNewTime">+</a></td>
        </tr>
        `;

		$('#timePriceTable').append(toAppend);
	});

	$(document).on('click','.removeTimePrice',function(){
		var thisClickedBtn = $(this);
		thisClickedBtn.closest('tr').remove();
	});
</script>
    <script>
		$('select[id="collection"]').on('change', (event) => {
			var value = $('select[id="collection"]').val();

			$.ajax({
				url: '{{url("/")}}/api/category/product/collection/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[id="category"]';
					var displayCollection = (result.data.collection_name == "all") ? "All category" : "All "+result.data.collection_name+" categories";

					content += '<option value="" selected>'+displayCollection+'</option>';
					$.each(result.data.category, (key, value) => {
						content += '<option value="'+value.cat_id+'">'+value.cat_name+'</option>';
					});
					$(slectTag).html(content).attr('disabled', false);
                }
			});
		});
    </script>
    <script>
		$('select[id="category"]').on('change', (event) => {
			var value = $('select[id="category"]').val();

			$.ajax({
				url: '{{url("/")}}/api/collection/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[id="collection"]';
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
     <script>
		$('select[id="color"]').on('change', (event) => {
			var value = $('select[id="color"]').val();

			$.ajax({
				url: '{{url("/")}}/api/size/list/'+value,
                method: 'GET',
                success: function(result) {
					var content = '';
					var slectTag = 'select[id="size"]';
					var displayCollection =  "All";

					content += '<option value="" selected>'+displayCollection+'</option>';
					$.each(result.data.primarySizes.size, (key, value) => {
						content += '<option value="'+value.id+'">'+value.name+'</option>';
					});
					$(slectTag).html(content).attr('disabled', false);
                }
			});
		});
    </script>
@endsection
