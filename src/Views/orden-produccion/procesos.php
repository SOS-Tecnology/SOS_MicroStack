<div class="max-w-7xl mx-auto bg-gray-500 text-white rounded-xl p-6">

    <!-- CABECERA -->
    <div class="flex justify-between items-center mb-6">

        <a href="/orden-produccion/avance/ver/<?= $documento ?>"
            class="bg-gray-600 px-4 py-2 rounded hover:bg-gray-400">
            ← Volver
        </a>

        <h2 class="text-2xl font-semibold text-gray-700">
            OPR <?= $documento ?> - Proceso: <?= $proceso ?>
        </h2>

        <div class="flex gap-2">

            <!-- NUEVO EPP -->
            <a href="/epp/create/<?= $documento ?>/<?= urlencode($proceso) ?>"
                class="bg-blue-600 px-4 py-2 rounded hover:bg-blue-400">
                + EPP
            </a>

            <!-- NUEVO RPP -->
            <?php if ($existenEpp): ?>

                <span class="bg-green-600 px-4 py-2 rounded opacity-50 cursor-not-allowed">
                    + RPP (Seleccione un EPP)
                </span>

            <?php else: ?>

                <span class="bg-gray-400 px-4 py-2 rounded cursor-not-allowed">
                    Sin EPP
                </span>

            <?php endif; ?>

        </div>

    </div>

    <!-- TABLA -->
    <table class="w-full bg-white text-black rounded">

        <thead class="bg-gray-200">
            <tr>
                <th class="p-2">Tipo</th>
                <th class="p-2">Documento</th>
                <th class="p-2">Fecha</th>
                <th class="p-2">Proveedor</th>
                <th class="p-2">Estado</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>

        <tbody>

            <?php if (!empty($movimientos)): ?>

                <?php foreach ($movimientos as $m): ?>

                    <tr class="border-t">

                        <td class="p-2 text-center"><?= $m['tm'] ?></td>

                        <td class="p-2 text-center"><?= $m['documento'] ?></td>

                        <td class="p-2 text-center"><?= $m['fecha'] ?></td>

                        <td class="p-2"><?= $m['proveedor'] ?></td>

                        <td class="p-2 text-center"><?= $m['estado'] ?></td>

                        <td class="p-2 text-center">

                            <a href="/<?= strtolower($m['tm']) ?>/show/<?= $m['documento'] ?>"
                                class="bg-blue-500 px-3 py-1 rounded text-white hover:bg-blue-400">
                                Ver
                            </a>
                            <?php if ($m['tm'] === 'EPP'): ?>

                                <a href="/rpp/create/<?= $m['documento'] ?>"
                                    class="bg-green-600 px-3 py-1 rounded text-white hover:bg-green-400">
                                    + RPP
                                </a>

                            <?php endif; ?>

                            <a href="/<?= strtolower($m['tm']) ?>/print/<?= $m['documento'] ?>"
                                class="bg-gray-600 px-3 py-1 rounded text-white hover:bg-gray-400">
                                Imprimir
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6" class="text-center p-4 text-gray-500">
                        No hay movimientos para este proceso
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>