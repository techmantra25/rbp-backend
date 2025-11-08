@extends('admin.layouts.app')

@section('page', 'Advertisement')

@section('content')

<section class="pro-edit">
    <div class="row">
        <div class="col-sm-9">
            <div class="card shadow-sm">
                <div class="card-body pt-0">
                    <div class="card-body">
                        <div class="w-100 product__thumb">
                           
                                @foreach($data as $item)
                                    <img id="output" height="500" width="500" src="{{ asset($item->video) }}">
                                    <a href="{{route('admin.branding.video.delete',$item->id)}}" class="working_area" title="Delete area/bit" onclick="return confirm('Are you sure ?')"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></a>
                                @endforeach
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <form method="POST" action="{{ route('admin.branding.video.save') }}" enctype="multipart/form-data">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-header">
                        Banner Image<span class="text-danger">*</span><p>(Diemension:  698 x 320)</p>
                    </div>
                    <div class="card-body">
                        <input type="file" id="thumbnail" accept="image/*" name="video">
                    </div>
                </div>

                <div class="card shadow-sm" style="position: sticky; top: 60px;">
                    <div class="card-body text-end">
                        <button type="submit" class="btn btn-danger w-100">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection