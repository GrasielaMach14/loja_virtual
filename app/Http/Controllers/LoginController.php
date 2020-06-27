<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('entrar.index');
    }

    public function entrar(Request $request)
    {
        if(!Auth::attempt($request->only(['email', 'password'])))
        {
            return redirect()
                    ->back()
                    ->withErrors('Usuário ou senha incorreto.');
        }

        return view('index');
    }
}
