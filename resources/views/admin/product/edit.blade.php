@extends('admin.layouts.app')

@section('page', 'Edit Product')

@section('content')

<style>
    .label-control {
        color: #525252;
        font-size: 12px;
    }
    .color_holder {
        display: flex;
        border: 1px dashed #ddd;
        border-radius: 6px;
        padding: 5px;
        background: #f0f0f0;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }
    .color_holder_single {
        margin: 5px;
    }
    .color_box {
        display: flex;
        padding: 6px 10px;
        border-radius: 3px;
        align-items: center;
        margin: 0;
        background: #fff;
    }
    .color_box p {
        margin: 0;
        margin-left: 10px;
    }
    .color_box span, .color_box img {
        display: inline-block;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        /* margin-right: 10px; */
    }
    .sizeUpload {
        margin-bottom: 10px;
    }
    .size_holder {
        padding: 10px 0;
        border-top: 1px solid #ddd;
    }
    .img_thumb img {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        object-fit: cover;
    }
    .remove_image {
        display: inline-flex;
        width: 30px;
        height: 30px;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
        position: absolute;
        top: 0;
        right: 0;
    }
    .remove_image i {
        line-height: 13px;
    }
    .image_upload {
        display: inline-flex;
        padding: 0 20px;
        border:  1px solid #ccc;
        background: #ddd;
        padding: 5px 12px;
        border-radius: 3px;
        vertical-align: top;
        cursor: pointer;
    }
    .status-toggle {
        padding: 6px 10px;
        border-radius: 3px;
        align-items: center;
        background: #fff;
    }
    .status-toggle a {
        text-decoration: none;
        color: #000
    }
    .color_holder {
        display: flex;
        border: 1px dashed #ddd;
        border-radius: 6px;
        padding: 5px;
        background: #f0f0f0;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }
    .color_holder_single {
        margin: 5px;
    }
    .color_box {
        display: flex;
        padding: 6px 10px;
        border-radius: 3px;
        align-items: center;
        margin: 0;
        background: #fff;
    }
    .sizeUpload {
        margin-bottom: 10px;
    }
    .img_thumb {
        width: 100%;
        padding-bottom: calc((4/3)*100%);
        position: relative;
        border:  1px solid #ccc;
        max-width: 80px;
        min-width: 80px;
    }
    .img_thumb img {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        object-fit: cover;
    }
    .remove_image {
        display: inline-flex;
        width: 30px;
        height: 30px;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
        position: absolute;
        top: 0;
        right: 0;
    }
    .remove_image i {
        line-height: 13px;
    }
    .image_upload {
        display: inline-flex;
        padding: 0 20px;
        border:  1px solid #ccc;
        background: #ddd;
        padding: 5px 12px;
        border-radius: 3px;
        vertical-align: top;
        cursor: pointer;
    }
    .status-toggle {
        padding: 6px 10px;
        border-radius: 3px;
        align-items: center;
        background: #fff;
    }
    .status-toggle a {
        text-decoration: none;
        color: #000
    }
    .color-fabric-image-holder {
        width: 36px;
        height: 36px;
    }
    .color-fabric-image {
        width: inherit;
        height: inherit;
        border-radius: 50%;
    }
    .change-image {
        position: absolute;
        bottom: -4px;
        right: -8px;
        background: #c1080a;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        padding: 0 0;
    }
    .change-image .badge {
        padding: 3px;
        cursor: pointer;
    }
    .croppie-container {
        height: auto;
    }
</style>

<section class="pro-edit">
    <form method="POST" action="{{ route('admin.products.update',$data->id) }}" enctype="multipart/form-data">
         @csrf
        @method('PUT')
        <div class="row">
            <div class="col-sm-9">

                @if (Session::has('message'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>{{ Session::get('message') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <label class="label-control">Category <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm select2" aria-label="Default select example" name="cat_id" id="category">
                                    <option value=""  selected>Select Category</option>
                                    @foreach ($category as $index => $item)
                                        <option value="{{$item->id}}" {{ ($data->cat_id == $item->id) ? 'selected' :  '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('cat_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-sm-6">
                                <label class="label-control">Range <span class="text-danger">*</span> ({{$data->collection->name}})</label>
                                <select class="form-select form-select-sm select2" aria-label="Default select example" name="collection_id" id="collection">
                                    <option value="" selected disabled>Select Collection</option>
                                    <option value="{{ (request()->input('collection_id')) ? 'selected' :  '' }}"></option>
                                </select>
                                @error('collection_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="label-control">Product Title <span class="text-danger">*</span></label>
                            <input type="text" name="name" placeholder="Add Product Title" class="form-control" value="{{$data->name}}">
                            @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <label class="label-control">Short Description </label>
                        <textarea id="product_short_des" name="short_desc">{{$data->short_desc}}</textarea>
                        @error('short_desc') <p class="small text-danger">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <label class="label-control">Description </label>
                        <textarea id="product_des" name="desc">{{$data->desc}}</textarea>
                        @error('desc') <p class="small text-danger">{{ $message }}</p> @enderror
                    </div>
                </div> 

                <div class="card shadow-sm">
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
                            <div class="col-9">
                                <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="price" value="{{$data->price}}">
                                @error('price') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                            </div>
                            <div class="row mb-2 align-items-center">
                            <div class="col-3">
                                <label for="inputprice6" class="col-form-label">Offer Price</label>
                            </div>
                            <div class="col-9">
                                <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="offer_price" value="{{$data->offer_price}}">
                                @error('offer_price') <p class="small text-danger">{{ $message }}</p> @enderror
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
                                    <div class="col-9">
                                        <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="meta_title" value="{{$data->meta_title}}">
                                        @error('meta_title') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="row mb-2 align-items-center">
                                    <div class="col-3">
                                        <label for="inputprice6" class="col-form-label">Description</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="meta_desc" value="{{$data->meta_desc}}">
                                        @error('meta_desc') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="row mb-2 align-items-center">
                                    <div class="col-3">
                                        <label for="inputprice6" class="col-form-label">Keyword</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="meta_keyword" value="{{$data->meta_keyword}}">
                                        @error('meta_keyword') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </content>
                        </div>
                        <div class="admin__content">
                            <aside>
                                <nav>Data </nav>
                            </aside>
                            <content>
                                <div class="row mb-2 align-items-center">
                                <div class="col-3">
                                    <label for="inputPassword6" class="col-form-label">Style No</label>
                                </div>
                                <div class="col-9">
                                    <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="style_no" value="{{$data->style_no}}">
                                    @error('style_no') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                                </div>
                            </content>
                        </div>
                        <div class="admin__content">
                            <aside>
                                <nav>Pack </nav>
                            </aside>
                            <content>
                                <div class="row mb-2 align-items-center">
                                    <div class="col-3">
                                        <label for="inputPassword6" class="col-form-label">Net Qty</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="text" id="inputprice6" class="form-control" aria-describedby="priceHelpInline" name="pack" value="{{ old('pack') ? old('pack') : $data->pack }}">
                                        @error('pack') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </content>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card shadow-sm">
                    <div class="card-header">
                        Product Image<span class="text-danger">*</span>
                    </div>
                    <div class="card-body">
                        <div class="w-100 product__thumb">
                            @if(!empty($data->image))
                            <label for="thumbnail"><img id="output" src="{{ asset($data->image) }}"/></label>
                            @else
                             <label for="thumbnail"><img id="output" src="{{asset('admin/images/default-placeholder-product-image.png')}}" />
                            @endif
                            @error('image') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <input type="file" id="thumbnail" accept="image/*" name="image" onchange="loadFile(event)" class="d-none">
                        <small>Image Size: 870px X 1160px</small>
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
				
                <div class="card shadow-sm" style="position: sticky;top: 60px;">
                    <div class="card-body text-end">
                        <input type="hidden" name="product_id" value="{{$data->id}}">
                        <button type="submit" class="btn btn-danger w-100">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>




    <div class="card shadow-sm" id="singleProductVariation">
        <div class="card-header">
            <div class="row justify-content-between">
                <div class="col-6">
                    <h3>Product variation</h3>
                    <p class="small text-muted m-0">Add color | size | multiple images from here</p>
                </div>
               {{-- <div class="col-6 text-end">
                    <a href="#csvUploadModal" data-bs-toggle="modal" class="btn btn-danger mt-2">Bulk upload</a>
                </div>--}}
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="admin__content">
                <aside>
                    <nav>Available colors</nav>
                    <p class="small text-muted">Drag & drop colors to set position</p>
                    <p class="small text-muted">Toggle color status</p>
                </aside>
                <content>
                    

                    <div class="color_holder row_position">
                        @foreach ($productColorGroup as $productWiseColorsKey => $productWiseColorsVal)
                        <div class="color_holder_single single-color-holder d-flex" id="{{$productWiseColorsVal->id}}">
                            <div class="color_box shadow-sm" style="{!! ($productWiseColorsVal->status == 0) ? 'background: #c1080a59;' : '' !!}" id="color_box_up_{{$productWiseColorsVal->color->name}}">
                                <div>
                                @if($productWiseColorsVal->color_fabric != null)
                                    <img src="{{ asset($productWiseColorsVal->color_fabric) }}" alt="">
                                @else
                                    @if($productWiseColorsVal->color->name == 'Assorted')
                                        <span style="background: -webkit-linear-gradient(left,  rgba(219,2,2,1) 0%,rgba(219,2,2,1) 9%,rgba(219,2,2,1) 10%,rgba(254,191,1,1) 10%,rgba(254,191,1,1) 10%,rgba(254,191,1,1) 20%,rgba(1,52,170,1) 20%,rgba(1,52,170,1) 20%,rgba(1,52,170,1) 30%,rgba(15,0,13,1) 30%,rgba(15,0,13,1) 30%,rgba(15,0,13,1) 40%,rgba(239,77,2,1) 40%,rgba(239,77,2,1) 40%,rgba(239,77,2,1) 50%,rgba(254,191,1,1) 50%,rgba(137,137,137,1) 50%,rgba(137,137,137,1) 60%,rgba(254,191,1,1) 60%,rgba(254,191,1,1) 60%,rgba(254,191,1,1) 70%,rgba(189,232,2,1) 70%,rgba(189,232,2,1) 80%,rgba(209,2,160,1) 80%,rgba(209,2,160,1) 90%,rgba(48,45,0,1) 90%);"></span>
                                    @else
                                        <span style="background-color:{{ $productWiseColorsVal->color->code }}"></span>
                                    @endif
                                @endif
                                </div>
                                <p class="small card-title">
                                    @if ($productWiseColorsVal->color_id)
                                        {{$productWiseColorsVal->color->name}}
                                    @else
                                        @php
                                            $orgColorName = \App\Models\Color::select('name')->where('id', $productWiseColorsVal->color_id)->first();
                                        @endphp
                                        {{$orgColorName->name}}
                                    @endif
                                </p>
                            </div>

                            {{--<div class="status-toggle shadow-sm">
                                <a href="javascript: void(0)" onclick="colorStatusToggle({{$productWiseColorsVal->id}}, {{$data->id}}, {{$productWiseColorsVal->color_id}})" title="Tap here to change status"><i class="fi fi-br-cube"></i></a>
                            </div>--}}
                        </div>
                        @endforeach
                    </div>

                    <a href="javascript: void(0)" onclick="addColorModal()" class="btn btn-sm btn-success new-color">Add new color</a>
                </content>
            </div>
            @foreach ($productColorGroup as $productColorKey => $productColorGroupVal)
            
            <div class="admin__content">
                <content>
                    @if ($productColorKey == 0)
                    <div class="row" style="position: sticky;top: 55px;background: white;z-index: 99;padding: 10px 0;">
                        <div class="col-sm-2">
                            <strong>SR</strong>
                            <strong>Color</strong>
                        </div>
                        <div class="col-sm-8">
                            
                            <strong>Size</strong>
                        </div>
                        <div class="col-sm-2">
                            
                            {{-- <strong>Action</strong> --}}
                        </div>
                    </div>
                    <hr>
                    @endif
                    <div class="row">
                        {{-- <div class="col-sm-2">
                            <label for="inputPassword6" class="col-form-label">{{ $productColorKey + 1 }}</label>
                        </div> --}}
                        <div class="col-sm-2">
                            <label for="inputPassword6" class="col-form-label">{{ $productColorKey + 1 }}</label>
                            <div class="color_box" id="color_box_down_{{$productColorGroupVal->color->name}}">
                                <div>
                                @if($productColorGroupVal->color_fabric != null)
                                    <img src="{{ asset($productColorGroupVal->color_fabric) }}" alt="">
                                @else
                                    @if($productColorGroupVal->color->name == 'Assorted')
                                        <span style="background: -webkit-linear-gradient(left,  rgba(219,2,2,1) 0%,rgba(219,2,2,1) 9%,rgba(219,2,2,1) 10%,rgba(254,191,1,1) 10%,rgba(254,191,1,1) 10%,rgba(254,191,1,1) 20%,rgba(1,52,170,1) 20%,rgba(1,52,170,1) 20%,rgba(1,52,170,1) 30%,rgba(15,0,13,1) 30%,rgba(15,0,13,1) 30%,rgba(15,0,13,1) 40%,rgba(239,77,2,1) 40%,rgba(239,77,2,1) 40%,rgba(239,77,2,1) 50%,rgba(254,191,1,1) 50%,rgba(137,137,137,1) 50%,rgba(137,137,137,1) 60%,rgba(254,191,1,1) 60%,rgba(254,191,1,1) 60%,rgba(254,191,1,1) 70%,rgba(189,232,2,1) 70%,rgba(189,232,2,1) 80%,rgba(209,2,160,1) 80%,rgba(209,2,160,1) 90%,rgba(48,45,0,1) 90%);"></span>
                                    @else
                                        <span style="background-color:{{ $productColorGroupVal->color->code }}"></span>
                                    @endif
								@endif
                                </div>
								<p>
                                    @if ($productColorGroupVal->color->name)
                                        {{$productColorGroupVal->color->name}}
                                    @else
                                        @php
                                            $orgColorName = \App\Models\Color::select('name')->where('id', $productColorGroupVal->color_id)->first();
                                        @endphp
                                        {{$orgColorName->name}}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-sm-11">
                                    <div class="size_holder" style="border-top: 0;padding-top: 0;">
                                        <div class="row align-items-center">
                                            <div class="col-sm-3"><strong>Size details</strong></div>
                                            <div class="col-sm-3"><strong>Price</strong></div>
                                            <div class="col-sm-3">Action</div>
                                        </div>
                                    </div>

                                    @php
                                        $productVariationColorSizes = \App\Models\ProductColorSize::where('product_id', $id)->where('color_id', $productColorGroupVal->color_id)->orderBy('size_id')->get();

                                        // dd($productVariationColorSizes);

                                        $prodSizesDIsplay = '';
                                        foreach($productVariationColorSizes as $productSizeKey => $productSizeVal) {
                                            $returnAlert = "return confirm('Are you sure ?')";

                                            $sizeName = $productSizeVal->size ? $productSizeVal->size->name : '<span class="text-danger" title="Please delete this & add again">SIZE MISMATCH</span>';

                                            if ($productSizeKey == 0) {
                                                $singleStyle = "border-top: 0;padding-top: 0;";
                                            } else {
                                                $singleStyle = '';
                                            }

                                            if ($productSizeVal->size->size_details != null || $productSizeVal->size->size_details != '') {
                                                $sizeDetailsDisplay = ' - <small class="text-muted">'.$productSizeVal->size->size_details.'</small>';
                                            } else {
                                                $sizeDetailsDisplay = '';
                                            }

                                            if ($productSizeVal->size->name) {
                                                $sizeDisplayName = $productSizeVal->size->name;
                                            } else {
                                                $orgSizeName = \App\Models\Size::select('name')->where('id', $productSizeVal->size_id)->first();

                                                $sizeDisplayName = $orgSizeName->name;
                                            }

                                            $funcSizeDetail = "'".$productSizeVal->size->size_details."'";
                                            $funcPriceDetail = "'".$productSizeVal->offer_price."'";
                                            $funcCodeDetail = "'".$productSizeVal->code."'";
                                            $funcSizeNameDetail = "'".$sizeDisplayName."'";


                                            $prodSizesDIsplay .= '
                                            <div class="size_holder" style="'.$singleStyle.'">
                                                <div class="row align-items-center justify-content-between">
                                                    <div class="col-sm-2">
                                                        

                                                        '.$sizeName.' '.$sizeDetailsDisplay.'
                                                    </div>
                                                    <div class="col-sm-2">Rs '.$productSizeVal->offer_price.'</div> 
                                                    <div class="col-sm-3">'.$productSizeVal->code.'</div>
                                                   
                                                    <div class="col-sm-3 text-end">
                                                        <div>
                                                            <a href="javascript: void(0)" onclick="editSizeFunc('.$funcSizeNameDetail.', '.$productSizeVal->id.', '.$funcSizeDetail.', '.$funcPriceDetail.', '.$funcCodeDetail.')" class="badge bg-success">Edit</a>
                                                            <a href='.route('admin.products.variation.size.delete', $productSizeVal->id).' onclick="'.$returnAlert.'" class="badge bg-danger">Delete</a>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>';

                                            /*
                                            delete option below edit (removed for now)
                                            <a href='.route('admin.products.variation.size.delete', $productSizeVal->id).' onclick="'.$returnAlert.'" class="badge bg-danger">Delete</a>
                                            */
                                        }
                                        $prodSizesDIsplay .= '';
                                    @endphp
                                    {!!$prodSizesDIsplay!!}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-1">
                                    <label for="inputPassword6" class="col-form-label">Images</label>
                                </div>
                                <div class="col-sm">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <form action="{{route('admin.products.variation.image.add')}}" method="post" enctype="multipart/form-data">@csrf
                                                <input type="file" name="image[]" id="prodVar{{$productColorKey}}" class="d-none" multiple>
                                                <label class="image_upload" for="prodVar{{$productColorKey}}">Browse Image</label>

                                                <input type="hidden" name="product_id" value="{{$id}}">
                                                <input type="hidden" name="color_id" value="{{$productColorGroupVal->color_id}}">
                                                <button type="submit" class="btn btn-sm btn-success">+</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                @php
                                    $productVariationImages = \App\Models\ProductImage::where('product_id', $id)->where('color_id', $productColorGroupVal->color_id)->get();

                                    $prodImagesDIsplay = '';
                                    foreach($productVariationImages as $productImgKey => $productImgVal) {
                                        $prodImagesDIsplay .= '<div class="col-sm-auto" id="img__holder_'.$productColorKey.'_'.$productImgKey.'"><figure class="img_thumb"><img src='.asset($productImgVal->image).'><a href="javascript: void(0)" class="remove_image" onclick="deleteImage('.$productImgVal->id.', '.$productColorKey.', '.$productImgKey.')"><i class="fi fi-br-trash"></i></a></figure></div>';
                                    }
                                @endphp
                                {!!$prodImagesDIsplay!!}
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="text-center">
                                 {{--<div class="color-fabric-image-holder position-relative mb-3">
                                     
                                   <img class="color-fabric-image" src="{{ ($productColorGroupVal->color_fabric) ? asset($productColorGroupVal->color_fabric) : asset('admin/images/square-placeholder-image.jpg') }}" alt="profile-picture" id="fabric_id_{{ $productColorGroupVal->color->name }}">

                                    <div class="change-image">
                                        <label for="upload_image_{{ $productColorGroupVal->color }}" class="badge badge-primary upload-image-label" data-bs-toggle="tooltip" title="Browse image" onclick="fabricUploadFunc({{ $productColorGroupVal->color_id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-camera"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                        </label>
                                        <input type="file" name="upload_image" id="upload_image_{{ $productColorGroupVal->color_id }}" class="d-none" accept="image/*">
                                    </div>
                                </div>--}}
                            </div>

							<a href="javascript: void(0)" onclick="addSizeModal({{$productColorGroupVal->color_id}}, '{{ ($productColorGroupVal->color) ? $productColorGroupVal->color->name : '' }}')" class="badge bg-success">Add new size</a>

                            <a href="javascript: void(0)" class="badge bg-primary" onclick="editColorModalOpen({{$productColorGroupVal->color_id}}, '{{ ($productColorGroupVal->color) ? $productColorGroupVal->color->name : '' }}')">Change Color</a>

                            <hr>

                            {{--<a href="javascript: void(0)" class="badge bg-primary" onclick="renameColorModalOpen({{$productColorGroupVal->color_id}}, '{{ $productColorGroupVal->color->name }}')">Rename Color</a>--}}

                            <a href="{{ route('admin.products.variation.color.delete',['productId' => $id, 'colorId' => $productColorGroupVal->color_id]) }}" onclick="return confirm('Are you sure ?')" class="badge bg-danger">Delete Color</a>
                        </div>
                    </div>
                </content>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- add new color modal --}}
<div class="modal fade" tabindex="-1" id="addColorModal">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add new color</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('admin.products.variation.color.add')}}" method="post">@csrf
                <input type="hidden" name="product_id" value="{{$id}}">
                
                <div class="form-group mb-3">
                <select class="form-control" name="color_id" id="">
                    <option value="" selected>Select color...</option>
                    @php
                        $color = \App\Models\Color::orderBy('name', 'asc')->get();
                        foreach ($color as $key => $value) {
                            echo '<option value="'.$value->id.'">'.$value->name.'</option>';
                        }
                    @endphp
                </select>
                </div>
                <div class="form-group mb-3">
                <select class="form-control" name="size_id" id="">
                    <option value="" selected>Select size...</option>
                    @php
                        $sizes = \App\Models\Size::get();
                        foreach ($sizes as $key => $value) {
                            echo '<option value="'.$value->id.'">'.$value->name.'</option>';
                        }
                    @endphp
                </select>
                </div>
               
                <div class="form-group mb-3">
                    <input class="form-control" type="text" name="price" id="" placeholder="Price">
                </div>
                {{--<div class="form-group mb-3">
                    <input class="form-control" type="text" name="sku_code" id="" placeholder="SKU code">
                </div>--}}
                <div class="form-group">
                    <button type="submit" class="btn btn-sm btn-success">+ Save changes</button>
                </div>
            </form>
        </div>
      </div>
    </div>
</div>

{{-- edit color modal --}}
<div class="modal fade" tabindex="-1" id="editColorModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change color</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.products.variation.color.edit')}}" method="post">@csrf
                    <input type="hidden" name="product_id" value="{{$id}}">
                    <input type="hidden" name="current_color" value="">
                    <div class="form-group">
                        <p>Style no: <strong>{{$data->style_no}}</strong></p>
                        <p>Product: <strong>{{$data->name}}</strong></p>
                        <p>Current Color: <strong><span id="colorName"></span></strong></p>
                    </div>
                    <div class="form-group mb-3">
                        <label for="editColorCode">Change color</label>
                        <select class="form-control" name="update_color" id="editColorCode">
                            <option value="" disabled selected>Select color...</option>
                            @php
                                $color = \App\Models\Color::orderBy('name', 'asc')->get();
                                foreach ($color as $key => $value) {
                                    echo '<option value="'.$value->id.'">'.$value->name.'</option>';
                                }
                            @endphp
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-sm btn-success">Change color</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- rename color modal --}}
<div class="modal fade" tabindex="-1" id="renameColorModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rename color</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.products.variation.color.rename')}}" method="post">@csrf
                    <input type="hidden" name="product_id" value="{{$id}}">
                    <input type="hidden" name="current_color2" value="">
                    <div class="form-group">
                        <p>Style no: <strong>{{$data->style_no}}</strong></p>
                        <p>Product: <strong>{{$data->name}}</strong></p>
                        <p>Current name: <strong><span id="colorName2"></span></strong></p>
                    </div>
                    <div class="form-group mb-3">
                        <label>Enter new name</label>
                        <input type="text" class="form-control" name="update_color_name" id="">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-sm btn-success">Rename color</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- rename size modal --}}
<div class="modal fade" tabindex="-1" id="sizeDetailModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Size detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.products.variation.size.edit')}}" method="post">@csrf
                    {{-- <input type="hidden" name="product_id" value="{{$id}}"> --}}
                    <input type="hidden" name="id" value="">
                    <div class="form-group">
                        <p>Style no: <strong>{{$data->style_no}}</strong></p>
                        <p>Product: <strong>{{$data->name}}</strong></p>
                        
                    </div>
                    <div class="form-group mb-3">
                        <label>Current Size: <span id="sizeNameDetail"></span> </label>
                        <select class="form-control" name="size_id" id="">
                            <option value="" selected>Change size...</option>
                            @php
                                $sizes = \App\Models\Size::get();
                                foreach ($sizes as $key => $value) {
                                    echo '<option value="'.$value->id.'">'.$value->name.'</option>';
                                }
                            @endphp
                        </select>
                    </div>
                    {{--<div class="form-group mb-3">
                        <label>Size detail</label>
                        <input type="text" class="form-control" name="size_details" id="">
                    </div>--}}
                    <div class="form-group mb-3">
                        <label>Price</label>
                        <input type="text" class="form-control" name="price" id="">
                    </div>
                    {{--<div class="form-group mb-3">
                        <label>Code</label>
                        <input type="text" class="form-control" name="code" id="">
                    </div>--}}
                    <div class="form-group">
                        <button type="submit" class="btn btn-sm btn-success">Save size detail</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- image change crop modal --}}
<div class="modal" id="uploadimageModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="image_demo"></div>
                        <p class="small text-muted">Scroll & Drag for perfect fit</p>
                        <input type="hidden" name="color_fabric_color_id" value="">
                        <button class="btn btn-sm btn-primary crop_image">Crop & Upload Image</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- bulk upload variation modal --}}
<div class="modal fade" id="csvUploadModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Bulk Upload Existing Product Variation with SKU code, color & size
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.products.variation.csv.upload') }}" enctype="multipart/form-data" id="borrowerCsvUpload">@csrf
                    <input type="file" name="file" class="form-control" accept=".csv">
                    <br>
                    <a href="{{ asset('admin/static/product-variation-sample.csv') }}">Download Sample CSV</a>
                    <br>
                    <button type="submit" class="btn btn-danger mt-3" id="csvImportBtn">Import <i class="fas fa-upload"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        function renameColorModalOpen(colorId, colorName) {
            $('#colorName2').text(colorName);
            $('input[name="update_color_name"]').val(colorName);
            $('input[name="current_color2"]').val(colorId);
            $('#renameColorModal').modal('show');
        }

		function editColorModalOpen(colorId, colorName) {
            $('#colorName').text(colorName);
            $('input[name="current_color"]').val(colorId);
            $('#editColorModal').modal('show');
        }

		function editSizeFunc(size, id, name, price, code) {
            $('#sizeNameDetail').text(size);
            $('#colorName3').text(name);
            $('input[name="id"]').val(id);
            $('input[name="size_details"]').val(name);
            $('input[name="price"]').val(price);
            $('input[name="code"]').val(code);
            $('#sizeDetailModal').modal('show');
        }

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

        $(document).on('click','.removeTimePrice',function(){
            var thisClickedBtn = $(this);
            thisClickedBtn.closest('tr').remove();
        });

        function sizeCheck(productId, colorId) {
            $.ajax({
                url : '{{route("admin.products.size")}}',
                method : 'POST',
                data : {'_token' : '{{csrf_token()}}', productId : productId, colorId : colorId},
                success : function(result) {
                    if (result.error === false) {
                        let content = '<div class="btn-group" role="group" aria-label="Basic radio toggle button group">';

                        $.each(result.data, (key, val) => {
                            content += `<input type="radio" class="btn-check" name="productSize" id="productSize${val.sizeId}" autocomplete="off"><label class="btn btn-outline-primary px-4" for="productSize${val.sizeId}">${val.sizeName}</label>`;
                        })

                        content += '</div>';

                        $('#sizeContainer').html(content);
                    }
                },
                error: function(xhr, status, error) {
                    // toastFire('danger', 'Something Went wrong');
                }
            });
        }

        function deleteImage(imgId, id1, id2) {
            $.ajax({
                url : '{{route("admin.products.variation.image.delete")}}',
                method : 'POST',
                data : {'_token' : '{{csrf_token()}}', id : imgId},
                beforeSend : function() {
                    $('#img__holder_'+id1+'_'+id2+' a').text('Deleting...');
                },
                success : function(result) {
                    $('#img__holder_'+id1+'_'+id2).hide();
                    toastFire('success', result.message);
                },
                error: function(xhr, status, error) {
                    // toastFire('danger', 'Something Went wrong');
                }
            });
        }

        $(".row_position").sortable({
            delay: 150,
            stop: function() {
                var selectedData = new Array();
                $('.row_position > .single-color-holder').each(function() {
                    selectedData.push($(this).attr("id"));
                });
                updateOrder(selectedData);
            }
        });

        function updateOrder(data) {
            // $('.loading-data').show();
            $.ajax({
                url : "{{route('admin.products.variation.color.position')}}",
                type : 'POST',
                data: {
                    _token : '{{csrf_token()}}',
                    position : data
                },
                success:function(data) {
                    // toastFire('success', 'Color position updated successfully');
                    // $('.loading-data').hide();
                    if (result.status == 200) {
                        toastFire('success', result.message);
                    } else {
                        toastFire('error', result.message);
                    }
                }
            });
        }

        // product color status change
        function colorStatusToggle(id, productId, colorId) {
            $.ajax({
                url : '{{route("admin.products.variation.color.status.toggle")}}',
                method : 'POST',
                data : {
                    _token : '{{csrf_token()}}',
                    productId : productId,
                    colorId : colorId,
                },
                success : function(result) {
                    if (result.status == 200) {
                        // toastFire('success', result.message);

                        if (result.type == 'active') {
                            $('#'+id+' .color_box').css('background', '#fff');
                        } else {
                            $('#'+id+' .color_box').css('background', '#c1080a59');
                        }
                    } else {
                        toastFire('error', result.message);
                    }
                }
            });
        }

        function addSizeModal(colorId, colorName) {
            $('#addColorModal .modal-title').text('Add new size');
            $('#addColorModal select[name="color_id"]').html('<option value="'+colorId+'">'+colorName+'</option>');
            $('#addColorModal').modal('show');
        }

        function addColorModal() {
            var contentData = `
            @php
                $color = \App\Models\Color::orderBy('name', 'asc')->get();
                foreach ($color as $key => $value) {
                    echo '<option value="'.$value->id.'">'.$value->name.'</option>';
                }
            @endphp
            `;
            $('#addColorModal .modal-title').text('Add new color');
            $('#addColorModal select[name="color"]').html('<option value="" selected>Select color...</option>'+ contentData);
            $('#addColorModal').modal('show');
        }

        // image fabric upload
        $image_crop = $('#image_demo').croppie({
            enableExif: true,
            viewport: {
                width: 150,
                height: 150,
                type: 'circle'
            },
            boundary: {
                width: 200,
                height: 200
            }
        });

        // $('#upload_image').on('change', function () {
        $('input[name=upload_image]').on('change', function () {
            var reader = new FileReader();
            reader.onload = function (event) {
                $image_crop.croppie('bind', {
                    url: event.target.result
                });
            }
            reader.readAsDataURL(this.files[0]);
            $('#uploadimageModal').modal('show');
        });

        function fabricUploadFunc(color_id) {
            $('input[name=color_fabric_color_id]').val(color_id)
        }

        $('.crop_image').click(function (event) {
            $image_crop.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function (response) {
                $.ajax({
                    url: "{{ route('admin.products.variation.color.fabric.upload') }}",
                    type: "POST",
                    data: {
                        "_token": '{{ csrf_token() }}',
                        "image": response,
                        "product_id": '{{$id}}',
                        "color_id": $('input[name=color_fabric_color_id]').val(),
                    },
                    beforeSend: function() {
                        $('.crop_image').html('Please wait').attr('disabled', true);
                    },
                    success: function (result) {
                        $('#uploadimageModal').modal('hide');
                        $('.crop_image').html('Crop & Upload Image').attr('disabled', false);
                        if(result.error == true){
                            toastFire('warning', result.message);
                        } else {
                            const img = `<img src="${result.image}" alt="">`;

                            $('#image_demo').html('');
                            $('#fabric_id_'+result.color_id).attr('src', result.image);
                            $('#color_box_down_'+result.color_id+' div').html(img);
                            $('#color_box_up_'+result.color_id+' div').html(img);
                            toastFire('success', result.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        toastFire('warning', error);
                    }
                });
            })
        });

        // bulk action
        $('select[name="bulkAction"]').on('change', function() {
            $('#bulkActionForm').submit();
        });

        // bulk select all checkbox
        // $('.bulkSelectAll').on();
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
@endsection
