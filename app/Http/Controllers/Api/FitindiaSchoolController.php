<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class FitindiaSchoolController extends Controller
{
	public function fitindiaschool(){
    	return view('api.fit-india-school');
	}

	public function fitindscreg(){
		return view('api.fit-india-school-registration');
	}

	public function fitindiacertification(){
		return view('api.fit-india-school-certification');
	}

	public function createevent(){
		return view('api.create-event');
	}

	public function myevent(){
		return view('api.my-events');
	}
}
