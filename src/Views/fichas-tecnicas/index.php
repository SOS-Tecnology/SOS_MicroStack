<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Fichas Técnicas</title>
    <link href="/css/tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <h2 class="text-2xl font-bold mb-6">Listado de Fichas Técnicas</h2>

    <a href="/fichas-tecnicas/create" class="mb-4 inline-block bg-blue-500 text-white px-4 py-2 rounded">
        + Nueva Ficha Técnica
    </a>

    <div class="bg-white shadow sm:rounded-lg p-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">ID</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nombre</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Cliente</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Producto Base</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Referencias</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Fotos</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($fichas as $ficha): ?>
                <tr>
                    <td class="px-4 py-2"><?= $ficha['id'] ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($ficha['nombre_ficha']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($ficha['id_cliente']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($ficha['id_producto_base']) ?></td>
                    <td class="px-4 py-2">
                        <ul class="list-disc list-inside text-sm">
                            <?php foreach ($ficha['referencias'] as $ref): ?>
                                <li><?= $ref['codr'] ?> - <?= $ref['descr'] ?> (<?= $ref['cantidad'] ?>, <?= $ref['talla'] ?>, <?= $ref['color'] ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td class="px-4 py-2">
                        <?php foreach ($ficha['fotos'] as $foto): ?>
                            <img src="/<?= $foto['ruta_imagen'] ?>" alt="Foto" class="h-16 inline-block mr-2">
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
