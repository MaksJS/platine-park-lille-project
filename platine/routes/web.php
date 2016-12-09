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
    return response()->json(json_decode(file_get_contents('data.json'), true));
});

Route::get('/spaces/{building}', function (Request $request, $building) {
    $data = json_decode(file_get_contents('data.json'), true);
    if (array_key_exists($building, $data))
        return response()->json($data[$building]);
    else abort(404);
});

Route::get('/space/{building}/{id}', function (Request $request, $building, $id) {
    $data = json_decode(file_get_contents('data.json'), true);    
    if (array_key_exists($building, $data) && array_key_exists($id, $data[$building]))  
        return response()->json($data[$building][$id]);
    else abort(404);
});

Route::post('/space/{building}/{id}/occuped', function (Request $request) {
    
});

Route::post('/space/{building}/{id}/free', function (Request $request) {
    
});