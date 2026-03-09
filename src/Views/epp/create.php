<div class="max-w-7xl mx-auto bg-gray-500 text-white rounded-xl p-6">

    <!-- ============================= -->
    <!-- CABECERA DOCUMENTO -->
    <!-- ============================= -->

    <div class="flex justify-between items-center mb-6">

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-xl font-bold">
                Envío a Proceso (EPP) #<?= $nextEpp ?>
            </h1>

            <a href="/epp"
                class="bg-gray-600 px-4 py-2 rounded hover:bg-gray-700">

                ← Volver

            </a>

        </div>

    </div>


    <form method="POST" action="/epp/store">


        <!-- ============================= -->
        <!-- BLOQUE DOCUMENTO -->
        <!-- ============================= -->


        <!-- ============================= -->
        <!-- ENCABEZADO -->
        <!-- ============================= -->

        <div class="grid grid-cols-4 gap-4 mb-6">

            <div>
                <label class="block mb-1">OPR</label>

                <select id="opr_select" name="opr"
                    class="w-full text-black select2" required>

                    <option value="">Seleccione OPR</option>

                    <?php foreach ($oprs as $o): ?>

                        <option value="<?= $o['documento'] ?>">
                            OPR <?= $o['documento'] ?>
                        </option>

                    <?php endforeach; ?>

                </select>
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
                <select name="proceso"
                    class="w-full text-black select2" required>

                    <option value="">Seleccione</option>

                    <?php foreach ($procesos as $p): ?>

                        <option value="<?= $p['id'] ?>">
                            <?= $p['nombre'] ?>
                        </option>

                    <?php endforeach; ?>

                </select>

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

                <select name="responsable"
                    class="w-full text-black select2">

                    <option value="">Seleccione</option>

                    <?php foreach ($personal as $p): ?>

                        <option value="<?= $p['id'] ?>">
                            <?= $p['nombre'] ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>

</div>


<!-- ============================= -->
<!-- MATERIA PRIMA -->
<!-- ============================= -->

<div class="bg-gray-700 p-4 rounded-lg mb-6">

    <div class="flex justify-between mb-3">

        <h2 class="font-semibold">
            Materia Prima Entregada
        </h2>

        <button type="button"
            onclick="agregarEntrega()"
            class="bg-green-600 px-3 py-1 rounded text-sm">

            + Agregar Material

        </button>

    </div>


    <table class="w-full bg-white text-black rounded" id="tabla_entrega">

        <thead class="bg-gray-200">

            <tr>

                <th class="p-2 text-left">Código</th>
                <th class="p-2 text-left">Descripción</th>
                <th class="p-2 text-center">Cantidad</th>
                <th class="p-2"></th>

            </tr>

        </thead>

        <tbody></tbody>

    </table>

</div>


<!-- ============================= -->
<!-- METAS -->
<!-- ============================= -->

<div class="bg-gray-700 p-4 rounded-lg mb-6">

    <div class="flex justify-between mb-3">

        <h2 class="font-semibold">
            Metas de Producción
        </h2>

        <button type="button"
            onclick="agregarMeta()"
            class="bg-green-600 px-3 py-1 rounded text-sm">

            + Agregar

        </button>

    </div>


    <table class="w-full bg-white text-black rounded mb-3" id="tabla_meta">

        <thead class="bg-gray-200">

            <tr>

                <th class="p-2">Código</th>
                <th class="p-2">Descripción</th>
                <th class="p-2">Cantidad</th>
                <th></th>

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

<div class="bg-gray-700 p-4 rounded-lg mb-6">

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

    <a href="/epp"
        class="bg-gray-600 px-5 py-2 rounded hover:bg-gray-700">

        Volver

    </a>

    <button class="bg-blue-600 px-6 py-2 rounded hover:bg-blue-700">

        Guardar EPP

    </button>

</div>


</form>

</div>

<script>
    $('#cliente').val($data.cliente);

    $(document).ready(function() {

        $('.select2').select2({
            width: '100%'
        });

        $('#opr_select').on('change', function() {

            let opr = $(this).val();

            $('#tabla_entrega tbody').html('');
            $('#tabla_meta tbody').html('');

            if (!opr) return;

            $.get('/epp/opr/' + opr, function(data) {

                let entrega = '';
                let meta = '';

                if (data.materiales && data.materiales.length) {
                    data.materiales.forEach(function(m) {
                        entrega += `
                        <tr>
                            <td>
                                <input type="text"
                                name="entrega_codr[]"
                                value="${m.codr}"
                                class="border p-1 w-full">
                            </td>
                            <td>
                                <input type="text"
                                value="${m.descr}"
                                class="border p-1 w-full">
                            </td>
                            <td>
                                <input type="number"
                                name="entrega_cant[]"
                                value="${m.cantidad ?? 0}"
                                class="border p-1 w-24">
                            </td>
                            <td>
                                <button type="button"
                                onclick="eliminarFila(this)"
                                class="text-red-600">X</button>
                            </td>
                        </tr>`;
                    });
                }

                if (data.meta && data.meta.length) {
                    data.meta.forEach(function(m) {
                        meta += `
                        <tr>
                            <td>
                                <input type="text"
                                name="meta_codr[]"
                                value="${m.codr}"
                                class="border p-1 w-full">
                            </td>
                            <td>
                                <input type="text"
                                value="${m.descr}"
                                class="border p-1 w-full">
                            </td>
                            <td>
                                <input type="number"
                                name="meta_cant[]"
                                value="${m.cantidad ?? 0}"
                                class="border p-1 w-24">
                            </td>
                            <td>
                                <button type="button"
                                onclick="eliminarFila(this)"
                                class="text-red-600">X</button>
                            </td>
                        </tr>`;
                    });
                } else {
                    meta = `
                    <tr>
                        <td colspan="3" class="text-center p-3 text-gray-500">
                            No hay metas configuradas
                        </td>
                    </tr>`;
                }

                $('#tabla_entrega tbody').html(entrega);
                $('#tabla_meta tbody').html(meta);

            }); // <-- cierre correcto del $.get

        }); // <-- cierre correcto del on('change')

        function eliminarFila(btn) {
            $(btn).closest('tr').remove();
        }

        function agregarEntrega() {
            let fila = `
            <tr>
                <td><input type="text" name="entrega_codr[]" class="border p-1 w-full"></td>
                <td><input type="text" class="border p-1 w-full"></td>
                <td><input type="number" name="entrega_cant[]" value="0" class="border p-1 w-24"></td>
                <td><button type="button" onclick="eliminarFila(this)" class="text-red-600">X</button></td>
            </tr>`;
            $('#tabla_entrega tbody').append(fila);
        }

        function agregarMeta() {
            let fila = `
            <tr>
                <td><input type="text" name="meta_codr[]" class="border p-1 w-full"></td>
                <td><input type="text" class="border p-1 w-full"></td>
                <td><input type="number" name="meta_cant[]" value="0" class="border p-1 w-24"></td>
                <td><button type="button" onclick="eliminarFila(this)" class="text-red-600">X</button></td>
            </tr>`;
            $('#tabla_meta tbody').append(fila);
        }

        function calcularTotalMeta() {
            let total = 0;
            $('input[name="meta_cant[]"]').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total_meta').text(total);
        }

        $(document).on('input', 'input[name="meta_cant[]"]', calcularTotalMeta);

    });
</script>

<script>
    $(document).ready(function() {

        $('.select2').select2({
            width: '100%'
        });

        $('#opr_select').on('change', function() {

            let opr = $(this).val();

            $('#tabla_entrega tbody').html('');
            $('#tabla_meta tbody').html('');

            if (!opr) return;

            $.get('/epp/opr/' + opr, function(data) {

                $('#cliente').val(data.cliente ?? '');

                let entrega = '';
                let meta = '';

                if (data.materiales) {

                    data.materiales.forEach(function(m) {

                        entrega += `
<tr>

<td>
<input type="text"
name="entrega_codr[]"
value="${m.codr}"
class="border p-1 w-full">
</td>

<td>${m.descr}</td>

<td>
<input type="number"
name="entrega_cant[]"
value="${m.cantidad ?? 0}"
class="border p-1 w-24">
</td>

<td>
<button type="button"
onclick="eliminarFila(this)"
class="text-red-600">X</button>
</td>

</tr>`;
                    });

                }

                if (data.meta) {

                    data.meta.forEach(function(m) {

                        meta += `
<tr>

<td>
<input type="text"
name="meta_codr[]"
value="${m.codr}"
class="border p-1 w-full">
</td>

<td>${m.descr}</td>

<td>
<input type="number"
name="meta_cant[]"
value="${m.cantidad ?? 0}"
class="border p-1 w-24">
</td>

<td>
<button type="button"
onclick="eliminarFila(this)"
class="text-red-600">X</button>
</td>

</tr>`;
                    });

                }

                $('#tabla_entrega tbody').html(entrega);
                $('#tabla_meta tbody').html(meta);

            });

        });

    });

    function eliminarFila(btn) {

        $(btn).closest('tr').remove();

    }


    function agregarEntrega() {

        let fila = `
<tr>

<td>
<input type="text"
name="entrega_codr[]"
class="border p-1 w-full">
</td>

<td>
<input type="text"
class="border p-1 w-full">
</td>

<td>
<input type="number"
name="entrega_cant[]"
value="0"
class="border p-1 w-24">
</td>

<td>
<button type="button"
onclick="eliminarFila(this)"
class="text-red-600">X</button>
</td>

</tr>`;

        $('#tabla_entrega tbody').append(fila);

    }


    function agregarMeta() {

        let fila = `
<tr>

<td>
<input type="text"
name="meta_codr[]"
class="border p-1 w-full">
</td>

<td>
<input type="text"
class="border p-1 w-full">
</td>

<td>
<input type="number"
name="meta_cant[]"
value="0"
class="border p-1 w-24">
</td>

<td>
<button type="button"
onclick="eliminarFila(this)"
class="text-red-600">X</button>
</td>

</tr>`;

        $('#tabla_meta tbody').append(fila);

    }
</script>