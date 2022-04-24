<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('curl', function (Request $request) {
    $data = $request->input('data', []);
    App\Helpers\Helper::convertToString($data);
    return \Illuminate\Support\Facades\Http::acceptJson()->withHeaders([
        'X-API-KEY' => $request->get('api_key'),
    ])->post($request->get('url'), $data)->json();
})->name('curl');


