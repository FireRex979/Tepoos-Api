<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FCM;
use Illuminate\Support\Facades\Auth;

class FCMController extends Controller
{
    public function store(Request $request){
        $fcm = FCM::where('id_user', Auth::user()->id)->first();
        if(isset($fcm)){
            $fcm->token = $request->token;
            $fcm->save();
        }else{
            $fcm = new FCM();
            $fcm->id_user = Auth::user()->id;
            $fcm->token = $request->token;
            $fcm->save();
        }
        return response()->json(['success' => true]);
    }
}
