<div class="max-w-7xl mx-auto bg-gray-800 text-gray-100 rounded-xl shadow-lg p-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">

            <a href="javascript:history.back()"
                class="inline-flex items-center gap-1 text-xs text-gray-300 hover:text-white transition">
                ← Volver
            </a>

            <h1 class="text-xl md:text-2xl font-bold tracking-wide">
                Orden de Producción #<?= $opr['documento'] ?>
            </h1>
        </div>

        <span class="px-3 py-1 text-xs md:text-sm rounded bg-blue-600">
            <?= $opr['estado'] ?: 'EN PROCESO' ?>
        </span>
        <a href="/orden-produccion/pdf/<?= $opr['documento'] ?>"
            target="_blank"
            class="px-3 py-1 text-xs md:text-sm rounded bg-gray-600 hover:bg-gray-500 text-white">
            🖨 Generar PDF
        </a>
    </div>

    <!-- DATOS -->
    <div class="bg-white text-gray-800 rounded-lg shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- OP ORIGEN -->
        <div>
            <p class="text-sm text-gray-500">Orden Producción (OP)</p>
            <p class="font-semibold">
                <?= $op ? $op['prefijo'] . '-' . $op['documento'] : 'N/A' ?>
            </p>
        </div>

        <!-- FECHA OP -->
        <div>
            <p class="text-sm text-gray-500">Fecha OP</p>
            <p class="font-semibold">
                <?= $op['fecha'] ?? 'N/A' ?>
            </p>
        </div>

        <!-- ENTREGA CLIENTE -->
        <div>
            <p class="text-sm text-gray-500">Entrega Cliente</p>
            <p class="font-semibold">
                <?= $op['fechent'] ?? 'N/A' ?>
            </p>
        </div>

        <!-- FECHA OPR -->
        <div>
            <p class="text-sm text-gray-500">Fecha OPR</p>
            <p class="font-semibold">
                <?= $opr['fecha'] ?>
            </p>
        </div>

        <!-- CLIENTE -->
        <div>
            <p class="text-sm text-gray-500">Cliente</p>
            <p class="font-semibold"><?= $opr['cliente'] ?></p>
        </div>

        <!-- OBSERVACIONES -->
        <div class="md:col-span-3">
            <p class="text-sm text-gray-500">Observaciones</p>
            <p class="font-semibold"><?= $opr['comen'] ?></p>
        </div>

    </div>

    <!-- PRODUCTOS -->
    <h2 class="text-lg font-semibold mb-3">Productos a fabricar</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm bg-white text-gray-800 rounded-lg overflow-hidden">
            <thead class="bg-gray-700 text-white text-xs uppercase">
                <tr>
                    <th class="px-3 py-2">Item</th>
                    <th class="px-3 py-2">Referencia</th>
                    <th class="px-3 py-2">Descripción</th>
                    <th class="px-3 py-2">Talla</th>
                    <th class="px-3 py-2">Color</th>
                    <th class="px-3 py-2">Cantidad</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($detalles as $d): ?>
                    <tr class="border-t">
                        <td class="px-3 py-2"><?= $d['item'] ?></td>
                        <td class="px-3 py-2"><?= $d['codr'] ?></td>
                        <td class="px-3 py-2"><?= $d['producto_nombre'] ?></td>
                        <td class="px-3 py-2"><?= $d['codtalla'] ?></td>
                        <td class="px-3 py-2"><?= $d['codcolor'] ?></td>
                        <td class="px-3 py-2 text-center"><?= number_format($d['cantidad'], 0) ?></td>
                    </tr>
                    <?php if (!empty(trim($d['comencpo']))): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-2 bg-yellow-50 text-sm text-gray-700 border-l-4 border-yellow-400">
                                <?= $d['comencpo'] ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-3 text-sm text-white">
            Total ítems: <?= $total_items ?> |
            Total piezas: <?= number_format($total_piezas, 0) ?>
        </div>
    </div>

    <!-- MATERIALES -->
    <h2 class="text-lg font-semibold mt-6 mb-3">Materiales e Insumos</h2>

    <table class="min-w-full bg-white text-gray-800 text-sm rounded-lg overflow-hidden">
        <thead class="bg-gray-700 text-white">
            <tr>
                <th class="px-3 py-2">Código</th>
                <th class="px-3 py-2">Descripción</th>
                <th class="px-3 py-2">Cantidad</th>
                <th class="px-3 py-2">Proveedor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materiales as $cod => $m): ?>
                <tr class="border-t">
                    <td class="px-3 py-2"><?= $cod ?></td>
                    <td class="px-3 py-2"><?= $m['nombre'] ?></td>
                    <td class="px-3 py-2"><?= number_format($m['cantidad'], 2) ?></td>
                    <td class="px-3 py-2"><?= $m['proveedor'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- PROCESOS -->
    <h2 class="text-lg font-semibold mt-6 mb-3">Procesos por Ficha Técnica</h2>

    <?php foreach ($fts as $ft_id => $ft): ?>

        <div class="bg-white text-gray-800 rounded-lg p-4 mb-4">
            <h3 class="font-bold mb-2">
                Ficha Técnica #<?= $ft_id ?> — Cantidad: <?= $ft['cantidad_total'] ?>
            </h3>

            <table class="min-w-full text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-2 py-1">Orden</th>
                        <th class="px-2 py-1">Proceso</th>
                        <th class="px-2 py-1">Dónde</th>
                        <th class="px-2 py-1">Min/Und</th>
                        <th class="px-2 py-1">Cantidad</th>
                        <th class="px-2 py-1">Tiempo Total</th>
                        <th class="px-2 py-1">Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ft['procesos'] as $p): ?>
                        <tr class="border-t">
                            <td class="px-2 py-1"><?= $p['orden'] ?></td>
                            <td class="px-2 py-1"><?= $p['proceso'] ?></td>
                            <td class="px-2 py-1"><?= $p['ejecutable_en'] ?></td>
                            <td class="px-2 py-1"><?= $p['tiempo_unit'] ?></td>
                            <td class="px-2 py-1"><?= $p['cantidad'] ?></td>
                            <td class="px-2 py-1"><?= $p['tiempo_total'] ?></td>
                            <td class="px-2 py-1"><?= $p['comentario'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="bg-white shadow rounded-xl p-4 mt-6">

                <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">
                    Fotos de la Ficha Técnica
                </h2>

                <?php if (!empty($ft['fotos'])): ?>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

                        <?php foreach ($ft['fotos'] as $foto): ?>

                            <?php
                            $ruta = BASE_URL . '/' . ltrim($foto['ruta_imagen'], '/');
                            ?>

                            <div class="border rounded-lg overflow-hidden shadow hover:shadow-lg transition">

                                <a href="<?= $ruta ?>" target="_blank">
                                    <img src="<?= $ruta ?>"
                                        class="w-full h-48 object-cover"
                                        alt="Foto ficha técnica">
                                </a>

                                <div class="p-2 text-xs text-gray-500 text-center">
                                    <?= basename($ruta) ?>
                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div class="text-gray-400 text-sm">
                        No hay fotos registradas para esta ficha técnica.
                    </div>

                <?php endif; ?>

            </div>

        </div>

    <?php endforeach; ?>

</div>