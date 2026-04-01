<div class="p-6">

    <div class="flex justify-between items-center mb-6">

        <a href="/orden-produccion/avance"
            class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">
            ← Volver
        </a>

        <h2 class="text-2xl font-semibold text-gray-700">
            Avance OPR <?= $opr['documento'] ?>
        </h2>

    </div>

    <!-- CABECERA -->
    <div class="bg-white shadow rounded-xl p-6 mb-6">
        <div class="grid grid-cols-4 gap-6">

            <div>
                <p class="text-sm text-gray-500">Cliente</p>
                <p class="font-semibold"><?= $opr['nombrecli'] ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Fecha</p>
                <p class="font-semibold"><?= $opr['fecha'] ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Fecha Entrega</p>
                <p class="font-semibold"><?= $opr['fechent'] ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Total Prendas</p>
                <p class="font-semibold text-blue-600">
                    <?= number_format($total_prendas) ?>
                </p>
            </div>

        </div>
    </div>

    <!-- PROCESOS -->
    <?php foreach ($fts as $ft): ?>

        <div class="bg-white shadow rounded-xl mb-6 overflow-hidden">

            <div class="bg-gray-100 px-4 py-2 font-semibold">
                Ficha Técnica #<?= $ft['ft_id'] ?> |
                Cantidad: <?= $ft['cantidad_total'] ?>
            </div>

            <table class="w-full text-sm">

                <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-2 text-left">Orden</th>
                        <th class="px-4 py-2 text-left">Proceso</th>
                        <th class="px-4 py-2 text-left">Ejecutable en</th>
                        <th class="px-4 py-2 text-left">Meta</th>
                        <th class="px-4 py-2 text-left">EPP</th>
                        <th class="px-4 py-2 text-left">RPP</th>
                        <th class="px-4 py-2 text-left">Avance</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    <?php foreach ($ft['procesos'] as $p): ?>

                        <?php
                        $meta = $p['cantidad_proceso'] ?? 0;
                        $epp  = $p['epp'] ?? 0;
                        $rpp  = $p['rpp'] ?? 0;

                        $porcentaje = $meta > 0 ? round(($rpp / $meta) * 100) : 0;
                        ?>

                        <tr>

                            <td class="px-4 py-2"><?= $p['orden'] ?></td>

                            <td class="px-4 py-2"><?= $p['nombre_proceso'] ?></td>

                            <td class="px-4 py-2"><?= $p['ejecutable_en'] ?></td>

                            <td class="px-4 py-2 font-semibold"><?= $meta ?></td>

                            <td class="px-4 py-2 text-blue-600"><?= $epp ?></td>

                            <td class="px-4 py-2 text-green-600"><?= $rpp ?></td>

                            <td class="px-4 py-2 w-48">

                                <div class="text-xs mb-1">
                                    <?= $porcentaje ?>%
                                </div>

                                <div class="w-full bg-gray-200 h-2 rounded">
                                    <div class="bg-green-500 h-2 rounded"
                                        style="width: <?= $porcentaje ?>%">
                                    </div>
                                </div>

                            </td>

                            <td class="px-4 py-2">
                                <div class="flex gap-2">
<!-- 
                                    {{-- Va directo al formulario crear EPP --}} -->
                                    <a href="/epp/create/<?= $opr['documento'] ?>/<?= $p['id_proceso'] ?>"
                                        class="text-blue-600 hover:underline text-sm">
                                        📤 EPP
                                    </a>

                                    <!-- {{-- Va a la pantalla de gestión del proceso (lista EPP+RPP) --}} -->
                                    <a href="/orden-produccion/procesos/<?= $opr['documento'] ?>/<?= $p['id_proceso'] ?>"
                                        class="text-green-600 hover:underline text-sm">
                                        📥 Ver proceso
                                    </a>

                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endforeach; ?>

</div>