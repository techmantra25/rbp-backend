@extends('admin.layouts.app')

@section('page', 'Qrcode generate')

@section('content')
<section>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reward.retailer.barcode.store') }}" enctype="multipart/form-data">
                    @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="label-control">State <span class="text-danger">*</span> </label>
                                        <select name="state_id" id="state" class="form-control form-control-sm select2">
                                            <option value="" disabled>Select</option>
                                            <option value="" selected>All</option>
                                            @foreach ($state as $state)
                                                <option value="{{$state->id}}" {{ request()->input('state_id') == $state->id ? 'selected' : '' }}>{{$state->name}}</option>
                                            @endforeach
                                        </select>
                                </div>
                            </div>
                            <!--<div class="col-md-4">-->
                            <!--    <div class="form-group mb-3">-->
                            <!--        <label class="label-control">Distributor <span class="text-danger">*</span> </label>-->
                            <!--            <select name="distributor_id" id="distributor_id" class="form-control form-control-sm select2">-->
                            <!--                <option value="" disabled>Select</option>-->
                            <!--                <option value="" selected>All</option>-->
                            <!--                @foreach ($allDistributors as $dist)-->
                            <!--                    <option value="{{$dist->id}}" {{ request()->input('distributor_id') == $dist->id ? 'selected' : '' }}>{{$dist->name}}({{$dist->state}})({{$dist->employee_id}})</option>-->
                            <!--                @endforeach-->
                            <!--            </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="label-control">Qrcode details <span class="text-danger">*</span> </label>
                                    <input type="text" name="name" id="name" placeholder="" class="form-control" value="{{old('name')}}">
                                    @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="label-control">Start date <span class="text-danger">*</span> </label>
                                    <input type="datetime-local" name="start_date" placeholder="" class="form-control" value="{{ date('Y-m-d\TH:i') }}" min="{{ date('Y-m-d\TH:i') }}">
                                    @error('start_date') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="label-control">End date <span class="text-danger">*</span> </label>
                                    <input type="datetime-local" name="end_date" placeholder="" class="form-control" value="{{ old('end_date') ? old('end_date') : date('Y-m-d\TH:i', strtotime('+5 years')) }}" 
                                    min="{{ date('Y-m-d\TH:i') }}">
                                    @error('end_date') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="label-control"> Amount <span class="text-danger">*</span> </label>
                                    <input type="number" name="amount" placeholder="" class="form-control" value="{{old('amount')? old('amount') : '100'}}">
                                    @error('amount') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="label-control">No of qrcodes to generate <span class="text-danger">*</span> </label>
                                    <input type="number" name="generate_number" placeholder="" class="form-control" value="{{ old('generate_number')   }}">
                                    @error('generate_number') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <p class="small text-danger">Qrcodes code will be auto-generated</p>
                            </div>
                            <div class="col-12">
                                {{-- <input type="hidden" name="type" value="1"> --}}
                                <input type="hidden" name="max_time_of_use" value="1">
                                <input type="hidden" name="max_time_one_can_use" value="1">
                                <button type="submit" class="btn btn-danger w-100">Tap here to generate codes</button>
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
        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');   // Get day and pad with 0 if needed
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Get month (0-indexed) and pad with 0
            const year = date.getFullYear();  // Get year

            return `${day}-${month}-${year}`;
            console.log(year);// Return formatted date in d-m-y format
        }

        // jQuery for listening to the change event of the select box
        document.getElementById('distributor_id').addEventListener('change', function () {
            const selectedTitle = this.value;  // Get selected title
            const today = new Date();  // Get today's date
            
            if (selectedTitle) {
                // Set the value of the text box with title + today's date in d-m-y format
                document.getElementById('name').value = selectedTitle + ' - ' + formatDate(today);
            } else {
                // Clear the text box if no title is selected
                document.getElementById('name').value = '';
            }
        });
    </script>
@endsection  
    