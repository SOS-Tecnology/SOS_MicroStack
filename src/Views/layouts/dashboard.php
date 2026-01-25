<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Dashboard' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- jQuery y Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body class="bg-gray-100 flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md h-screen">
        <div class="p-4 border-b">
            <h1 class="text-xl font-bold text-gray-700">Mi Empresa</h1>
        </div>
        <nav class="mt-4">
            <ul>
                <li>
                    <a href="/dashboard" class="block px-4 py-2 text-gray-600 hover:bg-gray-200">🏠 Dashboard</a>
                </li>
                <li>
                    <a href="/fichas-tecnicas" class="block px-4 py-2 text-gray-600 hover:bg-gray-200">📑 Fichas Técnicas</a>
                </li>
                <li>
                    <a href="/clientes" class="block px-4 py-2 text-gray-600 hover:bg-gray-200">👥 Clientes</a>
                </li>
                <li>
                    <a href="/productos" class="block px-4 py-2 text-gray-600 hover:bg-gray-200">📦 Productos</a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Contenido principal -->
    <div class="flex-1 flex flex-col">
        <!-- Navbar superior -->
        <header class="bg-white shadow-md p-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-700"><?= $title ?? '' ?></h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Usuario</span>
                <a href="/logout" class="text-red-500 hover:text-red-700">Salir</a>
            </div>
        </header>

        <!-- Contenido dinámico -->
        <main class="p-6">
            <?= $content ?>
        </main>
    </div>

</body>
</html>
