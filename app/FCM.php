<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FCM extends Model
{
    protected $table = 'fcm';

    public static function cobaKirim($fcm_token, $id_postingan, $title, $body)
    {
        $response = fcm()
            ->to([$fcm_token])
            ->priority('normal')
            ->timeToLive(0)
            ->data([
                'id_postingan' => $id_postingan
            ])
            ->notification([
                'title' => $title,
                'body' => $body,
            ])
            ->send();

        return $response;
    }
}
