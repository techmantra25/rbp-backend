@extends('admin.layouts.app')

@section('page', 'Product detail')

@section('content')
<section class="product-detail-sec">
    
        <div class="row">
            <div class="col-sm-3">
                <div class="card shadow-sm">
                    <div class="card-header">Main image</div>
                    <div class="card-body">
                        <div class="w-100 product__thumb">
                            @if(!empty($data->image))
                            <label for="thumbnail"><img id="output" src="{{ asset($data->image) }}"/></label>
                            @else
                             <label for="thumbnail"><img id="output" src="{{asset('admin/images/default-placeholder-product-image.png')}}" />
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header">More images</div>
                    <div class="card-body">
                        <div class="w-100 product__thumb">
                        @foreach($images as $index => $singleImage)
                            <label for="thumbnail"><img id="output" src="{{ asset($singleImage->image) }}" class="img-thumbnail mb-3"/></label>
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            @if(!empty($data->style_no))
                            <h2>#{{$data->style_no}}</h2>
                            @endif
                        </div>
                        <div class="form-group mb-3">
                            <h5 class="text-muted">{{$data->name}}</h5>
                        </div>
                        <div class="form-group mb-3">
                            <p>
								@if($data->category) <span class="text-muted">Category : </span>{{$data->category->name}} @endif
								@if($data->collection) | <span class="text-muted">Collection : </span>{{$data->collection->name}} @endif
							</p>
                        </div>

                        @if ($data->colorSize)
                            @php
                            $uniqueColors = [];

                            // custom function multi-dimensional in_array
                            /* function in_array_r($needle, $haystack, $strict = false) {
                                foreach ($haystack as $item) {
                                    if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
                                        return true;
                                    }
                                }
                                return false;
                            } */

                            foreach ($data->colorSize as $variantKey => $variantValue) {
                                if (in_array_r($variantValue->color->code, $uniqueColors)) continue;

                                $uniqueColors[] = [
                                    'id' => $variantValue->color->id,
                                    'code' => $variantValue->color->code,
                                    'name' => $variantValue->color->name,
                                ];
                            }

                            // echo '<pre>';print_r($uniqueColors);

                            echo '<hr><div class="d-flex">';

                            foreach($uniqueColors as $colorCode) {
                                echo '<div onclick="sizeCheck('.$data->id.', '.$colorCode['id'].')" style="text-align:center;height: 70px; min-width: 40px;margin-right: 20px;" class="pro-detail-size-flex"><div class="btn btn-sm rounded-circle" style="background-color: '.$colorCode['code'].';height: 40px;width: 40px;border: 1px solid #000;"></div><p class="small text-muted mb-0 mt-2">'.ucwords($colorCode['name']).'</p></div>';
                            }

                            echo '</div>';

                            echo '<p class="small text-dark font-default">Tap on color to get sizes</p>';

                            echo '<div id="sizeContainer"></div>';
                            @endphp
                        @endif
                        
                        <hr>
                        <div class="form-group mb-3">
                            <h4>
                                <span class="text-muted small"><del>Rs {{$data->price}}</del></span>
                                <span class="text-danger">Rs {{$data->offer_price}}</span>
                            </h4>
                        </div>
                        <hr>
                        <div class="form-group mb-3">
                            <p class="font-default">Short Description</p>
                            {!! $data->short_desc !!}
                        </div>
                        <hr>
                         <div class="form-group mb-3">
                            <p class="font-default">Description</p>
                            {!! $data->desc !!}
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
                                        <p class="small">{{$data->meta_title}}</p>
                                    </div>
                                </div>
                                <div class="row mb-2 align-items-center">
                                    <div class="col-3">
                                        <label for="inputprice6" class="col-form-label">Description</label>
                                    </div>
                                    <div class="col-9">
                                        <p class="small">{{$data->meta_desc}}</p>
                                    </div>
                                </div>
                                <div class="row mb-2 align-items-center">
                                    <div class="col-3">
                                        <label for="inputprice6" class="col-form-label">Keyword</label>
                                    </div>
                                    <div class="col-9">
                                        <p class="small">{{$data->meta_keyword}}</p>
                                    </div>
                                </div>
                            </content>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection

@section('script')
    <script>
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
    </script>
@endsection