<form action="/satelites/update/<?= $s['id'] ?>" method="POST" class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-800"><?= isset($s) ? 'Editar' : 'Nuevo' ?> Satélite</h2>
            <p class="text-sm text-gray-500">Gestión de taller externo</p>
        </div>
        <a href="/satelites" class="flex items-center text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
            </svg>
            Volver al listado
        </a>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border shadow-sm space-y-4">
            <h3 class="font-bold text-gray-800 border-b pb-2">Editar Información</h3>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Proveedor</label>
                <select name="id_proveedor" id="id_proveedor" class="w-full select2" required>
                    <?php foreach ($proveedores as $p): ?>
                        <option value="<?= $p['codp'] ?>"
                            <?= $s['id_proveedor'] == $p['codp'] ? 'selected' : '' ?>
                            data-dir="<?= htmlspecialchars($p['direcc']) ?>"
                            data-ciud="<?= htmlspecialchars($p['ciud']) ?>"
                            data-cont="<?= htmlspecialchars($p['contacto']) ?>"
                            data-tel="<?= htmlspecialchars($p['tels']) ?>">
                            <?= $p['nombre'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <select name="tipo" class="w-full border rounded-lg p-2 text-sm bg-gray-50">
                    <option value="Corte" <?= $s['tipo'] == 'Corte' ? 'selected' : '' ?>>Corte</option>
                    <option value="Confeccion" <?= $s['tipo'] == 'Confeccion' ? 'selected' : '' ?>>Confección</option>
                </select>
                <input type="text" name="especialidad" value="<?= $s['especialidad'] ?>" class="w-full border rounded-lg p-2 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <input type="number" name="capacidad_produccion" value="<?= $s['capacidad_produccion'] ?>" class="w-full border rounded-lg p-2 text-sm">
                <input type="number" step="0.1" name="calificacion" value="<?= $s['calificacion'] ?>" class="w-full border rounded-lg p-2 text-sm text-amber-600 font-bold">
            </div>

            <textarea name="comentarios" rows="3" class="w-full border rounded-lg p-2 text-sm"><?= $s['comentarios'] ?></textarea>
        </div>

        <div class="lg:col-span-1">
            <div id="infoProveedor" class="bg-gray-800 text-white rounded-xl p-6 shadow-lg">
                <h3 class="text-blue-400 font-bold text-sm mb-4">Ubicación y Contacto</h3>
                <div class="space-y-4">
                    <p id="p-direcc" class="text-sm">--</p>
                    <p id="p-ciud" class="text-sm font-bold">--</p>
                    <p id="p-cont" class="text-sm border-t border-gray-700 pt-2">--</p>
                </div>
                <button type="submit" class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-bold">Actualizar Datos</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.select2').select2();

        function updateBox() {
            const sel = $('#id_proveedor').find(':selected');
            $('#p-direcc').text(sel.data('dir'));
            $('#p-ciud').text(sel.data('ciud'));
            $('#p-cont').text(sel.data('cont') + ' - ' + sel.data('tel'));
        }
        $('#id_proveedor').on('change', updateBox);
        updateBox(); // Ejecutar al cargar
    });
</script>