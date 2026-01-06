@extends('admin.layouts.app')

@section('page', 'User')

@section('content')
<section class="inner-sec1">
    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reward.qrcode.redeem.update',$qrTrans->id) }}" enctype="multipart/form-data">
                    @csrf
                        <h4 class="page__subtitle">Update</h4>
                        
                        <div class="row mb-2">
                           
                            <div class="col-md-4">
                                <div class="form-group">
									                  <label for="designation">Amount <span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="amount" name="amount" placeholder="name@example.com" value="{{ old('amount',$qrTrans->amount) }} ">
                                        
                                    </div>
                                    @error('amount') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
										                 <label for="employee_id">Remarks<span class="text-danger">*</span></label>
                                    <div class="form-floating mb-3">
                                        <textarea type="text" class="form-control" id="description" name="description" placeholder="remarks" value="">{{ old('description', $qrTrans->description) }}</textarea>
                                       
                                    </div>
                                    @error('description') <p class="small text-danger">{{$message}}</p> @enderror
                                </div>
                            </div>
                        </div>
                        
                      
                        
                        
                        

                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger">Save changes</button>
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
<script>
    $('select[name="state"]').on('change', (event) => {
        var value = $('select[name="state"]').val();
      
        $.ajax({
            url: '{{url("/")}}/admin/users/state/'+value,
            method: 'GET',
            success: function(result) {
                var content = '';
                var slectTag = 'select[name="area"]';
                var displayCollection =  "All";

                content += '<option value="" selected>'+displayCollection+'</option>';
                $.each(result.data.area, (key, value) => {
                    content += '<option value="'+value.area+'">'+value.area+'</option>';
                });
                $(slectTag).html(content).attr('disabled', false);
            }
        });
    });
</script>
@endsection
