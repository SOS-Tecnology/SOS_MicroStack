<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm">
        <p class="text-sm font-bold"><?= $_SESSION['error'];
                                        unset($_SESSION['error']); ?></p>
    </div>
<?php endif; ?>

<form action="/satelites/store" method="POST" class="max-w-5xl mx-auto">
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
            <h3 class="font-bold text-gray-800 border-b pb-2 mb-4">Información del Satélite</h3>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Seleccionar Proveedor</label>
                <select name="id_proveedor" id="id_proveedor" class="w-full select2" required>
                    <option value="">Buscar por nombre o código...</option>
                    <?php foreach ($proveedores as $p): ?>
                        <option value="<?= $p['codp'] ?>"
                            data-dir="<?= htmlspecialchars($p['direcc']) ?>"
                            data-ciud="<?= htmlspecialchars($p['ciud']) ?>"
                            data-cont="<?= htmlspecialchars($p['contacto']) ?>"
                            data-tel="<?= htmlspecialchars($p['tels']) ?>">
                            <?= $p['nombre'] ?> (<?= $p['codp'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Tipo de Servicio</label>
                    <select name="tipo" class="w-full border rounded-lg p-2 text-sm bg-gray-50">
                        <option value="Corte">Corte</option>
                        <option value="Confeccion">Confección</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Especialidad</label>
                    <input type="text" name="especialidad" placeholder="Ej: Camisería" class="w-full border rounded-lg p-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Capacidad Mensual</label>
                    <input type="number" name="capacidad_produccion" class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Calificación Inicial</label>
                    <input type="number" step="0.1" name="calificacion" max="5" value="5.0" class="w-full border rounded-lg p-2 text-sm text-amber-600 font-bold">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Observaciones</label>
                <textarea name="comentarios" rows="3" class="w-full border rounded-lg p-2 text-sm"></textarea>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div id="infoProveedor" class="bg-blue-50 border border-blue-200 rounded-xl p-6 sticky top-6 transition-all opacity-50">
                <h3 class="text-blue-800 font-bold text-sm mb-4 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Datos del Proveedor
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-blue-400 uppercase font-bold">Dirección</p>
                        <p id="p-direcc" class="text-sm text-gray-700 font-medium">---</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-blue-400 uppercase font-bold">Ciudad</p>
                        <p id="p-ciud" class="text-sm text-gray-700 font-medium">---</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-blue-400 uppercase font-bold">Contacto Principal</p>
                        <p id="p-cont" class="text-sm text-gray-700 font-medium">---</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-blue-400 uppercase font-bold">Teléfonos</p>
                        <p id="p-tel" class="text-sm text-gray-700 font-medium">---</p>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full mt-4 bg-blue-600 text-white py-3 rounded-xl font-bold shadow-lg hover:bg-blue-700 transition">
                Registrar Satélite
            </button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.select2').select2();

        $('#id_proveedor').on('change', function() {
            const selected = $(this).find(':selected');
            const infoBox = $('#infoProveedor');

            if ($(this).val()) {
                $('#p-direcc').text(selected.data('dir'));
                $('#p-ciud').text(selected.data('ciud'));
                $('#p-cont').text(selected.data('cont'));
                $('#p-tel').text(selected.data('tel'));
                infoBox.removeClass('opacity-50').addClass('opacity-100 shadow-md');
            } else {
                infoBox.addClass('opacity-50').removeClass('shadow-md');
                $('.text-gray-700').text('---');
            }
        });
    });
</script>