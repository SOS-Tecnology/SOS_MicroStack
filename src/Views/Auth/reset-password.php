<?php
$errors = $_SESSION['errors'] ?? []; unset($_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center
             bg-gradient-to-br from-blue-800 to-blue-400">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">

        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6
                           a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-800">Nueva contraseña</h1>
            <p class="text-sm text-gray-500 mt-1">Elige una contraseña segura para tu cuenta.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                <?php foreach ($errors as $e): ?><p>⚠ <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/reset-password/<?= htmlspecialchars($token) ?>" id="form-reset">

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nueva contraseña
                </label>
                <input type="password" name="password" id="inp-pass" required
                       placeholder="Mínimo 8 caracteres"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Confirmar contraseña
                </label>
                <input type="password" name="password_confirm" id="inp-confirm" required
                       placeholder="Repite la contraseña"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <p id="mismatch" class="text-xs text-red-500 mt-1 hidden">Las contraseñas no coinciden.</p>
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                           py-2.5 rounded-lg transition text-sm">
                Guardar nueva contraseña
            </button>

        </form>

        <p class="text-center text-sm text-gray-500 mt-5">
            <a href="/login" class="text-indigo-600 hover:underline">← Volver al inicio de sesión</a>
        </p>
    </div>

    <script>
    document.getElementById('form-reset').addEventListener('submit', function(e) {
        const p1 = document.getElementById('inp-pass').value;
        const p2 = document.getElementById('inp-confirm').value;
        if (p1 !== p2) {
            document.getElementById('mismatch').classList.remove('hidden');
            e.preventDefault();
            return;
        }
        if (p1.length < 8) {
            alert('La contraseña debe tener al menos 8 caracteres.');
            e.preventDefault();
        }
    });
    document.getElementById('inp-confirm').addEventListener('input', function() {
        const match = this.value === document.getElementById('inp-pass').value;
        document.getElementById('mismatch').classList.toggle('hidden', match || this.value === '');
    });
    </script>
</body>
</html>
