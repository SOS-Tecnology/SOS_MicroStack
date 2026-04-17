<?php
$errors  = $_SESSION['errors']  ?? []; unset($_SESSION['errors']);
$success = $_SESSION['success'] ?? '';  unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center
             bg-gradient-to-br from-blue-800 to-blue-400">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">

        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7
                           a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-800">Recuperar contraseña</h1>
            <p class="text-sm text-gray-500 mt-1">
                Ingresa tu correo y te enviaremos un enlace de restablecimiento.
            </p>
        </div>

        <?php if ($success): ?>
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg px-4 py-3 text-sm text-center">
                ✔ <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                <?php foreach ($errors as $e): ?><p>⚠ <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" action="/forgot-password">
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                Correo electrónico
            </label>
            <input type="email" name="email" required autofocus
                   placeholder="correo@empresa.com"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm mb-4
                          focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                           py-2.5 rounded-lg transition text-sm">
                Enviar enlace de recuperación
            </button>
        </form>
        <?php endif; ?>

        <p class="text-center text-sm text-gray-500 mt-5">
            <a href="/login" class="text-indigo-600 hover:underline">← Volver al inicio de sesión</a>
        </p>

    </div>
</body>
</html>
