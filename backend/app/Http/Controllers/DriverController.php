<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller {

    // 運転手の情報取得
    public function show(Request $request) {
        $user = $request->user();
        $user->load('driver');

        return $user;
    }

    // 運転手の情報更新
    public function update(Request $request) {
        // バリデーション
        $request->validate([
            'year' => 'required|numeric|between:2010,2024',
            'make' => 'required',
            'model' => 'required',
            'color' => 'required|alpha',
            'license_plate' => 'required',
            'name' => 'required'
        ]);

        $user = $request->user();

        $user->update($request->only('name'));

        // 運転手作成/更新
        $user->driver()->updateOrCreate($request->only([
            'year',
            'make',
            'model',
            'color',
            'license_plate'
        ]));

        $user->load('driver');

        return $user;
    }
}
