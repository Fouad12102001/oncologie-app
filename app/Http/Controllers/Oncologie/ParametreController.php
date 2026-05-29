<?php

namespace App\Http\Controllers\Oncologie;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Oncologie\OncoUser;

class ParametreController extends Controller
{
    public function index()
    {
        $user = Auth::guard('oncologie')->user();
        return view('oncologie.parametres.index', compact('user'));
    }

    public function profil()
    {
        $user = Auth::guard('oncologie')->user();
        return view('oncologie.parametres.profil', compact('user'));
    }

    public function updateProfil(Request $request)
    {
        $user = Auth::guard('oncologie')->user();

        $request->validate([
            'name'  => 'required|string|max:150',
            'email' => 'required|email|unique:onco_users,email,' . $user->id,
        ]);

        $user->update($request->only('name', 'email'));

        return back()->with('success', '✅ Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::guard('oncologie')->user();

        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', '✅ Mot de passe modifié avec succès.');
    }
}