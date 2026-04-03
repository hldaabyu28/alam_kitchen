<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Alam Kitchen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #f5f0e8;
            --surface: #ffffff;
            --border: #e2d9cc;
            --accent: #e07b39;
            --accent-dim: rgba(224, 123, 57, 0.08);
            --accent-dark: #c4622a;
            --text: #1a1612;
            --muted: #8a7d6e;
            --light: #f9f6f1;
            --shadow: rgba(26, 22, 18, 0.08);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* ── Left panel ── */
        .panel-left {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            overflow: hidden;
            background: #1a1612;
        }

        /* Warm overlay texture */
        .panel-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 80%, rgba(224, 123, 57, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 20%, rgba(193, 154, 107, 0.12) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Decorative botanical line art */
        .deco-lines {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.04) 1px, transparent 0);
            background-size: 32px 32px;
            pointer-events: none;
        }

        /* Diagonal accent stripe */
        .panel-left::after {
            content: '';
            position: absolute;
            bottom: -40px;
            right: -40px;
            width: 280px;
            height: 280px;
            border: 1px solid rgba(224, 123, 57, 0.15);
            border-radius: 50%;
            pointer-events: none;
        }

        .brand {
            position: relative;
            z-index: 1;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 20px;
            color: #fff;
            flex-shrink: 0;
        }

        .brand-name {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            font-size: 20px;
            letter-spacing: 0.5px;
            color: #f5f0e8;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-eyebrow {
            font-size: 11px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--accent);
            font-weight: 500;
            margin-bottom: 1.25rem;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.8rem, 4vw, 3.8rem);
            font-weight: 700;
            line-height: 1.08;
            letter-spacing: -0.5px;
            color: #f5f0e8;
            margin-bottom: 1.5rem;
        }

        .hero-title em {
            font-style: italic;
            color: var(--accent);
        }

        .hero-desc {
            color: rgba(245, 240, 232, 0.55);
            font-size: 14px;
            line-height: 1.8;
            max-width: 340px;
        }

        /* Dish image collage */
        .dish-preview {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 2.5rem;
        }

        .dish-card {
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 4/3;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .dish-card:first-child {
            grid-column: span 2;
            aspect-ratio: 16/7;
        }

        .dish-card-inner {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.15);
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Decorative divider line */
        .divider-line {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 2rem 0 1.5rem;
        }

        .divider-line::before,
        .divider-line::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .divider-line span {
            font-size: 11px;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.3);
            text-transform: uppercase;
            white-space: nowrap;
        }

        .stats {
            position: relative;
            z-index: 1;
            display: flex;
            gap: 2.5rem;
        }

        .stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--accent);
        }

        .stat-label {
            font-size: 11px;
            letter-spacing: 1px;
            color: rgba(245, 240, 232, 0.4);
            margin-top: 2px;
            text-transform: uppercase;
        }

        /* ── Right panel ── */
        .panel-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background: var(--bg);
            position: relative;
        }

        /* Subtle botanical watermark */
        .panel-right::before {
            content: '🌿';
            position: absolute;
            bottom: 2rem;
            right: 2rem;
            font-size: 80px;
            opacity: 0.04;
            pointer-events: none;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
        }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-eyebrow {
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--accent);
            font-weight: 500;
            margin-bottom: 0.75rem;
        }

        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: var(--text);
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .form-subtitle {
            color: var(--muted);
            font-size: 14px;
        }

        .form-subtitle a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }

        .form-subtitle a:hover {
            color: var(--accent-dark);
        }

        /* Decorative horizontal rule */
        .form-rule {
            height: 1px;
            background: linear-gradient(to right, var(--accent), transparent);
            margin-bottom: 2rem;
            opacity: 0.3;
        }

        .field {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.5rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        input:focus {
            border-color: var(--accent);
            background: var(--surface);
            box-shadow: 0 0 0 3px rgba(224, 123, 57, 0.12);
        }

        input::placeholder {
            color: #c5b9a8;
        }

        input.is-invalid {
            border-color: #e05252;
        }

        .invalid-feedback {
            font-size: 12px;
            color: #e05252;
            margin-top: 0.35rem;
            display: block;
        }

        .row-opts {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.75rem;
        }

        .checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-wrap input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--accent);
            cursor: pointer;
        }

        .checkbox-wrap span {
            font-size: 13px;
            color: var(--muted);
        }

        .forgot-link {
            font-size: 13px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            color: var(--accent-dark);
        }

        .btn-primary {
            width: 100%;
            padding: 0.9rem;
            background: var(--accent);
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(224, 123, 57, 0.3);
        }

        .btn-primary:hover {
            background: var(--accent-dark);
            box-shadow: 0 6px 20px rgba(224, 123, 57, 0.35);
        }

        .btn-primary:active {
            transform: scale(0.99);
        }

        .alert-error {
            background: rgba(224, 82, 82, 0.08);
            border: 1px solid rgba(224, 82, 82, 0.25);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            font-size: 13px;
            color: #c0392b;
            margin-bottom: 1.5rem;
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 12px;
            color: var(--muted);
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            body {
                grid-template-columns: 1fr;
            }

            .panel-left {
                display: none;
            }
        }
    </style>
</head>

<body>

    <!-- Left branding panel -->
    <div class="panel-left">
        <div class="deco-lines"></div>

        <div class="brand">
            <div class="brand-mark">
                <div class="brand-icon">A</div>
                <span class="brand-name">Alam Kitchen</span>
            </div>
        </div>

        <div class="hero-content">
            <p class="hero-eyebrow">Harmony House Kitchen</p>
            <h1 class="hero-title">Taste<br><em>Flavors</em><br>From<br>The World.</h1>
            <p class="hero-desc">Where culinary excellence meets a symphony of flavors, creating unforgettable dining experiences with every bite.</p>

            <div class="divider-line">
                <span>Est. 2020</span>
            </div>
        </div>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-num">50+</div>
                <div class="stat-label">Menu Pilihan</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">3</div>
                <div class="stat-label">Level Akses</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">24/7</div>
                <div class="stat-label">Monitoring</div>
            </div>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="panel-right">
        <div class="form-container">
            <div class="form-header">
                <p class="form-eyebrow">Portal Kasir</p>
                <h2 class="form-title">Selamat<br>Datang Kembali</h2>
                <p class="form-subtitle" style="margin-top:0.5rem;">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
            </div>

            <div class="form-rule"></div>

            @if ($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            autocomplete="email"
                            class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                            required autofocus>
                    </div>
                    @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0110 0v4" />
                            </svg>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                            required>
                    </div>
                    @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row-opts">
                    <label class="checkbox-wrap">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-primary">Masuk ke Dashboard</button>
            </form>

            <p class="form-footer">© 2026 Alam Kitchen. All rights reserved.</p>
        </div>
    </div>

</body>

</html>