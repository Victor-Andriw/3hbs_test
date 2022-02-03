<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Modules;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Models\RolModules;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    //
    public function authenticate(Request $request)
    {
        try { 
       
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials)) {
                $token = $request->user()->createToken('authToken')->plainTextToken;
                $request->user()->save();
                return response()->json([ 'status' => true, 'token' => $token ]);
            }else{
                return response()->json([ 'status' => false, 'msg' => 'El Usuario o la contraseña no existe' ]);
            }
        } catch (Exception $e) {
            return response()->json([ 'status' => false, 'msg' => $e->getMessage() ]);
        }
    }  

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return [ 'status' => true];
    } 
}
