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
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Navbar superior -->
    <header class="bg-white shadow-md p-4 flex justify-between items-center">
        <h1 class="text-lg md:text-xl font-bold text-gray-700"><?= $title ?? '' ?></h1>
        <button id="menu-toggle" class="p-2 rounded-md hover:bg-gray-200 md:hidden">
            <!-- Ícono hamburguesa -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <!-- Menú visible en pantallas grandes -->
        <nav class="hidden md:flex space-x-6">
            <a href="/" class="text-gray-600 hover:text-blue-600">🏠 Inicio</a>
            <a href="/fichas-tecnicas" class="text-gray-600 hover:text-blue-600">📑 Fichas Técnicas</a>
            <!-- Placeholders para futuras secciones -->
            <!-- <a href="/clientes" class="text-gray-600 hover:text-blue-600">👥 Clientes</a> -->
            <!-- <a href="/satelites" class="text-gray-600 hover:text-blue-600">🛰️ Satélites</a> -->
            <!-- <a href="/orden-pedido" class="text-gray-600 hover:text-blue-600">📦 Orden de Pedido</a> -->
            <!-- <a href="/orden-produccion" class="text-gray-600 hover:text-blue-600">🏭 Orden de Producción</a> -->
            <!-- <a href="/seguimiento-op" class="text-gray-600 hover:text-blue-600">📊 Seguimiento a OPs</a> -->
            <a href="/logout" class="text-red-500 hover:text-red-700">🚪 Salir</a>
        </nav>
    </header>

    <!-- Menú colapsable en móviles -->
    <nav id="menu" class="hidden bg-white shadow-md p-4 md:hidden">
        <ul class="space-y-2">
            <li><a href="/" class="block px-4 py-2 text-gray-600 hover:bg-gray-200">🏠 Inicio</a></li>
            <li><a href="/fichas-tecnicas" class="block px-4 py-2 text-gray-600 hover:bg-gray-200">📑 Fichas Técnicas</a></li>
            <!-- Futuras secciones -->
            <!-- <li><a href="/clientes" class="block px-4 py-2 text-gray-600 hover:bg-gray-200">👥 Clientes</a></li> -->
            <!-- <li><a href="/satelites" class="block px-4 py-2 text-gray-600 hover:bg-gray-200">🛰️ Satélites</a></li> -->
            <!-- <li><a href="/orden-pedido" class="block px-4 py-2 text-gray-600 hover:bg-gray-200">📦 Orden de Pedido</a></li> -->
            <!-- <li><a href="/orden-produccion" class="block px-4 py-2 text-gray-600 hover:bg-gray-200">🏭 Orden de Producción</a></li> -->
            <!-- <li><a href="/seguimiento-op" class="block px-4 py-2 text-gray-600 hover:bg-gray-200">📊 Seguimiento a OPs</a></li> -->
            <li><a href="/logout" class="block px-4 py-2 text-red-500 hover:text-red-700">🚪 Salir</a></li>
        </ul>
    </nav>

    <!-- Contenido dinámico -->
    <main class="flex-1 p-6">
        <?= $content ?>
    </main>

    <script>
        // Toggle menú hamburguesa en móviles
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
