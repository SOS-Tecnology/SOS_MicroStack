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


    <!-- DETALLE PRODUCTOS -->

    <div class="bg-white shadow rounded-xl overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">

                <tr>

                    <th class="px-4 py-3 text-left">Referencia</th>
                    <th class="px-4 py-3 text-left">Talla</th>
                    <th class="px-4 py-3 text-left">Color</th>
                    <th class="px-4 py-3 text-left">Cantidad</th>
                    <th class="px-4 py-2 text-left">Acciones</th>

                </tr>

            </thead>

            <tbody class="divide-y">

                <?php foreach ($fts as $ft): ?>

                    <div class="bg-white shadow rounded-lg mb-6 overflow-hidden">

                        <table class="w-full text-sm">

                            <thead class="bg-gray-100">

                                <tr>

                                    <th class="px-4 py-2">Orden</th>
                                    <th class="px-4 py-2">Proceso</th>
                                    <th class="px-4 py-2">Ejecutable en</th>
                                    <th class="px-4 py-2">Cantidad</th>
                                    <th class="px-4 py-2">Tiempo total</th>
                                    <th class="px-4 py-2">Acciones</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($ft['procesos'] as $p): ?>

                                    <tr class="border-t">

                                        <td class="px-4 py-2"><?= $p['orden'] ?></td>

                                        <td class="px-4 py-2"><?= $p['proceso'] ?></td>

                                        <td class="px-4 py-2"><?= $p['ejecutable_en'] ?></td>

                                        <td class="px-4 py-2"><?= $p['cantidad'] ?></td>    

                                        <td class="px-4 py-2"><?= $p['tiempo_total'] ?></td>

                                        <td class="px-4 py-2">

                                            <div class="flex gap-2">

                                                <a href="/orden-produccion/procesos/<?= $opr['documento'] ?>/<?= urlencode($p['proceso']) ?>"
                                                    class="text-blue-600 hover:underline text-sm">
                                                    📤 EPP
                                                </a>

                                                <a href="/orden-produccion/procesos/<?= $opr['documento'] ?>/<?= urlencode($p['proceso']) ?>"
                                                    class="text-green-600 hover:underline text-sm">
                                                    📥 RPP
                                                </a>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>