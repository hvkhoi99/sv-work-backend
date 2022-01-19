<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {
        // $days = array_rand(array_flip(range(1, 20)), 10);
        $my_array = array(
            (object) [
                'value' => 'php 1',
                'label' => 'php 1'
            ],
            (object) [
                'value' => 'php 2',
                'label' => 'php 2'
            ],
            (object) [
                'value' => 'php 3',
                'label' => 'php 3'
            ]
        );

        $my_array = array_map(function($o) { return collect($o)->only(['label']); }, $my_array);

        return response()->json([
            'data' => $my_array
        ], 200);
    }
}
