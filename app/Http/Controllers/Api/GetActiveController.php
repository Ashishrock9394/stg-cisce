<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class GetActiveController extends Controller
{
    public function getactive(){
    	return view('api.get-active');
    }
}
