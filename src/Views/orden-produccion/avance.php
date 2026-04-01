<div class="p-6">

    <div class="flex justify-between items-center mb-6">

        <a href="/" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">
            ← Volver
        </a>

        <h2 class="text-2xl font-semibold text-gray-700">
            Avance en OPRs
        </h2>

    </div>
    <form method="GET" class="mb-4 flex gap-2">

        <input type="text" name="cliente"
            placeholder="Filtrar por cliente..."
            value="<?= $_GET['cliente'] ?? '' ?>"
            class="border px-3 py-2 rounded w-64">

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Filtrar
        </button>

    </form>

    <div class="bg-white shadow rounded-xl overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">

                <tr>

                    <th class="px-4 py-3 text-left">Documento</th>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Cliente</th>
                    <th class="px-4 py-3 text-left">O.Pedido</th>                    
                    <th class="px-4 py-3 text-left">Fecha Entrega</th>
                    <th class="px-4 py-3 text-left w-24">Total Prendas</th>
                    <th class="px-4 py-3 text-left">Días</th>
                    <th class="px-4 py-3 text-left">Estado Prod</th>
                    <th class="px-4 py-3 text-center">Acciones</th>

                </tr>

            </thead>

            <tbody class="divide-y">

                <?php foreach ($oprs as $opr): ?>

                    <?php
                    $hoy = new DateTime();
                    $entrega = new DateTime($opr['fechent']);
                    $dias = (int)$hoy->diff($entrega)->format('%r%a');
                    ?>
                    <tr class="hover:bg-gray-50">

                        <td class="px-4 py-3 font-mono">
                            <?= $opr['documento'] ?>
                        </td>

                        <td class="px-4 py-3">
                            <?= $opr['fecha'] ?>
                        </td>

                        <td class="px-4 py-3">
                            <?= $opr['nombrecli'] ?>

                         </td>

                       <td class="px-4 py-3">                         
                           <?php if (!empty($opr['op'])): ?>
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                                    <?= 'OP-' . str_pad($opr['op'], 8, '0', STR_PAD_LEFT) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?= $opr['fechent'] ?>
                        </td>

                        <td class="px-4 py-3 font-semibold">
                            <?= number_format($opr['total_prendas'] ?? 0) ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($dias < 0): ?>
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">
                                    <?= $dias ?> días
                                </span>
                            <?php elseif ($dias <= 5): ?>
                                <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">
                                    <?= $dias ?> días
                                </span>
                            <?php else: ?>
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">
                                    <?= $dias ?> días
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-3">

                            <?php if ($opr['tiene_epp'] > 0): ?>

                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                                    En Proceso
                                </span>

                            <?php else: ?>

                                <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs">
                                    Pendiente
                                </span>

                            <?php endif; ?>

                        </td>
                        <td class="px-4 py-3 text-center">

                            <a href="/orden-produccion/avance/ver/<?= $opr['documento'] ?>"
                                class="text-blue-600 hover:underline">
                                Ver
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>