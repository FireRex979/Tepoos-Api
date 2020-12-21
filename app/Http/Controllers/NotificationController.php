<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class NotificationController extends Controller
{
    public function getNotif(){
        $notif = Notification::where('id_user', Auth::user()->id)->orderby('created_at', 'desc')->limit(5)->get()->map(function($item){
            $item->tgl_komentar = $item->created_at->format('d/m/Y H:i');
            return $item;
        });
        return response()->json(['data' => $notif]);
    }

    public function readNotif($id){
        try {
            DB::beginTransaction();
            $notif = Notification::find($id);
            $notif->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => true]);
        }
    }
}
