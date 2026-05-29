<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — CLCC Draâ Ben Khedda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }

        /* PARTICULES ANIMÉES */
        .particles {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 4px; height: 4px;
            background: rgba(42,157,143,0.4);
            border-radius: 50%;
            animation: float linear infinite;
        }

        @keyframes float {
            0%   { transform: translateY(100vh) translateX(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { transform: translateY(-100px) translateX(100px); opacity: 0; }
        }

        /* CARTE PRINCIPALE */
        .auth-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 960px;
            margin: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
        }

        /* PANNEAU GAUCHE */
        .auth-left {
            background: linear-gradient(160deg, #2a9d8f, #264653);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 250px; height: 250px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }

        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 200px; height: 200px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }

        .auth-left .logo-area {
            position: relative; z-index: 2;
        }

        .auth-left .hospital-icon {
            font-size: 56px;
            margin-bottom: 16px;
            display: block;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50%       { transform: scale(1.08); }
        }

        .auth-left h1 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .auth-left .subtitle {
            font-size: 13px;
            opacity: 0.8;
            line-height: 1.5;
        }

        .auth-left .features {
            position: relative; z-index: 2;
        }

        .auth-left .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            font-size: 13px;
            opacity: 0.9;
        }

        .auth-left .feature-item .fi-icon {
            width: 34px; height: 34px;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .auth-left .bottom-badge {
            position: relative; z-index: 2;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 12px;
            opacity: 0.9;
            backdrop-filter: blur(10px);
        }

        /* PANNEAU DROIT */
        .auth-right {
            background: white;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        /* TABS */
        .auth-tabs {
            display: flex;
            border-bottom: 1px solid #e5e7eb;
        }

        .auth-tab {
            flex: 1;
            padding: 18px;
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            color: #9ca3af;
            cursor: pointer;
            border: none;
            background: none;
            transition: 0.2s;
            border-bottom: 3px solid transparent;
        }

        .auth-tab.active {
            color: #2a9d8f;
            border-bottom-color: #2a9d8f;
            background: #f0fffe;
        }

        .auth-tab:hover {
            background: #f9fafb;
            color: #374151;
        }

        /* FORMULAIRES */
        .tab-content-area {
            padding: 32px;
            flex: 1;
            overflow-y: auto;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .form-title {
            font-size: 20px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .form-subtitle {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 16px;
        }

        .input-group-custom label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .input-group-custom .input-wrap {
            position: relative;
        }

        .input-group-custom .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 15px;
            z-index: 2;
        }

        .input-group-custom input,
        .input-group-custom select {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            color: #1f2937;
            background: #f9fafb;
            transition: 0.2s;
            outline: none;
        }

        .input-group-custom input:focus,
        .input-group-custom select:focus {
            border-color: #2a9d8f;
            background: white;
            box-shadow: 0 0 0 4px rgba(42,157,143,0.1);
        }

        .input-group-custom .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            font-size: 15px;
            z-index: 2;
            transition: 0.2s;
        }

        .input-group-custom .toggle-pw:hover {
            color: #2a9d8f;
        }

        /* STRENGTH METER */
        .strength-meter {
            margin-top: 6px;
        }

        .strength-bar {
            display: flex;
            gap: 4px;
            margin-bottom: 4px;
        }

        .strength-bar span {
            flex: 1;
            height: 4px;
            border-radius: 2px;
            background: #e5e7eb;
            transition: 0.3s;
        }

        .strength-label {
            font-size: 11px;
            color: #9ca3af;
        }

        /* ROLE SELECTOR */
        .role-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 16px;
        }

        .role-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px;
            cursor: pointer;
            text-align: center;
            transition: 0.2s;
            background: #f9fafb;
        }

        .role-card:hover {
            border-color: #2a9d8f;
            background: #f0fffe;
        }

        .role-card.selected {
            border-color: #2a9d8f;
            background: linear-gradient(135deg, #f0fffe, #e6f7f5);
        }

        .role-card input[type="radio"] {
            display: none;
        }

        .role-card .role-icon {
            font-size: 22px;
            margin-bottom: 4px;
        }

        .role-card .role-label {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
        }

        .role-card.selected .role-label {
            color: #2a9d8f;
        }

        /* BOUTON SUBMIT */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2a9d8f, #21867a);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: 0.5s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(42,157,143,0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* ALERT BOXES */
        .alert-custom {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-error {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }

        .alert-success {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            color: #166534;
        }

        .alert-warning {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }

        /* DIVIDER */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 16px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            font-size: 12px;
            color: #9ca3af;
            white-space: nowrap;
        }

        /* TENTATIVES RESTANTES */
        .attempts-indicator {
            display: flex;
            gap: 6px;
            justify-content: center;
            margin-top: 10px;
        }

        .attempt-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            background: #22c55e;
            transition: 0.3s;
        }

        .attempt-dot.used {
            background: #ef4444;
        }

        /* BADGE RÔLE CONNECTÉ */
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .role-medecin      { background: #dbeafe; color: #1d4ed8; }
        .role-pharmacien   { background: #dcfce7; color: #166534; }
        .role-infirmier    { background: #fce7f3; color: #9d174d; }
        .role-administrateur { background: #fef3c7; color: #92400e; }

        /* FOOTER */
        .auth-footer {
            padding: 16px 32px;
            border-top: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .auth-footer span {
            font-size: 11px;
            color: #9ca3af;
        }

        /* LINK */
        .link-teal {
            color: #2a9d8f;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .link-teal:hover {
            text-decoration: underline;
        }

        /* REMEMBER + FORGOT */
        .remember-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-check {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #6b7280;
            cursor: pointer;
        }

        .remember-check input {
            width: 16px; height: 16px;
            accent-color: #2a9d8f;
            cursor: pointer;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .auth-wrapper {
                grid-template-columns: 1fr;
                max-width: 440px;
            }
            .auth-left {
                padding: 30px;
                min-height: auto;
            }
            .auth-left .features {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- PARTICULES -->
<div class="particles" id="particles"></div>

<!-- WRAPPER PRINCIPAL -->
<div class="auth-wrapper">

    <!-- ============ PANNEAU GAUCHE ============ -->
    <div class="auth-left">
        <div class="logo-area">
            <span class="hospital-icon">🏥</span>
            <h1>CLCC<br>Draâ Ben Khedda</h1>
            <p class="subtitle">
                Centre de Lutte Contre le Cancer<br>
                Système de Gestion Pharmacie Oncologique
            </p>
        </div>

        <div class="features">
            @foreach([
                ['💊','Gestion des médicaments cytotoxiques'],
                ['📋','Prescription et validation médicale'],
                ['🔬','Calcul automatique des doses (SC)'],
                ['📦','Traçabilité FIFO des lots'],
                ['👤','Dossier patient numérique complet'],
                ['📊','Statistiques et tableaux de bord'],
            ] as $feat)
                <div class="feature-item">
                    <div class="fi-icon">{{ $feat[0] }}</div>
                    <span>{{ $feat[1] }}</span>
                </div>
            @endforeach
        </div>

        <div class="bottom-badge">
            🔒 Accès sécurisé — Données confidentielles<br>
            <small style="opacity:0.7;">Wilaya de Tizi Ouzou — Algérie</small>
        </div>
    </div>

    <!-- ============ PANNEAU DROIT ============ -->
    <div class="auth-right">

        <!-- TABS -->
        <div class="auth-tabs">
            <button class="auth-tab active" onclick="switchTab('login', this)">
                <i class="fas fa-sign-in-alt"></i> Connexion
            </button>
            <button class="auth-tab" onclick="switchTab('register', this)">
                <i class="fas fa-user-plus"></i> Inscription
            </button>
            <button class="auth-tab" onclick="switchTab('forgot', this)">
                <i class="fas fa-key"></i> Mot de passe
            </button>
        </div>

        <div class="tab-content-area">

            <!-- ============ TAB : LOGIN ============ -->
            <div class="tab-pane active" id="tab-login">

                <h2 class="form-title">Bon retour 👋</h2>
                <p class="form-subtitle">Connectez-vous à votre espace sécurisé</p>

                @if(session('locked'))
                    <div class="alert-custom alert-error">
                        <i class="fas fa-lock" style="margin-top:2px;"></i>
                        <div>
                            <strong>Compte verrouillé</strong><br>
                            Après 3 tentatives échouées, votre compte a été suspendu.
                            Contactez l'administrateur système.
                        </div>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert-custom alert-success">
                        <i class="fas fa-check-circle" style="margin-top:2px;"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-custom alert-error">
                        <i class="fas fa-exclamation-triangle" style="margin-top:2px;"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('oncologie.login.post') }}" method="POST" id="loginForm">
                    @csrf

                    <div class="input-group-custom">
                        <label>Adresse email</label>
                        <div class="input-wrap">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email"
                                   value="{{ old('email') }}"
                                   placeholder="exemple@clcc.dz"
                                   required autofocus
                                   autocomplete="email">
                        </div>
                    </div>

                    <div class="input-group-custom">
                        <label>Mot de passe</label>
                        <div class="input-wrap">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" id="loginPw"
                                   placeholder="••••••••"
                                   required
                                   autocomplete="current-password">
                            <i class="fas fa-eye toggle-pw" onclick="togglePw('loginPw', this)"></i>
                        </div>
                    </div>

                    <div class="remember-row">
                        <label class="remember-check">
                            <input type="checkbox" name="remember">
                            Se souvenir de moi
                        </label>
                        <a href="#" class="link-teal"
                           onclick="switchTab('forgot', document.querySelector('.auth-tab:nth-child(3)'))">
                            Mot de passe oublié ?
                        </a>
                    </div>

                    <!-- INDICATEUR TENTATIVES -->
                    @if(session('attempts_left'))
                        <div class="alert-custom alert-warning" style="margin-bottom:12px;">
                            <i class="fas fa-exclamation-circle" style="margin-top:2px;"></i>
                            <div>
                                Identifiants incorrects.
                                <strong>{{ session('attempts_left') }} tentative(s) restante(s)</strong>
                                avant verrouillage.
                            </div>
                        </div>
                        <div class="attempts-indicator" style="margin-bottom:16px;">
                            @for($i = 0; $i < 3; $i++)
                                <div class="attempt-dot {{ $i >= session('attempts_left') ? 'used' : '' }}"></div>
                            @endfor
                        </div>
                    @endif

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                </form>

                <div class="divider"><span>Rôles disponibles</span></div>

                <div style="display:flex; gap:6px; flex-wrap:wrap; justify-content:center;">
                    @foreach([
                        ['role-medecin',       '🩺','Médecin'],
                        ['role-pharmacien',    '💊','Pharmacien'],
                        ['role-infirmier',     '👩‍⚕️','Infirmier'],
                        ['role-administrateur','⚙️','Admin'],
                    ] as $r)
                        <span class="role-badge {{ $r[0] }}">{{ $r[1] }} {{ $r[2] }}</span>
                    @endforeach
                </div>
            </div>

            <!-- ============ TAB : REGISTER ============ -->
            <div class="tab-pane" id="tab-register">

                <h2 class="form-title">Créer un compte ✨</h2>
                <p class="form-subtitle">Inscription réservée au personnel CLCC autorisé</p>

                @if(session('register_success'))
                    <div class="alert-custom alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>Compte créé. En attente de validation par l'administrateur.</div>
                    </div>
                @endif

                <form action="{{ route('oncologie.register.post') }}" method="POST" id="registerForm">
                    @csrf

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div class="input-group-custom">
                            <label>Prénom</label>
                            <div class="input-wrap">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="prenom"
                                       placeholder="Votre prénom"
                                       required>
                            </div>
                        </div>
                        <div class="input-group-custom">
                            <label>Nom</label>
                            <div class="input-wrap">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="nom"
                                       placeholder="Votre nom"
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="input-group-custom">
                        <label>Adresse email professionnelle</label>
                        <div class="input-wrap">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email"
                                   placeholder="nom@clcc.dz"
                                   required>
                        </div>
                    </div>

                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:12px; font-weight:700;
                                      color:#374151; text-transform:uppercase;
                                      letter-spacing:0.5px; margin-bottom:8px;">
                            Rôle professionnel *
                        </label>
                        <div class="role-grid">
                            @foreach([
                                ['medecin',      '🩺', 'Médecin'],
                                ['pharmacien',   '💊', 'Pharmacien'],
                                ['infirmier',    '👩‍⚕️', 'Infirmier(e)'],
                                ['administrateur','⚙️','Administrateur'],
                            ] as $role)
                                <label class="role-card" onclick="selectRole(this)">
                                    <input type="radio" name="role" value="{{ $role[0] }}">
                                    <div class="role-icon">{{ $role[1] }}</div>
                                    <div class="role-label">{{ $role[2] }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="input-group-custom">
                        <label>Mot de passe</label>
                        <div class="input-wrap">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" id="regPw"
                                   placeholder="Minimum 8 caractères"
                                   oninput="checkStrength(this.value)"
                                   required>
                            <i class="fas fa-eye toggle-pw" onclick="togglePw('regPw', this)"></i>
                        </div>
                        <div class="strength-meter">
                            <div class="strength-bar">
                                <span id="s1"></span>
                                <span id="s2"></span>
                                <span id="s3"></span>
                                <span id="s4"></span>
                            </div>
                            <div class="strength-label" id="strengthLabel">Entrez un mot de passe</div>
                        </div>
                    </div>

                    <div class="input-group-custom">
                        <label>Confirmer le mot de passe</label>
                        <div class="input-wrap">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password_confirmation" id="regPwConfirm"
                                   placeholder="Répétez le mot de passe"
                                   oninput="checkMatch()"
                                   required>
                            <i class="fas fa-eye toggle-pw" onclick="togglePw('regPwConfirm', this)"></i>
                        </div>
                        <div id="matchMsg" style="font-size:12px; margin-top:4px;"></div>
                    </div>

                    <button type="submit" class="btn-submit"
                            style="background:linear-gradient(135deg,#264653,#1a3e2b);">
                        <i class="fas fa-user-plus"></i> Créer mon compte
                    </button>
                </form>
            </div>

            <!-- ============ TAB : MOT DE PASSE OUBLIÉ ============ -->
            <div class="tab-pane" id="tab-forgot">

                <h2 class="form-title">Réinitialisation 🔑</h2>
                <p class="form-subtitle">Entrez votre email pour recevoir un code de réinitialisation</p>

                @if(session('reset_sent'))
                    <div class="alert-custom alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            Instructions envoyées à votre adresse email.
                            Vérifiez votre boîte de réception.
                        </div>
                    </div>
                @endif

                <!-- ÉTAPE 1 : Email -->
                <div id="step-email">
                    <form action="{{ route('oncologie.password.email') }}" method="POST">
                        @csrf

                        <div class="input-group-custom">
                            <label>Adresse email de votre compte</label>
                            <div class="input-wrap">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email"
                                       placeholder="votre-email@clcc.dz"
                                       required>
                            </div>
                        </div>

                        <div class="alert-custom alert-warning" style="margin-bottom:16px;">
                            <i class="fas fa-info-circle" style="margin-top:2px;"></i>
                            <div>
                                Un email de réinitialisation sera envoyé.
                                Si votre compte est bloqué, contactez
                                <strong>l'administrateur système</strong>.
                            </div>
                        </div>

                        <button type="submit" class="btn-submit"
                                style="background:linear-gradient(135deg,#6a4c93,#563c7e);">
                            <i class="fas fa-paper-plane"></i> Envoyer les instructions
                        </button>
                    </form>
                </div>

                <div class="divider"><span>ou</span></div>

                <div style="text-align:center;">
                    <p style="font-size:13px; color:#6b7280; margin-bottom:10px;">
                        Compte verrouillé après 3 tentatives échouées ?
                    </p>
                    <div style="background:#fef3c7; border:1px solid #fbbf24; border-radius:10px;
                                padding:12px 16px; font-size:13px; color:#92400e;">
                        <i class="fas fa-phone-alt"></i>
                        Contactez l'administrateur système :<br>
                        <strong>admin@clcc.dz</strong>
                    </div>
                </div>
            </div>

        </div><!-- / tab-content-area -->

        <!-- FOOTER -->
        <div class="auth-footer">
            <span>
                <i class="fas fa-shield-alt" style="color:#2a9d8f;"></i>
                Connexion sécurisée — CLCC 2024
            </span>
            <span>
                <i class="fas fa-lock" style="color:#9ca3af;"></i>
                Données confidentielles
            </span>
        </div>
    </div><!-- / auth-right -->

</div><!-- / auth-wrapper -->

<script>
// ========================
// PARTICULES
// ========================
const container = document.getElementById('particles');
for (let i = 0; i < 25; i++) {
    const p = document.createElement('div');
    p.className = 'particle';
    p.style.left      = Math.random() * 100 + 'vw';
    p.style.animationDuration  = (Math.random() * 15 + 10) + 's';
    p.style.animationDelay     = (Math.random() * 10) + 's';
    p.style.width  = (Math.random() * 4 + 2) + 'px';
    p.style.height = p.style.width;
    container.appendChild(p);
}

// ========================
// TABS
// ========================
function switchTab(name, btn) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.auth-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

// ========================
// TOGGLE MOT DE PASSE
// ========================
function togglePw(id, icon) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// ========================
// STRENGTH METER
// ========================
function checkStrength(pw) {
    const bars   = ['s1','s2','s3','s4'];
    const label  = document.getElementById('strengthLabel');
    const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
    const labels = ['Très faible','Faible','Moyen','Fort'];

    let score = 0;
    if (pw.length >= 8)           score++;
    if (/[A-Z]/.test(pw))         score++;
    if (/[0-9]/.test(pw))         score++;
    if (/[^A-Za-z0-9]/.test(pw))  score++;

    bars.forEach((id, i) => {
        const el = document.getElementById(id);
        el.style.background = i < score ? colors[Math.max(0, score - 1)] : '#e5e7eb';
    });

    label.textContent  = score > 0 ? labels[score - 1] : 'Entrez un mot de passe';
    label.style.color  = score > 0 ? colors[score - 1] : '#9ca3af';
}

// ========================
// VÉRIFICATION CONFIRMATION
// ========================
function checkMatch() {
    const pw1  = document.getElementById('regPw').value;
    const pw2  = document.getElementById('regPwConfirm').value;
    const msg  = document.getElementById('matchMsg');

    if (!pw2) { msg.textContent = ''; return; }

    if (pw1 === pw2) {
        msg.innerHTML = '<span style="color:#22c55e;"><i class="fas fa-check-circle"></i> Les mots de passe correspondent</span>';
    } else {
        msg.innerHTML = '<span style="color:#ef4444;"><i class="fas fa-times-circle"></i> Les mots de passe ne correspondent pas</span>';
    }
}

// ========================
// SÉLECTION RÔLE
// ========================
function selectRole(card) {
    document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    card.querySelector('input').checked = true;
}
</script>

</body>
</html>