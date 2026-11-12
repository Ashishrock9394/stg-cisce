<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use App\Http\Controllers\Api\CicseApiController;
use App\Http\Controllers\Api\Fitness365Controller;



Route::post('register-client', [App\Http\Controllers\Api\ApiRegistrationController::class, 'register']);
Route::post('get-token', [App\Http\Controllers\Api\ApiRegistrationController::class, 'getAuthorisedToken'])->name('api.token');
// Route::post('/oauth/token', [AccessTokenController::class, 'issueToken']);

Route::get('testapi', function(Request $request){

    echo "string"; exit();
    echo "Authorization via Laravel: ";
    var_dump($request->header('Authorization'));

    echo "\nAuthorization via \$_SERVER: ";
    var_dump($_SERVER['HTTP_AUTHORIZATION'] ?? 'Not Set');

    echo "\n\nAll headers:\n";
    print_r($request->headers->all());

    exit;

});



// state wise data !working
Route::get('/states-data', [Fitness365Controller::class, 'stateData']);
Route::get('/states-details/{stateCode}', [Fitness365Controller::class, 'stateDetails']);
Route::get('/states-fitness-data', [Fitness365Controller::class, 'stateWiseHealthData']);
Route::get('/district-fitness-data', [Fitness365Controller::class, 'districtWiseHealthData']);
Route::get('/national-fitness-series', [Fitness365Controller::class, 'nationalFitnessSeries']);


Route::prefix('studentdata')->middleware('client')->group(function () {

	Route::post('/', [App\Http\Controllers\Api\CicseApiController::class, 'fetchAllSchoolSummary']);
    Route::post('/status', [App\Http\Controllers\Api\CicseApiController::class, 'checkSchoolData']);
    Route::post('/fetch', [App\Http\Controllers\Api\CicseApiController::class, 'fetchStudentsByClassSection']); 


   
    Route::post('/fetchstudents', [App\Http\Controllers\Api\CicseApiController::class, 'fetchStudentsBySchoolCode']);
    Route::post('/updatebatchstatus', [App\Http\Controllers\Api\CicseApiController::class, 'updateBatchStatus']);

     /* Kept Only for Testing Purpose */
    Route::post('/test', [App\Http\Controllers\Api\CicseApiController::class, 'TestfetchAllSchoolSummary']);
    Route::post('/fetch/test', [App\Http\Controllers\Api\CicseApiController::class, 'TestfetchStudentsByClassSection']);

});

