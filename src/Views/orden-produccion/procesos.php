<div class="p-6">

    {{-- ============================================================ --}}
    {{-- CABECERA: título + botón volver + acciones                   --}}
    {{-- Misma estructura que ver_avance.php                          --}}
    {{-- ============================================================ --}}
    <div class="flex justify-between items-center mb-6">

        <a href="/orden-produccion/avance/ver/<?= $documento ?>"
            class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">
            ← Volver
        </a>

        <h2 class="text-2xl font-semibold text-gray-700">
            OPR <?= $documento ?> &mdash; <?= htmlspecialchars($proceso) ?>
        </h2>

        <div class="flex gap-2">

            <a href="/epp/create/<?= $documento ?>/<?= $proceso_id ?>"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                + Nuevo EPP
            </a>

            <?php if ($existenEpp): ?>
                <span class="bg-gray-200 text-gray-500 px-4 py-2 rounded-lg text-sm cursor-not-allowed">
                    + RPP (desde la fila EPP)
                </span>
            <?php else: ?>
                <span class="bg-gray-100 text-gray-400 px-4 py-2 rounded-lg text-sm cursor-not-allowed">
                    Sin EPP aún
                </span>
            <?php endif; ?>

        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- TARJETAS RESUMEN: Meta / EPP / RPP / Avance                  --}}
    {{-- Misma tipología de cards que ver_avance.php                  --}}
    {{-- ============================================================ --}}
    <?php
        $porcentaje_global = ($meta > 0) ? round(($rpp_total / $meta) * 100) : 0;
        $porcentaje_global = min($porcentaje_global, 100); // nunca superar 100%
    ?>

    <div class="grid grid-cols-4 gap-4 mb-6">

        <div class="bg-white shadow rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Meta (prendas)</p>
            <p class="text-2xl font-semibold text-gray-700">
                <?= number_format($meta) ?>
            </p>
        </div>

        <div class="bg-white shadow rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Enviado a proceso (EPP)</p>
            <p class="text-2xl font-semibold text-blue-600">
                <?= number_format($epp_total) ?>
            </p>
        </div>

        <div class="bg-white shadow rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Recibido (RPP)</p>
            <p class="text-2xl font-semibold text-green-600">
                <?= number_format($rpp_total) ?>
            </p>
        </div>

        <div class="bg-white shadow rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-1">Avance</p>
            <p class="text-2xl font-semibold text-green-600">
                <?= $porcentaje_global ?>%
            </p>
            <div class="w-full bg-gray-200 h-2 rounded mt-2">
                <div class="bg-green-500 h-2 rounded"
                     style="width: <?= $porcentaje_global ?>%">
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- TABLA DE MOVIMIENTOS                                         --}}
    {{-- ============================================================ --}}
    <div class="bg-white shadow rounded-xl overflow-hidden">

        <div class="bg-gray-100 px-4 py-2 font-semibold text-sm text-gray-600">
            Movimientos EPP / RPP
        </div>

        <table class="w-full text-sm">

            <thead class="bg-gray-50 text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-2 text-left">Tipo</th>
                    <th class="px-4 py-2 text-left">Documento</th>
                    <th class="px-4 py-2 text-left">Fecha</th>
                    <th class="px-4 py-2 text-left">Satélite / Proveedor</th>
                    <th class="px-4 py-2 text-right">Cantidad</th>
                    <th class="px-4 py-2 text-left">Estado</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                <?php if (!empty($movimientos)): ?>

                    <?php foreach ($movimientos as $m): ?>

                        <tr class="hover:bg-gray-50">

                            <td class="px-4 py-2">
                                <?php if ($m['tm'] === 'EPP'): ?>
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                                        EPP
                                    </span>
                                <?php else: ?>
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">
                                        RPP
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="px-4 py-2 font-mono text-gray-700">
                                <?= htmlspecialchars($m['documento']) ?>
                            </td>

                            <td class="px-4 py-2 text-gray-600">
                                <?= htmlspecialchars($m['fecha']) ?>
                            </td>

                            <td class="px-4 py-2 text-gray-700">
                                <?= htmlspecialchars($m['proveedor'] ?? '—') ?>
                            </td>

                            <td class="px-4 py-2 text-right font-semibold text-gray-700">
                                <?= number_format($m['cantidad'] ?? 0) ?>
                            </td>

                            <td class="px-4 py-2">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                    <?= htmlspecialchars($m['estado'] ?? 'Activo') ?>
                                </span>
                            </td>

                            <td class="px-4 py-2">
                                <div class="flex gap-2">

                                    <a href="/<?= strtolower($m['tm']) ?>/show/<?= $m['documento'] ?>"
                                        class="text-blue-600 hover:underline text-sm">
                                        Ver
                                    </a>

                                    <?php if ($m['tm'] === 'EPP'): ?>
                                        <a href="/rpp/create/<?= $m['documento'] ?>"
                                            class="text-green-600 hover:underline text-sm">
                                            + RPP
                                        </a>
                                    <?php endif; ?>

                                    <a href="/<?= strtolower($m['tm']) ?>/print/<?= $m['documento'] ?>"
                                        class="text-gray-500 hover:underline text-sm">
                                        Imprimir
                                    </a>

                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="text-center px-4 py-8 text-gray-400">
                            No hay movimientos registrados para este proceso.
                            <a href="/epp/create/<?= $documento ?>/<?= $proceso_id ?>"
                                class="text-blue-600 hover:underline ml-1">
                                Crear primer EPP
                            </a>
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>