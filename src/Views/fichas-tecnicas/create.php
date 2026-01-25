<?php
$title = "Nueva Ficha Técnica";
ob_start();
?>

<?php if (!empty($_SESSION['errors'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong>¡Error!</strong>
        <ul class="list-disc list-inside">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Nueva Ficha Técnica</h2>
        <a href="/fichas-tecnicas" class="px-4 py-2 border rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Volver al Listado
        </a>
    </div>

    <div class="bg-white shadow sm:rounded-lg p-6">
        <form action="/fichas-tecnicas/store" method="POST" enctype="multipart/form-data">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <!-- Producto Base -->
                    <label class="block text-xs font-semibold mb-1">Producto Base:</label>
                    <select name="id_producto_base" id="id_producto_base" class="js-example-basic-single w-full" required>
                        <option value="">-- Seleccione un producto --</option>
                        <?php foreach ($productosBase as $producto): ?>
                            <option value="<?= $producto['codr'] ?>"><?= $producto['descr'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Cliente -->
                    <label class="block text-xs font-semibold mb-1 mt-3">Cliente:</label>
                    <select name="id_cliente" id="id_cliente" class="js-example-basic-single w-full" required>
                        <option value="">-- Seleccione un cliente --</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= $cliente['codcli'] ?>"><?= $cliente['nombrecli'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Nombre Ficha -->
                    <label class="block text-xs font-semibold mb-1 mt-3">Nombre de la Ficha:</label>
                    <input type="text" name="nombre_ficha" class="border rounded w-full p-2" required>

                    <!-- Adicionales -->
                    <label class="block text-xs font-semibold mb-1 mt-3">Adicionales:</label>
                    <textarea name="adicionales" rows="3" class="border rounded w-full p-2"></textarea>
                </div>

                <div>
                    <!-- Tiempos -->
                    <label class="block text-xs font-semibold mb-1">Tiempo de Corte (min):</label>
                    <input type="number" step="0.01" name="tiempo_corte" class="border rounded w-full p-2">

                    <label class="block text-xs font-semibold mb-1 mt-3">Tiempo de Confección (min):</label>
                    <input type="number" step="0.01" name="tiempo_confeccion" class="border rounded w-full p-2">

                    <label class="block text-xs font-semibold mb-1 mt-3">Tiempo de Alistamiento (min):</label>
                    <input type="number" step="0.01" name="tiempo_alistamiento" class="border rounded w-full p-2">

                    <label class="block text-xs font-semibold mb-1 mt-3">Tiempo de Remate (min):</label>
                    <input type="number" step="0.01" name="tiempo_remate" class="border rounded w-full p-2">
                </div>
            </div>

            <!-- Fotos -->
            <div class="mt-6 p-4 bg-gray-50 rounded">
                <h3 class="text-lg font-semibold mb-3">Fotos del Producto</h3>
                <input type="file" name="fotos[]" multiple accept="image/*" class="border rounded w-full p-2">
                <p class="text-xs text-gray-500 mt-1">Puedes seleccionar varias imágenes a la vez.</p>
            </div>

            <!-- Referencias -->
            <div class="mt-6 p-4 bg-gray-50 rounded">
                <h3 class="text-lg font-semibold mb-3">Referencias (inrefinv)</h3>
                <div id="referencias">
                    <div class="flex gap-2 mb-2">
                        <select name="referencias[0][codr]" class="js-example-basic-single w-1/3">
                            <option value="">-- Seleccione referencia --</option>
                            <?php foreach ($referencias as $ref): ?>
                                <option value="<?= $ref['codr'] ?>"><?= $ref['descr'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="referencias[0][cantidad]" placeholder="Cantidad" class="border p-2 w-1/4">
                        <input type="text" name="referencias[0][talla]" placeholder="Talla" class="border p-2 w-1/4">
                        <input type="text" name="referencias[0][color]" placeholder="Color" class="border p-2 w-1/4">
                    </div>
                </div>
                <button type="button" id="addRef" class="mt-2 px-3 py-1 bg-green-500 text-white rounded">+ Agregar referencia</button>
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Crear Ficha Técnica</button>
                <a href="/fichas-tecnicas" class="bg-gray-300 text-gray-800 px-4 py-2 rounded">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Inicializar todos los selects al cargar la página
        $('.js-example-basic-single').select2({
            placeholder: "Buscar...",
            allowClear: true
        });

        // Manejar la adición dinámica de referencias
        let refIndex = 1;
        $('#addRef').click(function() {
            $('#referencias').append(`
                <div class="flex gap-2 mb-2">
                    <select name="referencias[${refIndex}][codr]" class="js-example-basic-single w-1/3">
                        <option value="">-- Seleccione referencia --</option>
                        <?php foreach ($referencias as $ref): ?>
                            <option value="<?= $ref['codr'] ?>"><?= $ref['descr'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="referencias[${refIndex}][cantidad]" placeholder="Cantidad" class="border p-2 w-1/4">
                    <input type="text" name="referencias[${refIndex}][talla]" placeholder="Talla" class="border p-2 w-1/4">
                    <input type="text" name="referencias[${refIndex}][color]" placeholder="Color" class="border p-2 w-1/4">
                </div>
            `);

            // Reaplicar Select2 a los nuevos selects
            $('.js-example-basic-single').select2({
                placeholder: "Buscar...",
                allowClear: true,
                width: '100%'
            });

            refIndex++;
        });
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/dashboard.php';
?>