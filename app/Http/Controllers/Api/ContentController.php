<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    public function termByState(Request $request)
    {
        $resp = "All India Terms data";

        if (!empty($request->state_id)) {
            $state = DB::table('states')->where('id', $request->state_id)->first();

            if (empty($state)) {
                $data = DB::table('term_by_states')->where('state_id', 0)->first();

                return response()->json([
                    'error'     => false,
                    'resp'      => $resp,
                    'data'      => $data
                ]);
            }

            $data = DB::table('term_by_states')->where('state_id', $request->state_id)->first();

            if (empty($data)) {
                $data = DB::table('term_by_states')->where('state_id', 0)->first();
            } else {
                $resp =  $state->name ." Terms data";
            }

            return response()->json([
                'error'     => false,
                'resp'      => $resp,
                'data'      => $data
            ]);
        } else {
            $data = DB::table('term_by_states')->where('state_id', 0)->first();

            return response()->json([
                'error'     => false,
                'resp'      => $resp,
                'data'      => $data
            ]);
        }
    }

}
