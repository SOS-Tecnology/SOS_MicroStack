<div class="max-w-7xl mx-auto bg-white shadow-xl rounded-2xl p-6 border border-gray-300">

    <!-- HEADER -->
    <div class="flex items-center gap-4 mb-6">
        <a href="/fichas-tecnicas/show/<?= $ficha['id'] ?>"
            class="text-sm text-gray-600 hover:text-black border px-3 py-1 rounded">
            ← Volver
        </a>

        <h1 class="text-2xl font-bold text-gray-800">
            Editar Ficha Técnica #<?= str_pad($ficha['id'], 5, '0', STR_PAD_LEFT) ?>
        </h1>
    </div>

    <div class="text-xs text-gray-500 mb-4">
        Creado: <?= $ficha['created_at'] ?>
    </div>

    <form method="POST" action="/fichas-tecnicas/update/<?= $ficha['id'] ?>" enctype="multipart/form-data">

        <!-- ========================= -->
        <!-- DATOS GENERALES -->
        <!-- ========================= -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            <div>
                <label class="block text-sm font-semibold">Producto Base</label>
                <select name="id_producto_base" class="select2 w-full border rounded p-2">
                    <?php foreach ($productosBase as $p): ?>
                        <option value="<?= $p['codr'] ?>"
                            <?= $p['codr'] == $ficha['id_producto_base'] ? 'selected' : '' ?>>
                            <?= $p['codr'] ?> - <?= $p['descr'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold">Cliente</label>
                <select name="id_cliente" class="select2 w-full border rounded p-2">
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['codcli'] ?>"
                            <?= $c['codcli'] == $ficha['id_cliente'] ? 'selected' : '' ?>>
                            <?= $c['nombrecli'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold">Nombre Ficha</label>
                <input type="text" name="nombre_ficha"
                    value="<?= htmlspecialchars($ficha['nombre_ficha']) ?>"
                    class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block text-sm font-semibold">Adicionales</label>
                <input type="text" name="adicionales"
                    value="<?= htmlspecialchars($ficha['adicionales']) ?>"
                    class="w-full border rounded p-2">
            </div>

        </div>

        <!-- ========================= -->
        <!-- INSUMOS -->
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
                <tbody>

                    <?php foreach ($detalles as $i => $det): ?>
                        <tr>
                            <td>
                                <select name="referencias[<?= $i ?>][codr]" class="select2 w-full">
                                    <option value="">Seleccione...</option>

                                    <?php foreach ($referencias as $r): ?>
                                        <?php
                                        $codr  = isset($r['codr']) && $r['codr'] !== null ? htmlspecialchars($r['codr']) : '';
                                        $descr = isset($r['descr']) && $r['descr'] !== null ? htmlspecialchars($r['descr']) : '';
                                        $unid  = isset($r['unid']) && $r['unid'] !== null ? htmlspecialchars($r['unid']) : '';
                                        ?>
                                        <option value="<?= $codr ?>"
                                            <?= (isset($det['codr']) && $codr == $det['codr']) ? 'selected' : '' ?>
                                            data-unid="<?= $unid ?>">
                                            <?= $codr ?> - <?= $descr ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>


                            </td>

                            <?php
                            $unid     = isset($det['unid']) && $det['unid'] !== null ? htmlspecialchars($det['unid']) : '';
                            $cantidad = isset($det['cantidad']) && $det['cantidad'] !== null ? htmlspecialchars($det['cantidad']) : '';
                            $color    = isset($det['color']) && $det['color'] !== null ? htmlspecialchars($det['color']) : '';
                            ?>

                            <td>
                                <input type="text"
                                    name="referencias[<?= $i ?>][unid]"
                                    value="<?= $unid ?>"
                                    class="w-full border rounded p-1 bg-gray-100" readonly>

                            </td>

                            <td>
                                <input type="number" step="1"
                                    name="referencias[<?= $i ?>][cantidad]"
                                    value="<?= $cantidad ?>"
                                    class="w-full border rounded p-1">
                            </td>

                            <td>
                                <input type="text"
                                    name="referencias[<?= $i ?>][color]"
                                    value="<?= $color ?>"
                                    class="w-full border rounded p-1">
                            </td>


                            <td>
                                <button type="button" onclick="this.closest('tr').remove()">❌</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>

            <button type="button" onclick="agregarInsumo()"
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
                        <th>Proceso</th>
                        <th class="w-34">Tiempo (Minutos)</th>
                        <th class="w-2/5">Comentarios</th>

                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($procesos as $i => $p): ?>
                        <tr>
                            <td>
                                <select name="procesos[<?= $i ?>][proceso_id]" class="select2 w-full">
                                    <?php foreach ($procesosCatalogo as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"
                                            <?= $cat['id'] == $p['codigo_proceso'] ? 'selected' : '' ?>>
                                            <?= $cat['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td class="w-34 flex items-center gap-2">
                                <input type="number"
                                    name="procesos[<?= $i ?>][tiempo]"
                                    value="<?= $p['tiempo_minutos'] ?>"
                                    class="w-full border rounded p-1">

                                <span class="text-xs text-gray-500">Min</span>
                            </td>

                            <td class="w-2/5">
                                <input type="text" name="procesos[<?= $i ?>][comentario]"
                                    value="<?= $p['comentario'] ?>"
                                    class="w-full border rounded p-1">
                            </td>

                            <td>
                                <button type="button" onclick="this.closest('tr').remove()">❌</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>

            <button type="button" onclick="agregarProceso()"
                class="mt-3 bg-blue-600 text-white px-3 py-1 rounded">
                + Agregar Proceso
            </button>
        </div>

        <!-- ========================= -->
        <!-- FOTOS -->
        <!-- ========================= -->
        <div class="mb-6">
            <h2 class="font-bold text-lg mb-2">Fotos actuales</h2>


            <div class="flex gap-3 flex-wrap">
                <?php foreach ($fotos as $f): ?>
                    <div class="relative foto-item" data-id="<?= $f['id'] ?>">
                        <img src="/<?= $f['ruta_imagen'] ?>"
                            class="w-24 h-24 object-cover border rounded">

                        <label class="absolute top-0 right-0 bg-red-600 text-white text-xs px-1 rounded cursor-pointer btn-eliminar-foto">
                            <input type="checkbox" name="eliminar_fotos[]" value="<?= $f['id'] ?>" class="hidden">
                            ✕
                        </label>
                    </div>
                <?php endforeach; ?>

            </div>



            <label class="block font-semibold mt-4">Agregar más fotos</label>
            <input type="file" name="fotos[]" multiple class="border p-2 rounded w-full">
            <div id="preview-fotos" class="flex flex-wrap gap-3 mt-3"></div>

        </div>

        <div class="text-right">
            <button class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow">
                Actualizar Ficha
            </button>
        </div>

    </form>
</div>

<script>
    let archivosFotos = [];

    $('input[name="fotos[]"]').on('change', function(e) {

        for (let file of e.target.files) {
            archivosFotos.push(file);
        }

        $('#previewFotos').html('');

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
                </div>
            `);
            }

            reader.readAsDataURL(file);
        });
    }

    $('form').on('submit', function() {
        let input = document.querySelector('input[name="fotos[]"]');
        let dt = new DataTransfer();

        archivosFotos.forEach(file => dt.items.add(file));

        input.files = dt.files;
    });

    let indexInsumo = <?= count($detalles) ?>;

    // =============================
    // AGREGAR NUEVO INSUMO
    // =============================
    function agregarInsumo() {

        let fila = `
    <tr class="insumo-row">
        <td>
            <select name="referencias[${indexInsumo}][codr]" class="select2 w-full select-referencia">
                <option value="">Seleccione...</option>

                <?php foreach ($referencias as $r): ?>
                    <?php
                    $codr  = isset($r['codr']) && $r['codr'] !== null ? htmlspecialchars($r['codr']) : '';
                    $descr = isset($r['descr']) && $r['descr'] !== null ? htmlspecialchars($r['descr']) : '';
                    $unid  = isset($r['unid']) && $r['unid'] !== null ? htmlspecialchars($r['unid']) : '';
                    ?>
                    <option value="<?= $codr ?>" data-unid="<?= $unid ?>">
                        <?= $codr ?> - <?= $descr ?>
                    </option>
                <?php endforeach; ?>

            </select>

        </td>

        <td>
            <input type="text"
                name="referencias[${indexInsumo}][unidad]"
                class="input-unid w-full border rounded p-1 bg-gray-100" readonly>

        </td>

        <td>
            <input type="number" step="0.01"
                name="referencias[${indexInsumo}][cantidad]"
                class="w-full border rounded p-1">
        </td>

        <td>
            <input type="text"
                name="referencias[${indexInsumo}][color]"
                class="w-full border rounded p-1">
        </td>

        <td>
            <button type="button" class="btn-remove-insumo text-red-600 font-bold">✖</button>
        </td>
    </tr>
    `;

        $('#tablaInsumos tbody').append(fila);

        // volver a activar select2 SOLO en el nuevo select
        $('#tablaInsumos tbody tr:last .select2').select2({
            width: '100%'
        });

        indexInsumo++;
    }
    // cuando cambia select2
    $('#tablaInsumos').on('select2:select', '.select-referencia', function(e) {

        let data = e.params.data.element;
        let unidad = $(data).data('unid');

        $(this).closest('tr').find('.input-unid').val(unidad || '');

    });

    let indexProceso = <?= count($procesos) ?>;

    function agregarProceso() {

        let fila = `
    <tr>
        <td>
            <select name="procesos[${indexProceso}][proceso_id]" class="select2 w-full">
                <option value="">Seleccione...</option>
                <?php foreach ($procesosCatalogo as $cat): ?>
                    <option value="<?= $cat['id'] ?>">
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>

        <td class="flex items-center gap-2">
            <input type="number"
                name="procesos[${indexProceso}][tiempo]"
                class="w-full border rounded p-1">

            <span class="text-xs text-gray-500">Min</span>
        </td>


        <td>
            <input type="text"
                name="procesos[${indexProceso}][comentario]"
                class="w-full border rounded p-1">
        </td>

        <td>
            <button type="button" onclick="this.closest('tr').remove()">❌</button>
        </td>
    </tr>
    `;

        $('#tablaProcesos tbody').append(fila);

        // activar select2 en el nuevo
        $('#tablaProcesos tbody tr:last .select2').select2({
            width: '100%'
        });

        indexProceso++;
    }
</script>
<script>
    document.querySelectorAll('.btn-eliminar-foto').forEach(btn => {

        btn.addEventListener('click', function(e) {

            // evitar comportamiento raro del label
            //  e.preventDefault();

            const contenedor = this.closest('.foto-item');
            const checkbox = this.querySelector('input[type="checkbox"]');

            // marcar el checkbox para que el backend lo reciba
            checkbox.checked = true;

            // eliminar visualmente la imagen
            contenedor.style.transition = "0.3s";
            contenedor.style.opacity = "0";

            setTimeout(() => {
                contenedor.style.display = "none";
            }, 300);


        });

    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const inputFotos = document.querySelector('input[name="fotos[]"]');
    const preview = document.getElementById('preview-fotos');

    // Array para almacenar archivos seleccionados
    let archivosSeleccionados = [];

    inputFotos.addEventListener('change', function (e) {

        const files = Array.from(e.target.files);

        files.forEach(file => {

            if (!file.type.startsWith('image/')) return;

            // evitar duplicados por nombre+tamaño
            const existe = archivosSeleccionados.some(f => 
                f.name === file.name && f.size === file.size
            );

            if (existe) return;

            archivosSeleccionados.push(file);

            const reader = new FileReader();

            reader.onload = function (e) {
                const div = document.createElement('div');
                div.classList.add('relative', 'w-24');

                div.innerHTML = `
                    <img src="${e.target.result}" 
                         class="w-24 h-24 object-cover rounded border shadow">
                    
                    <button type="button" 
                        class="absolute top-0 right-0 bg-red-500 text-white text-xs px-1 rounded eliminar-preview">
                        ✕
                    </button>

                    <p class="text-xs text-center truncate">${file.name}</p>
                `;

                // botón eliminar preview
                div.querySelector('.eliminar-preview').addEventListener('click', () => {
                    div.remove();
                    archivosSeleccionados = archivosSeleccionados.filter(f => f !== file);
                });

                preview.appendChild(div);
            };

            reader.readAsDataURL(file);
        });

        // limpiar input para permitir seleccionar el mismo archivo otra vez
        inputFotos.value = "";
    });

});
</script>

