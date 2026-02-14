<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SOS-MicroStack' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="/css/app.css" rel="stylesheet">
    <link href="/css/dashboard.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* Ajuste para que Select2 combine mejor con Tailwind */
        .select2-container--default .select2-selection--single {
            border-color: #D1D5DB;
            height: 38px;
            padding: 5px;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
</head>

<!-- <body class="min-h-screen flex flex-col bg-slate-100 text-slate-800"> -->

<body class="min-h-screen flex flex-col bg-gradient-to-r from-gray-100 via-gray-200 to-gray-300 text-gray-800">

    <header class="bg-gray-600 border-b border-gray-200 shadow-sm text-white">

    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
        <!-- IZQUIERDA: Empresa -->
        <div class="flex items-center space-x-3">
            <!-- Logo -->
            <div class="w-10 h-10 bg-blue-600 text-white rounded flex items-center justify-center font-bold">
                D&D
            </div>

            <!-- Nombre empresa -->
            <div class="leading-tight">
                <div class="font-semibold text-white text-lg">
                    DyD Dotaciones y Deportes SAS.
                </div>
                <div class="text-xs text-gray-300">
                    SOS-MicroStack
                </div>
            </div>
        </div>

        <!-- DERECHA: Inicio + Usuario -->
        <?php if (isset($_SESSION['user'])): ?>
            <div class="flex items-center space-x-4 relative">

                <!-- Inicio -->
                <a href="/dashboard_home"
                    class="text-gray-600 hover:text-blue-600 transition"
                    title="Inicio">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5">
                        <path d="M3 9.75L12 4l9 5.75V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.75z" />
                    </svg>
                </a>

                <!-- Usuario -->
                <button onclick="toggleUserMenu()"
                    class="flex items-center space-x-2 text-sm text-white-700
                           hover:text-gray-700 focus:outline-none">
                    <span class="font-medium">
                        <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Usuario') ?>
                    </span>

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown usuario -->
                <div id="userMenu"
                    class="hidden absolute right-0 top-10 w-48 bg-white
                        border rounded-lg shadow-lg z-50 overflow-hidden">

                    <a href="/perfil"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Cambiar información
                    </a>

                    <a href="/usuarios/create"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Crear usuario
                    </a>

                    <div class="border-t"></div>

                    <a href="/logout"
                        class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        Cerrar sesión
                    </a>
                </div>

            </div>
        <?php endif; ?>
    </div>
    </header>

    <main class="flex-1 p-6">
        <div class="container mx-auto">
            <?= $content ?>
        </div>
    </main>

    <footer class="bg-white border-t text-center p-4 text-sm text-gray-500">
        &copy; <?= date('Y'); ?> SOS Technology | Sistema de Gestión
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function toggleUserMenu() {
            document.getElementById('userMenu').classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('userMenu');
            const button = e.target.closest('button');

            if (menu && !menu.contains(e.target) && !button) {
                menu.classList.add('hidden');
            }
        });
    </script>


</body>

</html>