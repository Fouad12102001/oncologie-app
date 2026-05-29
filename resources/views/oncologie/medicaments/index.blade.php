@extends('layouts.app')
@section('title', 'Médicaments')

@section('content')

@php
    $total      = $medicaments->count();
    $expired    = $medicaments->filter(fn($m) => $m->estExpire())->count();
    $soon       = $medicaments->filter(fn($m) => $m->bientotExpire() && !$m->estExpire())->count();
    $rupture    = $medicaments->filter(fn($m) => $m->stockActuel() <= 0)->count();
    $alerteStock = $medicaments->filter(fn($m) =>
        $m->stockActuel() > 0 && $m->stockActuel() <= $m->quantite_min
    )->count();

    $alertesExp  = $medicaments->filter(fn($m) => $m->estExpire() || $m->bientotExpire());
    $alertesSt   = $medicaments->filter(fn($m) => $m->stockActuel() <= $m->quantite_min);
@endphp

{{-- ALERTES GLOBALES --}}
@if($alertesExp->count() > 0)
    <div style="background:#fee2e2; border-left:5px solid #dc2626; padding:12px 16px;
                border-radius:10px; margin-bottom:12px; color:#991b1b; font-weight:600;">
        ⚠️ {{ $alertesExp->count() }} médicament(s) expiré(s) ou bientôt expiré(s)
    </div>
@endif

@if($alertesSt->count() > 0)
    <div style="background:#fff7ed; border-left:5px solid #f59e0b; padding:12px 16px;
                border-radius:10px; margin-bottom:12px; color:#92400e; font-weight:600;">
        📦 {{ $alertesSt->count() }} médicament(s) en alerte de stock
    </div>
@endif

{{-- HEADER --}}
<div style="display:flex; justify-content:space-between; align-items:center;
            background:white; padding:16px; border-radius:12px; margin-bottom:16px;
            box-shadow:0 4px 14px rgba(0,0,0,0.05);">
    <div>
        <h2 style="margin:0; font-size:20px; font-weight:800;">💊 Gestion des Médicaments</h2>
        <p style="margin:0; font-size:13px; color:#6b7280;">Stock, expiration et mouvements</p>
    </div>
    <a href="{{ route('oncologie.medicaments.create') }}"
       style="background:linear-gradient(135deg,#0ea5e9,#0284c7); color:white;
              padding:10px 16px; border-radius:10px; font-weight:600; text-decoration:none;">
        ➕ Ajouter médicament
    </a>
</div>

{{-- DASHBOARD --}}
<div style="display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:16px;">
    @foreach([
        ['statut'=>'expired', 'label'=>'❌ Expirés',        'count'=>$expired,    'bg'=>'linear-gradient(135deg,#ef4444,#dc2626)'],
        ['statut'=>'soon',    'label'=>'⚠️ Bientôt',        'count'=>$soon,       'bg'=>'linear-gradient(135deg,#f59e0b,#d97706)'],
        ['statut'=>'rupture', 'label'=>'📦 Rupture',        'count'=>$rupture,    'bg'=>'linear-gradient(135deg,#334155,#1f2937)'],
        ['statut'=>'stock',   'label'=>'⚠️ Stock critique', 'count'=>$alerteStock,'bg'=>'linear-gradient(135deg,#facc15,#eab308)', 'dark'=>true],
        ['statut'=>null,      'label'=>'📊 Total',          'count'=>$total,      'bg'=>'linear-gradient(135deg,#0ea5e9,#0284c7)'],
    ] as $card)
        <a href="{{ route('oncologie.medicaments.index', $card['statut'] ? ['statut'=>$card['statut']] : []) }}"
           style="background:{{ $card['bg'] }}; color:{{ isset($card['dark']) ? '#111' : 'white' }};
                  padding:14px; border-radius:14px; text-align:center; font-weight:700;
                  text-decoration:none; display:block;
                  {{ $statut == $card['statut'] ? 'outline:3px solid #111827;' : '' }}
                  transition:0.2s;"
           onmouseover="this.style.transform='translateY(-2px)'"
           onmouseout="this.style.transform='translateY(0)'">
            {{ $card['label'] }}<br><b style="font-size:22px;">{{ $card['count'] }}</b>
        </a>
    @endforeach
</div>

{{-- SEARCH --}}
<form method="GET" style="margin-bottom:14px;">
    @if(request('statut'))
        <input type="hidden" name="statut" value="{{ request('statut') }}">
    @endif
    <input type="text" name="medicament"
           placeholder="🔎 Rechercher un médicament..."
           value="{{ request('medicament') }}"
           style="width:100%; padding:12px 14px; border-radius:10px;
                  border:1px solid #e5e7eb; outline:none; font-size:14px;
                  background:white; box-shadow:0 2px 8px rgba(0,0,0,0.03);">
</form>

{{-- TABLE --}}
<div style="background:white; padding:12px; border-radius:14px;
            box-shadow:0 6px 20px rgba(0,0,0,0.06); overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse; min-width:900px;">
        <thead>
            <tr style="background:#0f172a; color:white;">
                @foreach(['Nom','Stock','Min','Entrée','Sortie','Fabrication','Expiration','Stock','Expiration','Actions'] as $h)
                    <th style="padding:12px; font-size:12px; text-transform:uppercase;
                               letter-spacing:0.5px; text-align:center;">{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($medicaments as $m)
            @php
                $stock     = $m->stockActuel();
                $statStock = $m->statutStock();
                $statExp   = $m->statutExpiration();

                $badgeStock = match($statStock) {
                    'rupture' => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'RUPTURE'],
                    'alerte'  => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'ALERTE'],
                    default   => ['bg'=>'#dcfce7','color'=>'#166534','label'=>'OK'],
                };

                $badgeExp = match($statExp) {
                    'expired' => ['bg'=>'#fecaca','color'=>'#7f1d1d','label'=>'EXPIRÉ'],
                    'soon'    => ['bg'=>'#ffedd5','color'=>'#9a3412','label'=>'BIENTÔT'],
                    default   => ['bg'=>'#dcfce7','color'=>'#166534','label'=>'OK'],
                };
            @endphp
            <tr style="border-bottom:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <td style="padding:12px; font-weight:700; text-align:center;">{{ $m->nom }}</td>
                <td style="text-align:center;">
                    <span style="background:#0ea5e9; color:white; padding:5px 10px; border-radius:8px; font-weight:700;">
                        {{ $stock }}
                    </span>
                </td>
                <td style="padding:12px; font-weight:700; text-align:center;">{{ $m->quantite_min }}</td>

                {{-- ENTRÉE --}}
                <td style="padding:8px; text-align:center;">
                    <form method="POST" action="{{ route('oncologie.medicaments.entree', $m->id) }}"
                          style="display:flex; gap:4px; justify-content:center;">
                        @csrf
                        <input type="number" name="quantite" min="1"
                               style="width:60px; padding:5px; border-radius:6px;
                                      border:1px solid #e5e7eb; text-align:center;">
                        <button style="background:#10b981; color:white; border:none;
                                       padding:6px 10px; border-radius:6px; cursor:pointer;">
                            <i class="fas fa-plus"></i>
                        </button>
                    </form>
                </td>

                {{-- SORTIE --}}
                <td style="padding:8px; text-align:center;">
                    <form method="POST" action="{{ route('oncologie.medicaments.sortie', $m->id) }}"
                          style="display:flex; gap:4px; justify-content:center;">
                        @csrf
                        <input type="number" name="quantite" min="1"
                               style="width:60px; padding:5px; border-radius:6px;
                                      border:1px solid #e5e7eb; text-align:center;">
                        <button style="background:#ef4444; color:white; border:none;
                                       padding:6px 10px; border-radius:6px; cursor:pointer;">
                            <i class="fas fa-minus"></i>
                        </button>
                    </form>
                </td>

                <td style="padding:12px; text-align:center; color:#334155; font-weight:600;">
                    {{ $m->date_fabrication ? $m->date_fabrication->format('d/m/Y') : '-' }}
                </td>

                <td style="padding:12px; text-align:center; color:#334155; font-weight:600;">
                    {{ $m->date_expiration ? $m->date_expiration->format('d/m/Y') : '-' }}
                </td>

                <td style="text-align:center;">
                    <span style="background:{{ $badgeStock['bg'] }}; color:{{ $badgeStock['color'] }};
                                 padding:5px 10px; border-radius:999px; font-size:11px; font-weight:700;">
                        {{ $badgeStock['label'] }}
                    </span>
                </td>

                <td style="text-align:center;">
                    <span style="background:{{ $badgeExp['bg'] }}; color:{{ $badgeExp['color'] }};
                                 padding:5px 10px; border-radius:999px; font-size:11px; font-weight:700;">
                        {{ $badgeExp['label'] }}
                    </span>
                </td>

                <td style="padding:8px; text-align:center;">
                    <div style="display:flex; gap:5px; justify-content:center;">
                        <a href="{{ route('oncologie.medicaments.show', $m->id) }}"
                           style="background:#3b82f6; color:white; padding:6px 9px;
                                  border-radius:7px; text-decoration:none;">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('oncologie.medicaments.edit', $m->id) }}"
                           style="background:#f59e0b; color:white; padding:6px 9px;
                                  border-radius:7px; text-decoration:none;">
                            <i class="fas fa-pen"></i>
                        </a>
                        <a href="{{ route('oncologie.medicaments.lots', $m->id) }}"
                           style="background:#8b5cf6; color:white; padding:6px 9px;
                                  border-radius:7px; text-decoration:none;" title="Voir lots">
                            <i class="fas fa-boxes"></i>
                        </a>
                        <form method="POST"
                              action="{{ route('oncologie.medicaments.destroy', $m->id) }}"
                              onsubmit="return confirm('Supprimer ce médicament ?')">
                            @csrf @method('DELETE')
                            <button style="background:#dc2626; color:white; border:none;
                                           padding:6px 9px; border-radius:7px; cursor:pointer;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align:center; padding:30px; color:#6b7280;">
                    Aucun médicament trouvé
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection