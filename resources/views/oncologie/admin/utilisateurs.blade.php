@extends('layouts.app')
@section('title', 'Gestion Utilisateurs')

@section('content')

<!-- EN-TÊTE -->
<div style="background:linear-gradient(135deg,#264653,#1a3e2b);
            border-radius:18px;padding:24px 28px;margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;">
        <div>
            <h1 style="color:white;font-size:22px;font-weight:800;margin:0;">
                ⚙️ Gestion des Utilisateurs
            </h1>
            <p style="color:rgba(255,255,255,0.6);font-size:13px;margin:4px 0 0;">
                Administration — CLCC Draâ Ben Khedda
            </p>
        </div>
        <button onclick="document.getElementById('modalCreate').style.display='flex'"
                style="background:#2a9d8f;color:white;border:none;padding:10px 20px;
                       border-radius:12px;font-weight:700;cursor:pointer;font-size:13px;">
            <i class="fas fa-user-plus"></i> Ajouter utilisateur
        </button>
    </div>
</div>

<!-- STATS RÔLES -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px;">
    @foreach([
        ['label'=>'Médecins',       'value'=>$stats['medecins'],    'color'=>'#3b82f6','icon'=>'🩺'],
        ['label'=>'Pharmaciens',    'value'=>$stats['pharmaciens'], 'color'=>'#22c55e','icon'=>'💊'],
        ['label'=>'Infirmiers',     'value'=>$stats['infirmiers'],  'color'=>'#ec4899','icon'=>'👩‍⚕️'],
        ['label'=>'Administrateurs','value'=>$stats['admins'],      'color'=>'#f59e0b','icon'=>'⚙️'],
    ] as $s)
    <div style="background:{{ $s['color'] }}12;border:1px solid {{ $s['color'] }}25;
                border-radius:14px;padding:16px;text-align:center;">
        <div style="font-size:26px;margin-bottom:6px;">{{ $s['icon'] }}</div>
        <div style="font-size:24px;font-weight:800;color:{{ $s['color'] }};">
            {{ $s['value'] }}
        </div>
        <div style="font-size:12px;color:#64748b;font-weight:600;">{{ $s['label'] }}</div>
    </div>
    @endforeach
</div>

<!-- ALERTES COMPTES BLOQUÉS -->
@if($stats['bloques'] > 0)
<div style="background:#fef2f2;border:1px solid #fca5a5;border-left:4px solid #ef4444;
            border-radius:12px;padding:14px 18px;margin-bottom:16px;
            display:flex;align-items:center;gap:12px;">
    <i class="fas fa-lock" style="color:#ef4444;font-size:18px;"></i>
    <div>
        <strong style="color:#991b1b;">{{ $stats['bloques'] }} compte(s) bloqué(s)</strong>
        <div style="font-size:12px;color:#b91c1c;margin-top:2px;">
            Ces comptes ont été verrouillés suite à des tentatives de connexion échouées.
        </div>
    </div>
</div>
@endif

<!-- FILTRES -->
<form method="GET"
      style="background:white;border-radius:14px;padding:16px 20px;
             margin-bottom:16px;box-shadow:0 2px 12px rgba(0,0,0,0.05);
             display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
    <input type="text" name="search"
           placeholder="🔍 Nom ou email..."
           value="{{ request('search') }}"
           style="flex:2;min-width:200px;padding:10px 14px;border-radius:10px;
                  border:2px solid #e2e8f0;font-size:13px;outline:none;">
    <select name="role"
            style="flex:1;min-width:150px;padding:10px 14px;border-radius:10px;
                   border:2px solid #e2e8f0;font-size:13px;outline:none;">
        <option value="">Tous les rôles</option>
        <option value="medecin"       {{ request('role')=='medecin' ?'selected':'' }}>🩺 Médecin</option>
        <option value="pharmacien"    {{ request('role')=='pharmacien' ?'selected':'' }}>💊 Pharmacien</option>
        <option value="infirmier"     {{ request('role')=='infirmier' ?'selected':'' }}>👩‍⚕️ Infirmier</option>
        <option value="administrateur"{{ request('role')=='administrateur' ?'selected':'' }}>⚙️ Administrateur</option>
    </select>
    <button type="submit"
            style="background:#264653;color:white;border:none;padding:10px 20px;
                   border-radius:10px;font-weight:600;cursor:pointer;font-size:13px;">
        Filtrer
    </button>
    <a href="{{ route('oncologie.admin.utilisateurs') }}"
       style="background:#94a3b8;color:white;padding:10px 14px;border-radius:10px;
              text-decoration:none;font-size:13px;font-weight:600;">🔄</a>
</form>

<!-- TABLE UTILISATEURS -->
<div style="background:white;border-radius:16px;
            box-shadow:0 4px 20px rgba(0,0,0,0.06);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;min-width:800px;">
        <thead style="background:#0f172a;color:white;">
            <tr>
                @foreach(['#','Utilisateur','Email','Rôle','Statut','Dernière activité','Actions'] as $h)
                <th style="padding:13px 16px;text-align:left;font-size:11px;
                           font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                    {{ $h }}
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($utilisateurs as $u)
            <tr style="border-bottom:1px solid #f1f5f9;transition:0.15s;"
                onmouseover="this.style.background='#f8fafc'"
                onmouseout="this.style.background=''">
                <td style="padding:14px 16px;font-size:12px;color:#94a3b8;">
                    {{ $u->id }}
                </td>
                <td style="padding:14px 16px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:38px;height:38px;border-radius:10px;
                                    background:linear-gradient(135deg,#2a9d8f,#21867a);
                                    display:flex;align-items:center;justify-content:center;
                                    color:white;font-weight:700;font-size:14px;flex-shrink:0;">
                            {{ strtoupper(substr($u->name,0,2)) }}
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px;color:#1e293b;">
                                {{ $u->name }}
                            </div>
                            <div style="font-size:11px;color:#94a3b8;">
                                Créé {{ $u->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </td>
                <td style="padding:14px 16px;font-size:13px;color:#374151;">
                    {{ $u->email }}
                </td>
                <td style="padding:14px 16px;">
                    @php
                        $roleConfig = [
                            'medecin'       => ['color'=>'#1d4ed8','bg'=>'#dbeafe','icon'=>'🩺'],
                            'pharmacien'    => ['color'=>'#166534','bg'=>'#dcfce7','icon'=>'💊'],
                            'infirmier'     => ['color'=>'#9d174d','bg'=>'#fce7f3','icon'=>'👩‍⚕️'],
                            'administrateur'=> ['color'=>'#92400e','bg'=>'#fef3c7','icon'=>'⚙️'],
                        ][$u->role] ?? ['color'=>'#374151','bg'=>'#f1f5f9','icon'=>'👤'];
                    @endphp
                    <span style="background:{{ $roleConfig['bg'] }};color:{{ $roleConfig['color'] }};
                                 padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;">
                        {{ $roleConfig['icon'] }} {{ ucfirst($u->role) }}
                    </span>
                </td>
                <td style="padding:14px 16px;">
                    @if($u->actif)
                        <span style="background:#dcfce7;color:#166534;border:1px solid #86efac;
                                     padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;">
                            ✅ Actif
                        </span>
                    @else
                        <span style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;
                                     padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;">
                            🔒 Bloqué
                        </span>
                    @endif
                </td>
                <td style="padding:14px 16px;font-size:12px;color:#64748b;">
                    {{ $u->updated_at->diffForHumans() }}
                </td>
                <td style="padding:10px 16px;">
                    <div style="display:flex;gap:5px;flex-wrap:wrap;">
                        <!-- MODIFIER -->
                        <button onclick="openEditModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->email }}', '{{ $u->role }}')"
                                style="background:#f59e0b;color:white;border:none;
                                       padding:6px 10px;border-radius:8px;cursor:pointer;
                                       font-size:12px;" title="Modifier">
                            <i class="fas fa-pen"></i>
                        </button>

                        <!-- TOGGLE ACTIF -->
                        <form action="{{ route('oncologie.admin.utilisateurs.toggle', $u->id) }}"
                              method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    style="background:{{ $u->actif ? '#6b7280' : '#22c55e' }};
                                           color:white;border:none;padding:6px 10px;
                                           border-radius:8px;cursor:pointer;font-size:12px;"
                                    title="{{ $u->actif ? 'Désactiver' : 'Activer' }}">
                                <i class="fas {{ $u->actif ? 'fa-lock' : 'fa-unlock' }}"></i>
                            </button>
                        </form>

                        <!-- DÉBLOQUER (si bloqué) -->
                        @if(!$u->actif)
                        <form action="{{ route('oncologie.admin.utilisateurs.debloquer', $u->id) }}"
                              method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    style="background:#3b82f6;color:white;border:none;
                                           padding:6px 10px;border-radius:8px;
                                           cursor:pointer;font-size:12px;" title="Débloquer">
                                <i class="fas fa-user-check"></i>
                            </button>
                        </form>
                        @endif

                        <!-- SUPPRIMER -->
                        <form action="{{ route('oncologie.admin.utilisateurs.destroy', $u->id) }}"
                              method="POST" style="display:inline;"
                              onsubmit="return confirm('Supprimer cet utilisateur ?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="background:#ef4444;color:white;border:none;
                                           padding:6px 10px;border-radius:8px;
                                           cursor:pointer;font-size:12px;" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:#94a3b8;font-size:14px;">
                    <i class="fas fa-users" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                    Aucun utilisateur trouvé
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:14px 20px;border-top:1px solid #f1f5f9;">
        {{ $utilisateurs->links() }}
    </div>
</div>

<!-- MODAL CRÉER UTILISATEUR -->
<div id="modalCreate"
     style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
            background:rgba(0,0,0,0.6);z-index:9999;justify-content:center;
            align-items:center;backdrop-filter:blur(4px);"
     onclick="if(event.target===this) this.style.display='none'">
    <div style="background:white;border-radius:20px;padding:30px;
                width:90%;max-width:520px;box-shadow:0 30px 80px rgba(0,0,0,0.3);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
            <h3 style="font-weight:800;font-size:18px;color:#1e293b;margin:0;">
                ➕ Nouvel Utilisateur
            </h3>
            <button onclick="document.getElementById('modalCreate').style.display='none'"
                    style="background:#f1f5f9;border:none;width:32px;height:32px;
                           border-radius:8px;cursor:pointer;font-size:16px;color:#64748b;">
                ×
            </button>
        </div>

        <form action="{{ route('oncologie.admin.utilisateurs.store') }}" method="POST">
            @csrf

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;
                              color:#374151;margin-bottom:6px;text-transform:uppercase;">
                    Nom complet *
                </label>
                <input type="text" name="name" required
                       placeholder="Dr. Prénom Nom"
                       style="width:100%;padding:11px 14px;border-radius:10px;
                              border:2px solid #e2e8f0;font-size:14px;outline:none;"
                       onfocus="this.style.borderColor='#2a9d8f'"
                       onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;
                              color:#374151;margin-bottom:6px;text-transform:uppercase;">
                    Email *
                </label>
                <input type="email" name="email" required
                       placeholder="nom@clcc.dz"
                       style="width:100%;padding:11px 14px;border-radius:10px;
                              border:2px solid #e2e8f0;font-size:14px;outline:none;"
                       onfocus="this.style.borderColor='#2a9d8f'"
                       onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;
                              color:#374151;margin-bottom:8px;text-transform:uppercase;">
                    Rôle *
                </label>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;">
                    @foreach([
                        ['medecin',       '🩺', 'Médecin',        '#1d4ed8','#dbeafe'],
                        ['pharmacien',    '💊', 'Pharmacien',     '#166534','#dcfce7'],
                        ['infirmier',     '👩‍⚕️', 'Infirmier(e)',   '#9d174d','#fce7f3'],
                        ['administrateur','⚙️', 'Administrateur', '#92400e','#fef3c7'],
                    ] as $r)
                    <label style="border:2px solid #e2e8f0;border-radius:12px;
                                  padding:12px;cursor:pointer;text-align:center;
                                  transition:0.2s;"
                           onclick="this.parentNode.querySelectorAll('label').forEach(l=>l.style.borderColor='#e2e8f0');
                                    this.style.borderColor='{{ $r[3] }}';
                                    this.style.background='{{ $r[4] }}'">
                        <input type="radio" name="role" value="{{ $r[0] }}"
                               style="display:none;" required>
                        <div style="font-size:22px;margin-bottom:4px;">{{ $r[1] }}</div>
                        <div style="font-size:12px;font-weight:700;color:#374151;">
                            {{ $r[2] }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:12px;font-weight:700;
                              color:#374151;margin-bottom:6px;text-transform:uppercase;">
                    Mot de passe *
                </label>
                <input type="password" name="password" required minlength="8"
                       placeholder="Minimum 8 caractères"
                       style="width:100%;padding:11px 14px;border-radius:10px;
                              border:2px solid #e2e8f0;font-size:14px;outline:none;"
                       onfocus="this.style.borderColor='#2a9d8f'"
                       onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <button type="submit"
                    style="width:100%;background:linear-gradient(135deg,#2a9d8f,#21867a);
                           color:white;border:none;padding:13px;border-radius:12px;
                           font-weight:700;cursor:pointer;font-size:15px;">
                ✅ Créer le compte
            </button>
        </form>
    </div>
</div>

<!-- MODAL MODIFIER UTILISATEUR -->
<div id="modalEdit"
     style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
            background:rgba(0,0,0,0.6);z-index:9999;justify-content:center;
            align-items:center;backdrop-filter:blur(4px);"
     onclick="if(event.target===this) this.style.display='none'">
    <div style="background:white;border-radius:20px;padding:30px;
                width:90%;max-width:520px;box-shadow:0 30px 80px rgba(0,0,0,0.3);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
            <h3 style="font-weight:800;font-size:18px;color:#1e293b;margin:0;">
                ✏️ Modifier Utilisateur
            </h3>
            <button onclick="document.getElementById('modalEdit').style.display='none'"
                    style="background:#f1f5f9;border:none;width:32px;height:32px;
                           border-radius:8px;cursor:pointer;font-size:16px;color:#64748b;">
                ×
            </button>
        </div>

        <form id="editForm" action="" method="POST">
            @csrf @method('PUT')

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;
                              color:#374151;margin-bottom:6px;text-transform:uppercase;">
                    Nom complet *
                </label>
                <input type="text" name="name" id="editName" required
                       style="width:100%;padding:11px 14px;border-radius:10px;
                              border:2px solid #e2e8f0;font-size:14px;outline:none;">
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;
                              color:#374151;margin-bottom:6px;text-transform:uppercase;">
                    Email *
                </label>
                <input type="email" name="email" id="editEmail" required
                       style="width:100%;padding:11px 14px;border-radius:10px;
                              border:2px solid #e2e8f0;font-size:14px;outline:none;">
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;
                              color:#374151;margin-bottom:6px;text-transform:uppercase;">
                    Rôle *
                </label>
                <select name="role" id="editRole" required
                        style="width:100%;padding:11px 14px;border-radius:10px;
                               border:2px solid #e2e8f0;font-size:14px;outline:none;">
                    <option value="medecin">🩺 Médecin</option>
                    <option value="pharmacien">💊 Pharmacien</option>
                    <option value="infirmier">👩‍⚕️ Infirmier</option>
                    <option value="administrateur">⚙️ Administrateur</option>
                </select>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:12px;font-weight:700;
                              color:#374151;margin-bottom:6px;text-transform:uppercase;">
                    Nouveau mot de passe (laisser vide = inchangé)
                </label>
                <input type="password" name="password" minlength="8"
                       placeholder="Nouveau mot de passe (optionnel)"
                       style="width:100%;padding:11px 14px;border-radius:10px;
                              border:2px solid #e2e8f0;font-size:14px;outline:none;">
            </div>

            <button type="submit"
                    style="width:100%;background:linear-gradient(135deg,#f59e0b,#d97706);
                           color:white;border:none;padding:13px;border-radius:12px;
                           font-weight:700;cursor:pointer;font-size:15px;">
                💾 Enregistrer les modifications
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditModal(id, name, email, role) {
    document.getElementById('editName').value  = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editRole').value  = role;
    document.getElementById('editForm').action =
        `/oncologie/admin/utilisateurs/${id}`;
    document.getElementById('modalEdit').style.display = 'flex';
}
</script>
@endpush

@endsection