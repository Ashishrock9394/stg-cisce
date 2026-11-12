<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Shareyourstory;
use Illuminate\Support\Facades\DB;


class YourStoriesController extends Controller
{
    public function yourstory(){
		try{
			return view('api.your-stories');
		} catch(Exception $e) { 
		   
			return Response::json(array(
					'status'    => 'error',
					'code'      =>  404,
					'message'   =>  'Unauthorized : '.$e->getmessage()
				), 404);
			

		}
    }

    public function sharestory(){
		try{
			return view('api.share-your-story');
		} catch(Exception $e) { 
		   
			return Response::json(array(
					'status'    => 'error',
					'code'      =>  404,
					'message'   =>  'Unauthorized : '.$e->getmessage()
				), 404);
			

		}
    }

    public function storeyourstory(Request $request) 
    {
		try{
		//return  $request;
        $image = '';
        $year = date("Y/m"); 
        if($request->file('image'))
        {
            $image = $request->file('image')->store($year,['disk'=> 'uploads']);
            $image = url('wp-content/uploads/'.$image);
        }
        $request->validate([
            'youare' => 'required|string|min:3|max:255',
            'designation' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255',
            'fullname' => 'required|string|min:3|max:255',
            'videourl' => 'required|string|min:3|max:255',
            'title' => 'required|string|min:3|max:255',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'story' => 'required|string|min:3|max:255'
        ]);
         
        $yourstory = new Shareyourstory;
        $yourstory->youare = $request->youare;
        $yourstory->designation = $request->designation;
        $yourstory->email = $request->email;
        $yourstory->fullname = $request->fullname;
        $yourstory->videourl = $request->videourl;
        $yourstory->title = $request->title;
        $yourstory->image = $image;
        $yourstory->story = $request->story;
        $yourstory->save();
        return redirect('share-your-story')->with('message',"Insert successfully");
		
		} catch(Exception $e) { 
		   
			return Response::json(array(
					'status'    => 'error',
					'code'      =>  404,
					'message'   =>  'Unauthorized : '.$e->getmessage()
				), 404);
			

		}
		
    }
}