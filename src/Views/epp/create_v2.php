<div class="max-w-7xl mx-auto bg-gray-800 text-white rounded-xl p-6">

    <h1 class="text-xl font-bold mb-6">
        Nuevo Envío a Proceso (EPP)
    </h1>

    <form method="POST" action="/epp/store">

        <div class="grid grid-cols-4 gap-4 mb-6">
            <div>
                <label>EPP</label>
                <input type="text"
                    name="documento"
                    value="<?= $nextEpp ?>"
                    readonly
                    class="w-full text-black p-2 bg-gray-200">
            </div>

            <div>
                <label>Cliente</label>
                <input type="text"
                    id="cliente"
                    readonly
                    class="w-full text-black p-2 bg-gray-200">
            </div>
            <div>
                <label class="block mb-1">Fecha</label>
                <input type="date" name="fecha" required
                    class="w-full text-black p-2 rounded">
            </div>

            <div>
                <label class="block mb-1">Fecha Entrega</label>
                <input type="date" name="fechent"
                    class="w-full text-black p-2 rounded">
            </div>

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
                <label class="block mb-1">Proceso</label>

                <select name="proceso" class="w-full text-black select2" required>

                    <option value="">Seleccione</option>

                    <?php foreach ($procesos as $p): ?>

                        <option value="<?= $p['id'] ?>">
                            <?= $p['nombre'] ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div>
                <label class="block mb-1">Responsable</label>

                <select name="responsable" class="w-full text-black select2">

                    <option value="">Seleccione</option>

                    <?php foreach ($personal as $p): ?>

                        <option value="<?= $p['id'] ?>">
                            <?= $p['nombre'] ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div>
                <label class="block mb-1">Satelite</label>

                <select name="satelite" class="w-full text-black select2">

                    <option value="">Seleccione</option>

                    <?php foreach ($satelites as $s): ?>

                        <option value="<?= $s['id'] ?>">
                            <?= $s['nombre'] ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>


        <h2 class="text-lg font-semibold mb-3">
            Material que se entrega
        </h2>
        <button type="button"
            onclick="agregarEntrega()"
            class="bg-green-600 px-3 py-1 rounded text-sm">

            + Agregar Material

        </button>

        <table class="w-full bg-white text-black rounded mb-6" id="tabla_entrega">

            <thead class="bg-gray-200">

                <tr>

                    <th class="p-2">Código</th>
                    <th class="p-2">Descripción</th>
                    <th class="p-2">Cantidad</th>
                    <th class="p-2"></th>

                </tr>

            </thead>

            <tbody></tbody>

        </table>


        <h2 class="text-lg font-semibold mb-3">
            Meta esperada
        </h2>
        <button type="button"
            onclick="agregarMeta()"
            class="bg-green-600 px-3 py-1 rounded text-sm">

            + Agregar

        </button>

        <table class="w-full bg-white text-black rounded mb-6" id="tabla_meta">

            <thead class="bg-gray-200">

                <tr>

                    <th class="p-2">Código</th>
                    <th class="p-2">Descripción</th>
                    <th class="p-2">Cantidad</th>
                    <th class="p-2"></th>

                </tr>

            </thead>

            <tbody></tbody>

        </table>


        <div class="mb-4">

            <label class="block mb-1">Observaciones</label>

            <textarea name="observaciones"
                class="w-full text-black p-2 rounded"></textarea>

        </div>

        <button class="bg-blue-600 px-6 py-2 rounded hover:bg-blue-700">
            Guardar EPP
        </button>

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

                   data.materiales.forEach(function(m){

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
                        class="text-red-600">

                        X

                        </button>
                        </td>

                        </tr>`;
                        });

                if (data.meta && data.meta.length) {

data.meta.forEach(function(m){

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
class="text-red-600">

X

</button>
</td>

</tr>`;
});
                } else {

                    meta = `<tr>
<td colspan="3" class="text-center p-3 text-gray-500">
No hay metas configuradas
</td>
</tr>`;

                }

                $('#tabla_entrega tbody').html(entrega);
                $('#tabla_meta tbody').html(meta);

            });

        });

    });

    function eliminarFila(btn){

$(btn).closest('tr').remove();

}

function agregarEntrega(){

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
function agregarMeta(){

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