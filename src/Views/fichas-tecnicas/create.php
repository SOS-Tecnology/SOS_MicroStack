<!-- <?php //ob_start(); 
        ?>  -->

<div class="max-w-7xl mx-auto bg-white shadow-xl rounded-2xl p-6 border border-gray-300">

    <!-- HEADER -->
    <div class="flex items-center gap-4 mb-6">
        <a href="/fichas-tecnicas"
            class="text-sm text-gray-600 hover:text-black border px-3 py-1 rounded">
            ← Volver
        </a>

        <h1 class="text-2xl font-bold text-gray-800">
            Crear Ficha Técnica
        </h1>
    </div>
    <div class="text-xs text-gray-500">
        Fecha creación: <?= date('Y-m-d H:i') ?>
    </div>

    <form method="POST" action="/fichas-tecnicas/store" enctype="multipart/form-data">

        <!-- ========================= -->
        <!-- DATOS GENERALES -->
        <!-- ========================= -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            <div>
                <label class="block text-sm font-semibold">Producto Base</label>
                <select name="id_producto_base" id="producto_base"
                    class="select2 w-full border rounded p-2">
                    <option value="">Seleccione...</option>
                    <?php foreach ($productosBase as $p): ?>
                        <option value="<?= $p['codr'] ?>">
                            <?= $p['codr'] ?> - <?= $p['descr'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold">Cliente</label>
                <select name="id_cliente" class="select2 w-full border rounded p-2">
                    <option value="">Seleccione...</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['codcli'] ?>">
                            <?= $c['nombrecli'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold">Nombre Ficha</label>
                <input type="text" name="nombre_ficha"
                    class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-semibold">Adicionales</label>
                <input type="text" name="adicionales"
                    class="w-full border rounded p-2">
            </div>

        </div>
        <!-- ========================= -->
        <!-- INSUMOS / MATERIA PRIMA -->
        <!-- ========================= -->
        <div class="mb-8 bg-gray-50 p-4 rounded-xl border">
            <h2 class="font-bold text-lg mb-3">Materia Prima / Insumos</h2>

            <table class="w-full text-sm" id="tablaInsumos">
                <thead class="bg-gray-200">
                    <tr>
                        <th>Referencia</th>
                        <th>Unidad</th>
                        <th>Cantidad</th>
                        <th>Color</th>

                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <button type="button"
                onclick="agregarInsumo()"
                class="mt-3 bg-green-600 text-white px-3 py-1 rounded">
                + Agregar Insumo
            </button>
        </div>

        <!-- ========================= -->
        <!-- PROCESOS -->
        <!-- ========================= -->
        <div class="mb-8 bg-gray-50 p-4 rounded-xl border">
            <h2 class="font-bold text-lg mb-3">Procesos de Fabricación</h2>

            <table class="w-full text-sm" id="tablaProcesos">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2">Proceso</th>
                        <th>Tiempo (min)</th>
                        <th>Comentario</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <button type="button"
                onclick="agregarProceso()"
                class="mt-3 bg-blue-600 text-white px-3 py-1 rounded">
                + Agregar Proceso
            </button>
        </div>


        <!-- ========================= -->
        <!-- FOTOS -->
        <!-- ========================= -->
        <div class="mb-6">
            <label class="block font-semibold mb-2">Fotos</label>
            <input type="file" name="fotos[]" multiple class="border p-2 rounded w-full">
            <div id="previewFotos" class="flex flex-wrap gap-2 mt-2"></div>

        </div>

        <!-- SUBMIT -->
        <div class="text-right">
            <button class="bg-green-600 text-white px-6 py-2 rounded-lg shadow">
                Guardar Ficha Técnica
            </button>
        </div>

    </form>
</div>


<!-- ========================= -->
<!-- JS -->
<!-- ========================= -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $('.select2').select2({
        width: '100%'
    });

    let indexProceso = 0;

    function agregarProceso() {

        let fila = `
    <tr>
        <td>
            <select name="procesos[0][proceso_id]" class="w-48 border p-1 rounded">
                <option value="">-- Seleccionar proceso --</option>
                <?php foreach ($procesosCatalogo as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= $p['nombre'] ?></option>
                <?php endforeach; ?>
            </select>

        </td>
        <td>
            <input type="number" step="1" name="procesos[${indexProceso}][tiempo]"
                class="w-full border rounded p-1">
        </td>
        <td>
            <input type="text" name="procesos[${indexProceso}][comentario]"
                class="w-full border rounded p-1">
        </td>
        <td>
            <button type="button" onclick="this.closest('tr').remove()">❌</button>
        </td>
    </tr>
    `;

        $('#tablaProcesos tbody').append(fila);
        $('.select2').select2();
        indexProceso++;
    }


    let indexInsumo = 0;

    function agregarInsumo() {

        let fila = `
    <tr>
        <td class="w-2/4">
            <select name="referencias[${indexInsumo}][codr]" class="select2 w-full">
                <option value="">Seleccione...</option>
                <?php foreach ($referencias as $r): ?>
                    <option 
                        value="<?= $r['codr'] ?>" 
                        data-unid="<?= $r['unid'] ?? '' ?>">
                        <?= $r['codr'] ?> - <?= $r['descr'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>

        <td class="w-24">
            <input type="text" 
                name="referencias[${indexInsumo}][unidad]" 
                class="w-full border rounded p-1 bg-gray-100" readonly>
        </td>

        <td class="w-32">
            <input type="number" step="0.01"
                name="referencias[${indexInsumo}][cantidad]"
                class="w-full border rounded p-1">
        </td>

        <td class="w-32">
            <input type="text"
                name="referencias[${indexInsumo}][color]"
                class="w-full border rounded p-1">
        </td>

        <td class="w-10">
            <button type="button" onclick="this.closest('tr').remove()">❌</button>
        </td>
    </tr>
    `;

        $('#tablaInsumos tbody').append(fila);
        $('.select2').select2();

        // 🔥 detectar cambio y llenar unidad
        $('#tablaInsumos tbody tr:last select').on('change', function() {
            let unidad = $(this).find(':selected').data('unid');
            $(this).closest('tr').find('input[name*="[unidad]"]').val(unidad);
        });

        indexInsumo++;
    }

let archivosFotos = [];

$('input[name="fotos[]"]').on('change', function(e) {

    // agregar nuevos archivos al arreglo
    for (let file of e.target.files) {
        archivosFotos.push(file);
    }

    // limpiar preview
    $('#previewFotos').html('');

    // volver a pintar TODOS los archivos acumulados
    archivosFotos.forEach((file, index) => {

        let reader = new FileReader();

        reader.onload = function(ev) {

            $('#previewFotos').append(`
                <div class="relative">
                    <img src="${ev.target.result}" 
                        class="w-24 h-24 object-cover rounded border">

                    <button type="button"
                        onclick="eliminarFoto(${index})"
                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full px-1 text-xs">
                        ✕
                    </button>
                </div>
            `);
        }

        reader.readAsDataURL(file);
    });

});
function eliminarFoto(index) {
    archivosFotos.splice(index, 1);

    $('#previewFotos').html('');

    archivosFotos.forEach((file, i) => {
        let reader = new FileReader();

        reader.onload = function(ev) {
            $('#previewFotos').append(`
                <div class="relative">
                    <img src="${ev.target.result}" 
                        class="w-24 h-24 object-cover rounded border">

                    <button type="button"
                        onclick="eliminarFoto(${i})"
                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full px-1 text-xs">
                        ✕
                    </button>
                </div>
            `);
        }

        reader.readAsDataURL(file);
    });
}
$('form').on('submit', function(e) {

    let input = document.querySelector('input[name="fotos[]"]');

    let dt = new DataTransfer();

    archivosFotos.forEach(file => dt.items.add(file));

    input.files = dt.files;

});

</script>

<!-- <?php //$content = ob_get_clean(); 
        ?> -->