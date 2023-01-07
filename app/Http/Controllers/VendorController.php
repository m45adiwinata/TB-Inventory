<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['createExcelFormat']]);
    }

    public function index(Request $request)
    {
        $data = Vendor::where('id', '!=', 0);
        $count = $data->count();
        if (isset($request->maxrows)) {
            if (isset($request->pagenum)) {
                $data = $data->skip(($request->pagenum-1) * $request->maxrows)->take($request->maxrows);
            } else {
                $data = $data->skip(0)->take($request->maxrows);
            }
        }
        $data = $data->get();

        return response()->json(['message' => 'oke', 'jmldata' => $count, 'data' => $data], 200);
    }

    public function view($id)
    {
        $data = Vendor::find($id);

        return response()->json(['message' => 'oke', 'data' => $data], 200);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(),[ 
            'kode_vendor' => 'required|string',
            'type' => 'required|string',
            'nama_vendor' => 'required|string',
            'alamat' => 'required|string',
            'country' => 'required|string'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        $newdata = $request->toArray();
        $newdata['usercreate'] = auth()->user()->username;
        try {
            $data = Vendor::create($newdata);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 400);
        }

        return response()->json([
            'message'=>'Vendor berhasil dibuat', 
            'data' => $data
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[ 
            'kode_vendor' => 'required|string',
            'type' => 'required|string',
            'nama_vendor' => 'required|string',
            'alamat' => 'required|string',
            'country' => 'required|string'
        ]);
        if($validator->fails()) {          
            return response()->json(['error'=>$validator->errors()], 401);                        
        }
        $newdata = $request->toArray();
        $newdata['usermodify'] = auth()->user()->username;
        try {
            Vendor::where('id', $id)->update($newdata);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 400);
        }
        $data = Vendor::find($id);

        return response()->json([
            'message'=>'Vendor berhasil diupdate', 
            'data' => $data
        ], 201);
    }

    public function delete($id)
    {
        Vendor::where('id', $id)->update([
            'userdelete' => auth()->user()->username,
            'deleted_at' => date("Y-m-d H:i:s")
        ]);

        return response()->json([
            'message'=>'Vendor berhasil dihapus'
        ], 201);
    }

    public function createExcelFormat()
    {
        ob_start();
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $styleOutline = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $styleBold = [
            'font' => [
                'bold' => true
            ]
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle("material");
        $sheet->mergeCells("A1:H1");
        $sheet->setCellValue('A1', 'TEMAN BUNDA INVENTORY')->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->mergeCells("A2:H2");
        $sheet->setCellValue('A2', 'IMPORT MATERIAL')->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('A4', 'Type (*)')->getColumnDimension("A")->setWidth(20);
        $sheet->setCellValue('B4', 'Kode (*)')->getColumnDimension("B")->setWidth(20);
        $sheet->setCellValue('C4', 'Nama Vendor (*)')->getColumnDimension("C")->setWidth(20);
        $sheet->setCellValue('D4', 'Alamat (*)')->getColumnDimension("D")->setWidth(20);
        $sheet->setCellValue('E4', 'City')->getColumnDimension("E")->setWidth(20);
        $sheet->setCellValue('F4', 'Country (*)')->getColumnDimension("F")->setWidth(20);
        $sheet->setCellValue('G4', 'Email')->getColumnDimension("G")->setWidth(20);
        $sheet->setCellValue('H4', 'Handphone')->getColumnDimension("H")->setWidth(20);
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
        $validation->setFormula1('"Regular Suplier,Consignment,Consigment Open Price,GA"');
        $validation->setSqref("A5:A15");
        $sheet->getStyle("A1:A2")->applyFromArray($styleBold);
        $sheet->getStyle("A4:H15")->applyFromArray($styleBorder);
        $sheet->getStyle("A4:H4")->applyFromArray($styleBold);
        $sheet->getStyle("H5:H15")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        
        $filename = 'TBInventory - Template Import Vendor.xlsx';
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
            
            $newData = array();
            foreach ($data as $key => $d) {
                if ($key > 3) {
                    if (!is_null($d[0])) {
                        array_push($newData, 
                            array(
                                'kode_vendor' => $d[1],
                                'type' => $d[0],
                                'nama_vendor' => $d[2],
                                'alamat' => $d[3],
                                'city' => $d[4],
                                'country' => $d[5],
                                'email' => $d[6],
                                'hp' => $d[7],
                                'usercreate' => auth()->user()->username,
                                'created_at' => date('Y-m-d H:i:s')
                            )
                        );
                    }
                }
            }
            // dd($newData);
            DB::transaction(function () use ($newData) {
                DB::table('vendor')->insert($newData);
            });
            // $data = Vendor::create($newData[0]);

            return response()->json([
                'message'=>'Vendor berhasil diimport'
            ], 201);
        }
    }
}
