<?php if (!empty($_SESSION['error'])): ?>
    <div class="bg-red-500 text-white p-3 rounded mb-4">
        <?= $_SESSION['error'];
        unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="max-w-7xl mx-auto bg-gray-500 text-white rounded-xl p-6">

    <!-- ============================= -->
    <!-- CABECERA DOCUMENTO -->
    <!-- ============================= -->

    <div class="flex justify-between items-center mb-6">

        <a href="/orden-produccion/avance"
            class="bg-gray-600 px-4 py-2 rounded hover:bg-gray-400">
            ← Volver
        </a>
        <h2 class="text-2xl font-semibold text-gray-700">
            Envío a Proceso (EPP) #<?= $nextEpp ?>
        </h2>

    </div>


    <form method="POST" action="/epp/store">

        <!-- ============================= -->
        <!-- BLOQUE DOCUMENTO -->
        <!-- ============================= -->

        <input type="hidden" name="documento" value="<?= $nextEpp ?>">
        <input type="hidden" name="detalle_json" id="detalle_json">
        <input type="hidden" name="codcp" value="">
        <!-- ============================= -->
        <!-- ENCABEZADO -->
        <!-- ============================= -->

        <div class="grid grid-cols-4 gap-4 mb-6">

            <div>
                <label class="block mb-1">OPR</label>
                <select id="opr_select" name="opr"
                    class="w-full text-black select2"
                    <?= isset($opr_param) ? 'disabled' : '' ?> required>

                    <option value="">Seleccione OPR</option>

                    <?php foreach ($oprs as $o): ?>

                        <option value="<?= $o['documento'] ?>"
                            <?= (isset($opr_param) && $opr_param == $o['documento']) ? 'selected' : '' ?>>

                            OPR <?= $o['documento'] ?>

                        </option>

                    <?php endforeach; ?>

                </select>
                <?php if (isset($opr_param)): ?>

                    <input type="hidden" name="opr" value="<?= $opr_param ?>">

                <?php endif; ?>
            </div>


            <div>
                <label>Cliente</label>
                <input type="text"
                    id="cliente"
                    readonly
                    class="w-full text-black p-2 bg-gray-200">
            </div>


            <div>
                <label>Fecha</label>
                <input type="date" name="fecha"
                    class="w-full text-black p-2">
            </div>


            <div>
                <label>Fecha Entrega</label>
                <input type="date" name="fechent"
                    class="w-full text-black p-2">
            </div>

            <div>
                <label>Proceso</label>

                <?php if ($proceso_data): ?>
                    <input type="text"
                        value="<?= htmlspecialchars($proceso_data['nombre']) ?>"
                        readonly
                        class="w-full text-black p-2 bg-gray-200">
                    <?php if (!empty($comentario_proceso)): ?>
                        <p class="text-xs text-yellow-300 mt-1">
                            📌 <?= htmlspecialchars($comentario_proceso) ?>
                        </p>
                    <?php endif; ?>
                    <input type="hidden" name="proceso" value="<?= $proceso_param ?>">
                    <input type="hidden" name="entrada_tipo" value="<?= $proceso_data['entrada_tipo'] ?>">
                    <input type="hidden" name="salida_tipo" value="<?= $proceso_data['salida_tipo'] ?>">
                <?php else: ?>
                    <select name="proceso" class="w-full text-black select2" required>
                        <option value="">Seleccione</option>
                        <?php foreach ($procesos as $p): ?>
                            <option value="<?= $p['id'] ?>"
                                data-entrada="<?= $p['entrada_tipo'] ?>">
                                <?= $p['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <div>

                <label class="block text-sm">Satélite</label>

                <select name="satelite"
                    class="w-full text-black select2">

                    <option value="">Seleccione</option>

                    <?php foreach ($satelites as $s): ?>

                        <option value="<?= $s['id'] ?>">
                            <?= $s['nombre'] ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div>

                <label class="block text-sm">Responsable</label>

                <select name="personal"
                    class="w-full text-black select2">

                    <option value="">Seleccione</option>

                    <?php foreach ($personal as $p): ?>

                        <option value="<?= $p['id'] ?>">
                            <?= $p['nombres'] ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>

</div>


<!-- ============================= -->
<!-- MATERIA PRIMA -->
<!-- ============================= -->

<div id="bloque_mp" class="bg-gray-400 p-4 rounded-lg mb-6">

    <div class="flex justify-between mb-3">

        <h2 class="text-lg font-semibold mb-3">
            Entrada al proceso
        </h2>

        <button type="button"
            onclick="agregarEntrega()"
            class="bg-green-600 px-3 py-1 rounded text-sm">

            + Agregar Material

        </button>


    </div>


    <table class="w-full bg-white text-black rounded" id="tabla_entrega">

        <thead class="bg-gray-200 text-xs">
            <tr>
                <th class="px-2 py-2 text-left">Código</th>
                <th class="px-2 py-2 text-left">Descripción</th>
                <th class="px-2 py-2 text-center">Cant. FT</th>
                <th class="px-2 py-2 text-center">Cant. a enviar</th>
                <th class="px-2 py-2"></th>
            </tr>
        </thead>

        <tbody></tbody>

    </table>

</div>


<!-- ============================= -->
<!-- METAS -->
<!-- ============================= -->

<div class="bg-gray-400 p-4 rounded-lg mb-6">

    <div class="flex justify-between mb-3">

        <h2 class="text-lg font-semibold mb-3">
            Metas de Producción
        </h2>

        <button type="button"
            onclick="agregarMeta()"
            class="bg-green-600 px-3 py-1 rounded text-sm">

            + Agregar

        </button>

    </div>


    <table class="w-full bg-white text-black rounded mb-3" id="tabla_meta">
        <thead class="bg-gray-200 text-xs">
            <tr>
                <th class="px-2 py-2 text-left">Código</th>
                <th class="px-2 py-2 text-left">Descripción</th>
                <th class="px-2 py-2 text-center">Talla</th>
                <th class="px-2 py-2 text-center">Color</th>
                <th class="px-2 py-2 text-center">Cant. OPR</th>
                <th class="px-2 py-2 text-center">Cant. a enviar</th>
                <th class="px-2 py-2"></th>
            </tr>
        </thead>

        <tbody></tbody>

    </table>


    <!-- SUBTOTAL -->

    <div class="text-right font-semibold">

        Total Metas:
        <span id="total_meta">0</span>

    </div>

</div>


<!-- ============================= -->
<!-- OBSERVACIONES -->
<!-- ============================= -->

<div class="bg-gray-400 p-4 rounded-lg mb-6">

    <label class="block mb-2 font-semibold">
        Observaciones
    </label>

    <textarea name="observaciones"
        class="w-full text-black p-2 rounded"></textarea>

</div>


<!-- ============================= -->
<!-- BOTONES -->
<!-- ============================= -->

<div class="flex justify-end gap-3">

    <a href="/orden-produccion/avance"
        class="bg-gray-600 px-5 py-2 rounded hover:bg-gray-400">

        Volver

    </a>

    <button class="bg-blue-600 px-6 py-2 rounded hover:bg-blue-400">

        Guardar EPP

    </button>

</div>


</form>

</div>

<script>
    $(document).ready(function() {

        $('.select2').select2({
            width: '100%'
        });

        // ── Mostrar / ocultar bloque MP ──
        function aplicarTipoEntrada(entrada_tipo) {
            if (entrada_tipo === 'MP') {
                $('#bloque_mp').show();
            } else {
                $('#bloque_mp').hide();
                $('#tabla_entrega tbody').html('');
            }
        }

        // ── Llenar tabla MP ──
        function cargarTablaMP(materiales) {
            let html = '';
            materiales.forEach(function(m) {
                html += `<tr class="border-b">
            <td class="px-2 py-1 text-sm">${m.codr.trim()}</td>
            <td class="px-2 py-1 text-sm">${m.descr.trim()}</td>
            <td class="px-2 py-1 text-center text-sm text-gray-500">${parseFloat(m.cantidad).toFixed(2)}</td>
            <td class="px-2 py-1">
                <input type="hidden" name="entrega_codr[]" value="${m.codr}">
                <input type="number" name="entrega_cant[]"
                    value="${parseFloat(m.cantidad).toFixed(2)}"
                    min="0"
                    class="border rounded px-1 py-0.5 w-24 text-center text-sm">
            </td>
            <td class="px-2 py-1 text-center">
                <button type="button" onclick="eliminarFila(this)" class="text-red-500 text-sm">✕</button>
            </td>
        </tr>`;
            });
            if (!html) html = `<tr><td colspan="5" class="text-center p-3 text-gray-500 text-sm">Sin materias primas en FT</td></tr>`;
            $('#tabla_entrega tbody').html(html);
        }

        function cargarTablaMeta(meta) {
            let html = '';
            meta.forEach(function(m) {
                html += `<tr class="border-b">
            <td class="px-2 py-1 text-sm">${m.codr.trim()}</td>
            <td class="px-2 py-1 text-sm">${m.descr.trim()}</td>
            <td class="px-2 py-1 text-center text-sm">${m.codtalla ?? ''}</td>
            <td class="px-2 py-1 text-center text-sm">${m.codcolor ?? ''}</td>
            <td class="px-2 py-1 text-center text-sm text-gray-500">${parseFloat(m.cantidad).toFixed(0)}</td>
            <td class="px-2 py-1">
                <input type="hidden" name="meta_codr[]"  value="${m.codr}">
                <input type="hidden" name="meta_talla[]" value="${m.codtalla ?? ''}">
                <input type="hidden" name="meta_color[]" value="${m.codcolor ?? ''}">
                <input type="number" name="meta_cant[]"
                    value="${m.cantidad ?? 0}"
                    min="0"
                    class="border rounded px-1 py-0.5 w-24 text-center text-sm"
                    oninput="calcularTotalMeta()">
            </td>
            <td class="px-2 py-1 text-center">
                <button type="button" onclick="eliminarFila(this)" class="text-red-500 text-sm">✕</button>
            </td>
        </tr>`;
            });
            if (!html) html = `<tr><td colspan="7" class="text-center p-3 text-gray-500 text-sm">Sin metas configuradas</td></tr>`;
            $('#tabla_meta tbody').html(html);
            calcularTotalMeta();
        } // ── Cargar datos de la OPR vía AJAX ──
        function cargarOpr(opr, entrada_tipo_forzada) {
            if (!opr) return;
            $.get('/epp/opr/' + opr, function(data) {
                $('#cliente').val(data.cliente ?? '');
                let entrada_tipo = entrada_tipo_forzada ??
                    $('select[name="proceso"]').find(':selected').data('entrada') ??
                    '';
                aplicarTipoEntrada(entrada_tipo);
                cargarTablaMP(data.materiales ?? []);
                cargarTablaMeta(data.meta ?? []);
            });
        }

        // ── Proceso libre (sin parámetro de URL) ──
        <?php if (!$proceso_data): ?>
            $('select[name="proceso"]').on('change', function() {
                let tipo = $(this).find(':selected').data('entrada');
                aplicarTipoEntrada(tipo);
                let opr = $('#opr_select').val();
                if (opr) cargarOpr(opr, null);
            });
            $('#opr_select').on('change', function() {
                cargarOpr($(this).val(), null);
            });
            <?php if (isset($opr_param)): ?>
                cargarOpr('<?= $opr_param ?>', null);
            <?php endif; ?>

        <?php else: ?>
            // ── Proceso fijo (viene de avance OPR) ──
            cargarOpr('<?= $opr_param ?>', '<?= $proceso_data['entrada_tipo'] ?>');
        <?php endif; ?>

        // ── Submit: armar detalle_json ──
        $('form').on('submit', function(e) {
            let fecha = $('input[name="fecha"]').val();
            let fechent = $('input[name="fechent"]').val();
            let satelite = $('select[name="satelite"]').val();

            if (!fecha || !fechent) {
                alert('Debe ingresar las fechas');
                e.preventDefault();
                return;
            }
            if (fechent < fecha) {
                alert('La fecha de entrega no puede ser menor a la fecha');
                e.preventDefault();
                return;
            }
            if (!satelite) {
                alert('Debe seleccionar un satélite');
                e.preventDefault();
                return;
            }

            let detalle = [];

            $('input[name="entrega_codr[]"]').each(function(i) {
                let codr = $(this).val();
                let cantidad = $('input[name="entrega_cant[]"]').eq(i).val();
                if (codr && parseFloat(cantidad) > 0) {
                    detalle.push({
                        codr,
                        cantidad,
                        tipo_registro: 'MP'
                    });
                }
            });

            $('input[name="meta_codr[]"]').each(function(i) {
                let codr = $(this).val();
                let talla = $('input[name="meta_talla[]"]').eq(i).val();
                let color = $('input[name="meta_color[]"]').eq(i).val();
                let cantidad = $('input[name="meta_cant[]"]').eq(i).val();
                if (codr && parseFloat(cantidad) > 0) {
                    detalle.push({
                        codr,
                        codtalla: talla,
                        codcolor: color,
                        cantidad,
                        tipo_registro: 'META'
                    });
                }
            });

            $('#detalle_json').val(JSON.stringify(detalle));
        });

    }); // fin document.ready

    function eliminarFila(btn) {
        $(btn).closest('tr').remove();
        calcularTotalMeta();
    }

    function calcularTotalMeta() {
        let total = 0;
        $('input[name="meta_cant[]"]').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total_meta').text(total);
    }

    function agregarEntrega() {
        $('#tabla_entrega tbody').append(`
        <tr>
            <td class="p-2"><input type="text" name="entrega_codr[]" class="border p-1 w-full"></td>
            <td class="p-2"><input type="text" class="border p-1 w-full" placeholder="Descripción"></td>
            <td class="p-2"><input type="number" name="entrega_cant[]" value="0" class="border p-1 w-24 text-center"></td>
            <td class="p-2 text-center"><button type="button" onclick="eliminarFila(this)" class="text-red-600">✕</button></td>
        </tr>`);
    }

    function agregarMeta() {
        $('#tabla_meta tbody').append(`
        <tr>
            <td class="p-2"><input type="text" name="meta_codr[]" class="border p-1 w-full"></td>
            <td class="p-2"><input type="text" class="border p-1 w-full" placeholder="Descripción"></td>
            <td class="p-2"><input type="text" name="meta_talla[]" class="border p-1 w-20 text-center"></td>
            <td class="p-2"><input type="text" name="meta_color[]" class="border p-1 w-24 text-center"></td>
            <td class="p-2"><input type="number" name="meta_cant[]" value="0" class="border p-1 w-24 text-center" oninput="calcularTotalMeta()"></td>
            <td class="p-2 text-center"><button type="button" onclick="eliminarFila(this)" class="text-red-600">✕</button></td>
        </tr>`);
    }
</script>