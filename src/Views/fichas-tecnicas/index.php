<?php
$title = "Fichas Técnicas";
$fichas = $fichas ?? [];
?>

<div class="flex flex-col md:row justify-between items-start md:items-center mb-6 gap-4 border-b pb-4">
    <div>
        <h2 class="text-2xl font-extrabold text-gray-800">Fichas Técnicas</h2>
        <p class="text-sm text-gray-500">Gestión de producción y especificaciones</p>
    </div>
    
    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
        <div class="relative">
            <input type="text" id="searchInput" placeholder="Buscar ficha o cliente..." 
                   class="pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none w-full sm:w-64 transition-all shadow-sm">
            <div class="absolute left-3 top-2.5 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <a href="/fichas-tecnicas/create" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm transition flex items-center justify-center text-sm font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Ficha
        </a>
    </div>
</div>

<div id="fichasGrid" class="space-y-2">
    <?php if (empty($fichas)): ?>
        <div id="emptyState" class="bg-white p-8 rounded-lg border-2 border-dashed border-gray-200 text-center">
            <p class="text-gray-400 text-sm italic">No hay fichas registradas actualmente.</p>
        </div>
    <?php else: ?>
        <?php foreach ($fichas as $index => $ficha): ?>
            <div class="ficha-card <?= ($index % 2 == 0) ? 'bg-white' : 'bg-gray-50' ?> border border-gray-200 rounded-lg px-4 py-3 flex justify-between items-center hover:shadow-md hover:border-blue-300 transition-all group"
                 data-search="<?= strtolower(htmlspecialchars($ficha['nombre_ficha'] . ' ' . ($ficha['nombre_cliente'] ?? $ficha['id_cliente']))) ?>">
                
                <div class="flex flex-col">
                    <div class="flex items-center">
                        <span class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition-colors">
                            <?= htmlspecialchars($ficha['nombre_ficha']) ?>
                        </span>
                        <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-full uppercase tracking-tighter">
                            ID: <?= $ficha['id'] ?>
                        </span>
                    </div>
                    <span class="text-xs text-gray-500 mt-0.5">
                        <span class="font-medium text-gray-400 uppercase text-[10px]">Cliente:</span> 
                        <?= htmlspecialchars($ficha['nombre_cliente'] ?? $ficha['id_cliente']) ?>
                    </span>
                </div>
<div class="flex items-center space-x-1">
    <a href="/fichas-tecnicas/show/<?= $ficha['id'] ?>" 
       class="p-2 text-blue-500 hover:bg-blue-50 rounded-md transition-colors" title="Consultar">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
    </a>

    <a href="/fichas-tecnicas/edit/<?= $ficha['id'] ?>" 
       class="p-2 text-amber-500 hover:bg-amber-50 rounded-md transition-colors" title="Editar">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
    </a>

    <form action="/fichas-tecnicas/delete/<?= $ficha['id'] ?>" method="POST" onsubmit="return confirm('¿Eliminar esta ficha técnica definitivamente?')" class="inline">
        <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-md transition-colors" title="Eliminar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
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
                const cards = document.querySelectorAll('.ficha-card');
                
                cards.forEach(card => {
                    const searchText = card.getAttribute('data-search');
                    // Usamos display flex porque es el original del contenedor
                    if(searchText.includes(term)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });
</script>