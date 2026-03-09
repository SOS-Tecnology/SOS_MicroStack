<div class="max-w-7xl mx-auto bg-gray-800 text-gray-100 rounded-xl shadow-lg p-6">

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="text-sm text-gray-300 hover:text-white">
                ← Volver
            </a>

            <h1 class="text-2xl font-bold tracking-wide">
                Nueva EPP
            </h1>
        </div>

        <span class="px-3 py-1 text-xs rounded bg-blue-600">
            BORRADOR
        </span>
    </div>
    <div class="bg-white text-gray-800 rounded-lg shadow p-5 mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">

        <!-- FECHA -->
        <div>
            <label class="text-sm text-gray-500">Fecha</label>
            <input type="date" id="fecha"
                class="w-full border rounded px-3 py-2">
        </div>

        <!-- FECHA ENTREGA -->
        <div>
            <label class="text-sm text-gray-500">Fecha Entrega</label>
            <input type="date" id="fechent"
                class="w-full border rounded px-3 py-2">
        </div>

        <!-- SELECCIONAR OPR -->
        <div>
            <label class="text-sm text-gray-500">Orden Producción</label>
            <select id="opr_id" class="w-full border rounded px-3 py-2">
                <option value="">Seleccione OPR</option>
                <?php foreach ($oprs as $op): ?>
                    <option value="<?= $op['documento'] ?>">
                        <?= $op['documento'] ?> - <?= $op['cliente'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- RESPONSABLE -->
        <div>
            <label class="text-sm text-gray-500">Responsable</label>
            <select id="responsable_id" class="select2 w-full border rounded px-3 py-2">
                <option value="">Seleccione Responsable</option>
                <?php foreach ($responsables as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= $p['nombres'] ?> <?= $p['apellidos'] ?> — <?= $p['cargo'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- PROCESO -->
        <div>
            <label class="text-sm text-gray-500">Proceso</label>
            <select id="proceso_id" class="w-full border rounded px-3 py-2">
                <?php foreach ($procesos as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= $p['nombre'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- SATÉLITE -->
        <div>
            <label class="text-sm text-gray-500">Satélite</label>
            <select id="satelite_id" class="select2 w-full border rounded px-3 py-2">
                <option value="">Seleccione Satélite</option>
                <?php foreach ($satelites as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= $s['nombre'] ?> — <?= $s['especialidad'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- OBSERVACIONES -->
        <div class="md:col-span-3">
            <label class="text-sm text-gray-500">Observaciones</label>
            <textarea id="comen" class="w-full border rounded px-3 py-2"></textarea>
        </div>

    </div>
    <h2 class="text-lg font-semibold mb-3">Materia Prima</h2>

    <table class="min-w-full bg-white text-gray-800 text-sm rounded-lg overflow-hidden">
        <thead class="bg-gray-700 text-white">
            <tr>
                <th>Código</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="tabla-mp"></tbody>
    </table>

    <button onclick="agregarMP()"
        class="mt-3 bg-green-600 px-4 py-2 rounded text-white">
        + Agregar MP
    </button>

    <h2 class="text-lg font-semibold mt-6 mb-3">Meta (Producto terminado)</h2>

    <table class="min-w-full bg-white text-gray-800 text-sm rounded-lg overflow-hidden">
        <thead class="bg-gray-700 text-white">
            <tr>
                <th>Código</th>
                <th>Talla</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="tabla-meta"></tbody>
    </table>

    <button onclick="agregarMeta()"
        class="mt-3 bg-blue-600 px-4 py-2 rounded text-white">
        + Agregar META
    </button>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });
    });
</script>
<script>
    function recolectarDatos() {

        let mp = [];
        let meta = [];

        document.querySelectorAll("#tabla-mp tr").forEach(row => {
            mp.push({
                codr: row.querySelector(".codr").value,
                cantidad: row.querySelector(".cantidad").value,
                unidad: row.querySelector(".unidad").value
            });
        });

        document.querySelectorAll("#tabla-meta tr").forEach(row => {
            meta.push({
                codr: row.querySelector(".codr").value,
                codtalla: row.querySelector(".codtalla").value,
                cantidad: row.querySelector(".cantidad").value,
                unidad: row.querySelector(".unidad").value
            });
        });

        return {
            fecha: document.getElementById("fecha").value,
            fechent: document.getElementById("fechent").value,
            opr_id: document.getElementById("opr_id").value,
            proceso_id: document.getElementById("proceso_id").value,
            responsable_id: document.getElementById("responsable_id").value,
            comen: document.getElementById("comen").value,
            mp: mp,
            meta: meta
        };
    }
</script>
<script>
    function agregarMP() {

        let fila = `
        <tr>
            <td>
                <input type="text" class="codr border px-2 py-1 w-full">
            </td>
            <td>
                <input type="number" class="cantidad border px-2 py-1 w-full">
            </td>
            <td>
                <input type="text" class="unidad border px-2 py-1 w-full">
            </td>
            <td>
                <button onclick="this.closest('tr').remove()" 
                    class="text-red-600">X</button>
            </td>
        </tr>
    `;

        document.getElementById("tabla-mp")
            .insertAdjacentHTML("beforeend", fila);
    }

    function agregarMeta() {

        let fila = `
        <tr>
            <td>
                <input type="text" class="codr border px-2 py-1 w-full">
            </td>
            <td>
                <input type="text" class="codtalla border px-2 py-1 w-full">
            </td>
            <td>
                <input type="number" class="cantidad border px-2 py-1 w-full">
            </td>
            <td>
                <input type="text" class="unidad border px-2 py-1 w-full">
            </td>
            <td>
                <button onclick="this.closest('tr').remove()" 
                    class="text-red-600">X</button>
            </td>
        </tr>
    `;

        document.getElementById("tabla-meta")
            .insertAdjacentHTML("beforeend", fila);
    }
</script>
<script>
    document.getElementById("opr_id").addEventListener("change", function() {

        let documento = this.value;
        if (!documento) return;

        fetch(`/epp/opr/${documento}`)
            .then(res => res.json())
            .then(data => {

                // Limpiar tablas
                document.getElementById("tabla-mp").innerHTML = "";
                document.getElementById("tabla-meta").innerHTML = "";

                // ==========================
                // META (Productos)
                // ==========================
                data.meta.forEach(m => {

                    let fila = `
                    <tr>
                        <td>
                            <input type="text" class="codr border px-2 py-1 w-full"
                                value="${m.codr}">
                        </td>
                        <td>
                            <input type="text" class="codtalla border px-2 py-1 w-full"
                                value="${m.codtalla ?? ''}">
                        </td>
                        <td>
                            <input type="number" class="cantidad border px-2 py-1 w-full"
                                value="${m.cantidad}">
                        </td>
                        <td>
                            <input type="text" class="unidad border px-2 py-1 w-full">
                        </td>
                        <td>
                            <button onclick="this.closest('tr').remove()"
                                class="text-red-600">X</button>
                        </td>
                    </tr>
                `;

                    document.getElementById("tabla-meta")
                        .insertAdjacentHTML("beforeend", fila);
                });

                // ==========================
                // MP (Materiales)
                // ==========================
                data.mp.forEach(m => {

                    let fila = `
                    <tr>
                        <td>
                            <input type="text" class="codr border px-2 py-1 w-full"
                                value="${m.codr}">
                        </td>
                        <td>
                            <input type="number" class="cantidad border px-2 py-1 w-full"
                                value="${m.cantidad}">
                        </td>
                        <td>
                            <input type="text" class="unidad border px-2 py-1 w-full"
                                value="${m.unidad ?? ''}">
                        </td>
                        <td>
                            <button onclick="this.closest('tr').remove()"
                                class="text-red-600">X</button>
                        </td>
                    </tr>
                `;

                    document.getElementById("tabla-mp")
                        .insertAdjacentHTML("beforeend", fila);
                });

            });

    });
</script>