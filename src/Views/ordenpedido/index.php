<?php $title = "Control de Pedidos OP"; ?>

<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
    <a href="/../dashboard_home" class="flex items-center text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
        </svg>
        Volver
    </a>
    <div>
        <h2 class="text-2xl font-extrabold text-gray-800">Órdenes de Pedido (OP)</h2>
        <p class="text-sm text-gray-500">Filtrando inicialmente documentos pendientes</p>
    </div>

    <div class="flex gap-2 bg-white p-1 rounded-lg border shadow-sm">
        <a href="/orden-pedido?estado=PENDIENTE" class="px-3 py-1.5 text-xs font-bold rounded-md <?= $filtroActual == 'PENDIENTE' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-100' ?>">Pendientes</a>
        <a href="/orden-pedido?estado=C" class="px-3 py-1.5 text-xs font-bold rounded-md <?= $filtroActual == 'C' ? 'bg-green-600 text-white' : 'text-gray-500 hover:bg-gray-100' ?>">Cerradas (C)</a>
        <a href="/orden-pedido?estado=A" class="px-3 py-1.5 text-xs font-bold rounded-md <?= $filtroActual == 'A' ? 'bg-red-600 text-white' : 'text-gray-500 hover:bg-gray-100' ?>">Anuladas (A)</a>
        <a href="/orden-pedido?estado=ALL" class="px-3 py-1.5 text-xs font-bold rounded-md <?= $filtroActual == 'ALL' ? 'bg-gray-800 text-white' : 'text-gray-500 hover:bg-gray-100' ?>">Todas</a>
    </div>

    <a href="/orden-pedido/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition flex items-center shadow-md">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nueva OP
    </a>
</div>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="p-4 text-[10px] font-black text-gray-400 uppercase">Documento</th>
                <th class="p-4 text-[10px] font-black text-gray-400 uppercase">Fecha</th>
                <th class="p-4 text-[10px] font-black text-gray-400 uppercase">Cliente</th>
                <th class="p-4 text-[10px] font-black text-gray-400 uppercase text-right">Total</th>
                <th class="p-4 text-[10px] font-black text-gray-400 uppercase text-center">Estatus</th>
                <th class="p-4 text-[10px] font-black text-gray-400 uppercase text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">

            <?php foreach ($pedidos as $p): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-mono text-sm text-gray-600"><?= $p['prefijo'] ?>-<?= $p['documento'] ?></td>
                    <td class="p-4 text-sm text-gray-600"><?= date('d/m/Y', strtotime($p['fecha'])) ?></td>
                    <td class="p-4">
                        <div class="text-sm font-bold text-gray-800"><?= htmlspecialchars($p['cliente']) ?></div>
                        <div class="text-[10px] text-gray-400 uppercase italic">F. Entrega: <?= $p['fechent'] ?></div>
                    </td>
                    <td class="p-4 text-sm font-bold text-right text-gray-700">$ <?= number_format($p['valortotal'], 2) ?></td>
                    <td class="p-4 text-center">
                        <?php
                        if ($p['estado'] == 'C') {
                            $c = 'bg-green-100 text-green-700';
                            $t = 'CERRADA';
                        } elseif ($p['estado'] == 'A') {
                            $c = 'bg-red-100 text-red-700';
                            $t = 'ANULADA';
                        } else {
                            $c = 'bg-blue-100 text-blue-700';
                            $t = 'PENDIENTE';
                        }
                        ?>
                        <span class="px-2 py-1 rounded text-[9px] font-black <?= $c ?>"><?= $t ?></span>
                    </td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="/orden-pedido/show/<?= $p['documento'] ?>" class="text-blue-500 hover:bg-blue-50 p-1.5 rounded-lg" title="Ver OP">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <?php if ($p['estado'] != 'C' && $p['estado'] != 'A'): ?>
                                <a href="/orden-pedido/edit/<?= $p['documento'] ?>" class="text-amber-500 hover:bg-amber-50 p-1.5 rounded-lg" title="Editar OP">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>