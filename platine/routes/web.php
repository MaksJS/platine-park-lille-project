<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    abort(403);
});

Route::get('/spaces', function (Request $request) {
    return response()->json(json_decode(file_get_contents('data.json'), true), 200);
});

Route::get('/spaces/{building}', function (Request $request, $building) {
    $data = json_decode(file_get_contents('data.json'), true);
    if (array_key_exists($building, $data))
        return response()->json($data[$building], 200);
    else abort(404);
});

Route::get('/space/{building}/{space}', function (Request $request, $building, $space) {
    $data = json_decode(file_get_contents('data.json'), true);    
    if (array_key_exists($building, $data) && array_key_exists($space, $data[$building]))  
        return response()->json($data[$building][$space], 200);
    else abort(404);
});

Route::post('/space/{building}/{space}/occuped', function (Request $request, $building, $space) {
    \DB::table('spaces')->insert(
        [
            'building' => $building, 
            'space' => $space,
            'free' => false,
        ]
    );
    return response()->json(true, 200);
});

Route::post('/space/{building}/{space}/free', function (Request $request, $building, $space) {
    \DB::table('spaces')->insert(
        [
            'building' => $building, 
            'space' => $space,
            'free' => true,
        ]
    );
    return response()->json(true, 200);
});

Route::get('/space/{building}/{space}/last_state', function (Request $request, $building, $space) {
    $data = \DB::table('spaces')
        ->where('building', '=', $building)
        ->where('space', '=', $space)
        ->orderBy('created_at', 'DESC')
        ->first();
    if ($data != null)
        return response()->json(['free' => $data['free'], 'timestamp' => $data['created_at']], 200);
    else abort(404);
});