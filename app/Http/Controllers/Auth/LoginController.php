<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        if(\URL::previous() !== \Request::url()) session(['url_previous' => \URL::previous()]);
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $remember = $request->remember ? true : false;

        $authValid = Auth::guard('web')->validate(['email' => $request->email, 'password' => $request->password]);

        if($authValid){
            if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password],$remember)) {
                if(session()->get('url_previous')){
                    return response()->json(session()->get('url_previous'), 200);
                    session()->forget('url_previous');
                }else{
                    return response()->json(route('dash'), 200);
                }
            }
        }else{
            return response()->json(['invalid' => 'Email ou Senha invalidos'], 422);
        }
    }

    public function logout(Request $request)
    {
        auth()->guard('web')->logout();
        return redirect()->route('login');
    }
}
