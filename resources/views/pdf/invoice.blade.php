<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lux Cozi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px; /* Reduced font size */
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4;
            margin: 5mm; /* Reduced page margins */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            border: 1px solid #ccc;
            padding: 3px; /* Reduced padding */
            text-align: left;
        }

        img {
            width: 80px; /* Smaller image size */
            height: auto;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div style="width: 100%;">
        @php
            $orderProduct = DB::table('reward_order_products')->where('order_id', $data->id)->get();
             $distributorData = DB::table('teams')->where('store_id', $data->user->id)->first();
            $distributorD = DB::table('users')->where('id', $distributorData->distributor_id ?? null)->first();
            
            
                                            $state = DB::table('states')->where('id',$data->user->state_id)->first();
                                            $transactionH = DB::table('retailer_wallet_txns')->where('user_id', $data->user->id)->get();
                                            
                                            $qr = [];
                                            
                                            foreach ($transactionH as $rec) {
                                                $qr[] = $rec->barcode;
                                            }
                                          //  dd($qr);
                                            // Fetch distributor IDs and count their occurrences
                                        
                                            $distributorIdCounts = DB::table('retailer_barcodes')
                                                ->whereIn('code', $qr)
                                                ->select('distributor_id', DB::raw('COUNT(*) as count'))
                                                ->groupBy('distributor_id')
                                                ->orderByDesc('count')
                                                ->get();
                                                $distributorIdCounts = $distributorIdCounts->filter(function($item) {
                                                    return $item->distributor_id !== null;
                                                });
                                               // dd($distributorIdCounts);
                                            if (isset($distributorIdCounts[1])) {
                                           // dd($distributorIdCounts[1]);
                                                if ($distributorIdCounts[1]->distributor_id) {
                                                
                                                    $maxDistributorId = $distributorIdCounts[1]->distributor_id;
                                                    $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->first();
                                                    $maxCount = $distributorIdCounts[1]->count;
                                                }else{
                                                   
                                        			      $distributorIds = explode(',', $distributorData->distributor_id);
                                                          $teamDistributorIds = DB::table('teams')->where('area_id', $data->user->area_id)->where('state_id', $data->user->state_id)->where('ase_id',$data->user->user_id)->orWhere('asm_id',$data->user->user_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                           // dd($teamDistributorIds);
                                                            // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                            $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                        			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                        			    
                                                   $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                                }
                                            } elseif (isset($distributorIdCounts[0])) {
                                               if ($distributorIdCounts[0]->distributor_id) {
                                                
                                                    $maxDistributorId = $distributorIdCounts[0]->distributor_id;
                                                    $distributorDetails= DB::table('users')->where('id', $maxDistributorId)->first();
                                                    $maxCount = $distributorIdCounts[0]->count;
                                                }else{
                                                   
                                        			      $distributorIds = explode(',', $distributorData->distributor_id);
                                                          $teamDistributorIds = DB::table('teams')->where('area_id', $data->user->area_id)->where('state_id', $data->user->state_id)->where('ase_id',$data->user->user_id)->orWhere('asm_id',$data->user->user_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                           // dd($teamDistributorIds);
                                                            // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                            $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                        			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                        			    
                                                   $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                                }
                                            }else{
                                           // dd($distributorData);
                                                   $distributorIds = explode(',', $distributorData->distributor_id);
                                                      $aseId=DB::table('teams')->where('store_id',$data->user->id)->first();
                                                      //dd($aseId);
                                                      $teamDistributorIds = DB::table('teams')->where('area_id', $data->user->area_id)->where('state_id', $data->user->state_id)->where('ase_id',$aseId->ase_id)->where('store_id',NULL)->pluck('distributor_id')->toArray();
                                                        //dd($teamDistributorIds);
                                                        // Find the matching distributor IDs that are both in the team table and $distributorIds array
                                                        $matchingIds = array_intersect($distributorIds, $teamDistributorIds);
                                                        //dd($matchingIds);
                                    			      //$distributors=DB::table('users')->where('id',$matchingIds)->first();
                                               $distributorDetails = DB::table('users')->where('id', $matchingIds)->first();
                                            }
        @endphp
        
        
        <table>
            <tr>
                <td>Order Time: {{ date('d/m/Y H:i', strtotime($data->created_at)) }}</td>
                <td>Order No: {{ $data->order_no }}</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 12px;">Store Name : {{ $data->user->name }}</td>
            </tr>
            <tr>
                <td>Owner: {{ $data->user->owner_fname . ' ' . $data->user->owner_lname }}</td>
                <td>Email: {{ $data->user->email }}</td>
            </tr>
            <tr>
                <td>Mobile: {{ $data->user->contact }}</td>
                <td>Distributor: {{ $distributorDetails->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td colspan="2">Billing Address: {{ $data->user->city }}, {{ $data->user->pin }}, {{ $data->user->states->name }}</td>
            </tr>
            <tr>
                <td colspan="2">Shipping Address: 
                           @if(!empty($data->shipping_address) || !empty($data->shipping_city) || !empty($data->shipping_pin) || !empty($data->shipping_state) || !empty($data->shipping_landmark))
                            <p class="small text-dark mb-0">{{$data->shipping_address.', '.$data->shipping_city.', '.$data->shipping_pin.', '.$data->shipping_state.', '.$data->shipping_landmark}}</p>
                            @endif
                </td>
            </tr>
            
        </table>
        <table>
            <tr>
                <td colspan="2" style="font-weight: bold; padding: 5px;">Ordered Products ({{ count($orderProduct) }})</td>
            </tr>

            @forelse ($orderProduct as $productValue)
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td style="width: 60%; vertical-align: top;">{{ $productValue->product_name }}</td>
                                <td style="width: 40%; text-align: center;">
                                   
                                        <img src="{{ asset($productValue->product_image) }}" alt="Product Image" style="max-width: 100%; max-height: 100%;">
                                   
                                </td>
                             </tr>
                            <tr>
                                <td>Qty: {{ $productValue->qty }} | Currency: {{ $productValue->price }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
            @empty
                <tr>
                    <td colspan="2">No products found.</td>
                </tr>
            @endforelse
            <tr style="page-break-inside: avoid; page-break-after: avoid; page-break-before: avoid;">
                    <td colspan="2" style="font-weight: bold; text-align: right;">Final Currency: {{ $data->final_amount }}</td>
            </tr>
        </table>

       

    </div>
</body>
</html>
