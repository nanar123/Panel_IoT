<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemperatureController extends Controller
{
    function index() {
        return view('pages.temperature'); // file ekstensi blade.php di dalam folder resources/views -> resources/views/pages/temperature.blade.php
    }

}
