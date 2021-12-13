<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiCertificateRequest;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
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

            $certificates = Certificate::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(5);

            if (isset($certificates)) {
                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $certificates
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Certificate list does not exist.'
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
    public function store(ApiCertificateRequest $request)
    {
        $user = $request->user();

        if (isset($user)) {

            $new_certificate = Certificate::create([
                'title' => $request['title'],
                'issuing_organization' => $request['issuing_organization'],
                'description' => $request['description'],
                'image_link' => $request['image_link'],
                'user_id' => $user->id
            ]);

            return response()->json([
                'status' => 1,
                'code' => 200,
                'data' => $new_certificate
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
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        if (isset($user)) {
            
            $certificate = Certificate::whereId($id)->where('user_id', $user->id)->first();

            if (isset($certificate)) {
                $certificate->update($request->all());
                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'data' => $certificate
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
            
            $certificate = Certificate::whereId($id)->where('user_id', $user->id)->first();

            if (isset($certificate)) {
                $certificate->delete();
                return response()->json([
                    'status' => 1,
                    'code' => 200,
                    'message' => 'Successfully deleted.'
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'code' => 404,
                    'message' => 'Your certificate was not found or has not been created.'
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
