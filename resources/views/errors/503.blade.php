<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance en cours - ResaDZ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <meta http-equiv="refresh" content="30">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #F8FAFF;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            text-align: center;
            max-width: 520px;
            width: 100%;
        }

        .logo {
            font-size: 36px;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 32px;
        }

        .logo span { color: #EF4444; }

        .icon-wrapper {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #FF6B2C 0%, #F59E0B 100%);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            box-shadow: 0 8px 30px rgba(255, 107, 44, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .icon-wrapper svg {
            width: 48px;
            height: 48px;
            color: white;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 12px;
        }

        .subtitle {
            font-size: 16px;
            color: #64748B;
            line-height: 1.7;
            margin-bottom: 32px;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #E2E8F0;
            margin-bottom: 24px;
        }

        .card p {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
        }

        .card strong {
            color: #1E293B;
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background: #E2E8F0;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .progress-bar-inner {
            height: 100%;
            width: 60%;
            background: linear-gradient(90deg, #FF6B2C, #F59E0B);
            border-radius: 4px;
            animation: loading 2s ease-in-out infinite;
        }

        @keyframes loading {
            0% { width: 20%; margin-left: 0; }
            50% { width: 60%; margin-left: 20%; }
            100% { width: 20%; margin-left: 80%; }
        }

        .contact {
            font-size: 14px;
            color: #64748B;
        }

        .contact a {
            color: #FF6B2C;
            text-decoration: none;
            font-weight: 600;
        }

        .contact a:hover {
            text-decoration: underline;
        }

        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">Resa<span>DZ</span></div>

        <div class="icon-wrapper">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>

        <h1>Maintenance en cours</h1>

        <p class="subtitle">
            Nous améliorons ResaDZ pour vous offrir une meilleure expérience.<br>
            Le site sera de retour dans quelques minutes.
        </p>

        <div class="progress-bar">
            <div class="progress-bar-inner"></div>
        </div>

        <div class="card">
            <p>
                <strong>Que se passe-t-il ?</strong><br>
                Nous mettons à jour la plateforme avec de nouvelles fonctionnalités.
                Vos réservations et données sont en sécurité.
            </p>
        </div>

        <p class="contact">
            Une urgence ? Contactez-nous sur
            <a href="https://wa.me/213555000000">WhatsApp</a>
            ou par email à
            <a href="mailto:contact@resadz.com">contact@resadz.com</a>
        </p>

        <p class="footer">Cette page se rafraîchit automatiquement toutes les 30 secondes.</p>
    </div>
</body>
</html>
