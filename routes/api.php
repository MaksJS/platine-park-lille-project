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
    \Carbon\Carbon::setLocale('fr');
    $places = json_decode(file_get_contents('data.json'), true);
    $buildings = ["m5", "m3"];
    foreach ($buildings as $building) {
        for ($i = 0; $i < count($places[$building]); $i++) {
            unset($places[$building][$i]['handicap']);
            unset($places[$building][$i]['center']);
            unset($places[$building][$i]['top_left_corner']);
            unset($places[$building][$i]['top_right_corner']);
            unset($places[$building][$i]['bottom_left_corner']);
            unset($places[$building][$i]['bottom_right_corner']);
            $data = \DB::table('spaces')
                ->where('building', '=', $building)
                ->where('space', '=', $places[$building][$i]['id'])
                ->orderBy('created_at', 'DESC')
                ->first();
            if ($data == null) {
                $places[$building][$i]['state'] = "UNKNOWN";
            }
            else {
                $places[$building][$i]['state'] = boolval($data->free) ? "FREE" : "TAKEN";
                $places[$building][$i]['message'] = (new \Carbon\Carbon($data->created_at))->diffForHumans(\Carbon\Carbon::now());
            }
        }
    }
    return response()->json($places, 200);
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