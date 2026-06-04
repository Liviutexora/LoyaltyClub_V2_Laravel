<script>
// Set window title if running as installed PWA (standalone)
if (
    window.matchMedia('(display-mode: standalone)').matches ||
    window.navigator.standalone === true
) {
    document.title = 'Loyalty Club';
}
</script>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loyalty Club Mobile App</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#000000">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
            font-family: "Segoe UI", Arial, sans-serif;
            background:
                radial-gradient(circle at top, rgba(212,175,55,0.12), transparent 35%),
                linear-gradient(180deg, #050505 0%, #000000 100%);
            color: #ffffff;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .app-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .install-card {
            position: relative;
            overflow: hidden;

            background:
                linear-gradient(180deg, rgba(20,20,20,0.96) 0%, rgba(10,10,10,0.98) 100%);

            border: 1px solid rgba(212,175,55,0.18);

            border-radius: 34px;

            padding: 42px 28px;

            box-shadow:
                0 0 60px rgba(212,175,55,0.08),
                0 10px 40px rgba(0,0,0,0.8),
                inset 0 1px 0 rgba(255,255,255,0.04);
        }

        .install-card::before {
            content: "";

            position: absolute;
            top: -120px;
            left: -120px;

            width: 240px;
            height: 240px;

            background: radial-gradient(circle, rgba(212,175,55,0.12), transparent 70%);

            pointer-events: none;
        }

        .install-card::after {
            content: "";

            position: absolute;
            bottom: -140px;
            right: -140px;

            width: 280px;
            height: 280px;

            background: radial-gradient(circle, rgba(212,175,55,0.08), transparent 70%);

            pointer-events: none;
        }

        .logo-wrapper {
            position: relative;

            width: 132px;
            height: 132px;

            margin: 0 auto 30px auto;

            border-radius: 32px;

            background:
                linear-gradient(145deg, rgba(40,40,40,0.95), rgba(8,8,8,1));

            display: flex;
            align-items: center;
            justify-content: center;

            box-shadow:
                0 0 25px rgba(212,175,55,0.16),
                inset 0 1px 0 rgba(255,255,255,0.06),
                inset 0 -10px 25px rgba(0,0,0,0.45);

            border: 1px solid rgba(212,175,55,0.16);
        }

        .logo-wrapper::before {
            content: "";

            position: absolute;
            inset: 0;

            border-radius: 32px;

            padding: 1px;

            background: linear-gradient(
                135deg,
                rgba(212,175,55,0.55),
                transparent,
                rgba(212,175,55,0.25)
            );

            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);

            -webkit-mask-composite: xor;

            mask-composite: exclude;
        }

        .app-icon {
            width: 96px;
            height: 96px;
            object-fit: contain;

            filter:
                drop-shadow(0 0 10px rgba(212,175,55,0.35))
                drop-shadow(0 0 18px rgba(212,175,55,0.12));
        }

        .title {
            text-align: center;

            font-size: 2.1rem;
            font-weight: 800;

            line-height: 1.15;

            margin-bottom: 18px;

            letter-spacing: -0.02em;

            text-shadow:
                0 0 18px rgba(255,255,255,0.05),
                0 0 24px rgba(212,175,55,0.08);
        }

        .gold-text {
            background: linear-gradient(
                180deg,
                #ffe58f 0%,
                #f5cf5a 20%,
                #d4af37 50%,
                #b88916 100%
            );

            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            text-align: center;

            color: #d7b54a;

            font-size: 1rem;
            line-height: 1.7;

            margin-bottom: 34px;

            opacity: 0.95;
        }

        .divider {
            width: 90px;
            height: 2px;

            margin: 0 auto 32px auto;

            background: linear-gradient(
                90deg,
                transparent,
                rgba(212,175,55,0.9),
                transparent
            );

            border-radius: 999px;
        }

        .main-btn {
            position: relative;

            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;

            width: 100%;

            padding: 18px 22px;

            border-radius: 20px;

            text-decoration: none;

            color: #111111;

            font-size: 1.08rem;
            font-weight: 800;

            background:
                linear-gradient(
                    180deg,
                    #f5d66d 0%,
                    #d4af37 45%,
                    #b88916 100%
                );

            box-shadow:
                0 8px 24px rgba(212,175,55,0.22),
                inset 0 1px 0 rgba(255,255,255,0.45);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .main-btn:hover {
            transform: translateY(-2px);

            box-shadow:
                0 12px 30px rgba(212,175,55,0.28),
                inset 0 1px 0 rgba(255,255,255,0.5);
        }

        .main-btn svg {
            flex-shrink: 0;
        }

        .bottom-box {
            margin-top: 24px;

            padding: 18px 16px;

            border-radius: 18px;

            background:
                linear-gradient(
                    180deg,
                    rgba(28,28,28,0.95),
                    rgba(16,16,16,0.95)
                );

            border: 1px solid rgba(212,175,55,0.08);

            text-align: center;

            color: #c7a63b;

            font-size: 0.95rem;
            line-height: 1.6;

            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.03);
        }

        .footer-note {
            margin-top: 18px;

            text-align: center;

            font-size: 0.8rem;

            color: rgba(255,255,255,0.28);

            letter-spacing: 0.03em;
        }

        @media (max-width: 480px) {
            body {
                padding: 18px;
            }

            .install-card {
                padding: 34px 22px;
                border-radius: 28px;
            }

            .title {
                font-size: 1.75rem;
            }

            .subtitle {
                font-size: 0.95rem;
            }

            .main-btn {
                font-size: 1rem;
                padding: 16px 18px;
            }

            .logo-wrapper {
                width: 118px;
                height: 118px;
            }

            .app-icon {
                width: 86px;
                height: 86px;
            }
        }
    </style>
</head>
<body>

    <div class="app-wrapper">

        <div class="install-card">

            <div class="logo-wrapper">
                <img
                    src="/assets/mobile-app/icon-512.png"
                    alt="Loyalty Club Icon"
                    class="app-icon"
                >
            </div>

            <div class="title">
                Loyalty Club<br>
                <span class="gold-text">Mobile App</span>
            </div>

            <div class="subtitle">
                Accesează rapid contul tău Loyalty Club direct de pe telefon,
                într-o experiență premium dedicată.
            </div>

            <div class="divider"></div>

            <a href="/mobile-app/{{ $legacyUserId }}" class="main-btn" id="installBtn">

                <svg xmlns="http://www.w3.org/2000/svg"
                     width="22"
                     height="22"
                     fill="none"
                     viewBox="0 0 24 24">
                    <path
                        fill="#111"
                        d="M12 5a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H6a1 1 0 1 1 0-2h5V6a1 1 0 0 1 1-1Z"
                    />
                </svg>

                <span>Deschide Mobile App</span>

            </a>

            <div class="bottom-box">
                Instalează Loyalty Club pe dispozitivul tău pentru acces rapid,
                experiență fluidă și utilizare direct din ecranul principal.
            </div>

            <div class="footer-note">
                Loyalty Club Premium Experience
            </div>

        </div>

    </div>
    <script>
let deferredPrompt = null;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
});

const installBtn = document.getElementById('installBtn');

// Hide install button if running as installed PWA (standalone)
if (
    window.matchMedia('(display-mode: standalone)').matches ||
    window.navigator.standalone === true
) {
    if (installBtn) installBtn.style.display = 'none';
}

if (installBtn) {
    installBtn.addEventListener('click', async (e) => {
        if (!deferredPrompt) {
            // No prompt available, allow normal navigation
            return;
        }

        e.preventDefault();

        deferredPrompt.prompt();

        await deferredPrompt.userChoice;

        deferredPrompt = null;
    });
}
    </script>
<script>
// Redirect if running as installed PWA (standalone)
if (
    window.matchMedia('(display-mode: standalone)').matches ||
    window.navigator.standalone === true
) {
    const legacyAppUrl = @json(rtrim((string) config('services.legacy_app_url', env('LEGACY_APP_URL', 'http://localhost/loyaltyclub.ro/')), '/') . '/');
    window.location.href = legacyAppUrl;
}
</script>
</body>
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then(() => console.log('SW registered'))
        .catch(err => console.log('SW failed', err));
}
</script>
</html>