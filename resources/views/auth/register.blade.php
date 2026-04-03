<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Alam Kitchen</title>
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
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            background: #1a1612;
        }

        .panel-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 80%, rgba(224, 123, 57, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 20%, rgba(193, 154, 107, 0.12) 0%, transparent 50%);
            pointer-events: none;
        }

        .deco-lines {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.04) 1px, transparent 0);
            background-size: 32px 32px;
            pointer-events: none;
        }

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

        /* Roles showcase */
        .roles-showcase {
            position: relative;
            z-index: 1;
        }

        .roles-eyebrow {
            font-size: 11px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--accent);
            font-weight: 500;
            margin-bottom: 1.25rem;
        }

        .roles-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 3.5vw, 3rem);
            font-weight: 700;
            line-height: 1.1;
            color: #f5f0e8;
            margin-bottom: 2rem;
            letter-spacing: -0.5px;
        }

        .roles-title em {
            font-style: italic;
            color: var(--accent);
        }

        .role-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: border-color 0.2s, background 0.2s;
        }

        .role-card:hover {
            border-color: rgba(224, 123, 57, 0.4);
            background: rgba(224, 123, 57, 0.06);
        }

        .role-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .role-dot.superadmin {
            background: var(--accent);
        }

        .role-dot.admin {
            background: #4ecba8;
        }

        .role-dot.kasir {
            background: #7eb3f7;
        }

        .role-name {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            font-size: 14px;
            color: #f5f0e8;
        }

        .role-desc {
            font-size: 12px;
            color: rgba(245, 240, 232, 0.45);
            margin-top: 2px;
        }

        /* Bottom tagline */
        .left-footer {
            position: relative;
            z-index: 1;
        }

        .left-footer p {
            font-size: 12px;
            color: rgba(245, 240, 232, 0.3);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ── Right panel ── */
        .panel-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background: var(--bg);
            overflow-y: auto;
            position: relative;
        }

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
            padding: 1rem 0;
        }

        .form-header {
            margin-bottom: 2rem;
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

        .form-rule {
            height: 1px;
            background: linear-gradient(to right, var(--accent), transparent);
            margin-bottom: 2rem;
            opacity: 0.3;
        }

        .field {
            margin-bottom: 1.1rem;
        }

        label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.45rem;
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
        input[type="text"],
        select {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.8rem 1rem 0.8rem 2.75rem;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            appearance: none;
            -webkit-appearance: none;
        }

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238a7d6e' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
            cursor: pointer;
        }

        select option {
            background: #fff;
            color: var(--text);
        }

        input:focus,
        select:focus {
            border-color: var(--accent);
            background: var(--surface);
            box-shadow: 0 0 0 3px rgba(224, 123, 57, 0.12);
        }

        input::placeholder {
            color: #c5b9a8;
        }

        input.is-invalid,
        select.is-invalid {
            border-color: #e05252;
        }

        .invalid-feedback {
            font-size: 12px;
            color: #e05252;
            margin-top: 0.3rem;
            display: block;
        }

        /* Section divider */
        .section-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.25rem 0;
        }

        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .section-divider span {
            font-size: 11px;
            letter-spacing: 2px;
            color: var(--muted);
            text-transform: uppercase;
            white-space: nowrap;
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
            margin-top: 0.5rem;
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

        <div class="roles-showcase">
            <p class="roles-eyebrow">Manajemen Akses</p>
            <h2 class="roles-title">Tiga Level<br><em>Hak Akses</em></h2>

            <div class="role-card">
                <span class="role-dot superadmin"></span>
                <div>
                    <div class="role-name">Super Admin</div>
                    <div class="role-desc">Akses penuh semua fitur & manajemen user</div>
                </div>
            </div>

            <div class="role-card">
                <span class="role-dot admin"></span>
                <div>
                    <div class="role-name">Admin</div>
                    <div class="role-desc">Kelola produk, laporan & pengaturan toko</div>
                </div>
            </div>

            <div class="role-card">
                <span class="role-dot kasir"></span>
                <div>
                    <div class="role-name">Kasir</div>
                    <div class="role-desc">Proses transaksi & riwayat penjualan</div>
                </div>
            </div>
        </div>

        <div class="left-footer">
            <p>© 2026 Alam Kitchen</p>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="panel-right">
        <div class="form-container">
            <div class="form-header">
                <p class="form-eyebrow">Buat Akun Baru</p>
                <h2 class="form-title">Daftar<br>Sekarang</h2>
                <p class="form-subtitle" style="margin-top:0.5rem;">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
            </div>

            <div class="form-rule"></div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="field">
                    <label for="name">Nama Lengkap</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Budi Santoso"
                            autocomplete="name"
                            class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                            required autofocus>
                    </div>
                    @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

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
                            required>
                    </div>
                    @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="role">Role / Jabatan</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                            </svg>
                        </span>
                        <select
                            id="role"
                            name="role"
                            class="{{ $errors->has('role') ? 'is-invalid' : '' }}"
                            required>
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>— Pilih role —</option>
                            <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="admin" {{ old('role') == 'admin'       ? 'selected' : '' }}>Admin</option>
                            <option value="kasir" {{ old('role') == 'kasir'       ? 'selected' : '' }}>Kasir</option>
                        </select>
                    </div>
                    @error('role')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="section-divider">
                    <span>Keamanan</span>
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
                            placeholder="Min. 8 karakter"
                            autocomplete="new-password"
                            class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                            required>
                    </div>
                    @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0110 0v4" />
                                <path d="M9 16l2 2 4-4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi password"
                            autocomplete="new-password"
                            class="{{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                            required>
                    </div>
                    @error('password_confirmation')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-primary">Buat Akun</button>
            </form>

            <p class="form-footer">© 2026 Alam Kitchen. All rights reserved.</p>
        </div>
    </div>

</body>

</html>