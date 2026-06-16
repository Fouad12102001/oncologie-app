@extends('layouts.app')
@section('title', 'Gestion Utilisateurs')
 
@push('styles')
<style>
.onco-card{background:#fff;border-radius:16px;border:1px solid #e8edf2;overflow:hidden;}
.stat-card{background:#fff;border-radius:14px;border:1px solid #e8edf2;padding:18px 20px;display:flex;align-items:center;gap:16px;transition:box-shadow .2s;}
.stat-card:hover{box-shadow:0 4px 20px rgba(0,0,0,0.08);}
.stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;}
.badge{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:.3px;}
.btn-action{width:32px;height:32px;border-radius:9px;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:14px;transition:.15s;flex-shrink:0;}
.btn-action:hover{filter:brightness(.9);}
.avatar{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#1a7a6e,#0d5c52);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;flex-shrink:0;letter-spacing:.5px;}
input[type=text],input[type=email],input[type=password],select{width:100%;padding:10px 14px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:14px;outline:none;transition:border-color .2s;background:#fff;color:#1e293b;}
input:focus,select:focus{border-color:#1a7a6e;}
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(3px);}
.modal-box{background:#fff;border-radius:20px;padding:32px;width:90%;max-width:500px;max-height:90vh;overflow-y:auto;}
.form-label{display:block;font-size:11px;font-weight:700;color:#64748b;margin-bottom:7px;text-transform:uppercase;letter-spacing:.5px;}
.role-card{border:2px solid #e2e8f0;border-radius:12px;padding:14px 10px;cursor:pointer;text-align:center;transition:.2s;}
.role-card:hover{border-color:#94a3b8;}
tr.user-row:hover td{background:#f8fafc;}
.filter-input{padding:9px 14px;border-radius:10px;border:1.5px solid #e2e8f0;font-size:13px;outline:none;transition:border-color .2s;background:#fff;}
.filter-input:focus{border-color:#1a7a6e;}
</style>
@endpush
 
@section('content')
 
{{-- EN-TÊTE --}}
<div style="background:linear-gradient(135deg,#0f2d24 0%,#1a4a35 50%,#0d3d50 100%);border-radius:20px;padding:28px 32px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;background:rgba(255,255,255,.04);border-radius:50%;"></div>
    <div style="position:absolute;bottom:-60px;right:80px;width:140px;height:140px;background:rgba(255,255,255,.03);border-radius:50%;"></div>
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;position:relative;">
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                <div style="width:36px;height:36px;background:rgba(255,255,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;">⚙️</div>
                <h1 style="color:#fff;font-size:21px;font-weight:800;margin:0;letter-spacing:-.3px;">Gestion des Utilisateurs</h1>
            </div>
            <p style="color:rgba(255,255,255,.5);font-size:13px;margin:0;padding-left:46px;">CLCC Draâ Ben Khedda — Administration</p>
        </div>
        <button onclick="document.getElementById('modalCreate').style.display='flex'"
                style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.25);padding:11px 22px;border-radius:12px;font-weight:700;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:8px;transition:.2s;backdrop-filter:blur(4px);"
                onmouseover="this.style.background='rgba(255,255,255,.22)'"
                onmouseout="this.style.background='rgba(255,255,255,.15)'">
            <i class="fas fa-user-plus"></i> Ajouter un utilisateur
        </button>
    </div>
</div>
 
{{-- STATS --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:20px;">
    @foreach([
        ['label'=>'Médecins',       'value'=>$stats['medecins'],    'color'=>'#1d4ed8','bg'=>'#dbeafe','icon'=>'🩺'],
        ['label'=>'Pharmaciens',    'value'=>$stats['pharmaciens'], 'color'=>'#166534','bg'=>'#dcfce7','icon'=>'💊'],
        ['label'=>'Infirmiers',     'value'=>$stats['infirmiers'],  'color'=>'#9d174d','bg'=>'#fce7f3','icon'=>'👩‍⚕️'],
        ['label'=>'Administrateurs','value'=>$stats['admins'],      'color'=>'#92400e','bg'=>'#fef3c7','icon'=>'⚙️'],
        ['label'=>'Total',          'value'=>$stats['total'],       'color'=>'#1e293b','bg'=>'#f1f5f9','icon'=>'👤'],
        ['label'=>'Bloqués',        'value'=>$stats['bloques'],     'color'=>'#991b1b','bg'=>'#fee2e2','icon'=>'🔒'],
    ] as $s)
    <div class="stat-card">
        <div class="stat-icon" style="background:{{ $s['bg'] }};">{{ $s['icon'] }}</div>
        <div>
            <div style="font-size:24px;font-weight:800;color:{{ $s['color'] }};line-height:1;">{{ $s['value'] }}</div>
            <div style="font-size:12px;color:#64748b;font-weight:600;margin-top:3px;">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>
 
{{-- ALERTE BLOQUÉS --}}
@if($stats['bloques'] > 0)
<div style="background:#fff5f5;border:1px solid #fca5a5;border-left:4px solid #ef4444;border-radius:12px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;gap:12px;">
    <i class="fas fa-exclamation-triangle" style="color:#ef4444;font-size:16px;flex-shrink:0;"></i>
    <div>
        <strong style="color:#991b1b;font-size:13px;">{{ $stats['bloques'] }} compte(s) verrouillé(s)</strong>
        <div style="font-size:12px;color:#b91c1c;margin-top:2px;">Verrouillés après tentatives de connexion échouées. Cliquez sur <i class="fas fa-user-check"></i> pour débloquer.</div>
    </div>
</div>
@endif
 
{{-- FILTRES --}}
<form method="GET" style="background:#fff;border-radius:14px;padding:14px 18px;margin-bottom:18px;border:1px solid #e8edf2;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
    <div style="flex:2;min-width:200px;position:relative;">
        <i class="fas fa-search" style="position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;pointer-events:none;"></i>
        <input type="text" name="search" placeholder="Rechercher un nom ou email..." value="{{ request('search') }}"
               class="filter-input" style="width:100%;padding-left:36px;">
    </div>
    <select name="role" class="filter-input" style="flex:1;min-width:160px;">
        <option value="">Tous les rôles</option>
        <option value="medecin"        {{ request('role')=='medecin'        ?'selected':'' }}>🩺 Médecin</option>
        <option value="pharmacien"     {{ request('role')=='pharmacien'     ?'selected':'' }}>💊 Pharmacien</option>
        <option value="infirmier"      {{ request('role')=='infirmier'      ?'selected':'' }}>👩‍⚕️ Infirmier</option>
        <option value="administrateur" {{ request('role')=='administrateur' ?'selected':'' }}>⚙️ Administrateur</option>
    </select>
    <button type="submit" style="background:#0f2d24;color:#fff;border:none;padding:10px 20px;border-radius:10px;font-weight:700;cursor:pointer;font-size:13px;white-space:nowrap;">
        <i class="fas fa-filter" style="margin-right:6px;"></i>Filtrer
    </button>
    <a href="{{ route('oncologie.admin.utilisateurs') }}" title="Réinitialiser"
       style="background:#f1f5f9;color:#475569;padding:10px 14px;border-radius:10px;text-decoration:none;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;">
        <i class="fas fa-redo-alt"></i>
    </a>
</form>
 
{{-- TABLE --}}
<div class="onco-card" style="box-shadow:0 4px 24px rgba(0,0,0,.06);">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:820px;">
            <thead>
                <tr style="background:#0f172a;">
                    @foreach(['#','Utilisateur','Email','Rôle','Statut','Activité','Actions'] as $h)
                    <th style="padding:14px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:rgba(255,255,255,.7);">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($utilisateurs as $u)
                <tr class="user-row" style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 16px;font-size:12px;color:#94a3b8;font-weight:600;">#{{ $u->id }}</td>
                    <td style="padding:14px 16px;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div class="avatar">{{ strtoupper(substr($u->name,0,2)) }}</div>
                            <div>
                                <div style="font-weight:700;font-size:14px;color:#1e293b;">{{ $u->name }}</div>
                                <div style="font-size:11px;color:#94a3b8;margin-top:2px;">Créé {{ $u->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:14px 16px;font-size:13px;color:#374151;">{{ $u->email }}</td>
                    <td style="padding:14px 16px;">
                        @php
                            $rc = [
                                'medecin'       => ['c'=>'#1d4ed8','bg'=>'#dbeafe','icon'=>'🩺'],
                                'pharmacien'    => ['c'=>'#166534','bg'=>'#dcfce7','icon'=>'💊'],
                                'infirmier'     => ['c'=>'#9d174d','bg'=>'#fce7f3','icon'=>'👩‍⚕️'],
                                'administrateur'=> ['c'=>'#92400e','bg'=>'#fef3c7','icon'=>'⚙️'],
                            ][$u->role] ?? ['c'=>'#374151','bg'=>'#f1f5f9','icon'=>'👤'];
                        @endphp
                        <span class="badge" style="background:{{ $rc['bg'] }};color:{{ $rc['c'] }};">
                            {{ $rc['icon'] }} {{ ucfirst($u->role) }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;">
                        @if($u->is_locked)
                            <span class="badge" style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;">
                                <i class="fas fa-lock" style="font-size:10px;"></i> Bloqué
                            </span>
                        @elseif($u->actif)
                            <span class="badge" style="background:#dcfce7;color:#166534;border:1px solid #86efac;">
                                <i class="fas fa-circle" style="font-size:8px;"></i> Actif
                            </span>
                        @else
                            <span class="badge" style="background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;">
                                <i class="fas fa-circle" style="font-size:8px;"></i> Inactif
                            </span>
                        @endif
                    </td>
                    <td style="padding:14px 16px;font-size:12px;color:#64748b;">{{ $u->updated_at->diffForHumans() }}</td>
                    <td style="padding:10px 16px;">
                        <div style="display:flex;gap:6px;align-items:center;">
                            {{-- MODIFIER --}}
                            <button onclick="openEditModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->email }}', '{{ $u->role }}')"
                                    class="btn-action" style="background:#fef3c7;color:#92400e;" title="Modifier">
                                <i class="fas fa-pen" style="font-size:12px;"></i>
                            </button>
 
                            {{-- TOGGLE ACTIF --}}
                            <form action="{{ route('oncologie.admin.utilisateurs.toggle', $u->id) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-action"
                                        style="background:{{ $u->actif ? '#f1f5f9' : '#dcfce7' }};color:{{ $u->actif ? '#64748b' : '#166534' }};"
                                        title="{{ $u->actif ? 'Désactiver' : 'Activer' }}">
                                    <i class="fas {{ $u->actif ? 'fa-lock' : 'fa-unlock' }}" style="font-size:12px;"></i>
                                </button>
                            </form>
 
                            {{-- DÉBLOQUER --}}
                            @if($u->is_locked)
                            <form action="{{ route('oncologie.admin.utilisateurs.debloquer', $u->id) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-action" style="background:#dbeafe;color:#1d4ed8;" title="Débloquer le compte">
                                    <i class="fas fa-user-check" style="font-size:12px;"></i>
                                </button>
                            </form>
                            @endif
 
                            {{-- SUPPRIMER --}}
                            <form action="{{ route('oncologie.admin.utilisateurs.destroy', $u->id) }}" method="POST"
                                  style="display:inline;" onsubmit="return confirm('Supprimer définitivement cet utilisateur ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action" style="background:#fee2e2;color:#ef4444;" title="Supprimer">
                                    <i class="fas fa-trash" style="font-size:12px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:50px 20px;color:#94a3b8;">
                        <i class="fas fa-users" style="font-size:36px;opacity:.25;display:block;margin-bottom:12px;"></i>
                        <div style="font-size:14px;font-weight:600;">Aucun utilisateur trouvé</div>
                        <div style="font-size:12px;margin-top:4px;">Essayez de modifier vos filtres</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 20px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="font-size:12px;color:#94a3b8;">
            {{ $utilisateurs->total() }} utilisateur(s) au total
        </div>
        {{ $utilisateurs->links() }}
    </div>
</div>
 
{{-- MODAL CRÉER --}}
<div id="modalCreate" class="modal-overlay" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <div>
                <h3 style="font-weight:800;font-size:18px;color:#1e293b;margin:0;">Nouvel Utilisateur</h3>
                <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Remplissez tous les champs requis</p>
            </div>
            <button onclick="document.getElementById('modalCreate').style.display='none'"
                    style="background:#f1f5f9;border:none;width:34px;height:34px;border-radius:9px;cursor:pointer;font-size:18px;color:#64748b;display:flex;align-items:center;justify-content:center;">×</button>
        </div>
 
        <form action="{{ route('oncologie.admin.utilisateurs.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label class="form-label">Nom complet *</label>
                <input type="text" name="name" required placeholder="Dr. Prénom Nom">
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label">Adresse email *</label>
                <input type="email" name="email" required placeholder="nom@clcc.dz">
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label">Rôle *</label>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
                    @foreach([
                        ['medecin',       '🩺','Médecin',       '#1d4ed8','#dbeafe'],
                        ['pharmacien',    '💊','Pharmacien',    '#166534','#dcfce7'],
                        ['infirmier',     '👩‍⚕️','Infirmier(e)',  '#9d174d','#fce7f3'],
                        ['administrateur','⚙️','Administrateur','#92400e','#fef3c7'],
                    ] as $r)
                    <label class="role-card"
                           onclick="this.closest('.role-grid').querySelectorAll('.role-card').forEach(c=>{c.style.borderColor='#e2e8f0';c.style.background='#fff'});this.style.borderColor='{{ $r[3] }}';this.style.background='{{ $r[4] }}'">
                        <input type="radio" name="role" value="{{ $r[0] }}" style="display:none;" required>
                        <div style="font-size:26px;margin-bottom:6px;">{{ $r[1] }}</div>
                        <div style="font-size:12px;font-weight:700;color:#374151;">{{ $r[2] }}</div>
                    </label>
                    @endforeach
                </div>
            </div>
            <div style="margin-bottom:24px;">
                <label class="form-label">Mot de passe *</label>
                <input type="password" name="password" required minlength="8" placeholder="Minimum 8 caractères">
            </div>
            <button type="submit" style="width:100%;background:linear-gradient(135deg,#1a7a6e,#0d5c52);color:#fff;border:none;padding:14px;border-radius:12px;font-weight:700;cursor:pointer;font-size:15px;">
                <i class="fas fa-user-plus" style="margin-right:8px;"></i>Créer le compte
            </button>
        </form>
    </div>
</div>
 
{{-- MODAL MODIFIER --}}
<div id="modalEdit" class="modal-overlay" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <div>
                <h3 style="font-weight:800;font-size:18px;color:#1e293b;margin:0;">Modifier l'Utilisateur</h3>
                <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Laissez le mot de passe vide pour le conserver</p>
            </div>
            <button onclick="document.getElementById('modalEdit').style.display='none'"
                    style="background:#f1f5f9;border:none;width:34px;height:34px;border-radius:9px;cursor:pointer;font-size:18px;color:#64748b;display:flex;align-items:center;justify-content:center;">×</button>
        </div>
 
        <form id="editForm" action="" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom:16px;">
                <label class="form-label">Nom complet *</label>
                <input type="text" name="name" id="editName" required>
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label">Adresse email *</label>
                <input type="email" name="email" id="editEmail" required>
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label">Rôle *</label>
                <select name="role" id="editRole" required>
                    <option value="medecin">🩺 Médecin</option>
                    <option value="pharmacien">💊 Pharmacien</option>
                    <option value="infirmier">👩‍⚕️ Infirmier</option>
                    <option value="administrateur">⚙️ Administrateur</option>
                </select>
            </div>
            <div style="margin-bottom:24px;">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" name="password" minlength="8" placeholder="Laisser vide = inchangé">
            </div>
            <button type="submit" style="width:100%;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none;padding:14px;border-radius:12px;font-weight:700;cursor:pointer;font-size:15px;">
                <i class="fas fa-save" style="margin-right:8px;"></i>Enregistrer les modifications
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
    document.getElementById('editForm').action = '{{ url("oncologie/admin/utilisateurs") }}/' + id;
    document.getElementById('modalEdit').style.display = 'flex';
}
</script>
@endpush
@endsection