<div class="max-w-7xl mx-auto bg-gray-500 text-white rounded-xl p-6">

    <!-- CABECERA -->
    <div class="flex justify-between items-center mb-6">

        <?php
        $backUrl = (!empty($cab['docaux']) && !empty($cab['proceso_nombre']))
            ? "/orden-produccion/procesos/{$cab['docaux']}/" . urlencode($cab['proceso_nombre'])
            : "/orden-produccion/avance";
        ?>

        <a href="<?= $backUrl ?>"
            class="bg-gray-600 px-4 py-2 rounded hover:bg-gray-400">
            ← Volver
        </a>

        <h2 class="text-2xl font-semibold text-gray-700">
            EPP #<?= $cab['documento'] ?? '' ?>
        </h2>

        <a href="/epp/print/<?= $cab['documento'] ?? '' ?>"
            class="bg-blue-600 px-4 py-2 rounded hover:bg-blue-400">
            Imprimir
        </a>

    </div>

    <!-- BLOQUE PRINCIPAL -->
    <div class="grid grid-cols-6 gap-4 mb-6">

        <!-- OPR -->
        <div class="col-span-1">
            <label>OPR</label>
            <input type="text" value="<?= $cab['docaux'] ?? '' ?>" class="w-full text-black p-2 bg-gray-200" readonly>
        </div>

        <!-- PROCESO -->
        <div class="col-span-2">
            <label>Proceso</label>
            <input type="text" value="<?= $cab['proceso_nombre'] ?? '' ?>" class="w-full text-black p-2 bg-gray-200" readonly>
        </div>

        <!-- ESTADO -->
        <div class="col-span-1">
            <label>Estado</label>
            <input type="text" value="<?= $cab['estado'] ?? '' ?>" class="w-full text-black p-2 bg-gray-200" readonly>
        </div>

        <!-- FECHA -->
        <div class="col-span-1">
            <label>Fecha</label>
            <input type="text" value="<?= $cab['fecha'] ?? '' ?>" class="w-full text-black p-2 bg-gray-200" readonly>
        </div>

        <!-- FECHA ENTREGA -->
        <div class="col-span-1">
            <label>Entrega</label>
            <input type="text" value="<?= $cab['fechent'] ?? '' ?>" class="w-full text-black p-2 bg-gray-200" readonly>
        </div>

        <!-- SATÉLITE -->
        <div class="col-span-3">
            <label>Satélite</label>
            <input type="text" value="<?= $cab['satelite'] ?? '' ?>" class="w-full text-black p-2 bg-gray-200" readonly>
        </div>

        <!-- RESPONSABLE -->
        <div class="col-span-2">
            <label>Responsable</label>
            <input type="text" value="<?= $cab['responsable'] ?? '' ?>" class="w-full text-black p-2 bg-gray-200" readonly>
        </div>

    </div>

</div>

<!-- ============================= -->
<!-- DETALLE -->
<!-- ============================= -->

<div class="max-w-7xl mx-auto bg-gray-400 p-4 rounded-lg mb-6">

    <h2 class="text-lg font-semibold mb-3">
        Detalle del Envío
    </h2>

    <table class="w-full bg-white text-black rounded">

        <thead class="bg-gray-200">
            <tr>
                <th class="p-2">Código</th>
                <th class="p-2">Talla</th>
                <th class="p-2">Color</th>
                <th class="p-2 text-center">Cantidad</th>
                <th class="p-2 text-center">Unidad</th>
                <th class="p-2 text-center">Tipo</th>
            </tr>
        </thead>

        <tbody>

            <?php if (!empty($det)): ?>

                <?php
                $mp = [];
                $meta = [];

                foreach ($det as $d) {
                    if (($d['tipo_registro'] ?? '') == 'MP') {
                        $mp[] = $d;
                    } else {
                        $meta[] = $d;
                    }
                }
                ?>

                <!-- MP -->
                <?php if (!empty($mp)): ?>
                    <tr class="bg-gray-300 font-semibold">
                        <td colspan="6" class="p-2">ENTRADA AL PROCESO (MP)</td>
                    </tr>

                    <?php foreach ($mp as $d): ?>
                        <tr class="border-t">
                            <td class="p-2"><?= $d['codr'] ?? '' ?></td>
                            <td class="p-2 text-center"><?= $d['codtalla'] ?? '' ?></td>
                            <td class="p-2 text-center"><?= $d['codcolor'] ?? '' ?></td>
                            <td class="p-2 text-center"><?= $d['cantidad'] ?? '' ?></td>
                            <td class="p-2 text-center"><?= $d['unidad'] ?? '' ?></td>
                            <td class="p-2 text-center"><?= $d['tipo_registro'] ?? '' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- META -->
                <?php if (!empty($meta)): ?>
                    <tr class="bg-gray-300 font-semibold">
                        <td colspan="6" class="p-2">SALIDA ESPERADA (META)</td>
                    </tr>

                    <?php foreach ($meta as $d): ?>
                        <tr class="border-t">
                            <td class="p-2"><?= $d['codr'] ?? '' ?></td>
                            <td class="p-2 text-center"><?= $d['codtalla'] ?? '' ?></td>
                            <td class="p-2 text-center"><?= $d['codcolor'] ?? '' ?></td>
                            <td class="p-2 text-center"><?= $d['cantidad'] ?? '' ?></td>
                            <td class="p-2 text-center"><?= $d['unidad'] ?? '' ?></td>
                            <td class="p-2 text-center"><?= $d['tipo_registro'] ?? '' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6" class="p-3 text-center text-gray-500">
                        Sin registros
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>

<!-- ============================= -->
<!-- OBSERVACIONES -->
<!-- ============================= -->

<div class="max-w-7xl mx-auto bg-gray-400 p-4 rounded-lg">

    <label class="block mb-2 font-semibold">
        Observaciones
    </label>

    <textarea class="w-full text-black p-2 rounded" readonly><?= $cab['comen'] ?? '' ?></textarea>

</div>

<!-- ============================= -->
<!-- ESTILOS DE IMPRESIÓN -->
<!-- ============================= -->

<style>
@media print {
    body {
        background: white;
        font-size: 12px;
    }

    a, button {
        display: none;
    }

    table {
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid #000;
        padding: 4px;
    }
}
</style>