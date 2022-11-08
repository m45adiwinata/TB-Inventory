<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubKategori;
use Validator;

class SubKategoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $data = SubKategori::with(['kategori' => function($query) {
            $query->select('id', 'nama_kategori');
        }])->get();

        return response()->json(['message' => 'oke', 'data' => $data], 200);
    }

    public function view($id)
    {
        $data = SubKategori::with('kategori')->find($id);

        return response()->json(['message' => 'oke', 'data' => $data], 200);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(),[ 
            'id_kategori' => 'required|integer',
            'kode_sub_kategori' => 'required|integer',
            'nama_sub_kategori' => 'required|string'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        $newsk = $validator->validated();
        $newsk['usercreate'] = auth()->user()->username;
        $sk = SubKategori::create($newsk);

        return response()->json([
            'message'=>'Sub kategori berhasil dibuat', 
            'data' => $sk
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[ 
            'id_kategori' => 'required|integer',
            'kode_sub_kategori' => 'required|integer',
            'nama_sub_kategori' => 'required|string'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        $newsk = $validator->validated();
        $newsk['usermodify'] = auth()->user()->username;
        SubKategori::where('id', $id)->update($newsk);
        $sk = SubKategori::find($id);

        return response()->json([
            'message'=>'Sub kategori berhasil diupdate', 
            'data' => $sk
        ], 201);
    }

    public function delete($id)
    {
        SubKategori::where('id', $id)->update([
            'userdelete' => auth()->user()->username,
            'deleted_at' => date("Y-m-d H:i:s")
        ]);

        return response()->json([
            'message'=>'Sub kategori berhasil dihapus'
        ], 201);
    }
}
