<?php if (!empty($_SESSION['error'])): ?>
    <div class="bg-red-500 text-white p-3 rounded mb-4">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="max-w-7xl mx-auto">

    <!-- ============================================================ -->
    <!-- TÍTULO                                                        -->
    <!-- ============================================================ -->
    <div class="flex justify-between items-center mb-6">
        <a href="/orden-produccion/avance/ver/<?= $opr ?>"
           class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-500">
            ← Volver
        </a>
        <h2 class="text-2xl font-semibold text-white">
            Recepción de Proceso (RPP) #<?= $nextRpp ?>
        </h2>
    </div>

    <form method="POST" action="/rpp/store" id="form-rpp">

        <!-- Campos ocultos de contexto -->
        <input type="hidden" name="documento"  value="<?= $nextRpp ?>">
        <input type="hidden" name="epp"        value="<?= $cab['documento'] ?>">
        <input type="hidden" name="opr"        value="<?= $cab['docaux'] ?? '' ?>">
        <input type="hidden" name="proceso"    value="<?= $cab['proceso_id'] ?>">
        <input type="hidden" name="satelite"   value="<?= $cab['satelite_id'] ?>">
        <input type="hidden" name="codcp"      value="<?= $cab['codcp'] ?>">

        <!-- ============================================================ -->
        <!-- ENCABEZADO INFORMATIVO + FECHA                               -->
        <!-- ============================================================ -->
        <div class="bg-gray-500 text-white rounded-xl p-6 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

                <div>
                    <label class="block text-xs text-gray-300 mb-1">OPR</label>
                    <input class="w-full bg-gray-600 text-white border border-gray-500 rounded px-3 py-2 text-sm"
                           value="<?= $opr ?>" readonly>
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-xs text-gray-300 mb-1">Cliente</label>
                    <input class="w-full bg-gray-600 text-white border border-gray-500 rounded px-3 py-2 text-sm"
                           value="<?= htmlspecialchars($cliente ?? '') ?>" readonly>
                </div>

                <div>
                    <label class="block text-xs text-gray-300 mb-1">EPP</label>
                    <input class="w-full bg-gray-600 text-white border border-gray-500 rounded px-3 py-2 text-sm"
                           value="<?= $cab['documento'] ?>" readonly>
                </div>

                <div>
                    <label class="block text-xs text-gray-300 mb-1">Proceso</label>
                    <input class="w-full bg-gray-600 text-white border border-gray-500 rounded px-3 py-2 text-sm"
                           value="<?= htmlspecialchars($cab['proceso_nombre'] ?? '') ?>" readonly>
                </div>

                <div>
                    <label class="block text-xs text-gray-300 mb-1">Satélite / Responsable</label>
                    <input class="w-full bg-gray-600 text-white border border-gray-500 rounded px-3 py-2 text-sm"
                           value="<?= htmlspecialchars($cab['satelite_nombre'] ?? '') ?>" readonly>
                </div>

                <div>
                    <label class="block text-xs text-gray-300 mb-1">
                        Fecha de recibo <span class="text-red-400">*</span>
                    </label>
                    <input type="date"
                           name="fecha"
                           id="fecha_recibo"
                           value="<?= date('Y-m-d') ?>"
                           class="w-full bg-white text-black border border-gray-300 rounded px-3 py-2 text-sm"
                           required>
                </div>

            </div>
        </div>

        <!-- ============================================================ -->
        <!-- TABLA MP — Retorno de materia prima sobrante                 -->
        <!-- Aparece solo si el proceso tiene ítems tipo MP               -->
        <!-- ============================================================ -->
        <?php if (!empty($mp)): ?>
        <div class="bg-gray-500 text-white rounded-xl p-6 mb-6">
            <h3 class="text-base font-semibold mb-1">Retorno de materia prima</h3>
            <p class="text-xs text-gray-300 mb-4">
                Material enviado que el satélite devuelve sin usar. Ingresa solo lo que efectivamente retorna.
            </p>

            <div class="overflow-x-auto">
                <table class="w-full bg-white text-black rounded text-sm">
                    <thead class="bg-gray-200 text-gray-700 text-xs uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">Código</th>
                            <th class="px-3 py-2 text-left">Descripción</th>
                            <th class="px-3 py-2 text-right">Enviado EPP</th>
                            <th class="px-3 py-2 text-right">Ya retornado</th>
                            <th class="px-3 py-2 text-right">Pendiente</th>
                            <th class="px-3 py-2 text-center w-36">Recibir ahora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mp as $i => $d): ?>
                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                            <td class="px-3 py-2 font-mono text-xs"><?= htmlspecialchars($d['coditem']) ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($d['descripcion'] ?? '') ?></td>
                            <td class="px-3 py-2 text-right"><?= number_format($d['cantidad'], 2) ?></td>
                            <td class="px-3 py-2 text-right text-gray-500"><?= number_format($d['recibido'], 2) ?></td>
                            <td class="px-3 py-2 text-right font-semibold text-blue-700">
                                <?= number_format($d['pendiente'], 2) ?>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <input type="number"
                                       name="detalle[<?= $i ?>][cantidad]"
                                       value="<?= number_format($d['pendiente'], 2, '.', '') ?>"
                                       min="0"
                                       max="<?= number_format($d['pendiente'], 2, '.', '') ?>"
                                       step="0.01"
                                       class="campo-cantidad w-28 border border-gray-400 rounded px-2 py-1 text-center text-sm"
                                       data-max="<?= floatval($d['pendiente']) ?>">
                                <input type="hidden" name="detalle[<?= $i ?>][codr]"          value="<?= htmlspecialchars($d['coditem']) ?>">
                                <input type="hidden" name="detalle[<?= $i ?>][codtalla]"      value="">
                                <input type="hidden" name="detalle[<?= $i ?>][codcolor]"      value="">
                                <input type="hidden" name="detalle[<?= $i ?>][tipo_registro]" value="MP">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ============================================================ -->
        <!-- TABLA META — Producción recibida del satélite                -->
        <!-- ============================================================ -->
        <?php if (!empty($metis)): ?>
        <?php $offset = count($mp); ?>
        <div class="bg-gray-500 text-white rounded-xl p-6 mb-6">
            <h3 class="text-base font-semibold mb-1">Producción recibida</h3>
            <p class="text-xs text-gray-300 mb-4">
                Prendas o productos terminados que entrega el satélite.
                Puedes recibir parcialmente — el saldo queda pendiente para la siguiente RPP.
            </p>

            <div class="overflow-x-auto">
                <table class="w-full bg-white text-black rounded text-sm">
                    <thead class="bg-gray-200 text-gray-700 text-xs uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">Código</th>
                            <th class="px-3 py-2 text-left">Descripción</th>
                            <th class="px-3 py-2 text-center">Talla</th>
                            <th class="px-3 py-2 text-center">Color</th>
                            <th class="px-3 py-2 text-right">Meta EPP</th>
                            <th class="px-3 py-2 text-right">Ya recibido</th>
                            <th class="px-3 py-2 text-right">Pendiente</th>
                            <th class="px-3 py-2 text-center w-36">Recibir ahora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metis as $i => $d): ?>
                        <?php $idx = $offset + $i; ?>
                        <tr class="border-t border-gray-200 hover:bg-gray-50">
                            <td class="px-3 py-2 font-mono text-xs"><?= htmlspecialchars($d['coditem']) ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($d['descripcion'] ?? '') ?></td>
                            <td class="px-3 py-2 text-center"><?= htmlspecialchars($d['talla'] ?? '') ?></td>
                            <td class="px-3 py-2 text-center"><?= htmlspecialchars($d['color'] ?? '') ?></td>
                            <td class="px-3 py-2 text-right"><?= number_format($d['cantidad'], 0) ?></td>
                            <td class="px-3 py-2 text-right text-gray-500"><?= number_format($d['recibido'], 0) ?></td>
                            <td class="px-3 py-2 text-right font-semibold text-blue-700">
                                <?= number_format($d['pendiente'], 0) ?>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <input type="number"
                                       name="detalle[<?= $idx ?>][cantidad]"
                                       value="<?= intval($d['pendiente']) ?>"
                                       min="0"
                                       max="<?= intval($d['pendiente']) ?>"
                                       step="1"
                                       class="campo-cantidad w-28 border border-gray-400 rounded px-2 py-1 text-center text-sm"
                                       data-max="<?= intval($d['pendiente']) ?>">
                                <input type="hidden" name="detalle[<?= $idx ?>][codr]"          value="<?= htmlspecialchars($d['coditem']) ?>">
                                <input type="hidden" name="detalle[<?= $idx ?>][codtalla]"      value="<?= htmlspecialchars($d['talla'] ?? '') ?>">
                                <input type="hidden" name="detalle[<?= $idx ?>][codcolor]"      value="<?= htmlspecialchars($d['color'] ?? '') ?>">
                                <input type="hidden" name="detalle[<?= $idx ?>][tipo_registro]" value="META">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-100 border-t-2 border-gray-300">
                        <tr>
                            <td colspan="7" class="px-3 py-2 text-right font-semibold text-gray-700 text-sm">
                                Total unidades a recibir ahora:
                            </td>
                            <td class="px-3 py-2 text-center font-bold text-blue-700" id="total_meta">
                                0
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- EPP completamente recibida -->
        <?php if (empty($mp) && empty($metis)): ?>
        <div class="bg-green-100 border border-green-300 text-green-800 rounded-xl p-6 mb-6 text-center">
            <p class="font-semibold text-lg">✔ EPP completamente recibida</p>
            <p class="text-sm mt-1">No quedan saldos pendientes en esta EPP.</p>
        </div>
        <?php endif; ?>

        <!-- ============================================================ -->
        <!-- OBSERVACIONES                                                 -->
        <!-- ============================================================ -->
        <div class="bg-gray-500 text-white rounded-xl p-6 mb-6">
            <label class="block text-sm font-semibold mb-2">Observaciones</label>
            <textarea name="comen"
                      rows="3"
                      placeholder="Novedades, diferencias, condiciones de entrega..."
                      class="w-full text-black bg-white rounded px-3 py-2 text-sm resize-none"></textarea>
        </div>

        <!-- ============================================================ -->
        <!-- BOTONES                                                       -->
        <!-- ============================================================ -->
        <div class="flex justify-end gap-3 mb-6">
            <a href="/orden-produccion/avance/ver/<?= $opr ?>"
               class="bg-gray-600 text-white px-5 py-2 rounded hover:bg-gray-500">
                Cancelar
            </a>
            <?php if (!empty($mp) || !empty($metis)): ?>
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-500 font-semibold">
                Guardar RPP
            </button>
            <?php endif; ?>
        </div>

    </form>
</div>

<!-- ============================================================ -->
<!-- JAVASCRIPT                                                   -->
<!-- ============================================================ -->
<script>
$(document).ready(function () {

    // ----------------------------------------------------------
    // Validar max en tiempo real y recalcular total META
    // ----------------------------------------------------------
    $(document).on('input', '.campo-cantidad', function () {
        var max = parseFloat($(this).data('max'));
        var val = parseFloat($(this).val());

        if (isNaN(val) || val < 0) {
            $(this).val(0);
        } else if (val > max) {
            $(this).val(max);
        }

        recalcularTotal();
    });

    function recalcularTotal() {
        var total = 0;
        // Recorrer solo los inputs cuyo hidden tipo_registro = META
        $('input[name$="[cantidad]"]').each(function () {
            var nombre = $(this).attr('name');
            // Extraer índice: detalle[N][cantidad] → N
            var match = nombre.match(/detalle\[(\d+)\]/);
            if (!match) return;
            var idx  = match[1];
            var tipo = $('input[name="detalle[' + idx + '][tipo_registro]"]').val();
            if (tipo === 'META') {
                total += parseFloat($(this).val()) || 0;
            }
        });
        $('#total_meta').text(total);
    }

    recalcularTotal();

    // ----------------------------------------------------------
    // Validación submit
    // ----------------------------------------------------------
    $('#form-rpp').on('submit', function (e) {
        var fecha = $('#fecha_recibo').val();
        if (!fecha) {
            alert('Debe ingresar la fecha de recibo.');
            e.preventDefault();
            return;
        }

        var hayAlgo = false;
        $('.campo-cantidad').each(function () {
            if (parseFloat($(this).val()) > 0) {
                hayAlgo = true;
                return false;
            }
        });

        if (!hayAlgo) {
            alert('Debe ingresar al menos una cantidad mayor a cero para guardar.');
            e.preventDefault();
        }
    });

});
</script>
