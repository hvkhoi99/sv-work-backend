<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recruitment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function getTopRecruiters()
    {
        $users = User::withCount('recruitments')->orderBy('recruitments_count', 'desc')->take(9)->get();
        if (isset($users)) {
            // $recruitments->transform(function ($recruitment) {
            //     $recruitment->user = User::whereHas('recruitments', function ($q) use ($recruitment) {
            //         $q->where('id', $recruitment->id);
            //     })
            //         ->take(10)
            //         ->get();
            //     return $recruitment;
            // });
            $users = $users->reverse();
            $halved = $users->split(ceil($users->count()/2))->toArray();
    
            $newArray[0] = $halved[1];
            $newArray[1] = $halved[2];
            $newArray[2] = $halved[4];
            $newArray[3] = $halved[3];
            $newArray[4] = $halved[0];
            
            return response()->json([
                "status" => 1, 
                "code" => 200,
                "data" => $newArray
            ], 200);
        } else {
            return response()->json([
                'status' => 0,
                'code' => 404,
                'message' => 'Data was not found. Please try again later.'
            ], 404);
        }
    }
}
