<?php $title = "Consulta Orden de Pedido OP # " . $pedido['documento']; ?>

<div class="max-w-7xl mx-auto my-4 px-4">
    <div class="flex items-center justify-between mb-3">
        <a href="/orden-pedido" class="flex items-center text-xs font-bold text-blue-600 hover:underline">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            VOLVER AL LISTADO
        </a>
        <div class="flex gap-2">
            <a href="/orden-pedido/pdf/<?= $pedido['documento'] ?>" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded text-xs font-black shadow-sm flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                GENERAR PDF
            </a>

            <a href="/orden-pedido/edit/<?= $pedido['documento'] ?>" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-1.5 rounded text-xs font-black shadow-sm flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                EDITAR ORDEN
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">

        <div class="bg-gray-800 p-4 text-white">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Documento</span>
                    <div class="text-lg font-black text-white">OP-<?= $pedido['documento'] ?></div>
                </div>
                <div class="md:col-span-2">
                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Cliente</span>
                    <div class="text-sm font-bold truncate"><?= $pedido['codcp'] ?> - <?= $pedido['cliente'] ?></div>
                </div>
                <div>
                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Sucursal</span>
                    <div class="text-sm font-bold"><?= $pedido['codsuc'] ?> - Principal</div>
                </div>
                <div>
                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Estado</span>
                    <span class="inline-block mt-1 text-[10px] bg-blue-500 px-2 py-0.5 rounded font-black uppercase"><?= $pedido['estado'] == ' ' ? 'PENDIENTE' : $pedido['estado'] ?></span>
                </div>

                <div>
                    <span class="block text-[9px] font-bold text-blue-300 uppercase">Fecha OP</span>
                    <div class="text-xs font-bold"><?= date('d/m/Y', strtotime($pedido['fecha'])) ?></div>
                </div>
                <div>
                    <span class="block text-[9px] font-bold text-green-400 uppercase">Fecha Entrega</span>
                    <div class="text-xs font-bold"><?= date('d/m/Y', strtotime($pedido['fechent'])) ?></div>
                </div>
                <div class="md:col-span-3">
                    <span class="block text-[9px] font-bold text-gray-400 uppercase">Comentario General</span>
                    <div class="text-xs italic text-gray-200"><?= $pedido['comen'] ?: 'Sin observaciones' ?></div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 border-b text-[9px] font-black text-gray-500 uppercase">
                    <tr>
                        <th class="px-3 py-2">Referencia / Producto</th>
                        <th class="px-2 py-2 text-center">Talla</th>
                        <th class="px-2 py-2 text-center">Color</th>
                        <th class="px-2 py-2 text-center">Cant.</th>
                        <th class="px-2 py-2 text-right">Precio Un.</th>
                        <th class="px-3 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                    $totalQty = 0;
                    foreach ($detalles as $d):
                        $subtotal = $d['cantidad'] * $d['valor'];
                        $totalQty += $d['cantidad'];
                    ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-2 px-3">
                                <div class="text-xs font-bold text-gray-800"><?= $d['codr'] ?></div>
                                <div class="text-[10px] text-gray-500 uppercase"><?= $d['producto_nombre'] ?></div>
                            </td>
                            <td class="p-2 text-center text-xs font-bold"><?= $d['codtalla'] ?></td>
                            <td class="p-2 text-center text-xs"><?= $d['codcolor'] ?></td>
                            <td class="p-2 text-center text-xs font-black text-blue-600"><?= number_format($d['cantidad'], 0) ?></td>
                            <td class="p-2 text-right text-xs">$ <?= number_format($d['valor'], 2) ?></td>
                            <td class="p-2 px-3 text-right text-xs font-bold text-gray-700">$ <?= number_format($subtotal, 2) ?></td>
                        </tr>
                        <?php if (!empty($d['comencpo'])): ?>
                            <tr class="bg-gray-50/30">
                                <td colspan="6" class="px-3 py-1 text-[9px] text-blue-500 italic border-b border-gray-100">
                                    Nota ítem: <?= $d['comencpo'] ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-blue-50 border-t border-blue-100 px-6 py-3 flex justify-between items-center text-blue-900">
            <div class="flex gap-8">
                <div class="flex flex-col">
                    <span class="text-[9px] font-black uppercase text-blue-400">Items</span>
                    <span class="text-sm font-black"><?= count($detalles) ?></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[9px] font-black uppercase text-blue-400">Total Unidades</span>
                    <span class="text-sm font-black"><?= number_format($totalQty, 0) ?></span>
                </div>
            </div>
            <div class="text-right">
                <span class="text-[9px] font-black uppercase text-blue-400 block mb-1">Total de la Orden</span>
                <span class="text-2xl font-black text-blue-700">
                    $ <?= number_format($pedido['valortotal'] ?? 0, 2) ?>
                </span>
            </div>
        </div>
    </div>
</div>