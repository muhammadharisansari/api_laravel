<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CobaController extends Controller
{
    public function update(Request $request)
    {
        dd($request->all());
    }
}
