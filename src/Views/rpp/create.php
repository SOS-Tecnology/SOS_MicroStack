<?php if (!empty($_SESSION['error'])): ?>
    <div class="bg-red-500 text-white p-3 rounded mb-4">
        <?= $_SESSION['error'];
        unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="max-w-7xl mx-auto bg-gray-500 text-white rounded-xl p-6">

    <!-- CABECERA -->
    <div class="flex justify-between items-center mb-6">

        <a href="/orden-produccion/avance"
            class="bg-gray-600 px-4 py-2 rounded hover:bg-gray-400">
            ← Volver
        </a>

        <h2 class="text-2xl font-semibold text-gray-700">
            Recepción de Proceso (RPP) #<?= $nextRpp ?>
        </h2>

    </div>

    <form method="POST" action="/rpp/store">

        <!-- HIDDEN -->
        <input type="hidden" name="documento" value="<?= $nextRpp ?>">
        <input type="hidden" name="epp" value="<?= $cab['documento'] ?>">
        <input type="hidden" name="opr" value="<?= $cab['docaux'] ?? '' ?>">
        <input type="hidden" name="proceso" value="<?= $cab['proceso_id'] ?>">
        <input type="hidden" name="satelite" value="<?= $cab['satelite_id'] ?>">
        <input type="hidden" name="codcp" value="<?= $cab['codcp'] ?>">

        <!-- ENCABEZADO -->
        <div class="grid grid-cols-4 gap-4 mt-4">

            <div>
                <label>OPR</label>
                <input class="w-full" value="<?= $opr ?>" readonly>
            </div>

            <div>
                <label>Proceso</label>
                <input class="w-full" value="<?= $cab['proceso_nombre'] ?>" readonly>
            </div>

            <div>
                <label>Fecha</label>
                <input type="date" class="w-full" name="fecha">
            </div>

            <div>
                <label>Satélite</label>
                <input class="w-full" value="<?= $cab['satelite_nombre'] ?>" readonly>
            </div>

        </div>

</div>

<!-- ============================= -->
<!-- DETALLE -->
<!-- ============================= -->

<div class="max-w-7xl mx-auto bg-gray-400 p-4 rounded-lg mb-6">

    <h2 class="text-lg font-semibold mb-3">
        Detalle recibido
    </h2>
    <?php if (!empty($mp)): ?>

        <h3 class="mt-6 font-bold">Materia Prima</h3>

        <table class="w-full bg-white text-black">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Enviado</th>
                    <th>Recibido</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mp as $d): ?>
                    <tr>
                        <td><?= $d['coditem'] ?></td>
                        <td><?= $d['cantidad'] ?></td>
                        <td>
                            <input type="number" name="recibido[]" value="<?= $d['cantidad'] ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>
    <?php if (!empty($metis)): ?>

        <h3 class="mt-6 font-bold">Producción</h3>

        <table class="w-full bg-white text-black">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Talla</th>
                    <th>Color</th>
                    <th>Enviado</th>
                    <th>Recibido</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($metis as $d): ?>
                    <tr>
                        <td><?= $d['coditem'] ?></td>
                        <td><?= $d['talla'] ?></td>
                        <td><?= $d['color'] ?></td>
                        <td><?= $d['cantidad'] ?></td>
                        <td>
                            <input type="number" name="recibido[]" value="<?= $d['cantidad'] ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

</div>

<!-- ============================= -->
<!-- OBSERVACIONES -->
<!-- ============================= -->

<div class="max-w-7xl mx-auto bg-gray-400 p-4 rounded-lg mb-6">

    <label class="block mb-2 font-semibold">
        Observaciones
    </label>

    <textarea name="comen"
        class="w-full text-black p-2 rounded"></textarea>

</div>

<!-- BOTONES -->

<div class="max-w-7xl mx-auto flex justify-end gap-3">

    <a href="/orden-produccion/avance"
        class="bg-gray-600 px-5 py-2 rounded hover:bg-gray-400">
        Volver
    </a>

    <button class="bg-blue-600 px-6 py-2 rounded hover:bg-blue-400">
        Guardar RPP
    </button>

</div>

</form>

<script>
    $('form').on('submit', function(e) {

        let fecha = $('input[name="fecha"]').val();

        if (!fecha) {
            alert('Debe ingresar la fecha');
            e.preventDefault();
        }

    });
</script>