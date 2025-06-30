<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }
    public function submitLogin(Request $request)
    {
        // Validate the request data
        $karyawan = Karyawan::where('username', $request->username)->first();

        if(!$karyawan) {
            return redirect()->back()->withErrors(['message' => 'Username atau password salah'])->withInput();
        }
        // Check if the password is correct
        if (password_verify($request->password, $karyawan->password)) {
            // Store the user in the session
            session(['user' => $karyawan]);
            return redirect()->route('billing');
        }

        
        return redirect()->back()->withErrors(['message' => 'Username atau password salah'])->withInput();
    }
}
