<?php
$title = "Fichas Técnicas";
// $fichas ya está disponible gracias al extract($data) en renderView
?>

<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold text-gray-800 tracking-tight">Fichas Técnicas</h2>
    <a href="/fichas-tecnicas/create" 
       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md shadow-sm transition flex items-center text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva Ficha
    </a>
</div>

<div class="space-y-2">
    <?php if (empty($fichas)): ?>
        <div class="bg-white p-6 rounded-lg border border-dashed border-gray-300 text-center text-gray-500">
            No se encontraron fichas técnicas.
        </div>
    <?php else: ?>
        <?php foreach ($fichas as $index => $ficha): ?>
            <div class="<?= ($index % 2 == 0) ? 'bg-white' : 'bg-gray-50' ?> border border-gray-200 rounded-md px-4 py-2 flex justify-between items-center hover:shadow-sm transition-all">
                
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-800">
                        <?= htmlspecialchars($ficha['nombre_ficha']) ?>
                    </span>
                    <span class="text-xs text-gray-500">
                        <span class="font-medium text-blue-500">Cliente:</span> 
                        <?= htmlspecialchars($ficha['nombre_cliente'] ?? $ficha['id_cliente']) ?>
                    </span>
                </div>

                <div class="flex items-center space-x-2">
                    <a href="/fichas-tecnicas/edit/<?= $ficha['id'] ?>" 
                       class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-full transition" title="Editar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </a>

                    <a href="/fichas-tecnicas/delete/<?= $ficha['id'] ?>" 
                       onclick="return confirm('¿Está seguro de eliminar esta ficha?')"
                       class="p-1.5 text-red-600 hover:bg-red-100 rounded-full transition" title="Eliminar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>