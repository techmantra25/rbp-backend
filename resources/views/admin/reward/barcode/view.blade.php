@extends('admin.layouts.app')

@section('page', 'QRcode detail')

@section('content')
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="text-muted">{{ $data->code ?? ''}}</h3>
                            {{-- <h6>{{ $data->name }}</h6> --}}
                        </div>
                        <div class="col-md-4 text-end">
                            @if ($data->end_date < \Carbon\Carbon::now() )
                            <h3 class="text-danger mt-3 fw-bold">EXPIRED</h3>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="small text-muted mt-4 mb-2">Details</p>
                            <table class="">

                                <tr>
                                    <td class="text-muted">Points: </td>
                                    <td>{{$data->type == 1 ? $data->amount.' ' : ' '. $data->amount}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Max time usage : </td>
                                    <td>{{$data->max_time_of_use ?? ''}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Max time usage for single user :  </td>
                                    <td>{{$data->max_time_one_can_use ?? ''}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No of usage : </td>
                                    <td>{{$data->no_of_usage ?? ''}}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Start date: </td>
                                    <td>{{ date('j M Y h:m A', strtotime($data->start_date)) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">End date: </td>
                                    <td>{{ date('j M Y h:m A', strtotime($data->end_date)) }}</td>
                                </tr>
                            </table>

                            <hr>

                            {{-- <p class="small text-muted mt-4 mb-2">QRcodes</p>
                            <div class="col-auto">
                                <a type="button" id="basic" class="btn btn-outline-danger btn-sm">Download pdf</a>
                            </div>
                            <table class="table table-sm print-code">
                                <tr>
                                    <th>#SR</th>
                                    <th>Code</th>
                                    <th>QR</th>
                                    <th>Points</th>
                                    <th>Usage</th>
                                    <th>Status</th>
                                </tr>
                                @forelse ($coupons as $couponKey => $coupon)
                                <tr>
                                    <td>{{$couponKey+1}}</td>
                                    <td>{{$coupon->code}}
                                        <div class="row__action">
                                            <a href="{{ route('admin.qrcode.edit', $coupon->id) }}">Edit</a>
                                            <a href="{{ route('admin.qrcode.edit', $coupon->id) }}">Edit</a>
                                            <a href="{{ route('admin.qrcode.status', $coupon->id) }}">{{($coupon->status == 1) ? 'Active' : 'Inactive'}}</a>

                                        </div>
                                    </td>
                                    <td><div class="card-body ">
                                        {!! QrCode::size(100)->generate($coupon->code) !!}
                                    </div></td>
                                    <td>{{$coupon->points}}</td>
                                    <td>
                                        @if($coupon->no_of_usage >= $coupon->max_time_use)
                                            {{$coupon->no_of_usage}}</a>

                                        @else
                                            <p class="small text-danger">Not used yet</p>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-{{($coupon->status == 1) ? 'success' : 'danger'}}">{{($coupon->status == 1) ? 'Active' : 'Inactive'}}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                                @endforelse
                            </table> --}}
                            <p class="small text-muted mt-4 mb-2">QRcode Usage</p>

                            <table class="table table-sm">
                                <tr>
                                    <th>#SR</th>
                                    <th> Points</th>
                                    <th>Scanned points</th>
                                    <th>User details</th>
                                    <th>Time</th>
                                </tr>
                                @forelse ($usage as $usageKey => $usageValue)
                                <tr>
                                    <td>{{$usageKey+1}}</td>
                                    <td>{{$usageValue->amount ?? ''}}</td>
                                    <td>{{$usageValue->amount ?? ''}}</td>
                                    <td>
                                        @if($usageValue->user_id != 0)
										    <p colspan="100%" class="small text-dark">{{$usageValue->users->owner_name?? ''}}</p>
                                            <p colspan="100%" class="small text-muted">{{$usageValue->users->store_name ?? ''}}</p>
                                        @endif
                                        <p class="small mb-0">{{$usageValue->users->email ?? ''}} </p>
										 <p class="small mb-0">{{$usageValue->users->contact ?? ''}} </p>
										 <p class="small mb-0">{{$usageValue->users->unique_code ?? ''}} </p>
                                    </td>
                                    <td>{{ date('j M Y H:i a', strtotime($usageValue->created_at)) }}</td>

                                </tr>
                                @empty
                                <tr><td colspan="100%" class="small text-muted">No data found</td></tr>
                                @endforelse
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

</section>
@endsection

@section('script')
<script src="{{ asset('admin/js/printThis.js') }}"></script>
<script>
 $('#basic').on("click", function () {
      $('.print-code').printThis();
    });
</script>
@endsection
