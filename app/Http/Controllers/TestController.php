<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {
        // $days = array_rand(array_flip(range(1, 20)), 10);
        // $my_array = json_encode(array(
        //     (object) [
        //         'value' => 'php 1',
        //         'label' => 'php 1'
        //     ],
        //     (object) [
        //         'value' => 'php 2',
        //         'label' => 'php 2'
        //     ],
        //     (object) [
        //         'value' => 'php 3',
        //         'label' => 'php 3'
        //     ]
        // ));

        $obj = $request->all();

        return response()->json([
            'data' => $obj
        ], 200);
    }
}
