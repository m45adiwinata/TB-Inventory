<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubSegmen;
use Validator;

class SubSegmenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $data = SubSegmen::with(['segmen' => function($query) {
            $query->select('id', 'kode_segmen', 'nama_segmen');
        }]);
        if (isset($request->maxrows)) {
            if (isset($request->pagenum)) {
                $data = $data->skip(($request->pagenum-1) * $request->maxrows)->take($request->maxrows);
            } else {
                $data = $data->skip(0)->take($request->maxrows);
            }
        }
        $data = $data->get();

        return response()->json(['message' => 'oke', 'data' => $data], 200);
    }

    public function view($id)
    {
        $data = SubSegmen::with('segmen')->find($id);

        return response()->json(['message' => 'oke', 'data' => $data], 200);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(),[ 
            'id_segmen' => 'required|integer',
            'kode_sub_segmen' => 'required|integer',
            'nama_sub_segmen' => 'required|string'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        $newdata = $validator->validated();
        $newdata['usercreate'] = auth()->user()->username;
        $data = SubSegmen::create($newdata);

        return response()->json([
            'message'=>'Sub segmen berhasil dibuat', 
            'data' => $data
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[ 
            'id_segmen' => 'required|integer',
            'kode_sub_segmen' => 'required|integer',
            'nama_sub_segmen' => 'required|string'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        $newdata = $validator->validated();
        $newdata['usermodify'] = auth()->user()->username;
        SubSegmen::where('id', $id)->update($newdata);
        $data = SubSegmen::find($id);

        return response()->json([
            'message'=>'Sub segmen berhasil diupdate', 
            'data' => $data
        ], 201);
    }

    public function delete($id)
    {
        SubSegmen::where('id', $id)->update([
            'userdelete' => auth()->user()->username,
            'deleted_at' => date("Y-m-d H:i:s")
        ]);

        return response()->json([
            'message'=>'Sub segmen berhasil dihapus'
        ], 201);
    }
}
