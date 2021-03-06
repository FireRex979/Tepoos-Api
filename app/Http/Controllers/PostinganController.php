<?php

namespace App\Http\Controllers;
use App\Postingan;
use App\Komentar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class PostinganController extends Controller
{

    public function index(){
        $postingan = Postingan::with('user', 'komentar')->orderby('created_at', 'desc')->get()->map(function($item){
            $item->user->foto_profile = $item->user->getUrlFoto();
            $item->foto = $item->getUrlFoto();
            $item->tgl_postingan = Carbon::parse($item->tgl_postingan)->format('m F Y | H:i');
            return $item;
        });
        return response()->json(['data' => $postingan]);
    }

    public function postinganUser(){
        $postingan = Postingan::where('id_user', Auth::user()->id)->orderby('created_at', 'desc')->get()->map(function($item){
            $item->foto = $item->getUrlFoto();
            $item->tgl_postingan = Carbon::parse($item->tgl_postingan)->format('m F Y | H:i');
            return $item;
        });
        return response()->json(['data' => $postingan]);
    }

    public function store(Request $request){
        try {
            DB::beginTransaction();
            $filename = time().Str::random(3).'.'.$request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move(storage_path('app/postingan'), $filename);
            $postingan = new Postingan;
            $postingan->id_user = $request->id_user;
            $postingan->caption = $request->caption;
            $postingan->tgl_postingan = NOW();
            $postingan->foto = 'app/postingan/'.$filename;
            $postingan->like = 0;
            $postingan->save();
            DB::commit();
            $postingan->foto = $postingan->getUrlFoto();
            $postingan->tgl_postingan = Carbon::parse($postingan->tgl_postingan)->format('m F Y | H:i');
            $postingan->id_postingan = $postingan->id;
            return response()->json(['success' => true, 'data' => $postingan]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false]);
        }

    }

    public function show($id){
        $postingan = Postingan::with('user')->find($id);
        $postingan->foto = $postingan->getUrlFoto();
        $postingan->user->foto_profile = $postingan->user->getUrlFoto();
        $postingan->tgl_postingan = Carbon::parse($postingan->tgl_postingan)->format('m F Y | H:i');
        $komentar = Komentar::with('user')->where('id_postingan', $id)->get()->map(function($item){
            $item->user->foto_profile = $item->user->getUrlFoto();
            return $item;
        });
        return response()->json(['postingan' => $postingan, 'komentar' => $komentar]);
    }

    public function update(Request $request){
        DB::beginTransaction();
        try {

            $postingan = Postingan::find($request->id);

            if($request->file('foto') != null){
                File::delete(storage_path($postingan->foto));
                $filename = time().Str::random(3).'.'.$request->file('foto')->getClientOriginalExtension();
                $request->file('foto')->move(storage_path('app/postingan'), $filename);
                $postingan->foto = 'app/postingan/'.$filename;
            }

            $postingan->caption = $request->caption;
            $postingan->save();
            DB::commit();

            $postingan->foto = $postingan->getUrlFoto();
            $postingan->tgl_postingan = Carbon::parse($postingan->tgl_postingan)->format('m F Y | H:i');
            $postingan->id_postingan = $postingan->id;

            return response()->json(['success' => true, 'data' => $postingan]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false]);
        }
    }

    public function destroy($id){
        try {
            DB::beginTransaction();
            $postingan = Postingan::find($id);
            File::delete(storage_path($postingan->foto));
            foreach($postingan->komentar as $komentar){
                $komentar->delete();
            }
            $postingan->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false]);
        }
    }

    public function getFotoPostingan($id){
        $postingan = Postingan::find($id);
        return response()->file(
            storage_path($postingan->foto)
        );
    }
}
