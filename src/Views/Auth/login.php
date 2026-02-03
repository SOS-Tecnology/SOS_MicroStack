<?php
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="flex items-center justify-center min-h-screen">
    <div class="w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">

        <div class="bg-gray-100 px-8 pt-6 pb-4">
            <h1 class="text-2xl font-bold text-center text-gray-800">
                Iniciar Sesión
            </h1>
        </div>

        <div class="px-8 py-6">

            <?php if (!empty($errors)): ?>
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">
                        Correo Electrónico
                    </label>
                    <input type="email" name="email" required
                        class="shadow border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">
                        Contraseña
                    </label>
                    <input type="password" name="password" required
                        class="shadow border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                    Aceptar
                </button>

            </form>
        </div>
    </div>
</div>

</body>
</html>
