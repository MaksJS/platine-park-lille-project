<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/spaces', function (Request $request) {
    return response()->json(json_decode(file_get_contents('data.json'), true), 200);
})->middleware('api');

Route::get('/spaces/{building}', function (Request $request, $building) {
    $data = json_decode(file_get_contents('data.json'), true);
    if (array_key_exists($building, $data))
        return response()->json($data[$building], 200);
    else abort(404);
})->middleware('api');

Route::get('/space/{building}/{space}', function (Request $request, $building, $space) {
    $data = json_decode(file_get_contents('data.json'), true);    
    if (array_key_exists($building, $data) && array_key_exists($space, $data[$building]))  
        return response()->json($data[$building][$space], 200);
    else abort(404);
})->middleware('api');

Route::post('/space/{building}/{space}/occuped', function (Request $request, $building, $space) {
    \DB::table('spaces')->insert(
        [
            'building' => $building, 
            'space' => $space,
            'created_at' => new \DateTime(),
            'free' => false,
        ]
    );
    return response()->json(true, 200);
})->middleware('api');

Route::post('/space/{building}/{space}/free', function (Request $request, $building, $space) {
    \DB::table('spaces')->insert(
        [
            'building' => $building, 
            'space' => $space,
            'created_at' => new \DateTime(),
            'free' => true,
        ]
    );
    return response()->json(true, 200);
})->middleware('api');

Route::get('/space/{building}/{space}/last_state', function (Request $request, $building, $space) {
    $data = \DB::table('spaces')
        ->where('building', '=', $building)
        ->where('space', '=', $space)
        ->orderBy('created_at', 'DESC')
        ->first();
    \Carbon\Carbon::setLocale('fr');
    if ($data != null)
        return response()->json([
            'free' => boolval($data->free), 
            'timestamp' => (new \Carbon\Carbon($data->created_at))->diffForHumans(\Carbon\Carbon::now())
        ], 200);
    else abort(404);
})->middleware('api');