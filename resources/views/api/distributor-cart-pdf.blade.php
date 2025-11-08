<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Lux Distributor Cart preview</title>

    {{-- <style>
        .bg-dark-new {
            background-color: #323639;
            box-shadow: 1px 1px 10px 1px #000000;
            z-index: 1;
        }
    </style> --}}
</head>
{{-- <body class="bg-dark"> --}}
<body class="">

    @if ($cartData)
        <nav class="navbar bg-dark-new">
            <div class="container">
                <div class="w-100">
                <div class="row">
                    <div class="col-auto">
                        <h5 class="text-dark mb-3 mt-2">Cart preview</h5>
                        
                    </div>
                    
                </div>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="card rounded-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="DivIdToPrint" style="border-left:2px solid #000; border-bottom:2px solid #000;">
                            <thead>
								@if(count(orderProductsUpdatedMatrix($cartData)) > 0)
                                <tr>
                                    <th style="font-size: 13px; min-width:200px; border-top:2px solid #000; width: 242px;">Name of Quality Shape & Unit</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">75</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">80</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">85</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">90</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">95</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">100</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">105</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">110</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">115</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;">120</th>
                                    <th style="font-size: 13px; border-left:2px solid #000; border-top:2px solid #000;border-right:2px solid #000;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                $totalCOunt = 0;
                            @endphp
                            @foreach(orderProductsUpdatedMatrix($cartData) as $productKey => $productValue)
                           
                                @php
								   
                                    $totalCOunt += $productValue['total'];
                                @endphp

                                <tr>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;">
                                        <p class="small text-dark fw-bold mb-0">{{$productValue['product_name']}}</p>
                                        <p class="small text-dark fw-bold mb-0">{{$productValue['product_style_no']}}</p>
                                        <p class="small text-dark fw-bold mb-0">{{$productValue['color']}}</p>
                                    </td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['75'] ? $productValue['75'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['80'] ? $productValue['80'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['85'] ? $productValue['85'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['90'] ? $productValue['90'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['95'] ? $productValue['95'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['100'] ? $productValue['100'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['105'] ? $productValue['105'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['110'] ? $productValue['110'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['115'] ? $productValue['115'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productValue['120'] ? $productValue['120'] : '' }}</p></td>
                                    
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;border-right:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{$productValue['total']}}</p></td>
                                </tr>
                            @endforeach
						 @endif
                            @if(count(orderProductsUpdatedMatrixChild($cartData)) > 0)
                            <thead>
                                <tr>
                                    <th style="color: #6c757d; font-size: 13px; min-width:200px; border-bottom:2px solid #000; width: 242px;">Name of Quality Shape & Unit</th>
                                    <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">35</th>
                                    <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">40</th>
                                    <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">45</th>
                                    <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">50</th>
                                    <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">55</th>
                                    <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">60</th>
                                    <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">65</th>
                                    <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">70</th>
									<th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">73</th>
									<th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;">75</th>
									
                                    <th style="color: #6c757d; font-size: 13px; border-left:2px solid #000; border-bottom:2px solid #000;border-right:2px solid #000;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
								@php
                                    $totalProductCount = 0;
                                @endphp
                                @foreach(orderProductsUpdatedMatrixChild($cartData) as $productKey => $productOrderValue)
                                
                                @php
								 
                                    $totalCOunt += $productOrderValue['total'];
                                    $totalProductCount += $productOrderValue['total'];
                                    $tcount=$totalProductCount+$totalCOunt;
                                @endphp
                                <tr>
                                    <td style="; border-bottom:2px solid #000;">
                                        <p class="small text-dark fw-bold mb-0">{{$productOrderValue['product_name']}}</p>
                                        <p class="small text-dark fw-bold mb-0">{{$productOrderValue['product_style_no']}}</p>
                                        <p class="small text-dark fw-bold mb-0">{{$productOrderValue['color']}}</p>
                                    </td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['35'] ? $productOrderValue['35'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['40'] ? $productOrderValue['40'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['45'] ? $productOrderValue['45'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['50'] ? $productOrderValue['50'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['55'] ? $productOrderValue['55'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['60'] ? $productOrderValue['60'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['65'] ? $productOrderValue['65'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['70'] ? $productOrderValue['70'] : '' }}</p></td>
									<td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['73'] ? $productOrderValue['73'] : '' }}</p></td>
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{ $productOrderValue['75'] ? $productOrderValue['75'] : '' }}</p></td>
									
                                    <td style="border-left:2px solid #000; border-bottom:2px solid #000;border-right:2px solid #000;"><p class="small text-dark fw-bold mb-0">{{$productOrderValue['total']}}</p></td>
                                </tr>
                                @endforeach
                            </tbody>
                            @endif
                            <tbody>
                                <tr>
                                    <td style="">
                                        <p class="small text-muted  mb-0">Total</p>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td> 
									<td></td>
									<td></td>
                                    <td style="border-left:2px solid #000;border-right:2px solid #000;border-right:2px solid #000;"><p class="small text-muted fw-bold mb-0">{{ $totalCOunt }}</p></td>
                                    {{-- <td style="border-left:2px solid #000;"><p class="small text-muted fw-bold mb-0">{{$cartData->sum('qty')}}</p></td> --}}
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://jasonday.github.io/printThis/printThis.js"></script>

    <script>
        function printDiv() {
            $('#DivIdToPrint').printThis({
                importCSS: true,
                importStyle: true,
            });
        }
    </script>
</body>
</html>
