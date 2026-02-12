<?php $title = "Órdenes de Producción (OPR)"; ?>

<div class="flex items-center justify-between mb-4">

    <div class="flex items-center gap-4">
        <a href="/dashboard_home"
           class="text-sm text-gray-600 hover:text-blue-600 transition">
            &larr; Volver
        </a>

        <div>
            <h2 class="text-xl font-bold text-gray-800">
                Órdenes de Pedido → Producción
            </h2>
            <p class="text-xs text-gray-500">
                Seleccione una OP para enviarla a Producción (OPR)
            </p>
        </div>
    </div>

</div>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-3 py-2 text-[10px] font-black text-gray-400 uppercase">Documento</th>
                <th class="px-3 py-2 text-[10px] font-black text-gray-400 uppercase">Fecha OP</th>
                <th class="px-3 py-2 text-[10px] font-black text-gray-400 uppercase">Cliente</th>
                <th class="px-3 py-2 text-[10px] font-black text-gray-400 uppercase">Entrega</th>
                <th class="px-3 py-2 text-[10px] font-black text-gray-400 uppercase text-center">OPR</th>
                <th class="px-3 py-2 text-[10px] font-black text-gray-400 uppercase text-center">Acción</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            <?php foreach ($ordenes as $op): ?>
                <tr class="hover:bg-gray-50 transition-colors">

                    <td class="px-3 py-2 font-mono text-sm text-gray-700">
                        <?= $op['prefijo'] ?>-<?= $op['documento'] ?>
                    </td>

                    <td class="px-3 py-2 text-sm text-gray-600">
                        <?= date('d/m/Y', strtotime($op['fecha_op'])) ?>
                    </td>

                    <td class="px-3 py-2">
                        <div class="text-sm font-bold text-gray-800">
                            <?= htmlspecialchars($op['cliente_nombre']) ?>
                        </div>
                        <div class="text-[10px] text-gray-400 uppercase">
                            <?= $op['cliente_id'] ?>
                        </div>
                    </td>

                    <td class="px-3 py-2 text-sm text-gray-600">
                        <?= $op['fecha_entrega'] ?>
                    </td>

                    <td class="px-3 py-2 text-center">
                        <?php if ($op['tiene_opr'] > 0): ?>
                            <span class="px-2 py-1 rounded text-[9px] font-black bg-green-100 text-green-700">
                                GENERADA
                            </span>
                        <?php else: ?>
                            <span class="px-2 py-1 rounded text-[9px] font-black bg-gray-100 text-gray-600">
                                NO
                            </span>
                        <?php endif; ?>
                    </td>

                    <td class="px-3 py-2 text-center">
                        <?php if ($op['tiene_opr'] == 0): ?>
                            <a href="/orden-produccion/create/<?= $op['documento'] ?>"
                               class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow transition">
                                Enviar a Producción
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-gray-400 italic">
                                Bloqueada
                            </span>
                        <?php endif; ?>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
