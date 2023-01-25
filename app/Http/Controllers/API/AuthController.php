<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\Semester;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $messages = [
			'email.required' => 'Email tidak boleh kosong',
            'email.exists' => 'Email tidak terdaftar',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password tidak boleh kosong'
		];
		$validator = Validator::make(request()->all(), [
			'email' => 'required|email|exists:users',
            'password' => 'required',
		 ],
		$messages
		)->validate();
        $fieldType = 'email';
		if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

                $Auth = Auth::user();
                // $success['token'] = $Auth->createToken('auth_token')->plainTextToken;
                // $success['name'] = $Auth;

                $success = array([
                    'token' => $Auth->createToken('auth_token')->plainTextToken,
                    'name'  => $Auth,
                ]);

                $user = auth()->user();
                $user->last_login_at = date('Y-m-d H:i:s');
                $user->last_login_ip = $request->ip();
                $user->save();

                // dd($r);
                return response()->json([
                'success' => true,
                'message' => 'Login Sukses',
                'data' => $success
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Cek email dan password',
                'data' => null
            ]);
        }
            // if(!$user->peserta_didik_id && !$user->guru_id){
            //     $team = Team::updateOrCreate([
            //         'name' => $semester->nama,
            //         'display_name' => $semester->nama,
            //         'description' => $semester->nama,
            //     ]);
                // if(!$user->hasRole('admin', $semester->nama)){
                //     $user->attachRole('admin', $team);
                // }
            // }
            // return redirect()->route('index');
        
        // session()->flash('status', 'Password salah');
        // return redirect()->back();
    }
}
