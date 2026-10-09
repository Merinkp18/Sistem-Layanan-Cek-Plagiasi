<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beranda | SILACEK</title>
    @vite(['resources/css/app.css'])
</head>
<body class="dashboard-page">
    <main class="dashboard-shell">
        <div>
            <p class="eyebrow">SILACEK</p>
            <h1>Selamat datang, {{ session('actor_name') }}</h1>
            <p>Login berhasil sebagai {{ session('actor_role') === 'admin' ? 'Admin' : 'Staff' }}.</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="submit-button" type="submit">Keluar</button>
        </form>
    </main>
</body>
</html>