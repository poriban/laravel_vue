<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class LoginNeedsVerification extends Notification
{
    use Queueable;

    /**
     * toTwilio SMSメッセージ
     */
    public function via(object $notifiable): array {
        return [TwilioChannel::class];
    }

    /**
     * login_codeメッセージ
     */
    public function toTwilio($notifiable) {
        $loginCode = rand(111111, 999999);

        // ログインコード 該当する電話番号のユーザー情報に追加
        $notifiable->update([
            'login_code' => $loginCode
        ]);

        //メッセージの内容
        return (new TwilioSmsMessage())->content("ログインコードは、{$loginCode}です。");
    }
}
