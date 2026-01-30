<?php $title = "Detalle de Satélite"; ?>

<div class="max-w-4xl mx-auto bg-white border rounded-2xl shadow-sm overflow-hidden">
    <div class="max-w-4xl mx-auto mb-4">
        <a href="/satelites" class="inline-flex items-center text-sm font-medium text-blue-600 hover:underline">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Regresar al control de satélites
        </a>
    </div>
    <div class="bg-gray-800 p-6 text-white flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold"><?= htmlspecialchars($s['nombre']) ?></h2>
            <p class="text-gray-400 text-sm uppercase tracking-widest font-bold"><?= $s['tipo'] ?> - <?= $s['especialidad'] ?></p>
        </div>
        <div class="text-right">
            <span class="block text-[10px] text-gray-400 uppercase">Estado</span>
            <span class="px-3 py-1 bg-green-500 text-xs font-bold rounded-full">ACTIVO</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-0 border-b">
        <div class="p-8 border-r bg-gray-50/50">
            <h3 class="text-xs font-black text-blue-600 uppercase mb-4 tracking-widest">Información Operativa</h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Capacidad:</span>
                    <span class="font-bold text-gray-800"><?= number_format($s['capacidad_produccion']) ?> pndas/mes</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-sm">Calificación:</span>
                    <span class="text-amber-500 font-black text-xl flex items-center">
                        <?= $s['calificacion'] ?>
                        <svg class="w-5 h-5 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        <div class="p-8">
            <h3 class="text-xs font-black text-blue-600 uppercase mb-4 tracking-widest">Datos del Proveedor</h3>
            <div class="space-y-3">
                <div class="flex items-start">
                    <div class="bg-blue-100 p-2 rounded-lg mr-3 text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold leading-none mb-1">Dirección y Ciudad</p>
                        <p class="text-sm text-gray-700"><?= $s['direcc'] ?> - <strong><?= $s['ciud'] ?></strong></p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-green-100 p-2 rounded-lg mr-3 text-green-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold leading-none mb-1">Contacto y Teléfono</p>
                        <p class="text-sm text-gray-700"><?= $s['contacto'] ?> (<?= $s['tels'] ?>)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-8 bg-white">
        <p class="text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Comentarios Internos</p>
        <p class="text-sm text-gray-600 italic bg-gray-50 p-4 rounded-lg border border-dashed">
            <?= $s['comentarios'] ?: 'No hay comentarios registrados para este satélite.' ?>
        </p>
    </div>
</div>