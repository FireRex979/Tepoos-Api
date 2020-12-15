<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Komentar;
use Illuminate\Support\Facades\Auth;

class KomentarController extends Controller
{
    public function store(Request $request){
        $komentar = new Komentar();
        $komentar->id_user = $request->id_user;
        $komentar->id_postingan = $request->id_postingan;
        $komentar->komentar = $request->komentar;
        $komentar->save();

        $new_komentar = Komentar::with('user')->find($komentar->id);
        return response()->json(['data' => $new_komentar]);
    }
}
