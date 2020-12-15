<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use File;
use Image;
class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);
        if($request->kelamin == 'Laki-laki'){
            $kelamin = 'l';
        }else{
            $kelamin = 'p';
        }
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'tgl_lahir' => $request->tgl_lahir,
            'kelamin' => $kelamin,
            'foto_profile' => 'app/user/profile.png',
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    public function updateProfile(Request $request, $id){
        $request->validate([
            'name' => 'required|string',
        ]);
        if($request->kelamin == 'Laki-laki'){
            $kelamin = 'l';
        }else{
            $kelamin = 'p';
        }
        $user = User::find($id);
        $user->name = $request->name;
        $user->tgl_lahir = $request->tgl_lahir;
        $user->kelamin = $kelamin;
        $user->save();
        return response()->json($user);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $user->foto_profile = $request->user()->getUrlFoto();
        return response()->json($user);
    }

    public function uploadFotoProfile(Request $request){
        try {
            DB::beginTransaction();

            $ukuran = getimagesize($request->file('foto_profile'));

            $width = ceil(($ukuran[0]/$ukuran[1]) * 100);
            $height = ceil(($ukuran[1]/$ukuran[0]) * 100);

            $filename = time().Str::random(3).'.'.$request->file('foto_profile')->getClientOriginalExtension();

            $image = Image::make($request->file('foto_profile')->getRealPath());
            $image->resize($width, $height, function($constraint){
                $constraint->aspectRatio();
            })->save(storage_path('app/user').'/'.$filename);

            $user = User::find($request->id_user);
            File::delete(storage_path($user->foto_profile));

            // $request->file('foto_profile')->move(storage_path('app/user'), $filename);
            $user->foto_profile = 'app/user/'.$filename;
            $user->save();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function getFotoProfile($id){
        $user = User::find($id);
        return response()->file(
            storage_path($user->foto_profile)
        );
    }

}
