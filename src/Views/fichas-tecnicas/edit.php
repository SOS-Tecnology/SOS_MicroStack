<?php
$title = "Editar Ficha Técnica: " . htmlspecialchars($ficha['nombre_ficha']);
?>

<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-4 border-b pb-2">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar Ficha Técnica #<?= $ficha['id'] ?>
        </h2>
        <a href="/fichas-tecnicas" class="text-gray-500 hover:text-gray-700 flex items-center text-sm font-medium transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver
        </a>
    </div>

    <div class="bg-white shadow-sm border rounded-lg p-5">
        <form action="/fichas-tecnicas/update/<?= $ficha['id'] ?>" method="POST" enctype="multipart/form-data">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Producto Base:</label>
                        <select name="id_producto_base" class="js-example-basic-single w-full" required>
                            <?php foreach ($productosBase as $producto): ?>
                                <option value="<?= $producto['codr'] ?>" <?= ($producto['codr'] == $ficha['id_producto_base']) ? 'selected' : '' ?>>
                                    <?= $producto['codr'] ?> - <?= $producto['descr'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Cliente:</label>
                        <select name="id_cliente" class="js-example-basic-single w-full" required>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['codcli'] ?>" <?= ($cliente['codcli'] == $ficha['id_cliente']) ? 'selected' : '' ?>>
                                    <?= $cliente['nombrecli'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre de la Ficha:</label>
                        <input type="text" name="nombre_ficha" value="<?= htmlspecialchars($ficha['nombre_ficha']) ?>" class="w-full border-gray-300 rounded-md shadow-sm p-2 text-sm border" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Adicionales / Observaciones:</label>
                        <textarea name="adicionales" rows="2" class="w-full border-gray-300 rounded-md shadow-sm p-2 text-sm border"><?= htmlspecialchars($ficha['adicionales'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="bg-blue-50 p-4 rounded-md">
                    <h3 class="text-sm font-bold text-blue-800 mb-3 flex items-center">Tiempos de Proceso</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase">Corte:</label>
                            <input type="number" step="0.01" name="tiempo_corte" value="<?= $ficha['tiempo_corte'] ?>" class="w-full border-gray-300 rounded p-1.5 text-sm border">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase">Confección:</label>
                            <input type="number" step="0.01" name="tiempo_confeccion" value="<?= $ficha['tiempo_confeccion'] ?>" class="w-full border-gray-300 rounded p-1.5 text-sm border">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase">Alistamiento:</label>
                            <input type="number" step="0.01" name="tiempo_alistamiento" value="<?= $ficha['tiempo_alistamiento'] ?>" class="w-full border-gray-300 rounded p-1.5 text-sm border">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase">Remate:</label>
                            <input type="number" step="0.01" name="tiempo_remate" value="<?= $ficha['tiempo_remate'] ?>" class="w-full border-gray-300 rounded p-1.5 text-sm border">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t pt-4">
                <h3 class="text-sm font-bold text-gray-700 mb-3 uppercase">Fotos de la Ficha</h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4 mb-4">
                    <?php foreach ($fotos as $foto): ?>
                        <div class="relative group border rounded-lg p-1 bg-gray-50 shadow-sm overflow-hidden">
                            <img src="/<?= $foto['ruta_imagen'] ?>" class="h-24 w-full object-cover rounded-md cursor-pointer hover:opacity-75" onclick="window.open(this.src, '_blank')">
                            <input type="hidden" name="fotos_existentes[]" value="<?= $foto['id'] ?>">
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex items-center mb-4">
                    <label class="cursor-pointer bg-blue-50 text-blue-700 px-4 py-2 rounded-lg border border-blue-200 hover:bg-blue-100 transition text-sm font-semibold">
                        <span>+ Añadir Nuevas Imágenes</span>
                        <input type="file" id="input-fotos" name="fotos[]" multiple accept="image/*" class="hidden">
                    </label>
                </div>
                <div id="preview-container" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4"></div>
            </div>

            <div class="mt-6 border-t pt-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-gray-700 uppercase">Insumos y Referencias</h3>
                    <button type="button" id="addRef" class="text-xs bg-green-600 text-white px-2 py-1 rounded flex items-center hover:bg-green-700 transition">
                        Añadir
                    </button>
                </div>

                <div id="referencias" class="space-y-2">
                    <?php foreach ($detalles as $index => $detalle): ?>
                        <div class="flex flex-wrap md:flex-nowrap gap-2 p-2 bg-gray-50 rounded-md border border-gray-100">
                            <div class="w-full md:w-2/5">
                                <select name="referencias[<?= $index ?>][codr]" class="js-example-basic-single w-full">
                                    <?php foreach ($referencias as $ref): ?>
                                        <option value="<?= $ref['codr'] ?>" <?= ($ref['codr'] == $detalle['codr']) ? 'selected' : '' ?>>
                                            <?= $ref['codr'] ?> - <?= $ref['descr'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <input type="number" name="referencias[<?= $index ?>][cantidad]" value="<?= $detalle['cantidad'] ?>" placeholder="Cant." class="border p-1.5 w-full md:w-20 rounded text-sm">
                            <input type="text" name="referencias[<?= $index ?>][talla]" value="<?= $detalle['talla'] ?>" placeholder="Talla" class="border p-1.5 w-full md:w-24 rounded text-sm">
                            <input type="text" name="referencias[<?= $index ?>][color]" value="<?= $detalle['color'] ?>" placeholder="Color" class="border p-1.5 w-full md:w-32 rounded text-sm flex-1">
                            <button type="button" class="remove-ref text-red-500 hover:text-red-700 px-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-4 border-t">
                <a href="/fichas-tecnicas" class="text-sm font-medium text-gray-600 hover:underline">Cancelar</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-bold shadow-md transition">
                    Actualizar Ficha Técnica
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        function aplicarSelect2(selector) {
            $(selector).select2({
                placeholder: "Seleccione una opción...",
                allowClear: true,
                width: '100%'
            });
        }

        aplicarSelect2('.js-example-basic-single');

        let refIndex = <?= count($detalles) ?>;
        $('#addRef').click(function() {
            const nuevoID = `ref_select_${refIndex}`;
            const html = `
                <div class="flex flex-wrap md:flex-nowrap gap-2 p-2 bg-gray-50 rounded-md border border-gray-100 mt-2">
                    <div class="w-full md:w-2/5">
                        <select name="referencias[${refIndex}][codr]" id="${nuevoID}" class="js-example-basic-single w-full">
                            <option value="">-- Referencia --</option>
                            <?php foreach ($referencias as $ref): ?>
                                <option value="<?= $ref['codr'] ?>"><?= $ref['codr'] ?> - <?= $ref['descr'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="number" name="referencias[${refIndex}][cantidad]" placeholder="Cant." class="border p-1.5 w-full md:w-20 rounded text-sm">
                    <input type="text" name="referencias[${refIndex}][talla]" placeholder="Talla" class="border p-1.5 w-full md:w-24 rounded text-sm">
                    <input type="text" name="referencias[${refIndex}][color]" placeholder="Color" class="border p-1.5 w-full md:w-32 rounded text-sm flex-1">
                    <button type="button" class="remove-ref text-red-500 hover:text-red-700 px-2">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>`;
            $('#referencias').append(html);
            aplicarSelect2(`#${nuevoID}`);
            refIndex++;
        });

        $(document).on('click', '.remove-ref', function() {
            $(this).closest('div').remove();
        });

        $('#input-fotos').on('change', function(e) {
            const files = Array.from(e.target.files);
            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const html = `
                        <div class="relative group border rounded-lg p-1 bg-white shadow-sm overflow-hidden">
                            <img src="${event.target.result}" class="h-24 w-full object-cover rounded-md">
                            <button type="button" class="remove-foto absolute top-0 right-0 bg-red-500 text-white rounded-bl-lg p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>`;
                    $('#preview-container').append(html);
                };
                reader.readAsDataURL(file);
            });
        });

        $(document).on('click', '.remove-foto', function() {
            $(this).closest('div').remove();
        });
    });
</script>