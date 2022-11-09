<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use Validator;

class KategoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(Request $request)
    {
        $data = Kategori::with('division');
        if (isset($request->maxrows)) {
            if (isset($request->pagenum)) {
                $data = $data->skip(($request->pagenum-1) * $request->maxrows)->take($request->maxrows);
            } else {
                $data = $data->skip(0)->take($request->maxrows);
            }
        }
        $data = $data->get();

        return response()->json(['message'=>'oke', 'data' => $data], 200);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(),[ 
            'type' => 'required|string',
            'division_code' => 'required|integer',
            'kode_kategori' => 'required|integer',
            'nama_kategori' => 'required|string',
            'sales' => 'required',
            'grosir_sales' => 'required',
            'delivery_order' => 'required',
            'expense_aset' => 'required',
            'cogs' => 'required',
            'grosir_cogs' => 'required',
            'inventory' => 'required',
            'grosir_inventory' => 'required',
            'adjustment' => 'required',
            'grosir_adjustment' => 'required',
            'bonus_item' => 'required',
            'costing_expense' => 'required'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        $newdata = $validator->validated();
        $newdata['usercreate'] = auth()->user()->username;
        $kategori = Kategori::create($newdata);

        return response()->json([
            'message'=>'Kategori berhasil dibuat', 
            'data' => $kategori
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[ 
            'type' => 'required|string',
            'division_code' => 'required|integer',
            'kode_kategori' => 'required',
            'nama_kategori' => 'required',
            'sales' => 'required',
            'grosir_sales' => 'required',
            'delivery_order' => 'required',
            'expense_aset' => 'required',
            'cogs' => 'required',
            'grosir_cogs' => 'required',
            'inventory' => 'required',
            'grosir_inventory' => 'required',
            'adjustment' => 'required',
            'grosir_adjustment' => 'required',
            'bonus_item' => 'required',
            'costing_expense' => 'required'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        $newdata = $validator->validated();
        $newdata['usermodify'] = auth()->user()->username;
        Kategori::where('id', $id)->update($newdata);
        $data = Kategori::find($id);

        return response()->json([
            'message' => 'Kategori berhasil diupdate',
            'data' => $data
        ], 201);
    }

    public function delete($id)
    {
        Kategori::where('id', $id)->update([
            'userdelete' => auth()->user()->username,
            'deleted_at' => date("Y-m-d H:i:s")
        ]);

        return response()->json([
            'message'=>'Kategori berhasil dihapus'
        ], 201);
    }
}
