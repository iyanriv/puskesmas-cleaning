<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class GantiPasswordController extends Controller
{
    public function edit()
    {
        return view('profil.ganti-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak cocok.'])->withInput();
        }

        $user->password = Hash::make($request->password_baru);
        $user->save();

        return redirect()->route('profil.ganti-password')->with('sukses', 'Password berhasil diubah.');
    }
}
