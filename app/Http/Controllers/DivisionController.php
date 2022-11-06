<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Models\Division;

class DivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index()
    {
        return response()->json(['message'=>'oke', 'data' => Division::get()], 200);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(),[ 
            'code' => 'required|integer',
            'division_name' => 'required|string'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        $division = Division::create($validator->validated());

        return response()->json([
            'message'=>'Division berhasil dibuat', 
            'data' => $division
        ], 201);
    }

    public function update(Request $request, $code)
    {
        $validator = Validator::make($request->all(),[ 
            'division_name' => 'required|string'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        Division::where('code', $code)->update($validator->validated());

        return response()->json([
            'message'=>'Division berhasil diupdate'
        ], 201);
    }

    public function delete($code)
    {
        Division::where('code', $code)->delete();

        return response()->json([
            'message'=>'Division berhasil dihapus'
        ], 201);
    }
}
