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
    
    public function index()
    {
        return response()->json(['message'=>'oke', 'data' => Kategori::get()], 200);
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
        $kategori = Kategori::create($validator->validated());

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
        Kategori::where('id', $id)->update($validator->validated());

        return response()->json([
            'message'=>'Kategori berhasil diupdate'
        ], 201);
    }

    public function delete($id)
    {
        Kategori::where('id', $id)->delete();

        return response()->json([
            'message'=>'Kategori berhasil dihapus'
        ], 201);
    }
}
