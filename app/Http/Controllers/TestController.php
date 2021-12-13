<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        $days = array_rand(array_flip(range(1, 20)), 10);


        return response()->json([
            'data' => $days
        ], 200);
    }
}
