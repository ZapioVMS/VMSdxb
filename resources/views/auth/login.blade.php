<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Madkhal Visitor Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">
    <style>
        :root {
            --accent: #0c6b64;
            --accent-hover: #0f7a72;
            --accent-soft: rgba(12, 107, 100, 0.10);
            --bg: #f4f7f6;
            --surface: #ffffff;
            --text-primary: #0e1a18;
            --text-secondary: #4d5b57;
            --text-muted: #7c8985;
            --border: rgba(14, 26, 24, 0.12);
            --critical: #d03b3b;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 4px 20px -4px rgba(14, 26, 24, 0.12);
        }
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 420px;
            padding: 32px 28px;
        }
        .brand-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0f7a72, var(--accent));
            color: #ffffff;
            font-weight: 700;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .brand-title {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .brand-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            font-family: inherit;
            font-size: 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--surface);
            color: var(--text-primary);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-soft);
        }
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            font-size: 13px;
            color: var(--text-secondary);
        }
        .remember-row label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .remember-row input[type="checkbox"] {
            accent-color: var(--accent);
            width: 16px;
            height: 16px;
        }
        .btn-submit {
            width: 100%;
            padding: 11px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--accent);
            color: #ffffff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }
        .btn-submit:hover {
            background: var(--accent-hover);
        }
        .alert-danger {
            background: rgba(208, 59, 59, 0.10);
            border: 1px solid rgba(208, 59, 59, 0.25);
            color: var(--critical);
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            margin-bottom: 18px;
        }
        .demo-box {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid var(--border);
            text-align: center;
        }
        .demo-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 10px;
        }
        .demo-btns {
            display: flex;
            gap: 8px;
        }
        .btn-demo {
            flex: 1;
            padding: 8px 10px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--bg);
            color: var(--text-secondary);
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }
        .btn-demo:hover {
            background: #e6ebe9;
            color: var(--text-primary);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-header">
            <div class="brand-mark">M</div>
            <div>
                <div class="brand-title">Madkhal</div>
                <div class="brand-sub">Visitor Management System</div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', 'allanjacob03@gmail.com') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" value="password" required>
            </div>

            <div class="remember-row">
                <label>
                    <input type="checkbox" name="remember" value="1" checked>
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn-submit">Sign In to Dashboard</button>
        </form>

        <div class="demo-box">
            <div class="demo-title">Quick Demo Logins</div>
            <div class="demo-btns">
                <button type="button" class="btn-demo" onclick="fillDemo('allanjacob03@gmail.com')">Super Admin</button>
                <button type="button" class="btn-demo" onclick="fillDemo('sarah.khan@zapio.ae')">HR Manager</button>
            </div>
        </div>
    </div>

    <script>
        function fillDemo(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</body>
</html>
