<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Code de réinitialisation</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;">

    <div style="
        max-width:600px;
        margin:auto;
        background:white;
        padding:30px;
        border-radius:10px;
        box-shadow:0 0 10px rgba(0,0,0,.1);
    ">

        <h2 style="color:#2a9d8f;">
            🔐 Réinitialisation du mot de passe
        </h2>

        <p>Bonjour,</p>

        <p>
            Une demande de réinitialisation de mot de passe a été effectuée
            pour votre compte Oncologie CLCC.
        </p>

        <p>
            Utilisez le code suivant :
        </p>

        <div style="
            text-align:center;
            margin:30px 0;
        ">
            <span style="
                font-size:32px;
                font-weight:bold;
                letter-spacing:8px;
                color:#264653;
                background:#f3f4f6;
                padding:15px 25px;
                border-radius:8px;
                display:inline-block;
            ">
                {{ $code }}
            </span>
        </div>

        <p>
            Ce code est valable pendant
            <strong>10 minutes</strong>.
        </p>

        <p>
            Si vous n'êtes pas à l'origine de cette demande,
            ignorez simplement cet email.
        </p>

        <hr>

        <p style="font-size:12px;color:#666;">
            CLCC Draâ Ben Khedda<br>
            Système de Gestion Pharmacie Oncologique
        </p>

    </div>

</body>
</html>