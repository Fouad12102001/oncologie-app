@extends('layouts.app')
@section('title', 'Référentiels')

@section('content')

<div style="background:linear-gradient(135deg,#264653,#1a3e2b);
            border-radius:18px;padding:24px 28px;margin-bottom:20px;">
    <h1 style="color:white;font-size:22px;font-weight:800;margin:0;">
        📚 Gestion des Référentiels
    </h1>
    <p style="color:rgba(255,255,255,0.6);font-size:13px;margin:4px 0 0;">
        Protocoles thérapeutiques et médicaments standards
    </p>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <!-- PROTOCOLES -->
    <div style="background:white;border-radius:16px;padding:22px;
                box-shadow:0 4px 16px rgba(0,0,0,0.06);">
        <div style="display:flex;justify-content:space-between;align-items:center;
                    margin-bottom:18px;">
            <h3 style="font-weight:800;font-size:16px;color:#1e293b;margin:0;">
                🧬 Protocoles Thérapeutiques
            </h3>
            <button onclick="document.getElementById('modalProtocole').style.display='flex'"
                    style="background:#2a9d8f;color:white;border:none;padding:8px 16px;
                           border-radius:10px;font-weight:600;cursor:pointer;font-size:13px;">
                + Ajouter
            </button>
        </div>

        <div style="max-height:500px;overflow-y:auto;">
            @forelse($protocoles as $p)
            <div style="display:flex;align-items:center;justify-content:space-between;
                        padding:12px 0;border-bottom:1px solid #f1f5f9;">
                <div>
                    <div style="font-weight:700;font-size:14px;color:#1e293b;">
                        {{ $p->nom }}
                    </div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;">
                        @if($p->type_cancer)
                            <span style="background:#fee2e2;color:#991b1b;
                                         padding:2px 8px;border-radius:999px;font-size:10px;
                                         font-weight:600;margin-right:6px;">
                                🔬 {{ $p->type_cancer }}
                            </span>
                        @endif
                        <span style="background:#dbeafe;color:#1d4ed8;
                                     padding:2px 8px;border-radius:999px;font-size:10px;
                                     font-weight:600;">
                            💊 {{ $p->medicaments_count }} médicament(s)
                        </span>
                    </div>
                </div>
                <form action="{{ route('oncologie.admin.referentiels.protocole.destroy', $p->id) }}"
                      method="POST" onsubmit="return confirm('Supprimer ce protocole ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            style="background:#fee2e2;color:#ef4444;border:1px solid #fca5a5;
                                   padding:5px 10px;border-radius:8px;cursor:pointer;
                                   font-size:12px;">
                        🗑
                    </button>
                </form>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:#94a3b8;">
                Aucun protocole défini
            </div>
            @endforelse
        </div>
    </div>

    <!-- MÉDICAMENTS RÉFÉRENTIEL -->
    <div style="background:white;border-radius:16px;padding:22px;
                box-shadow:0 4px 16px rgba(0,0,0,0.06);">
        <div style="display:flex;justify-content:space-between;align-items:center;
                    margin-bottom:18px;">
            <h3 style="font-weight:800;font-size:16px;color:#1e293b;margin:0;">
                💊 Médicaments Enregistrés
            </h3>
            <a href="{{ route('oncologie.medicaments.create') }}"
               style="background:#264653;color:white;border:none;padding:8px 16px;
                      border-radius:10px;font-weight:600;font-size:13px;text-decoration:none;">
                + Ajouter
            </a>
        </div>

        <div style="margin-bottom:12px;">
            <input type="text" id="filterMeds"
                   placeholder="🔍 Rechercher..."
                   onkeyup="filterMedicaments(this.value)"
                   style="width:100%;padding:9px 14px;border-radius:10px;
                          border:2px solid #e2e8f0;font-size:13px;outline:none;">
        </div>

        <div style="max-height:440px;overflow-y:auto;" id="medsList">
            @forelse($medicaments as $m)
            <div class="med-item"
                 data-name="{{ strtolower($m->nom) }}"
                 style="display:flex;align-items:center;justify-content:space-between;
                        padding:10px 0;border-bottom:1px solid #f1f5f9;">
                <div>
                    <div style="font-weight:700;font-size:13px;color:#1e293b;">{{ $m->nom }}</div>
                    <div style="font-size:11px;color:#64748b;margin-top:2px;">
                        Stock : <strong>{{ $m->stockActuel() }}</strong>
                        — Min : {{ $m->quantite_min }}
                    </div>
                </div>
                @php $st = $m->statutStock(); $se = $m->statutExpiration(); @endphp
                <div style="display:flex;gap:5px;align-items:center;">
                    <span style="background:{{ $st==='ok'?'#dcfce7':($st==='alerte'?'#fef3c7':'#fee2e2') }};
                                 color:{{ $st==='ok'?'#166534':($st==='alerte'?'#92400e':'#991b1b') }};
                                 padding:3px 8px;border-radius:999px;font-size:10px;font-weight:700;">
                        {{ strtoupper($st) }}
                    </span>
                    <a href="{{ route('oncologie.medicaments.edit', $m->id) }}"
                       style="background:#f59e0b;color:white;padding:5px 8px;
                              border-radius:7px;text-decoration:none;font-size:11px;">
                        ✏
                    </a>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:#94a3b8;">
                Aucun médicament enregistré
            </div>
            @endforelse
        </div>
    </div>

</div>

<!-- MODAL CRÉER PROTOCOLE -->
<div id="modalProtocole"
     style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
            background:rgba(0,0,0,0.6);z-index:9999;justify-content:center;
            align-items:center;backdrop-filter:blur(4px);"
     onclick="if(event.target===this) this.style.display='none'">
    <div style="background:white;border-radius:20px;padding:30px;
                width:90%;max-width:500px;box-shadow:0 30px 80px rgba(0,0,0,0.3);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
            <h3 style="font-weight:800;font-size:18px;color:#1e293b;margin:0;">
                🧬 Nouveau Protocole
            </h3>
            <button onclick="document.getElementById('modalProtocole').style.display='none'"
                    style="background:#f1f5f9;border:none;width:32px;height:32px;
                           border-radius:8px;cursor:pointer;font-size:16px;color:#64748b;">
                ×
            </button>
        </div>

        <form action="{{ route('oncologie.admin.referentiels.protocole.store') }}" method="POST">
            @csrf
            @foreach([
                ['name'=>'nom',         'label'=>'Nom du protocole *', 'type'=>'text',   'placeholder'=>'ex: FOLFOX, AC-Taxol...', 'required'=>true],
                ['name'=>'type_cancer', 'label'=>'Type de cancer',     'type'=>'text',   'placeholder'=>'ex: Cancer du sein', 'required'=>false],
                ['name'=>'duree',       'label'=>'Durée cycle (jours)','type'=>'number', 'placeholder'=>'ex: 21', 'required'=>false],
            ] as $field)
            <div style="margin-bottom:14px;">
                <label style="display:block;font-size:12px;font-weight:700;
                              color:#374151;margin-bottom:6px;text-transform:uppercase;">
                    {{ $field['label'] }}
                </label>
                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                       placeholder="{{ $field['placeholder'] }}"
                       {{ $field['required'] ? 'required' : '' }}
                       style="width:100%;padding:11px 14px;border-radius:10px;
                              border:2px solid #e2e8f0;font-size:14px;outline:none;">
            </div>
            @endforeach

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:12px;font-weight:700;
                              color:#374151;margin-bottom:6px;text-transform:uppercase;">
                    Description
                </label>
                <textarea name="description" rows="3"
                          placeholder="Description du protocole..."
                          style="width:100%;padding:11px 14px;border-radius:10px;
                                 border:2px solid #e2e8f0;font-size:14px;outline:none;
                                 resize:vertical;"></textarea>
            </div>

            <button type="submit"
                    style="width:100%;background:linear-gradient(135deg,#2a9d8f,#21867a);
                           color:white;border:none;padding:13px;border-radius:12px;
                           font-weight:700;cursor:pointer;font-size:15px;">
                ✅ Créer le protocole
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function filterMedicaments(val) {
    document.querySelectorAll('.med-item').forEach(el => {
        const name = el.dataset.name;
        el.style.display = name.includes(val.toLowerCase()) ? '' : 'none';
    });
}
</script>
@endpush

@endsection