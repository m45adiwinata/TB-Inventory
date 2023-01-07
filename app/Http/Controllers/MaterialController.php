<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use Validator;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['createExcelFormat']]);
    }

    public function index(Request $request)
    {
        $data = Material::with('subSegmen')->with('stock');
        if (isset($request->barcode)) {
            if ($request->barcode != "") {
                $data = $data->where('barcode', $request->barcode);
            }
        }
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

    public function view($id)
    {
        $data = Material::find($id);

        return response()->json(['message' => 'oke', 'data' => $data], 200);
    }

    public function createExcelFormat()
    {
        $subsegmen = DB::table('sub_segmen')
                        ->whereNull('deleted_at')
                        ->select('nama_sub_segmen')
                        ->orderBy('nama_sub_segmen')
                        ->get()->toArray();
        
        $subsegmen = array_map(function($e) {
            return $e->nama_sub_segmen;
        }, $subsegmen);
        ob_start();
        $spreadsheet = new Spreadsheet();
        $myWorkSheet = new Worksheet($spreadsheet, 'sub segmen');
        $spreadsheet->addSheet($myWorkSheet, 1);
        $sheet = $spreadsheet->getActiveSheet()->setTitle("material");
        $sheet2 = $spreadsheet->getSheetByName('sub segmen');
        $sheet->mergeCells("A1:J1");
        $sheet->setCellValue('A1', 'TEMAN BUNDA INVENTORY');
        $sheet->mergeCells("A2:J2");
        $sheet->setCellValue('A2', 'IMPORT MATERIAL');
        $sheet->setCellValue('A4', 'SUB SEGMEN');
        $sheet->setCellValue('B4', 'NAMA');
        $sheet->setCellValue('C4', 'BARCODE');
        $sheet->setCellValue('D4', 'SKU');
        $sheet->setCellValue('E4', 'CLASS');
        $sheet->setCellValue('F4', 'TOTAL STOCK');
        $sheet->setCellValue('G4', 'UNIT STOCK');
        $sheet->setCellValue('H4', 'COGS');
        $sheet->setCellValue('I4', 'VENDOR');
        $sheet->setCellValue('J4', 'VENDOR PRICE');
        $j = 1;
        foreach ($subsegmen as $key => $s) {
            $sheet2->setCellValue("A$j", $s);
            $j++;
        }
        $validation = $sheet->getCell("A5")->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Value is not in list.');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please pick a value from the drop-down list.');
        $validation->setFormula1('\'sub segmen\'!$A$1:$A$'.($j-1));
        $validation->setSqref("A5:A15");
        
        $filename = 'TBInventory - Template Import Material.xlsx';
        try {
            ob_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit(0);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(),[ 
            'file' => 'required|mimes:xlsx'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);
        }
        if ($file = $request->file('file')) {
            $spreadsheet = IOFactory::load($file);
            $data = $spreadsheet->getActiveSheet()->toArray();
            
            dd($data);
            
        }
    }
}
