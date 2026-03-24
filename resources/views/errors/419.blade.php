<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page expirée - ResaDZ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
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
            max-width: 480px;
            width: 100%;
        }

        .logo-text {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 32px;
            display: inline-block;
            text-decoration: none;
        }

        .logo-text .resa { color: #1E293B; }
        .logo-text .dz {
            background: linear-gradient(135deg, #F97316, #EA580C);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo { width: 160px; height: auto; margin-bottom: 32px; }

        .icon-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .icon-wrapper svg { width: 48px; height: 48px; color: #3B82F6; }

        h1 { font-size: 22px; font-weight: 700; color: #1E293B; margin-bottom: 12px; }

        .message {
            font-size: 15px;
            color: #64748B;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .countdown {
            font-size: 14px;
            color: #3B82F6;
            font-weight: 600;
            margin-bottom: 28px;
        }

        .countdown span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #EFF6FF;
            color: #2563EB;
            font-weight: 700;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            margin: 0 2px;
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(59,130,246,0.3);
            border-top-color: #3B82F6;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            vertical-align: middle;
            margin-right: 6px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-refresh {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            max-width: 300px;
            padding: 14px 28px;
            background: linear-gradient(135deg, #3B82F6, #2563EB);
            color: white;
            font-family: 'Cairo', sans-serif;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-refresh:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .btn-refresh svg { width: 20px; height: 20px; }

        .error-code {
            margin-top: 32px;
            font-size: 12px;
            color: #CBD5E1;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Logo --}}
        @php
            $logoPath = null;
            try {
                $logoSetting = \App\Models\Setting::get('logo_light', '');
                if ($logoSetting) {
                    $logoPath = \Illuminate\Support\Facades\Storage::url($logoSetting);
                }
            } catch (\Exception $e) {}
        @endphp

        @if($logoPath)
            <img src="{{ $logoPath }}" alt="ResaDZ" class="logo">
        @else
            <span class="logo-text"><span class="resa">Resa</span><span class="dz">DZ</span></span>
        @endif

        {{-- Icon --}}
        <div class="icon-wrapper">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
            </svg>
        </div>

        {{-- Title --}}
        <h1>Page expiree, on actualise...</h1>

        {{-- Message --}}
        <p class="message">
            Votre session a expire. La page se recharge automatiquement.
        </p>

        {{-- Countdown --}}
        <p class="countdown">
            <span class="spinner"></span> Rechargement dans <span id="timer">3</span>s
        </p>

        {{-- Manual button --}}
        <a href="{{ $redirectUrl ?? url()->current() }}" class="btn-refresh" id="btn-refresh">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
            </svg>
            Recharger maintenant
        </a>

        <p class="error-code">Erreur 419 — session expiree</p>
    </div>

    <script>
        var seconds = 3;
        var timer = document.getElementById('timer');
        var interval = setInterval(function() {
            seconds--;
            if (timer) timer.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.reload();
            }
        }, 1000);
    </script>
</body>
</html>
