<?php
$title = "Nueva Ficha Técnica";
// Eliminamos ob_start() y el include del final para que funcione con renderView
?>

<?php if (!empty($_SESSION['errors'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong>¡Error!</strong>
        <ul class="list-disc list-inside text-sm">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-4 border-b pb-2">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V6a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z" />
            </svg>
            Nueva Ficha Técnica
        </h2>
        <a href="/fichas-tecnicas" class="text-gray-500 hover:text-gray-700 flex items-center text-sm font-medium transition" title="Volver al Listado">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="hidden sm:inline">Volver</span>
        </a>
    </div>

    <div class="bg-white shadow-sm border rounded-lg p-5">
        <form action="/fichas-tecnicas/store" method="POST" enctype="multipart/form-data">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Producto Base:</label>
                        <select name="id_producto_base" id="id_producto_base" class="js-example-basic-single w-full" required>
                            <option value="">-- Seleccione un producto --</option>
                            <?php foreach ($productosBase as $producto): ?>
                                <option value="<?= $producto['codr'] ?>"><?= $producto['codr'] ?> - <?= $producto['descr'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Cliente:</label>
                        <select name="id_cliente" id="id_cliente" class="js-example-basic-single w-full" required>
                            <option value="">-- Seleccione un cliente --</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['codcli'] ?>"><?= $cliente['nombrecli'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre de la Ficha:</label>
                        <input type="text" name="nombre_ficha" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 text-sm border" placeholder="Ej: Camisa Slim Fit" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Adicionales / Observaciones:</label>
                        <textarea name="adicionales" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 text-sm border"></textarea>
                    </div>
                </div>

                <div class="bg-blue-50 p-4 rounded-md">
                    <h3 class="text-sm font-bold text-blue-800 mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tiempos de Proceso (Minutos)
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase">Corte:</label>
                            <input type="number" step="0.01" name="tiempo_corte" class="w-full border-gray-300 rounded p-1.5 text-sm border">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase">Confección:</label>
                            <input type="number" step="0.01" name="tiempo_confeccion" class="w-full border-gray-300 rounded p-1.5 text-sm border">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase">Alistamiento:</label>
                            <input type="number" step="0.01" name="tiempo_alistamiento" class="w-full border-gray-300 rounded p-1.5 text-sm border">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase">Remate:</label>
                            <input type="number" step="0.01" name="tiempo_remate" class="w-full border-gray-300 rounded p-1.5 text-sm border">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t pt-4">
                <h3 class="text-sm font-bold text-gray-700 mb-2 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Fotos del Producto
                </h3>

                <div class="flex items-center mb-4">
                    <label class="cursor-pointer bg-blue-50 text-blue-700 px-4 py-2 rounded-lg border border-blue-200 hover:bg-blue-100 transition text-sm font-semibold">
                        <span>+ Seleccionar Imágenes</span>
                        <input type="file" id="input-fotos" name="fotos[]" multiple accept="image/*" class="hidden">
                    </label>
                    <span id="count-fotos" class="ml-3 text-xs text-gray-500">0 imágenes seleccionadas</span>
                </div>

                <div id="preview-container" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                </div>
            </div>

            <div class="mt-6 border-t pt-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Insumos y Referencias
                    </h3>
                    <button type="button" id="addRef" class="text-xs bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded flex items-center transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Añadir
                    </button>
                </div>

                <div id="referencias" class="space-y-2">
                    <div class="flex flex-wrap md:flex-nowrap gap-2 p-2 bg-gray-50 rounded-md border border-gray-100">
                        <div class="w-full md:w-2/5">
                            <select name="referencias[0][codr]" class="js-example-basic-single w-full">
                                <option value="">-- Referencia --</option>
                                <?php foreach ($referencias as $ref): ?>
                                    <option value="<?= $ref['codr'] ?>"><?= $ref['codr'] ?> - <?= $ref['descr'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="number" name="referencias[0][cantidad]" placeholder="Cant." class="border p-1.5 w-full md:w-20 rounded text-sm">
                        <input type="text" name="referencias[0][talla]" placeholder="Talla" class="border p-1.5 w-full md:w-24 rounded text-sm">
                        <input type="text" name="referencias[0][color]" placeholder="Color" class="border p-1.5 w-full md:w-32 rounded text-sm flex-1">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 mt-8 pt-4 border-t">
                <a href="/fichas-tecnicas" class="text-sm font-medium text-gray-600 hover:underline">Cancelar</a>
                <form action="/fichas-tecnicas/store" method="POST" enctype="multipart/form-data">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-bold">
                        Guardar Ficha Técnica
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Función reutilizable para inicializar Select2
        function aplicarSelect2(selector) {
            $(selector).select2({
                placeholder: "Seleccione una opción...",
                allowClear: true,
                width: '100%' // Crucial para que no se encoja el select
            });
        }

        // Inicializar los existentes
        aplicarSelect2('.js-example-basic-single');

        let refIndex = 1;
        $('#addRef').click(function() {
            // Creamos el nuevo bloque
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
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>`;

            $('#referencias').append(html);

            // Inicializar SOLO el nuevo select para evitar conflictos
            aplicarSelect2(`#${nuevoID}`);

            refIndex++;
        });

        $(document).on('click', '.remove-ref', function() {
            $(this).closest('div').remove();
        });
        let archivosSeleccionados = []; // Array para trackear archivos

        $('#input-fotos').on('change', function(e) {
            const files = Array.from(e.target.files);

            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const html = `
                    <div class="relative group border rounded-lg p-1 bg-white shadow-sm overflow-hidden" data-name="${file.name}">
                        <img src="${event.target.result}" class="h-24 w-full object-cover rounded-md">
                        <button type="button" class="remove-foto absolute top-0 right-0 bg-red-500 text-white rounded-bl-lg p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="text-[10px] truncate text-gray-500 mt-1 px-1">${file.name}</div>
                    </div>
                `;
                    $('#preview-container').append(html);
                };
                reader.readAsDataURL(file);
            });

            actualizarContador();
        });

        // Eliminar miniatura (Visualmente)
        $(document).on('click', '.remove-foto', function() {
            $(this).closest('div').remove();
            actualizarContador();
            // Nota: En un entorno real sin AJAX, el input file es difícil de limpiar parcialmente.
            // Se recomienda advertir que si se equivoca, limpie y suba de nuevo, o usar AJAX.
        });

        function actualizarContador() {
            const count = $('#preview-container > div').length;
            $('#count-fotos').text(count + ' imágenes listas para subir');
        }
    });
</script>