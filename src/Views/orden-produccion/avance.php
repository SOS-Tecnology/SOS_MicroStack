<div class="p-3">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-3 gap-3">

        <a href="/dashboard_home"
           class="flex items-center gap-1 text-xs text-gray-500 hover:text-gray-700 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver
        </a>

        <h2 class="text-lg font-semibold text-gray-700 shrink-0">Avance en OPRs</h2>

        <form method="GET" class="flex gap-2">
            <input type="text" name="cliente"
                placeholder="Filtrar por cliente…"
                value="<?= htmlspecialchars($_GET['cliente'] ?? '') ?>"
                class="border border-gray-300 px-2 py-1.5 rounded-lg text-xs w-44
                       focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs transition">
                Filtrar
            </button>
        </form>

    </div>

    <!-- TABLA -->
    <div class="bg-white shadow rounded-xl overflow-x-auto">

        <table class="w-full text-xs">

            <thead>
                <tr class="bg-gradient-to-r from-indigo-700 to-blue-600 text-white uppercase tracking-wide">
                    <th class="px-2 py-2.5 text-left font-semibold">Documento</th>
                    <th class="px-2 py-2.5 text-left font-semibold">Fecha</th>
                    <th class="px-2 py-2.5 text-left font-semibold">Cliente</th>
                    <th class="px-2 py-2.5 text-left font-semibold">O.Pedido</th>
                    <th class="px-2 py-2.5 text-left font-semibold">Entrega</th>
                    <th class="px-2 py-2.5 text-center font-semibold">Prendas</th>
                    <th class="px-2 py-2.5 text-center font-semibold">Term.</th>
                    <th class="px-2 py-2.5 text-left font-semibold w-36">Avance</th>
                    <th class="px-2 py-2.5 text-center font-semibold">Días</th>
                    <th class="px-2 py-2.5 text-center font-semibold">Estado</th>
                    <th class="px-2 py-2.5 text-center font-semibold">Acc.</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                <?php foreach ($oprs as $opr): ?>

                    <?php
                    $fechent  = $opr['fechent'] ?? '0000-00-00';
                    $hoy      = new DateTime();
                    try { $entrega = new DateTime($fechent); }
                    catch (\Exception $e) { $entrega = $hoy; }
                    $dias     = (int)$hoy->diff($entrega)->format('%r%a');
                    $pct      = $opr['porcentaje']    ?? 0;
                    $term     = $opr['terminadas']    ?? 0;
                    $total    = $opr['total_prendas'] ?? 0;

                    $barColor = $pct >= 100 ? 'bg-emerald-500'
                              : ($pct >= 60 ? 'bg-blue-500'
                              : ($pct >= 30 ? 'bg-yellow-400'
                              :              'bg-red-400'));
                    ?>

                    <tr class="hover:bg-indigo-50 transition">

                        <td class="px-2 py-1.5 font-mono text-gray-700 font-medium">
                            <?= $opr['documento'] ?>
                        </td>

                        <td class="px-2 py-1.5 text-gray-500 whitespace-nowrap">
                            <?= $opr['fecha'] ?>
                        </td>

                        <td class="px-2 py-1.5 text-gray-700 max-w-[180px] truncate">
                            <?= htmlspecialchars($opr['nombrecli']) ?>
                        </td>

                        <td class="px-2 py-1.5 whitespace-nowrap">
                            <?php if (!empty($opr['op'])): ?>
                                <span class="bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-medium">
                                    OP-<?= str_pad($opr['op'], 8, '0', STR_PAD_LEFT) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-gray-300">—</span>
                            <?php endif; ?>
                        </td>

                        <td class="px-2 py-1.5 text-gray-500 whitespace-nowrap">
                            <?= ($fechent !== '0000-00-00') ? $fechent : '—' ?>
                        </td>

                        <td class="px-2 py-1.5 text-center font-semibold text-gray-700">
                            <?= number_format($total) ?>
                        </td>

                        <td class="px-2 py-1.5 text-center font-semibold
                            <?= ($term >= $total && $total > 0) ? 'text-emerald-600' : 'text-indigo-600' ?>">
                            <?= number_format($term) ?>
                        </td>

                        <!-- BARRA DE AVANCE -->
                        <td class="px-2 py-1.5 w-36">
                            <div class="flex items-center gap-1.5">
                                <div class="w-20 bg-gray-100 rounded-full h-2 overflow-hidden shrink-0">
                                    <div class="<?= $barColor ?> h-2 rounded-full"
                                         style="width:<?= $pct ?>%"></div>
                                </div>
                                <span class="text-xs font-semibold
                                    <?= $pct >= 100 ? 'text-emerald-600' : 'text-gray-500' ?>">
                                    <?= $pct ?>%
                                </span>
                            </div>
                        </td>

                        <td class="px-2 py-1.5 text-center whitespace-nowrap">
                            <?php
                            $dc = $dias < 0 ? 'bg-red-100 text-red-700'
                                : ($dias <= 5 ? 'bg-yellow-100 text-yellow-700'
                                :              'bg-green-100 text-green-700');
                            ?>
                            <span class="<?= $dc ?> px-1.5 py-0.5 rounded font-medium">
                                <?= $dias ?>d
                            </span>
                        </td>

                        <td class="px-2 py-1.5 text-center whitespace-nowrap">
                            <?php if ($pct >= 100): ?>
                                <span class="bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded font-medium">
                                    Completo
                                </span>
                            <?php elseif ($opr['tiene_epp'] > 0): ?>
                                <span class="bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-medium">
                                    En proceso
                                </span>
                            <?php else: ?>
                                <span class="bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-medium">
                                    Pendiente
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="px-2 py-1.5 text-center">
                            <a href="/orden-produccion/avance/ver/<?= $opr['documento'] ?>"
                               class="text-indigo-600 hover:text-indigo-800 font-medium hover:underline">
                                Ver
                            </a>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>
