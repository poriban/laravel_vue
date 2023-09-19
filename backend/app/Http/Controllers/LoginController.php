<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class LoginController extends Controller {
    // 携帯の番号取得 DBになければ、新規追加
    public function submit(Request $request) {
        // バリデーション
        $request->validate([
            'phone' => 'required|numeric|min:10'
        ]);

        // DB検索
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        // ユーザーがない場合
        if (!$user) {
            return response()->json(['message' => 'Could not process a user with that phone number.'], 401);
        }

        // ユーザーに通知
        $user->notify(new LoginNeedsVerification());

        // json形式でメッセージ通知
        return response()->json(['message' => 'テキストメッセージ通知が送信されました。']);
    }

    public function verify(Request $request) {
        // バリデーション
        $request->validate([
            'phone' => 'required|numeric|min:10',
            'login_code' => 'required|numeric|between:111111,999999'
        ]);

        // 携帯番号とログインコードの一致チェック
        $user = User::where('phone', $request->phone)
            ->where('login_code', $request->login_code)
            ->first();

        // 一致した場合、再びログインコードを破棄
        if ($user) {
            $user->update([
                'login_code' => null
            ]);
            
            return $user->createToken($request->login_code)->plainTextToken;
        }

        // json形式でメッセージ通知
        return response()->json(['message' => '無効な認証コードです。'], 401);
    }
}
