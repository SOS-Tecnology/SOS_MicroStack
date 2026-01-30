<?php $title = "Control de Satélites"; ?>

<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
    <div>
        <h2 class="text-2xl font-extrabold text-gray-800">Control de Satélites</h2>
        <p class="text-sm text-gray-500">Talleres externos de Corte y Confección</p>
    </div>
    <div class="flex gap-3 w-full md:w-auto">
        <div class="relative">
            <input type="text" id="searchInput" placeholder="Buscar taller o ciudad..." 
                   class="pl-10 pr-4 py-2 border rounded-lg text-sm w-full md:w-64 outline-none focus:ring-2 focus:ring-blue-500 transition-all shadow-sm">
            <div class="absolute left-3 top-2.5 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
        <a href="/satelites/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition flex items-center shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Satélite
        </a>
    </div>
</div>

<div id="satelitesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php if (empty($satelites)): ?>
        <div class="col-span-full bg-white p-8 rounded-xl border-2 border-dashed text-center">
            <p class="text-gray-400 italic">No se encontraron satélites registrados.</p>
        </div>
    <?php else: ?>
        <?php foreach ($satelites as $s): ?>
            <div class="satelite-card bg-white border border-gray-200 rounded-xl p-5 hover:shadow-lg transition-all relative group" 
                 data-search="<?= strtolower(htmlspecialchars($s['nombre_proveedor'] . ' ' . $s['ciudad'] . ' ' . $s['tipo'] . ' ' . ($s['contacto'] ?? ''))) ?>">
                
                <div class="flex justify-between items-start mb-3">
                    <div class="flex flex-col gap-1">
                        <span class="px-2 py-0.5 <?= $s['tipo'] == 'Corte' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' ?> text-[10px] font-black uppercase rounded w-fit">
                            <?= $s['tipo'] ?>
                        </span>
                        <span class="text-[9px] font-bold <?= $s['estado'] == 'Activo' ? 'text-green-500' : 'text-red-500' ?> uppercase">
                            ● <?= $s['estado'] ?>
                        </span>
                    </div>
                    <div class="flex items-center text-amber-500 font-bold bg-amber-50 px-2 py-1 rounded-lg border border-amber-100">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <span class="text-sm"><?= number_format($s['calificacion'], 1) ?></span>
                    </div>
                </div>

                <h3 class="font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors" title="<?= htmlspecialchars($s['nombre_proveedor']) ?>">
                    <?= htmlspecialchars($s['nombre_proveedor']) ?>
                </h3>
                <p class="text-[11px] text-gray-500 font-medium uppercase tracking-tighter border-b pb-2 mb-3">
                    <?= $s['especialidad'] ?: 'Sin especialidad definida' ?>
                </p>

                <div class="space-y-1.5 mb-4">
                    <div class="flex items-center text-xs text-gray-600">
                        <svg class="w-3.5 h-3.5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="truncate"><?= htmlspecialchars($s['ciudad'] ?? 'No registra') ?></span>
                    </div>
                    <div class="flex items-center text-xs text-gray-600">
                        <svg class="w-3.5 h-3.5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="truncate"><?= htmlspecialchars($s['contacto'] ?? 'Sin contacto') ?></span>
                    </div>
                </div>
                
                <div class="flex justify-between items-center border-t pt-3 mt-auto">
                    <div class="flex space-x-1">
                        <a href="/satelites/show/<?= $s['id'] ?>" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-md transition-colors" title="Visualizar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="/satelites/edit/<?= $s['id'] ?>" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-md transition-colors" title="Editar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    </div>

                    <form action="/satelites/anular/<?= $s['id'] ?>" method="POST" onsubmit="return confirm('¿Desea cambiar el estado de este satélite a Bloqueado?')">
                        <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md transition-colors" title="Anular/Bloquear">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        if(searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase().trim();
                const cards = document.querySelectorAll('.satelite-card');
                
                cards.forEach(card => {
                    const searchText = card.getAttribute('data-search');
                    if(searchText.includes(term)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });
</script>