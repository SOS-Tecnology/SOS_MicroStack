<?php
$title = "Consulta: " . htmlspecialchars($ficha['nombre_ficha']);
?>

<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6 no-print">
        <a href="/fichas-tecnicas" class="flex items-center text-gray-600 hover:text-blue-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver al listado
        </a>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-bold flex items-center hover:bg-black transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2" />
                </svg>
                Imprimir Ficha
            </button>
            <a href="/fichas-tecnicas/edit/<?= $ficha['id'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-blue-700 transition">
                Editar Datos
            </a>
        </div>
    </div>

    <div class="bg-white border shadow-lg rounded-lg overflow-hidden">
        <div class="bg-gray-50 border-b p-6 flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 leading-tight uppercase"><?= htmlspecialchars($ficha['nombre_ficha']) ?></h1>
                <p class="text-blue-600 font-bold tracking-widest text-sm mt-1">CLIENTE: <?= htmlspecialchars($ficha['nombre_cliente'] ?? 'N/A') ?></p>
            </div>
            <div class="text-right">
                <span class="text-xs text-gray-400 uppercase font-bold block">ID Ficha</span>
                <span class="text-2xl font-mono text-gray-800">#<?= str_pad($ficha['id'], 5, "0", STR_PAD_LEFT) ?></span>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-1 space-y-6">
                <section>
                    <h3 class="text-xs font-black text-gray-400 uppercase border-b mb-3">Tiempos de Producción</h3>
                    <div class="space-y-2">
                        <?php 
                        $tiempos = [
                            'Corte' => $ficha['tiempo_corte'],
                            'Confección' => $ficha['tiempo_confeccion'],
                            'Alistamiento' => $ficha['tiempo_alistamiento'],
                            'Remate' => $ficha['tiempo_remate']
                        ];
                        $total = array_sum($tiempos);
                        foreach($tiempos as $label => $valor): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600"><?= $label ?>:</span>
                                <span class="font-bold text-gray-800"><?= number_format($valor, 2) ?> min</span>
                            </div>
                        <?php endforeach; ?>
                        <div class="flex justify-between text-base border-t pt-2 mt-2 font-black text-blue-800">
                            <span>TOTAL:</span>
                            <span><?= number_format($total, 2) ?> min</span>
                        </div>
                    </div>
                </section>

                <section>
                    <h3 class="text-xs font-black text-gray-400 uppercase border-b mb-3">Observaciones</h3>
                    <p class="text-sm text-gray-700 italic bg-yellow-50 p-3 rounded border border-yellow-100">
                        <?= !empty($ficha['adicionales']) ? nl2br(htmlspecialchars($ficha['adicionales'])) : 'Sin observaciones adicionales.' ?>
                    </p>
                </section>
            </div>

            <div class="md:col-span-2 space-y-8">
                <section>
                    <h3 class="text-xs font-black text-gray-400 uppercase border-b mb-4 tracking-tighter text-right">Insumos y Referencias Requeridas</h3>
                    <div class="overflow-hidden border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-[10px] font-bold text-gray-500 uppercase">Referencia</th>
                                    <th class="px-4 py-2 text-center text-[10px] font-bold text-gray-500 uppercase">Cant</th>
                                    <th class="px-4 py-2 text-center text-[10px] font-bold text-gray-500 uppercase">Talla</th>
                                    <th class="px-4 py-2 text-left text-[10px] font-bold text-gray-500 uppercase">Color</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php foreach($detalles as $det): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-xs">
                                        <div class="font-bold text-gray-900"><?= $det['codr'] ?></div>
                                        <div class="text-[10px] text-gray-500"><?= htmlspecialchars($det['descr']) ?></div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-center font-mono font-bold"><?= $det['cantidad'] ?></td>
                                    <td class="px-4 py-3 text-xs text-center uppercase"><?= $det['talla'] ?></td>
                                    <td class="px-4 py-3 text-xs uppercase font-medium text-gray-600"><?= $det['color'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section>
                    <h3 class="text-xs font-black text-gray-400 uppercase border-b mb-4 text-right">Galería de Referencia</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <?php foreach($fotos as $f): ?>
                        <div class="border rounded-md overflow-hidden bg-gray-100 h-48 group">
                            <img src="/<?= $f['ruta_imagen'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500 cursor-pointer" onclick="window.open(this.src)">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .bg-white { border: none !important; shadow: none !important; }
    .max-w-5xl { max-width: 100% !important; }
}
</style>