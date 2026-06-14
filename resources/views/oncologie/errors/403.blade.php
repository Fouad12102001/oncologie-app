@extends('layouts.app')
@section('title', 'Accès refusé')

@section('content')
<div style="min-height:60vh; display:flex; align-items:center; justify-content:center;">
    <div style="text-align:center; max-width:500px; padding:40px;">

        <div style="font-size:80px; margin-bottom:20px;">⛔</div>

        <div style="font-size:72px; font-weight:900; color:#ef4444;
                    letter-spacing:-2px; line-height:1; margin-bottom:12px;">
            403
        </div>

        <h2 style="font-size:22px; font-weight:800; color:#1e293b; margin-bottom:10px;">
            Accès non autorisé
        </h2>

        <p style="font-size:14px; color:#64748b; margin-bottom:16px; line-height:1.6;">
            Votre rôle
            <strong>« {{ ucfirst(Auth::guard('oncologie')->user()?->role ?? '?') }} »</strong>
            ne permet pas d'accéder à cette ressource.
        </p>

        {{-- Mention Loi 25-11 --}}
        <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px;
                    padding:12px 16px; font-size:12px; color:#1e40af; margin-bottom:24px;">
            ℹ️ Cette restriction est conforme à la <strong>Loi algérienne 25-11</strong>
            relative à la protection des données personnelles (principe de finalité limitée).
        </div>

        <div style="display:flex; gap:10px; justify-content:center;">
            <a href="{{ url()->previous() }}"
               style="background:#64748b; color:white; padding:10px 20px;
                      border-radius:10px; text-decoration:none; font-weight:700;">
                ← Retour
            </a>
            <a href="{{ route('oncologie.dashboard') }}"
               style="background:linear-gradient(135deg,#264653,#2a9d8f); color:white;
                      padding:10px 20px; border-radius:10px; text-decoration:none;
                      font-weight:700;">
                🏠 Tableau de bord
            </a>
        </div>
    </div>
</div>
@endsection