<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class FitindSch2020Controller extends Controller
{
    public function fitindsch2020(){
    	return view('api.fit-india-school-week-2020');
    }

    public function fitindCyclothon2020(){
    	return view('api.fit-india-cyclothon-2020');
    }

    public function fitindPrabhatpheri2020(){
    	return view('api.fit-india-prabhatpheri-2020');
    }
}
