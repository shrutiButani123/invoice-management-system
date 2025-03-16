<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function create(){
        return view('auth.login');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){        
           
            $isAdmin = strtolower(Auth()->user()->role);

            if ($isAdmin === 'admin') {           
                return redirect()->route('admin.dashboard')->with('success', 'Your account has been successfully logged in!');
            } elseif ($isAdmin === 'user') {
                return redirect()->route('user.dashboard')->with('success', 'Your account has been successfully logged in!');
            }
        }
        return redirect()->back()->with('error', 'Invalid credentials');
    }

    public function logout(){
        Auth::logout();
     
        return redirect()->route('login.create');
    }
}
