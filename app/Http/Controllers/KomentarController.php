<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Komentar;
use App\Notification;
use App\User;
use App\FCM;
use App\Postingan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class KomentarController extends Controller
{
    public function store(Request $request){
        try {
            DB::beginTransaction();
            $komentar = new Komentar();
            $komentar->id_user = $request->id_user;
            $komentar->id_postingan = $request->id_postingan;
            $komentar->komentar = $request->komentar;
            $komentar->save();

            $user = User::find($request->id_user);
            $postingan = Postingan::find($request->id_postingan);
            $fcm = FCM::where('id_user', $postingan->id_user)->first();

            $title = $user->name;
            $body = 'Mengomentari Postinganmu : "'.$request->komentar.'"';
            $fcm_token = $fcm->token;

            if($postingan->id_user != $request->id_user){
                FCM::cobaKirim($fcm_token, $request->id_postingan, $title, $body);

                $notif = new Notification();
                $notif->title = $title;
                $notif->body = $body;
                $notif->id_postingan = $request->id_postingan;
                $notif->id_user = $postingan->id_user;
                $notif->save();
            }

            DB::commit();

            $new_komentar = Komentar::with('user')->find($komentar->id);
            return response()->json(['data' => $new_komentar]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false]);
        }
    }
}
