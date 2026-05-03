<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Gourmet Express Admin</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($user['name'] ?? 'Admin') ?></h1>
    <p>Role: <?= htmlspecialchars($user['role'] ?? '') ?></p>
    <form method="POST" action="/logout">
        <button type="submit">Logout</button>
    </form>
</body>
</html>