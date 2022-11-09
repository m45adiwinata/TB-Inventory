<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Segmen;
use Validator;

class SegmenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $data = Segmen::with(['subKategori' => function($query) {
            $query->select('id', 'kode_sub_kategori', 'nama_sub_kategori');
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
        $data = Segmen::with('subKategori')->find($id);

        return response()->json(['message' => 'oke', 'data' => $data], 200);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(),[ 
            'id_sub_kategori' => 'required|integer',
            'kode_segmen' => 'required|integer',
            'nama_segmen' => 'required|string'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        $newdata = $validator->validated();
        $newdata['usercreate'] = auth()->user()->username;
        $data = Segmen::create($newdata);

        return response()->json([
            'message'=>'Segmen berhasil dibuat', 
            'data' => $data
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[ 
            'id_sub_kategori' => 'required|integer',
            'kode_segmen' => 'required|integer',
            'nama_segmen' => 'required|string'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        $newdata = $validator->validated();
        $newdata['usermodify'] = auth()->user()->username;
        Segmen::where('id', $id)->update($newdata);
        $data = Segmen::find($id);

        return response()->json([
            'message'=>'Segmen berhasil diupdate', 
            'data' => $data
        ], 201);
    }

    public function delete($id)
    {
        Segmen::where('id', $id)->update([
            'userdelete' => auth()->user()->username,
            'deleted_at' => date("Y-m-d H:i:s")
        ]);

        return response()->json([
            'message'=>'Segmen berhasil dihapus'
        ], 201);
    }
}
