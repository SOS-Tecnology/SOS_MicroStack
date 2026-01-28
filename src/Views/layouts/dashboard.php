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

<body class="bg-gray-100 min-h-screen flex flex-col">

    <header class="bg-blue-600 text-white p-4 flex justify-between items-center shadow-md">
        <h1 class="text-xl font-bold italic tracking-tight">SOS-MicroStack</h1>
        <nav class="space-x-4 flex items-center">
            <a href="/" class="hover:text-blue-200 transition text-sm font-medium">Inicio</a>
            <a href="/fichas-tecnicas" class="hover:text-blue-200 transition text-sm font-medium">Fichas Técnicas</a>
            <a href="/logout" class="bg-blue-700 px-3 py-1 rounded hover:bg-red-500 transition text-xs">Cerrar Sesión</a>
        </nav>
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

</body>
</html>