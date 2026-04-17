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
        /* Select2 + Tailwind */
        .select2-container--default .select2-selection--single {
            border-color: #D1D5DB; height: 38px; padding: 5px; border-radius: 0.375rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        /* ── SIDEBAR ─────────────────────────────────────────── */
        #sidebar {
            width: 240px;
            min-height: calc(100vh - 56px);
            background: #1e2a3a;
            transition: width 0.28s ease;
            overflow: hidden;
            flex-shrink: 0;
        }
        #sidebar.collapsed { width: 64px; }

        /* etiquetas de texto */
        .sb-label {
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s, max-width 0.25s;
            max-width: 160px;
            opacity: 1;
        }
        #sidebar.collapsed .sb-label { opacity: 0; max-width: 0; }

        /* chevron de submenu */
        .sb-chevron {
            transition: transform 0.25s;
            flex-shrink: 0;
        }
        #sidebar.collapsed .sb-chevron { display: none; }

        /* submenú */
        .sb-sub {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease;
        }
        .sb-sub.open { max-height: 300px; }
        #sidebar.collapsed .sb-sub { max-height: 0 !important; }

        /* ítem activo */
        .sb-item.active, .sb-item:hover {
            background: rgba(255,255,255,0.08);
        }
        .sb-subitem:hover { background: rgba(255,255,255,0.06); }

        /* tooltip: oculto por defecto, visible solo colapsado+hover */
        .sb-tooltip {
            display: none;
            position: absolute;
            left: 64px;
            top: 50%;
            transform: translateY(-50%);
            background: #1e2a3a;
            color: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 9999;
            pointer-events: none;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.4);
        }
        #sidebar.collapsed .sb-item { position: relative; }
        #sidebar.collapsed .sb-item:hover .sb-tooltip { display: block; }
    </style>
</head>

<body class="min-h-screen flex flex-col bg-gray-100 text-gray-800">

    <!-- ══ HEADER ══════════════════════════════════════════════ -->
    <header class="bg-gray-700 text-white shadow-sm" style="height:56px;">
        <div class="px-4 h-full flex items-center justify-between">

            <!-- Toggle sidebar + Logo -->
            <div class="flex items-center gap-3">
                <button id="sidebarToggle"
                    class="text-gray-300 hover:text-white focus:outline-none p-1 rounded">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <a href="/dashboard_home" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center
                                font-bold text-sm hover:bg-blue-700 transition">
                        D&amp;D
                    </div>
                    <div class="leading-tight">
                        <div class="font-semibold text-sm">DyD Dotaciones y Deportes SAS.</div>
                        <div class="text-xs text-gray-400">SOS-MicroStack</div>
                    </div>
                </a>
            </div>

            <!-- Usuario -->
            <?php if (isset($_SESSION['user'])): ?>
            <div class="flex items-center gap-3 relative">

                <a href="/dashboard_home" title="Inicio"
                    class="text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 9.75L12 4l9 5.75V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.75z"/>
                    </svg>
                </a>

                <button onclick="toggleUserMenu()"
                    class="flex items-center gap-2 text-sm hover:text-gray-300 focus:outline-none">
                    <span><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Usuario') ?></span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="userMenu"
                    class="hidden absolute right-0 top-10 w-48 bg-white border rounded-lg shadow-lg z-50">
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

    <!-- ══ BODY: sidebar + contenido ════════════════════════════ -->
    <div class="flex flex-1">

        <!-- ── SIDEBAR ─────────────────────────────────────────── -->
        <aside id="sidebar">
            <nav class="py-3">

                <?php
                $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

                /* ── helper: ítem simple ─────────────────────────── */
                function sbItem(string $href, string $label, string $svgPath, string $current): void {
                    $active = ($current === $href) ? 'active' : '';
                    echo <<<HTML
                    <a href="{$href}" class="sb-item {$active} flex items-center gap-3 px-4 py-2.5 text-gray-300 hover:text-white cursor-pointer text-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {$svgPath}
                        </svg>
                        <span class="sb-label">{$label}</span>
                        <span class="sb-tooltip">{$label}</span>
                    </a>
                    HTML;
                }
                ?>

                <!-- 1. Panel -->
                <?php sbItem(
                    '/dashboard_home', 'Panel',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M3 3h7v7H3zm11 0h7v7h-7zM3 14h7v7H3zm11 0h7v7h-7z"/>',
                    $currentPath
                ); ?>

                <!-- 2. Producción (CON SUBMENÚ) -->
                <div class="sb-group">
                    <button onclick="toggleSub('sub-produccion', this)"
                        class="sb-item w-full flex items-center gap-3 px-4 py-2.5 text-gray-300 hover:text-white text-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158
                                   a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172
                                   a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828
                                   c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        <span class="sb-label flex-1 text-left">Producción</span>
                        <svg class="sb-chevron w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-7 6-7-6"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 13l-7 6-7-6" opacity="0.45"/>
                        </svg>
                        <span class="sb-tooltip">Producción</span>
                    </button>
                    <div id="sub-produccion" class="sb-sub bg-black bg-opacity-20">
                        <a href="/fichas-tecnicas"
                            class="sb-subitem flex items-center gap-2 pl-12 pr-4 py-2 text-gray-400 hover:text-white text-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500 flex-shrink-0"></span>
                            Fichas Técnicas
                        </a>
                        <a href="/procesos-ft"
                            class="sb-subitem flex items-center gap-2 pl-12 pr-4 py-2 text-gray-400 hover:text-white text-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500 flex-shrink-0"></span>
                            Procesos de Fabricación
                        </a>
                        <a href="/orden-produccion"
                            class="sb-subitem flex items-center gap-2 pl-12 pr-4 py-2 text-gray-400 hover:text-white text-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500 flex-shrink-0"></span>
                            Órdenes de Producción
                        </a>
                    </div>
                </div>

                <!-- 3. Pedidos (CON SUBMENÚ) -->
                <div class="sb-group">
                    <button onclick="toggleSub('sub-pedidos', this)"
                        class="sb-item w-full flex items-center gap-3 px-4 py-2.5 text-gray-300 hover:text-white text-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="sb-label flex-1 text-left">Pedidos</span>
                        <svg class="sb-chevron w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-7 6-7-6"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M19 13l-7 6-7-6" opacity="0.45"/>
                        </svg>
                        <span class="sb-tooltip">Pedidos</span>
                    </button>
                    <div id="sub-pedidos" class="sb-sub bg-black bg-opacity-20">
                        <a href="/orden-pedido"
                            class="sb-subitem flex items-center gap-2 pl-12 pr-4 py-2 text-gray-400 hover:text-white text-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500 flex-shrink-0"></span>
                            Nueva Orden de Pedido
                        </a>
                        <a href="/seguimiento-opr"
                            class="sb-subitem flex items-center gap-2 pl-12 pr-4 py-2 text-gray-400 hover:text-white text-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500 flex-shrink-0"></span>
                            Seguimiento OPRs
                        </a>
                        <a href="/orden-produccion/avance"
                            class="sb-subitem flex items-center gap-2 pl-12 pr-4 py-2 text-gray-400 hover:text-white text-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500 flex-shrink-0"></span>
                            Avance OPRs
                        </a>
                    </div>
                </div>

                <!-- 4. Maestros -->
                <?php sbItem(
                    '/clientes', 'Maestros',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87
                           M12 12a4 4 0 100-8 4 4 0 000 8z"/>',
                    $currentPath
                ); ?>

                <!-- 5. Configuración -->
                <?php sbItem(
                    '/configuracion', 'Configuración',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0
                           002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0
                           001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0
                           00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0
                           00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0
                           00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0
                           00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0
                           001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07
                           2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                    $currentPath
                ); ?>

                <!-- Separador + Satelites -->
                <div class="border-t border-gray-700 mx-4 my-2"></div>

                <?php sbItem(
                    '/satelites', 'Satélites',
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01
                           m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0
                           M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>',
                    $currentPath
                ); ?>

            </nav>
        </aside>

        <!-- ── ÁREA DE TRABAJO ──────────────────────────────────── -->
        <main class="flex-1 overflow-auto p-6">
            <?= $content ?>
        </main>

    </div>

    <footer class="bg-white border-t text-center py-3 text-xs text-gray-400">
        &copy; <?= date('Y') ?> SOS Technology | Sistema de Gestión
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // ── Sidebar: persistir estado en localStorage ──────
        const sidebar = document.getElementById('sidebar');

        if (localStorage.getItem('sidebarCollapsed') === '1') {
            sidebar.classList.add('collapsed');
        }

        document.getElementById('sidebarToggle').addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
        });

        // ── Toggle submenú ─────────────────────────────────
        function toggleSub(id, btn) {
            const sub    = document.getElementById(id);
            const chev   = btn.querySelector('.sb-chevron');
            const isOpen = sub.classList.contains('open');

            document.querySelectorAll('.sb-sub.open').forEach(function (el) {
                el.classList.remove('open');
            });
            document.querySelectorAll('.sb-chevron').forEach(function (el) {
                el.style.transform = '';
            });

            if (!isOpen) {
                sub.classList.add('open');
                if (chev) chev.style.transform = 'rotate(180deg)';
            }
        }

        // ── Abrir submenú del ítem activo al cargar ─────────
        (function () {
            const path = window.location.pathname;
            document.querySelectorAll('.sb-sub a').forEach(function (link) {
                if (link.getAttribute('href') === path) {
                    const sub = link.closest('.sb-sub');
                    if (sub) {
                        sub.classList.add('open');
                        const btn  = sub.previousElementSibling;
                        const chev = btn && btn.querySelector('.sb-chevron');
                        if (chev) chev.style.transform = 'rotate(180deg)';
                    }
                }
            });
        })();

        // ── User dropdown ───────────────────────────────────
        function toggleUserMenu() {
            document.getElementById('userMenu').classList.toggle('hidden');
        }
        document.addEventListener('click', function (e) {
            const menu = document.getElementById('userMenu');
            if (menu && !menu.contains(e.target) && !e.target.closest('button[onclick="toggleUserMenu()"]')) {
                menu.classList.add('hidden');
            }
        });
    </script>

</body>
</html>
