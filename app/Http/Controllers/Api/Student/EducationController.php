<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiEducationRequest;
use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (isset($user)) {

            $educations = Education::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(5);

            if (isset($educations)) {
                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $educations
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Education list does not exist.'
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 0,
                'code' => 401,
                'message' => 'UNAUTHORIZED'
            ], 401);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ApiEducationRequest $request)
    {
        $user = $request->user();

        if (isset($user)) {

            $new_education = Education::create([
                'major' => $request['major'],
                'school' => $request['school'],
                'from_date' => $request['from_date'],
                'to_date' => $request['to_date'],
                'achievements' => $request['achievements'],
                'user_id' => $user->id
            ]);

            return response()->json([
                'status' => 1,
                'code' => 200,
                'data' => $new_education
            ], 200);

        } else {
            return response()->json([
                'status' => 0,
                'code' => 401,
                'message' => 'UNAUTHORIZED'
            ], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ApiEducationRequest $request, $id)
    {
        $user = $request->user();
        
        if (isset($user)) {
            
            $education = Education::whereId($id)->where('user_id', $user->id)->first();

            if (isset($education)) {
                $education->update($request->all());
                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $education
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 400,
                    'message' => 'Something went wrong, please try again later.'
                ], 400);
            }

        } else {
            return response()->json([
                'status' => 0,
                'code' => 401,
                'message' => 'UNAUTHORIZED'
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        if (isset($user)) {
            
            $education = Education::whereId($id)->where('user_id', $user->id)->first();

            if (isset($education)) {
                $education->delete();
                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'message' => 'Successfully deleted.'
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Your education was not found or has not been created.'
                ], 404);
            }

        } else {
            return response()->json([
                'status' => 0,
                'code' => 401,
                'message' => 'UNAUTHORIZED'
            ], 401);
        }
    }
}
