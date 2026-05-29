<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pharmacie Oncologie') — CLCC</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:      #2a9d8f;
            --primary-dark: #21867a;
            --secondary:    #264653;
            --accent:       #e76f51;
            --bg:           #f0f4f8;
            --sidebar-bg:   #0f172a;
            --sidebar-text: #94a3b8;
            --card-bg:      #ffffff;
            --border:       #e2e8f0;
            --text:         #1e293b;
            --text-muted:   #64748b;
            --success:      #22c55e;
            --danger:       #ef4444;
            --warning:      #f59e0b;
            --info:         #3b82f6;
            --radius:       14px;
            --shadow:       0 4px 20px rgba(0,0,0,0.06);
            --shadow-lg:    0 10px 40px rgba(0,0,0,0.1);
            --transition:   0.25s cubic-bezier(0.4,0,0.2,1);
            --sidebar-w:    260px;
            --header-h:     60px;
        }

        [data-theme="dark"] {
            --bg:       #0f172a;
            --card-bg:  #1e293b;
            --border:   #334155;
            --text:     #f1f5f9;
            --text-muted: #94a3b8;
            --sidebar-bg: #020617;
        }

        * {
            box-sizing: border-box;
            margin: 0; padding: 0;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background var(--transition), color var(--transition);
        }

        /* ========================
           TOPBAR
        ======================== */
        .topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--header-h);
            background: var(--sidebar-bg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px 0 calc(var(--sidebar-w) + 20px);
            z-index: 100;
            box-shadow: 0 2px 20px rgba(0,0,0,0.2);
            transition: padding var(--transition);
        }

        .topbar.sidebar-collapsed {
            padding-left: 80px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .sidebar-toggle {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.08);
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            font-size: 16px;
        }

        .sidebar-toggle:hover {
            background: rgba(42,157,143,0.3);
            color: var(--primary);
        }

        .breadcrumb-onco {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #94a3b8;
        }

        .breadcrumb-onco .sep {
            color: #475569;
        }

        .breadcrumb-onco .current {
            color: white;
            font-weight: 600;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-btn {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #94a3b8;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: var(--transition);
            text-decoration: none;
            position: relative;
        }

        .topbar-btn:hover {
            background: rgba(42,157,143,0.2);
            color: var(--primary);
            border-color: rgba(42,157,143,0.3);
        }

        .topbar-btn .notif-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid var(--sidebar-bg);
        }

        /* USER MENU */
        .user-menu {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.06);
            transition: var(--transition);
            position: relative;
        }

        .user-menu:hover {
            background: rgba(42,157,143,0.15);
            border-color: rgba(42,157,143,0.3);
        }

        .user-avatar {
            width: 30px; height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: white;
        }

        .user-role-badge {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 999px;
            font-weight: 600;
        }

        .role-medecin      { background: #1d4ed820; color: #60a5fa; border: 1px solid #1d4ed840; }
        .role-pharmacien   { background: #16653020; color: #4ade80; border: 1px solid #16653040; }
        .role-infirmier    { background: #9d174d20; color: #f472b6; border: 1px solid #9d174d40; }
        .role-administrateur { background: #92400e20; color: #fbbf24; border: 1px solid #92400e40; }

        .dropdown-menu-onco {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: var(--sidebar-bg);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            width: 200px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            display: none;
            z-index: 200;
        }

        .dropdown-menu-onco.open {
            display: block;
            animation: dropDown 0.2s ease;
        }

        @keyframes dropDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .dropdown-menu-onco a,
        .dropdown-menu-onco button {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            font-size: 13px;
            color: #94a3b8;
            text-decoration: none;
            width: 100%;
            border: none;
            background: none;
            text-align: left;
            cursor: pointer;
            transition: var(--transition);
        }

        .dropdown-menu-onco a:hover,
        .dropdown-menu-onco button:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }

        .dropdown-menu-onco .separator {
            height: 1px;
            background: rgba(255,255,255,0.08);
            margin: 4px 0;
        }

        /* ========================
           SIDEBAR
        ======================== */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 101;
            transition: width var(--transition), transform var(--transition);
            overflow: hidden;
            box-shadow: 4px 0 20px rgba(0,0,0,0.2);
        }

        .sidebar.collapsed {
            width: 60px;
        }

        .sidebar-brand {
            height: var(--header-h);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 16px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
            text-decoration: none;
        }

        .brand-icon {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(42,157,143,0.4);
        }

        .brand-text {
            overflow: hidden;
            transition: opacity var(--transition);
        }

        .brand-text .name {
            font-size: 14px;
            font-weight: 800;
            color: white;
            white-space: nowrap;
        }

        .brand-text .sub {
            font-size: 10px;
            color: #475569;
            white-space: nowrap;
        }

        .sidebar.collapsed .brand-text,
        .sidebar.collapsed .nav-label,
        .sidebar.collapsed .section-title span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        /* NAV */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 12px 8px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.1) transparent;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 8px 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #334155;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.06);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--sidebar-text);
            font-size: 13px;
            font-weight: 500;
            transition: var(--transition);
            margin-bottom: 2px;
            position: relative;
            white-space: nowrap;
        }

        .nav-item .nav-icon {
            width: 34px; height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            background: rgba(255,255,255,0.04);
            transition: var(--transition);
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: white;
        }

        .nav-item:hover .nav-icon {
            background: rgba(42,157,143,0.2);
            color: var(--primary);
        }

        .nav-item.active {
            background: rgba(42,157,143,0.15);
            color: var(--primary);
        }

        .nav-item.active .nav-icon {
            background: var(--primary);
            color: white;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 3px;
            background: var(--primary);
            border-radius: 0 3px 3px 0;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            flex-shrink: 0;
        }

        /* TOOLTIP sidebar collapsed */
        .sidebar.collapsed .nav-item {
            justify-content: center;
        }

        .sidebar.collapsed .nav-item .nav-label,
        .sidebar.collapsed .nav-item .nav-badge {
            display: none;
        }

        /* SIDEBAR FOOTER */
        .sidebar-footer {
            padding: 12px 8px;
            border-top: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }

        /* ========================
           MAIN CONTENT
        ======================== */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            padding-top: var(--header-h);
            min-height: 100vh;
            transition: margin-left var(--transition);
        }

        .main-wrapper.sidebar-collapsed {
            margin-left: 60px;
        }

        .main-content {
            padding: 24px;
            max-width: 1400px;
        }

        /* ========================
           FLASH MESSAGES
        ======================== */
        .flash-container {
            position: fixed;
            top: calc(var(--header-h) + 16px);
            right: 20px;
            z-index: 999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-width: 380px;
        }

        .flash-msg {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            box-shadow: var(--shadow-lg);
            animation: slideIn 0.3s ease;
            cursor: pointer;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }

        .flash-msg.hiding {
            animation: slideOut 0.3s ease forwards;
        }

        @keyframes slideOut {
            to { transform: translateX(100%); opacity: 0; }
        }

        .flash-success {
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
        }

        .flash-error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .flash-warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .flash-icon {
            font-size: 16px;
            margin-top: 1px;
            flex-shrink: 0;
        }

        .flash-close {
            margin-left: auto;
            opacity: 0.5;
            cursor: pointer;
            font-size: 14px;
            flex-shrink: 0;
        }

        .flash-close:hover { opacity: 1; }

        /* ========================
           SEARCH OVERLAY
        ======================== */
        .search-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 999;
            display: none;
            justify-content: center;
            align-items: flex-start;
            padding-top: 80px;
            backdrop-filter: blur(4px);
        }

        .search-overlay.open {
            display: flex;
            animation: fadeIn 0.2s ease;
        }

        .search-box-big {
            background: var(--card-bg);
            border-radius: 16px;
            width: 600px;
            max-width: 90vw;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .search-box-big input {
            width: 100%;
            padding: 18px 24px;
            border: none;
            font-size: 16px;
            outline: none;
            background: transparent;
            color: var(--text);
        }

        .search-results {
            border-top: 1px solid var(--border);
            padding: 8px;
            max-height: 300px;
            overflow-y: auto;
        }

        .search-result-item {
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-result-item:hover {
            background: var(--bg);
        }

        /* ========================
           DARK MODE TOGGLE
        ======================== */
        .theme-toggle {
            width: 44px; height: 24px;
            background: #334155;
            border-radius: 12px;
            position: relative;
            cursor: pointer;
            border: none;
            transition: background 0.3s;
        }

        .theme-toggle::after {
            content: '🌙';
            position: absolute;
            top: 2px; left: 2px;
            width: 20px; height: 20px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            transition: transform 0.3s;
        }

        .theme-toggle.dark::after {
            transform: translateX(20px);
            content: '☀️';
        }

        .theme-toggle.dark {
            background: var(--primary);
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- ======================== SIDEBAR ======================== -->
<aside class="sidebar" id="sidebar">

    <a href="{{ route('oncologie.dashboard') }}" class="sidebar-brand">
        <div class="brand-icon">🏥</div>
        <div class="brand-text">
            <div class="name">CLCC</div>
            <div class="sub">Draâ Ben Khedda</div>
        </div>
    </a>

    <nav class="sidebar-nav">

        <!-- PRINCIPAL -->
        <div class="section-title">
            <span>Principal</span>
        </div>

        <a href="{{ route('oncologie.dashboard') }}"
           class="nav-item {{ request()->routeIs('oncologie.dashboard') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-tachometer-alt"></i></div>
            <span class="nav-label">Dashboard</span>
        </a>

        <!-- CLINIQUE -->
        <div class="section-title">
            <span>Clinique</span>
        </div>

        <a href="{{ route('oncologie.patients.index') }}"
           class="nav-item {{ request()->routeIs('oncologie.patients.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-user-injured"></i></div>
            <span class="nav-label">Patients</span>
        </a>

        <a href="{{ route('oncologie.prescriptions.index') }}"
           class="nav-item {{ request()->routeIs('oncologie.prescriptions.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-file-prescription"></i></div>
            <span class="nav-label">Prescriptions</span>
        </a>

        <a href="{{ route('oncologie.dispensations.index') }}"
           class="nav-item {{ request()->routeIs('oncologie.dispensations.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-hand-holding-medical"></i></div>
            <span class="nav-label">Dispensations</span>
        </a>

        <!-- PHARMACIE -->
        <div class="section-title">
            <span>Pharmacie</span>
        </div>

        <a href="{{ route('oncologie.medicaments.index') }}"
           class="nav-item {{ request()->routeIs('oncologie.medicaments.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-pills"></i></div>
            <span class="nav-label">Médicaments</span>
        </a>

        <a href="{{ route('oncologie.lots.index') }}"
           class="nav-item {{ request()->routeIs('oncologie.lots.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-boxes"></i></div>
            <span class="nav-label">Lots Et Stock</span>
        </a>

        <!-- RAPPORTS -->
        <div class="section-title">
            <span>Rapports</span>
        </div>

        {{-- Ajouter dans la sidebar après "Rapports" --}}

<!-- ADMINISTRATION (admin uniquement) -->
@if(Auth::guard('oncologie')->check() && Auth::guard('oncologie')->user()->role === 'administrateur')
<div class="section-title">
    <span>Administration</span>
</div>

<a href="{{ route('oncologie.admin.utilisateurs') }}"
   class="nav-item {{ request()->routeIs('oncologie.admin.utilisateurs') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-users-cog"></i></div>
    <span class="nav-label">Utilisateurs</span>
</a>

<a href="{{ route('oncologie.admin.logs') }}"
   class="nav-item {{ request()->routeIs('oncologie.admin.logs') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-clipboard-list"></i></div>
    <span class="nav-label">Journal d'activité</span>
</a>

<a href="{{ route('oncologie.admin.referentiels') }}"
   class="nav-item {{ request()->routeIs('oncologie.admin.referentiels') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-book-medical"></i></div>
    <span class="nav-label">Référentiels</span>
</a>
@endif

<!-- PARAMÈTRES (tous) -->
<div class="section-title">
    <span>Compte</span>
</div>

<a href="{{ route('oncologie.parametres.index') }}"
   class="nav-item {{ request()->routeIs('oncologie.parametres.*') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-cog"></i></div>
    <span class="nav-label">Paramètres</span>
</a>

<a href="{{ route('oncologie.statistiques.index') }}"
   class="nav-item {{ request()->routeIs('oncologie.statistiques.*') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-chart-pie"></i></div>
    <span class="nav-label">Statistiques</span>
</a>

    

        <a href="{{ route('oncologie.prescriptions.export') }}"
           class="nav-item">
            <div class="nav-icon"><i class="fas fa-download"></i></div>
            <span class="nav-label">Exports</span>
        </a>

    </nav>

    <!-- SIDEBAR FOOTER -->
    <div class="sidebar-footer">
        @auth('oncologie')
        @php $user = Auth::guard('oncologie')->user(); @endphp
        <div style="display:flex; align-items:center; gap:10px; padding:10px 8px;
                    border-radius:10px; background:rgba(255,255,255,0.04);">
            <div class="user-avatar"
                 style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div class="nav-label" style="overflow:hidden;">
                <div style="font-size:12px; font-weight:700; color:white;
                            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $user->name }}
                </div>
                <span class="user-role-badge role-{{ $user->role }}" style="font-size:10px;">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
        </div>
        @endauth
    </div>
</aside>

<!-- ======================== TOPBAR ======================== -->
<header class="topbar" id="topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle" title="Réduire/Agrandir le menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="breadcrumb-onco">
            <span>CLCC</span>
            <span class="sep">/</span>
            <span class="current">@yield('title', 'Dashboard')</span>
        </div>
    </div>

    <div class="topbar-right">

        <!-- RECHERCHE -->
        <button class="topbar-btn" title="Recherche (Ctrl+K)"
                onclick="openSearch()" id="searchBtn">
            <i class="fas fa-search"></i>
        </button>

        <!-- NOTIFICATIONS -->
        <button class="topbar-btn" title="Alertes stock" id="notifBtn">
            <i class="fas fa-bell"></i>
            @php
                try {
                    $alertCount = \App\Models\Oncologie\Medicament::with('mouvements')->get()
                        ->filter(fn($m) => $m->enRupture() || $m->estExpire())->count();
                } catch(\Exception $e) { $alertCount = 0; }
            @endphp
            @if($alertCount > 0)
                <span class="notif-dot"></span>
            @endif
        </button>

        <!-- DARK MODE -->
        <button class="theme-toggle" id="themeToggle" title="Mode sombre/clair"></button>

        <!-- USER MENU -->
        @auth('oncologie')
        @php $u = Auth::guard('oncologie')->user(); @endphp
        <div class="user-menu" id="userMenuBtn" onclick="toggleUserMenu()">
            <div class="user-avatar"
                 style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));">
                {{ strtoupper(substr($u->name, 0, 2)) }}
            </div>
            <div style="display:flex; flex-direction:column; gap:2px;">
                <span class="user-name">{{ Str::limit($u->name, 16) }}</span>
                <span class="user-role-badge role-{{ $u->role }}">{{ ucfirst($u->role) }}</span>
            </div>
            <i class="fas fa-chevron-down" style="color:#64748b; font-size:11px;"></i>

            <!-- DROPDOWN -->
            <div class="dropdown-menu-onco" id="userDropdown">
                <a href="#"><i class="fas fa-user-circle"></i> Mon profil</a>
                <a href="#"><i class="fas fa-cog"></i> Paramètres</a>
                <div class="separator"></div>
                <a href="{{ route('oncologie.prescriptions.stats') }}">
                    <i class="fas fa-chart-line"></i> Statistiques prescription
                </a>
                <div class="separator"></div>
                <form action="{{ route('oncologie.logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="color:#ef4444;">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </div>
</header>

<!-- ======================== SEARCH OVERLAY ======================== -->
<div class="search-overlay" id="searchOverlay" onclick="if(event.target===this) closeSearch()">
    <div class="search-box-big">
        <input type="text" id="searchInput"
               placeholder="🔍  Rechercher un patient, médicament, prescription..."
               oninput="handleSearch(this.value)"
               autocomplete="off">
        <div class="search-results" id="searchResults">
            <div class="search-result-item" style="color:#9ca3af; font-size:12px;">
                Commencez à taper pour rechercher...
            </div>
        </div>
    </div>
</div>

<!-- ======================== FLASH MESSAGES ======================== -->
<div class="flash-container" id="flashContainer">
    @if(session('success'))
        <div class="flash-msg flash-success" onclick="closeFlash(this)">
            <span class="flash-icon">✅</span>
            <span>{{ session('success') }}</span>
            <span class="flash-close"><i class="fas fa-times"></i></span>
        </div>
    @endif
    @if(session('error'))
        <div class="flash-msg flash-error" onclick="closeFlash(this)">
            <span class="flash-icon">❌</span>
            <span>{{ session('error') }}</span>
            <span class="flash-close"><i class="fas fa-times"></i></span>
        </div>
    @endif
    @if(session('warning'))
        <div class="flash-msg flash-warning" onclick="closeFlash(this)">
            <span class="flash-icon">⚠️</span>
            <span>{{ session('warning') }}</span>
            <span class="flash-close"><i class="fas fa-times"></i></span>
        </div>
    @endif
</div>

<!-- ======================== MAIN ======================== -->
<div class="main-wrapper" id="mainWrapper">
    <div class="main-content">
        @yield('content')
    </div>
</div>

@once
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endonce

<script>
// ========================
// SIDEBAR TOGGLE
// ========================
const sidebar      = document.getElementById('sidebar');
const mainWrapper  = document.getElementById('mainWrapper');
const topbar       = document.getElementById('topbar');
let collapsed      = localStorage.getItem('sidebar_collapsed') === 'true';

function applySidebar() {
    if (collapsed) {
        sidebar.classList.add('collapsed');
        mainWrapper.classList.add('sidebar-collapsed');
        topbar.classList.add('sidebar-collapsed');
    } else {
        sidebar.classList.remove('collapsed');
        mainWrapper.classList.remove('sidebar-collapsed');
        topbar.classList.remove('sidebar-collapsed');
    }
}

applySidebar();

document.getElementById('sidebarToggle').addEventListener('click', () => {
    collapsed = !collapsed;
    localStorage.setItem('sidebar_collapsed', collapsed);
    applySidebar();
});

// ========================
// USER DROPDOWN
// ========================
function toggleUserMenu() {
    document.getElementById('userDropdown').classList.toggle('open');
}

document.addEventListener('click', (e) => {
    const btn = document.getElementById('userMenuBtn');
    if (btn && !btn.contains(e.target)) {
        document.getElementById('userDropdown')?.classList.remove('open');
    }
});

// ========================
// DARK MODE
// ========================
const themeToggle = document.getElementById('themeToggle');
let darkMode      = localStorage.getItem('dark_mode') === 'true';

function applyTheme() {
    document.documentElement.setAttribute('data-theme', darkMode ? 'dark' : 'light');
    if (darkMode) themeToggle.classList.add('dark');
    else          themeToggle.classList.remove('dark');
}

applyTheme();

themeToggle.addEventListener('click', () => {
    darkMode = !darkMode;
    localStorage.setItem('dark_mode', darkMode);
    applyTheme();
});

// ========================
// SEARCH OVERLAY
// ========================
function openSearch() {
    document.getElementById('searchOverlay').classList.add('open');
    setTimeout(() => document.getElementById('searchInput').focus(), 100);
}

function closeSearch() {
    document.getElementById('searchOverlay').classList.remove('open');
    document.getElementById('searchInput').value = '';
    document.getElementById('searchResults').innerHTML =
        '<div class="search-result-item" style="color:#9ca3af;font-size:12px;">Commencez à taper...</div>';
}

// Ctrl+K
document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        openSearch();
    }
    if (e.key === 'Escape') closeSearch();
});

function handleSearch(val) {
    const results = document.getElementById('searchResults');
    if (!val.trim()) {
        results.innerHTML = '<div class="search-result-item" style="color:#9ca3af;font-size:12px;">Commencez à taper...</div>';
        return;
    }

    // Liens rapides côté client
    const quickLinks = [
        { icon: '👤', label: 'Patients', url: '{{ route("oncologie.patients.index") }}' },
        { icon: '📋', label: 'Prescriptions', url: '{{ route("oncologie.prescriptions.index") }}' },
        { icon: '💊', label: 'Médicaments', url: '{{ route("oncologie.medicaments.index") }}' },
        { icon: '📦', label: 'Lots', url: '{{ route("oncologie.lots.index") }}' },
        { icon: '🔬', label: 'Dispensations', url: '{{ route("oncologie.dispensations.index") }}' },
        { icon: '📊', label: 'Statistiques', url: '{{ route("oncologie.prescriptions.stats") }}' },
    ];

    const filtered = quickLinks.filter(l =>
        l.label.toLowerCase().includes(val.toLowerCase())
    );

    if (filtered.length === 0) {
        results.innerHTML = '<div class="search-result-item" style="color:#9ca3af;font-size:12px;">Aucun résultat trouvé</div>';
        return;
    }

    results.innerHTML = filtered.map(l =>
        `<a href="${l.url}" class="search-result-item" style="text-decoration:none;color:inherit;">
            <span style="font-size:18px;">${l.icon}</span>
            <span>${l.label}</span>
        </a>`
    ).join('');
}

// ========================
// FLASH AUTO-CLOSE
// ========================
function closeFlash(el) {
    el.classList.add('hiding');
    setTimeout(() => el.remove(), 300);
}

document.querySelectorAll('.flash-msg').forEach(msg => {
    setTimeout(() => closeFlash(msg), 5000);
});
</script>

@stack('scripts')
</body>
</html>