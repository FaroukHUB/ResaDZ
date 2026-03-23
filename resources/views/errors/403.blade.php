<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session expirée - ResaDZ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .logo {
            width: 160px;
            height: auto;
            margin-bottom: 32px;
        }

        .logo-text {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 32px;
            text-decoration: none;
            display: inline-block;
        }

        .logo-text .resa {
            color: #1E293B;
        }

        .logo-text .dz {
            background: linear-gradient(135deg, #F97316, #EA580C);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .icon-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #FFF7ED, #FFEDD5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-wrapper svg {
            width: 48px;
            height: 48px;
            color: #EA580C;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 12px;
        }

        .message {
            font-size: 16px;
            color: #64748B;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: center;
        }

        .btn-reconnect {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            max-width: 300px;
            padding: 14px 28px;
            background: linear-gradient(135deg, #F97316, #EA580C);
            color: white;
            font-family: 'Cairo', sans-serif;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .btn-reconnect:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(249, 115, 22, 0.4);
        }

        .btn-reconnect svg {
            width: 20px;
            height: 20px;
        }

        .btn-support {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            max-width: 300px;
            padding: 12px 28px;
            background: transparent;
            color: #64748B;
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            font-weight: 600;
            border: 2px solid #E2E8F0;
            border-radius: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-support:hover {
            border-color: #CBD5E1;
            color: #475569;
            background: #F8FAFC;
        }

        .btn-support svg {
            width: 18px;
            height: 18px;
        }

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
            } catch (\Exception $e) {
                // DB/cache unavailable — fallback to text
            }
        @endphp

        @if($logoPath)
            <img src="{{ $logoPath }}" alt="ResaDZ" class="logo">
        @else
            <span class="logo-text"><span class="resa">Resa</span><span class="dz">DZ</span></span>
        @endif

        {{-- Icon --}}
        <div class="icon-wrapper">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
        </div>

        {{-- Title --}}
        <h1>Oups, une petite erreur 😕</h1>

        {{-- Message --}}
        <p class="message">
            Votre session a expiré ou un problème temporaire est survenu.<br>
            Pas de panique, cliquez ci-dessous pour vous reconnecter.
        </p>

        {{-- Buttons --}}
        <div class="buttons">
            <a href="{{ $loginUrl ?? '/loueur/login' }}" class="btn-reconnect">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                Se reconnecter
            </a>

            @php
                $whatsapp = '213555000000';
                try {
                    $wa = \App\Models\Setting::get('whatsapp', '');
                    if ($wa) $whatsapp = preg_replace('/[^0-9]/', '', $wa);
                } catch (\Exception $e) {}
            @endphp
            <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="btn-support">
                <svg fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Contacter le support
            </a>
        </div>

        <p class="error-code">Erreur 403 — {{ $panel ?? 'loueur' }}</p>
    </div>
</body>
</html>
