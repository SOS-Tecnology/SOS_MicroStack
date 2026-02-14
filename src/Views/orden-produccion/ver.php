


<div class="max-w-7xl mx-auto bg-gray-800 text-gray-100 rounded-xl shadow-lg p-6">

    <!-- HEADER -->
<div class="flex items-center justify-between mb-6">

    <!-- IZQUIERDA: Volver + Título -->
    <div class="flex items-center gap-3">

        <!-- BOTÓN VOLVER -->
        <a href="javascript:history.back()"
           class="inline-flex items-center gap-1 text-xs text-gray-300 hover:text-white transition">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
                 stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15 19l-7-7 7-7" />
            </svg>

            Volver
        </a>

        <!-- TÍTULO -->
        <h1 class="text-xl md:text-2xl font-bold tracking-wide">
            Orden de Producción #<?= $opr['documento'] ?>
        </h1>

    </div>

    <!-- DERECHA: Estado -->
    <span class="px-3 py-1 text-xs md:text-sm rounded bg-blue-600">
        <?= $opr['estado'] ?: 'EN PROCESO' ?>
    </span>

</div>


    <div class="bg-white text-gray-800 rounded-lg shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <p class="text-sm text-gray-500">Cliente</p>
            <p class="font-semibold"><?= $opr['cliente'] ?></p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Fecha OPR</p>
            <p class="font-semibold"><?= $opr['fecha'] ?></p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Fecha Entrega</p>
            <p class="font-semibold"><?= $opr['fechent'] ?></p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Observaciones</p>
            <p class="font-semibold"><?= $opr['comen'] ?></p>
        </div>

    </div>


    <h2 class="text-lg font-semibold mb-3">Productos a fabricar</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm bg-white text-gray-800 rounded-lg overflow-hidden">

            <thead class="bg-gray-700 text-white text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-3 py-2 text-center">Item</th>
                    <th class="px-3 py-2 text-center">Referencia</th>
                    <th class="px-3 py-2">Descripción</th>
                    <th class="px-3 py-2 text-center">Talla</th>
                    <th class="px-3 py-2 text-center">Color</th>
                    <th class="px-3 py-2 text-center">Cantidad</th>
                    <th class="px-3 py-2 text-center">Estado</th>
                    <th class="px-3 py-2 text-center">% Avance</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $total_items = 0;
                $total_piezas = 0;
                ?>

                <?php foreach ($detalles as $d):
                    $total_items++;
                    $total_piezas += $d['cantidad'];

                    // Estado demo (luego lo conectamos con producción real)
                    $estado = "Pendiente";
                    $avance = 0;
                ?>
                    <tr class="border-t">
                        <td class="px-3 py-2 text-center"><?= $d['item'] ?></td>
                        <td class="px-3 py-2 text-center"><?= $d['codr'] ?></td>
                        <td class="px-3 py-2"><?= $d['producto_nombre'] ?></td>
                        <td class="px-3 py-2 text-center"><?= $d['codtalla'] ?></td>
                        <td class="px-3 py-2 text-center"><?= $d['codcolor'] ?></td>
                        <td class="px-3 py-2 text-center"><?= number_format($d['cantidad'], 2) ?></td>

                        <td class="px-3 py-2 text-center">
                            <span class="px-2 py-1 rounded text-xs bg-yellow-200 text-yellow-800">
                                <?= $estado ?>
                            </span>
                        </td>

                        <td class="px-3 py-2 text-center">
                            <div class="w-full bg-gray-200 rounded h-2">
                                <div class="bg-blue-600 h-2 rounded" style="width: <?= $avance ?>%"></div>
                            </div>
                            <span class="text-xs"><?= $avance ?>%</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4 bg-gray-700 text-white rounded-lg p-4 flex flex-col md:flex-row md:justify-between text-sm">

        <div>
            <strong>Total ítems:</strong> <?= $total_items ?>
        </div>

        <div>
            <strong>Total piezas:</strong> <?= number_format($total_piezas, 2) ?>
        </div>

    </div>

</div>