<?php

namespace App\Http\Controllers;

use App\Models\Applicantdata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApplicantController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([

            'email' => 'required|email',
            'password' => 'required|min:8'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email harus benar',
            'password.required' => 'Passward wajib diisi',
            'password.min' => 'Password harus diisi minimal 8 karakter'
        ]);

        $infoLogin = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        if (Auth::attempt($infoLogin)) {
            dd('sukses');
        } else {
            dd('gagal');
        }
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ], [
            'nama.required' => 'Nama wajib diisi',
            'nama.min' => 'Nama harus diisi minimal 3 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email harus benar',
            'password.required' => 'Passward wajib diisi',
            'password.min' => 'Password harus diisi minimal 8 karakter'
        ]);

        Applicantdata::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return view('applicant.login')->with('success', 'Berhasil di register');
    }
}
