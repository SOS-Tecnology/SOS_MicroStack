<?php $title = "Procesos de Fabricación"; ?>

<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-gray-800">Procesos de Fabricación</h2>
        <p class="text-sm text-gray-500">Configuración de operaciones productivas</p>
    </div>

    <div class="flex gap-3 w-full md:w-auto">
        <input type="text" id="searchInput" placeholder="Buscar proceso..."
            class="pl-4 pr-4 py-2 border rounded-lg text-sm w-full md:w-64 shadow-sm focus:ring-2 focus:ring-blue-500">

        <a href="/procesos-ft/create"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm">
            + Nuevo Proceso
        </a>

        <a href="/dashboard_home"
            class="flex items-center text-sm font-semibold text-gray-600 hover:text-blue-600">
            ← Volver
        </a>
    </div>
</div>

<div id="procesosGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

    <?php if (empty($procesos)): ?>

        <div class="col-span-full bg-white p-8 rounded-xl border-2 border-dashed text-center">
            <p class="text-gray-400 italic">No hay procesos registrados.</p>
        </div>

    <?php else: ?>

        <?php foreach ($procesos as $p): ?>

            <?php
            // Valores seguros
            $reproceso = $p['es_reproceso'] ?? 0;
            $tipoTiempo = $p['modo_tiempo'] ?? 'ACUMULADO';
            $activo = $p['activo'] ?? 1;

            $badgeTiempo = $tipoTiempo == 'POR_UNIDAD'
                ? 'bg-purple-100 text-purple-700'
                : 'bg-blue-100 text-blue-700';

            $estadoTxt = $activo ? 'Activo' : 'Inactivo';
            $estadoColor = $activo ? 'text-green-500' : 'text-red-500';
            ?>

            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow hover:shadow-lg transition">

                <!-- TITULO -->
                <h3 class="font-bold text-gray-900 text-lg">
                    <span class="px-2 py-0.5 <?= $badgeTiempo ?> text-[10px] font-black uppercase rounded w-fit">
                        <?= htmlspecialchars($p['nombre']) ?>
                    </span>
                </h3>
                <p class="text-xs text-gray-500 mb-3">
                    <?= htmlspecialchars($p['descripcion'] ?? '') ?>
                </p>

                <!-- DETALLE -->
                <div class="text-xs text-gray-600 space-y-1 mb-4">

                    <div>🏭 Origen: <?= htmlspecialchars($p['bod_origen'] ?? '-') ?></div>
                    <div>📦 Destino: <?= htmlspecialchars($p['bod_destino'] ?? '-') ?></div>
                    <div>⬅ Entrada: <?= htmlspecialchars($p['tipo_mov_entrada'] ?? '-') ?></div>
                    <div>➡ Salida: <?= htmlspecialchars($p['tipo_mov_salida'] ?? '-') ?></div>
                    <div>👤 Responsable: <?= htmlspecialchars($p['responsable'] ?? 'Interno') ?></div>

                </div>
                <!-- HEADER -->
                <div class="flex justify-between items-start mb-3">
                    <div class="flex flex-col gap-1">

                        <span class="text-xs text-gray-600 space-y-1 mb-4">
                            Como se toma el tiempo: <?= htmlspecialchars($tipoTiempo) ?>
                        </span>
                        <span class="text-xs text-gray-600 space-y-1 mb-4">
                            Estado del proceso: 
                            <span class="text-[10px] font-bold <?= $estadoColor ?> uppercase">
                                ● <?= $estadoTxt ?>
                            </span>
                        </span>

                        <!-- <span class="text-[10px] font-bold <?= $estadoColor ?> uppercase">
                           ● <?= $estadoTxt ?>
                        </span> -->

                    </div>

                    <?php if ($reproceso): ?>
                        <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">
                            Reproceso
                        </span>
                    <?php endif; ?>
                </div>
                <!-- ACCIONES -->
                <div class="flex justify-end gap-2 border-t pt-3">

                    <div class="flex space-x-1">

                        <!-- VER -->
                        <a href="/procesos-ft/show/<?= $p['id'] ?>"
                            class="relative group p-1.5 text-blue-500 hover:bg-blue-50 rounded-md transition">

                            <!-- icono -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0" />
                                <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                                     c4.478 0 8.268 2.943 9.542 7
                                     -1.274 4.057-5.064 7-9.542 7
                                     -4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <span class="tooltip">Ver</span>
                        </a>

                        <!-- EDITAR -->
                        <a href="/procesos-ft/edit/<?= $p['id'] ?>"
                            class="relative group p-1.5 text-amber-500 hover:bg-amber-50 rounded-md transition">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                         m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>

                            <span class="tooltip">Editar</span>
                        </a>

                        <!-- ELIMINAR -->
                        <form action="/procesos-ft/delete/<?= $p['id'] ?>" method="POST"
                            onsubmit="return confirm('¿Eliminar proceso?')"
                            class="relative group">

                            <button class="p-1.5 text-red-500 hover:bg-red-50 rounded-md transition">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                         a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                         M1 7h22M10 3h4a1 1 0 011 1v2H9V4a1 1 0 011-1z" />
                                </svg>

                            </button>

                            <span class="tooltip">Eliminar</span>
                        </form>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let term = this.value.toLowerCase();

        document.querySelectorAll('#procesosGrid > div').forEach(card => {
            card.style.display = card.innerText.toLowerCase().includes(term) ?
                'block' :
                'none';
        });
    });
</script>