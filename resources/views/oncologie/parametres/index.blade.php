@extends('layouts.app')
@section('title', 'Paramètres')

@section('content')

@php $u = Auth::guard('oncologie')->user(); @endphp

<div style="max-width:900px;margin:auto;">

    <!-- EN-TÊTE PROFIL -->
    <div style="background:linear-gradient(135deg,#264653,#2a9d8f);
                border-radius:20px;padding:28px 32px;margin-bottom:24px;
                display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
        <div style="width:72px;height:72px;border-radius:18px;flex-shrink:0;
                    background:rgba(255,255,255,0.15);
                    display:flex;align-items:center;justify-content:center;
                    font-size:28px;font-weight:800;color:white;
                    border:3px solid rgba(255,255,255,0.3);">
            {{ strtoupper(substr($u->name,0,2)) }}
        </div>
        <div>
            <h2 style="color:white;font-size:22px;font-weight:800;margin:0;">
                {{ $u->name }}
            </h2>
            <p style="color:rgba(255,255,255,0.7);font-size:14px;margin:4px 0 0;">
                {{ $u->email }}
            </p>
            @php
                $roleConfig = [
                    'medecin'       => ['icon'=>'🩺','color'=>'#bfdbfe'],
                    'pharmacien'    => ['icon'=>'💊','color'=>'#bbf7d0'],
                    'infirmier'     => ['icon'=>'👩‍⚕️','color'=>'#fbcfe8'],
                    'administrateur'=> ['icon'=>'⚙️','color'=>'#fde68a'],
                ][$u->role] ?? ['icon'=>'👤','color'=>'#e2e8f0'];
            @endphp
            <span style="background:rgba(255,255,255,0.15);color:white;
                         padding:5px 14px;border-radius:999px;font-size:12px;
                         font-weight:700;display:inline-block;margin-top:8px;">
                {{ $roleConfig['icon'] }} {{ ucfirst($u->role) }}
            </span>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

        <!-- MODIFIER PROFIL -->
        <div style="background:white;border-radius:18px;padding:24px;
                    box-shadow:0 4px 20px rgba(0,0,0,0.06);">
            <h3 style="font-weight:800;font-size:16px;color:#1e293b;margin-bottom:18px;
                       display:flex;align-items:center;gap:8px;">
                <span style="background:#2a9d8f;color:white;width:30px;height:30px;
                             border-radius:8px;display:inline-flex;align-items:center;
                             justify-content:center;font-size:14px;">👤</span>
                Informations personnelles
            </h3>

            <form action="{{ route('oncologie.parametres.profil.update') }}" method="POST">
                @csrf @method('PUT')

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;font-weight:700;
                                  color:#374151;margin-bottom:6px;text-transform:uppercase;">
                        Nom complet
                    </label>
                    <input type="text" name="name"
                           value="{{ old('name', $u->name) }}" required
                           style="width:100%;padding:11px 14px;border-radius:10px;
                                  border:2px solid #e2e8f0;font-size:14px;outline:none;
                                  transition:0.2s;"
                           onfocus="this.style.borderColor='#2a9d8f'"
                           onblur="this.style.borderColor='#e2e8f0'">
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:12px;font-weight:700;
                                  color:#374151;margin-bottom:6px;text-transform:uppercase;">
                        Adresse email
                    </label>
                    <input type="email" name="email"
                           value="{{ old('email', $u->email) }}" required
                           style="width:100%;padding:11px 14px;border-radius:10px;
                                  border:2px solid #e2e8f0;font-size:14px;outline:none;
                                  transition:0.2s;"
                           onfocus="this.style.borderColor='#2a9d8f'"
                           onblur="this.style.borderColor='#e2e8f0'">
                </div>

                <div style="background:#f8fafc;border-radius:10px;padding:12px 14px;
                            margin-bottom:18px;">
                    <div style="font-size:12px;color:#64748b;font-weight:600;">Rôle actuel</div>
                    <div style="font-size:14px;font-weight:700;color:#1e293b;margin-top:4px;">
                        {{ $roleConfig['icon'] }} {{ ucfirst($u->role) }}
                    </div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:2px;">
                        Le rôle est modifiable uniquement par un administrateur
                    </div>
                </div>

                <button type="submit"
                        style="width:100%;background:linear-gradient(135deg,#2a9d8f,#21867a);
                               color:white;border:none;padding:12px;border-radius:12px;
                               font-weight:700;cursor:pointer;font-size:14px;">
                    💾 Mettre à jour le profil
                </button>
            </form>
        </div>

        <!-- MODIFIER MOT DE PASSE -->
        <div style="background:white;border-radius:18px;padding:24px;
                    box-shadow:0 4px 20px rgba(0,0,0,0.06);">
            <h3 style="font-weight:800;font-size:16px;color:#1e293b;margin-bottom:18px;
                       display:flex;align-items:center;gap:8px;">
                <span style="background:#6a4c93;color:white;width:30px;height:30px;
                             border-radius:8px;display:inline-flex;align-items:center;
                             justify-content:center;font-size:14px;">🔑</span>
                Sécurité — Mot de passe
            </h3>

            <form action="{{ route('oncologie.parametres.password.update') }}" method="POST">
                @csrf @method('PUT')

                @foreach([
                    ['name'=>'current_password','label'=>'Mot de passe actuel *','ph'=>'••••••••'],
                    ['name'=>'password',         'label'=>'Nouveau mot de passe *','ph'=>'Minimum 8 caractères'],
                    ['name'=>'password_confirmation','label'=>'Confirmer le nouveau mot de passe *','ph'=>'Répéter le mot de passe'],
                ] as $f)
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;font-weight:700;
                                  color:#374151;margin-bottom:6px;text-transform:uppercase;">
                        {{ $f['label'] }}
                    </label>
                    <div style="position:relative;">
                        <input type="password" name="{{ $f['name'] }}"
                               placeholder="{{ $f['ph'] }}"
                               id="pw_{{ $f['name'] }}"
                               required
                               style="width:100%;padding:11px 42px 11px 14px;
                                      border-radius:10px;border:2px solid #e2e8f0;
                                      font-size:14px;outline:none;transition:0.2s;"
                               onfocus="this.style.borderColor='#6a4c93'"
                               onblur="this.style.borderColor='#e2e8f0'">
                        <i class="fas fa-eye"
                           onclick="togglePwField('pw_{{ $f['name'] }}', this)"
                           style="position:absolute;right:14px;top:50%;
                                  transform:translateY(-50%);cursor:pointer;
                                  color:#94a3b8;font-size:14px;"></i>
                    </div>
                    @error($f['name'])
                        <div style="color:#ef4444;font-size:12px;margin-top:4px;">
                            ⚠️ {{ $message }}
                        </div>
                    @enderror
                </div>
                @endforeach

                <div style="background:#fef3c7;border:1px solid #fde68a;
                            border-radius:10px;padding:12px 14px;margin-bottom:18px;">
                    <div style="font-size:12px;color:#92400e;">
                        <strong>Règles :</strong> minimum 8 caractères,
                        majuscule, chiffre, caractère spécial recommandés.
                    </div>
                </div>

                <button type="submit"
                        style="width:100%;background:linear-gradient(135deg,#6a4c93,#563c7e);
                               color:white;border:none;padding:12px;border-radius:12px;
                               font-weight:700;cursor:pointer;font-size:14px;">
                    🔐 Modifier le mot de passe
                </button>
            </form>
        </div>

    </div>

    <!-- ACCÈS RAPIDE SELON RÔLE -->
    <div style="background:white;border-radius:18px;padding:22px;
                box-shadow:0 4px 20px rgba(0,0,0,0.06);margin-top:20px;">
        <h3 style="font-weight:800;font-size:16px;color:#1e293b;margin-bottom:16px;">
            ⚡ Accès rapide — {{ ucfirst($u->role) }}
        </h3>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">

            @switch($u->role)
                @case('medecin')
                    @foreach([
                        [route('oncologie.patients.index'),      '#2a9d8f','👥','Mes patients'],
                        [route('oncologie.prescriptions.create'),'#264653','📋','Nouvelle prescription'],
                        [route('oncologie.prescriptions.index'), '#457b9d','📊','Mes prescriptions'],
                    ] as $a)
                    <a href="{{ $a[0] }}"
                       style="background:{{ $a[1] }}15;border:1px solid {{ $a[1] }}30;
                              color:{{ $a[1] }};padding:10px 18px;border-radius:12px;
                              text-decoration:none;font-size:13px;font-weight:700;
                              display:flex;align-items:center;gap:8px;">
                        {{ $a[2] }} {{ $a[3] }}
                    </a>
                    @endforeach
                @break

                @case('pharmacien')
                    @foreach([
                        [route('oncologie.medicaments.index'),    '#2a9d8f','💊','Médicaments'],
                        [route('oncologie.dispensations.create'), '#6a4c93','🔬','Dispenser'],
                        [route('oncologie.lots.index'),           '#264653','📦','Lots'],
                    ] as $a)
                    <a href="{{ $a[0] }}"
                       style="background:{{ $a[1] }}15;border:1px solid {{ $a[1] }}30;
                              color:{{ $a[1] }};padding:10px 18px;border-radius:12px;
                              text-decoration:none;font-size:13px;font-weight:700;
                              display:flex;align-items:center;gap:8px;">
                        {{ $a[2] }} {{ $a[3] }}
                    </a>
                    @endforeach
                @break

                @case('infirmier')
                    @foreach([
                        [route('oncologie.prescriptions.index'),  '#264653','📋','Prescriptions'],
                        [route('oncologie.patients.index'),       '#2a9d8f','👥','Patients'],
                    ] as $a)
                    <a href="{{ $a[0] }}"
                       style="background:{{ $a[1] }}15;border:1px solid {{ $a[1] }}30;
                              color:{{ $a[1] }};padding:10px 18px;border-radius:12px;
                              text-decoration:none;font-size:13px;font-weight:700;
                              display:flex;align-items:center;gap:8px;">
                        {{ $a[2] }} {{ $a[3] }}
                    </a>
                    @endforeach
                @break

                @case('administrateur')
                    @foreach([
                        [route('oncologie.admin.utilisateurs'), '#264653','⚙️','Utilisateurs'],
                        [route('oncologie.admin.logs'),         '#6a4c93','📋','Logs'],
                        [route('oncologie.admin.referentiels'), '#2a9d8f','📚','Référentiels'],
                        [route('oncologie.statistiques.index'), '#e63946','📊','Statistiques'],
                    ] as $a)
                    <a href="{{ $a[0] }}"
                       style="background:{{ $a[1] }}15;border:1px solid {{ $a[1] }}30;
                              color:{{ $a[1] }};padding:10px 18px;border-radius:12px;
                              text-decoration:none;font-size:13px;font-weight:700;
                              display:flex;align-items:center;gap:8px;">
                        {{ $a[2] }} {{ $a[3] }}
                    </a>
                    @endforeach
                @break
            @endswitch

        </div>
    </div>

</div>

@push('scripts')
<script>
function togglePwField(id, icon) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endpush

@endsection