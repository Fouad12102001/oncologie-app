@extends('layouts.app')
@section('title', 'Journal d\'activité')

@section('content')

<div style="background:linear-gradient(135deg,#264653,#1a3e2b);
            border-radius:18px;padding:24px 28px;margin-bottom:20px;">
    <h1 style="color:white;font-size:22px;font-weight:800;margin:0;">
        📋 Journal d'Activité
    </h1>
    <p style="color:rgba(255,255,255,0.6);font-size:13px;margin:4px 0 0;">
        Historique des actions récentes
    </p>
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">

    <!-- DERNIERS PATIENTS -->
    <div style="background:white;border-radius:16px;padding:20px;
                box-shadow:0 4px 16px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:15px;color:#1e293b;margin-bottom:14px;
                   display:flex;align-items:center;gap:8px;">
            <span style="background:#2a9d8f;color:white;width:28px;height:28px;
                         border-radius:8px;display:inline-flex;align-items:center;
                         justify-content:center;font-size:14px;">👥</span>
            Derniers Patients
        </h3>
        @forelse($derniersPat as $p)
        <div style="display:flex;align-items:center;gap:10px;padding:9px 0;
                    border-bottom:1px solid #f1f5f9;">
            <div style="width:34px;height:34px;border-radius:8px;flex-shrink:0;
                        background:#2a9d8f20;display:flex;align-items:center;
                        justify-content:center;font-size:16px;">👤</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:#1e293b;
                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $p->nom }} {{ $p->prenom }}
                </div>
                <div style="font-size:11px;color:#94a3b8;">
                    {{ $p->created_at->diffForHumans() }}
                </div>
            </div>
            <span style="background:{{ $p->est_vivant ? '#dcfce7' : '#fee2e2' }};
                         color:{{ $p->est_vivant ? '#166534' : '#991b1b' }};
                         padding:3px 8px;border-radius:999px;font-size:10px;font-weight:700;
                         flex-shrink:0;">
                {{ $p->est_vivant ? '🟢' : '🔴' }}
            </span>
        </div>
        @empty
        <p style="text-align:center;color:#94a3b8;font-size:13px;padding:16px 0;">
            Aucune activité
        </p>
        @endforelse
    </div>

    <!-- DERNIÈRES PRESCRIPTIONS -->
    <div style="background:white;border-radius:16px;padding:20px;
                box-shadow:0 4px 16px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:15px;color:#1e293b;margin-bottom:14px;
                   display:flex;align-items:center;gap:8px;">
            <span style="background:#264653;color:white;width:28px;height:28px;
                         border-radius:8px;display:inline-flex;align-items:center;
                         justify-content:center;font-size:14px;">📋</span>
            Dernières Prescriptions
        </h3>
        @forelse($derniersPresc as $pr)
        <div style="display:flex;align-items:center;gap:10px;padding:9px 0;
                    border-bottom:1px solid #f1f5f9;">
            <div style="width:34px;height:34px;border-radius:8px;flex-shrink:0;
                        background:#26465320;display:flex;align-items:center;
                        justify-content:center;font-size:16px;">📋</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:#1e293b;">
                    {{ optional($pr->patient)->nom ?? 'N/A' }}
                </div>
                <div style="font-size:11px;color:#94a3b8;">
                    {{ $pr->created_at->diffForHumans() }}
                </div>
            </div>
            @php
                $color = match($pr->statut) {
                    'validee'   => ['bg'=>'#dcfce7','c'=>'#166534','label'=>'✅'],
                    'annulee'   => ['bg'=>'#fee2e2','c'=>'#991b1b','label'=>'❌'],
                    default     => ['bg'=>'#fef3c7','c'=>'#92400e','label'=>'⏳'],
                };
            @endphp
            <span style="background:{{ $color['bg'] }};color:{{ $color['c'] }};
                         padding:3px 8px;border-radius:999px;font-size:10px;font-weight:700;
                         flex-shrink:0;">
                {{ $color['label'] }}
            </span>
        </div>
        @empty
        <p style="text-align:center;color:#94a3b8;font-size:13px;padding:16px 0;">
            Aucune activité
        </p>
        @endforelse
    </div>

    <!-- DERNIÈRES DISPENSATIONS -->
    <div style="background:white;border-radius:16px;padding:20px;
                box-shadow:0 4px 16px rgba(0,0,0,0.06);">
        <h3 style="font-weight:800;font-size:15px;color:#1e293b;margin-bottom:14px;
                   display:flex;align-items:center;gap:8px;">
            <span style="background:#6a4c93;color:white;width:28px;height:28px;
                         border-radius:8px;display:inline-flex;align-items:center;
                         justify-content:center;font-size:14px;">💊</span>
            Dernières Dispensations
        </h3>
        @forelse($derniersDisp as $d)
        <div style="display:flex;align-items:center;gap:10px;padding:9px 0;
                    border-bottom:1px solid #f1f5f9;">
            <div style="width:34px;height:34px;border-radius:8px;flex-shrink:0;
                        background:#6a4c9320;display:flex;align-items:center;
                        justify-content:center;font-size:16px;">💊</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:#1e293b;
                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ optional(optional($d->prescription)->patient)->nom ?? 'N/A' }}
                </div>
                <div style="font-size:11px;color:#94a3b8;">
                    {{ optional($d->date)->diffForHumans() }}
                </div>
            </div>
            <span style="background:#6a4c9320;color:#6a4c93;
                         padding:3px 8px;border-radius:999px;font-size:10px;font-weight:700;
                         flex-shrink:0;">
                ×{{ $d->quantite }}
            </span>
        </div>
        @empty
        <p style="text-align:center;color:#94a3b8;font-size:13px;padding:16px 0;">
            Aucune activité
        </p>
        @endforelse
    </div>

</div>

@endsection