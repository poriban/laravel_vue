<?php

namespace App\Http\Controllers;

use App\Events\TripAccepted;
use App\Events\TripCreated;
use App\Events\TripEnded;
use App\Events\TripLocationUpdated;
use App\Events\TripStarted;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller {

    // 旅先を登録する
    public function store(Request $request) {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required'
        ]);

        $trip = $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name'
        ]));

        // 作成イベント
        TripCreated::dispatch($trip, $request->user());

        return $trip;
    }

    // 旅先を表示
    public function show(Request $request, Trip $trip) {
        if ($trip->user->id === $request->user()->id) {
            return $trip;
        }

        if ($trip->driver && $request->user()->driver) {
            if ($trip->driver->id === $request->user()->driver->id) {
                return $trip;
            }
        }

        return response()->json(['message' => '目的地が見つかりません。'], 404);
    }

    // 運転手が旅先を承認
    public function accept(Request $request, Trip $trip) {
        
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_id' => $request->user()->id,
            'driver_location' => $request->driver_location,
        ]);

        $trip->load('driver.user');

        // 承認イベント
        TripAccepted::dispatch($trip, $trip->user);

        return $trip;
    }

    // 運転手が旅先を開始
    public function start(Request $request, Trip $trip) {
        $trip->update([
            'is_started' => true
        ]);

        $trip->load('driver.user');

        // 開始イベント
        TripStarted::dispatch($trip, $trip->user);

        return $trip;
    }

    // 運転手が旅先を終了
    public function end(Request $request, Trip $trip) {
        $trip->update([
            'is_complete' => true
        ]);

        $trip->load('driver.user');

        // 終了イベント
        TripEnded::dispatch($trip, $trip->user);

        return $trip;
    }

    // 運転手の現在の位置情報を更新
    public function location(Request $request, Trip $trip) {
        $request->validate([
            'driver_location' => 'required'
        ]);

        // 運転手の位置を更新
        $trip->update([
            'driver_location' => $request->driver_location
        ]);

        $trip->load('driver.user');

        // 更新イベント
        TripLocationUpdated::dispatch($trip, $trip->user);

        return $trip;
    }
}
