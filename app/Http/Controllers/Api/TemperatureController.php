<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Temperature;
use Illuminate\Http\Request;

class TemperatureController extends Controller
{
    // bikin 2 hal
    // 1. method untuk menampilkan data
    // 2. method untuk menyimpan data/ store

    public function index()
    {
        $temperatures = Temperature::orderBy('created_at', 'desc')->get();
         // select * from temperatures order by created_at desc

        return response()->json([
            'status' => 'success',
            'message' => 'List data temperatures',
            'data' => $temperatures
        ]);
    }

    public function store(Request $request){
        $value = $request->input('value');

        $temperatures = Temperature::create([
            'value' => $value
        ]);

        // $temperatures = new Temperature();
        // $temperatures->value = $value;
        // $temperatures->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data temperature berhasil disimpan',
            'data' => $temperatures
        ]);
    }
}
