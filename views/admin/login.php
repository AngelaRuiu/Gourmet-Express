<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login — Gourmet Express</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400&family=Josefin+Sans:wght@300;400&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:     #141210;
            --ink-soft:#1E1A17;
            --cream:   #FAF7F2;
            --muted:   #B0A396;
            --gold:    #CFA255;
            --gold-lt: #E8C07A;
            --error:   #C0392B;
            --border:  rgba(207,162,85,0.25);
        }

        body {
            background: var(--ink);
            color: var(--cream);
            font-family: 'Josefin Sans', sans-serif;
            font-weight: 400;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
        }

        /* Brand */
        .brand {
            text-align: center;
            margin-bottom: 3rem;
        }

        .brand-line {
            display: block;
            width: 40px;
            height: 1px;
            background: var(--gold);
            margin: 0 auto 1.5rem;
        }

        .brand-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.75rem;
            font-weight: 400;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--gold);
            display: block;
            margin-bottom: 0.5rem;
        }

        .brand-sub {
            font-size: 0.62rem;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: var(--muted);
        }

        /* Alerts */
        .alert {
            padding: 0.85rem 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            border-left: 2px solid;
        }

        .alert-error {
            background: rgba(192,57,43,0.1);
            border-color: var(--error);
            color: #E87B6E;
        }

        .alert-success {
            background: rgba(207,162,85,0.08);
            border-color: var(--gold);
            color: var(--gold-lt);
        }

        /* Form */
        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 0.62rem;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.6rem;
        }

        input[id="username"],
        input[id="password"] {
            width: 100%;
            background: var(--ink-soft);
            border: 1px solid var(--border);
            color: var(--cream);
            font-family: 'Josefin Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 300;
            letter-spacing: 0.05em;
            padding: 0.85rem 1rem;
            outline: none;
            transition: border-color 0.2s;
            border-radius: 0;
            -webkit-appearance: none;
        }

        input:focus {
            border-color: var(--gold);
        }

        input::placeholder {
            color: rgba(176,163,150,0.4);
            font-size: 0.82rem;
        }

        /* Submit */
        .btn-login {
            width: 100%;
            background: var(--gold);
            color: var(--ink);
            border: none;
            font-family: 'Josefin Sans', sans-serif;
            font-size: 0.68rem;
            font-weight: 400;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            padding: 1rem;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 0.5rem;
        }

        .btn-login:hover { background: var(--gold-lt); }
        .btn-login:active { transform: translateY(1px); }

        /* Footer note */
        .login-note {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.6rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--muted);
            opacity: 0.6;
        }
    </style>
</head>
<body>

    <div class="login-card">

        <div class="brand">
            <span class="brand-line"></span>
            <span class="brand-name">Gourmet Express</span>
            <span class="brand-sub">Personal Access</span>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login">

            <div class="form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Username"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" class="btn-login">Sign In</button>

        </form>

        <p class="login-note">Restricted access &mdash; authorised personnel only</p>

    </div>

</body>
</html>