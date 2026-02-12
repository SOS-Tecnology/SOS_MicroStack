<div class="max-w-5xl mx-auto text-gray-800">

    <div class="bg-white shadow rounded-2xl p-6">

        <!-- Header -->
        <div class="bg-gray-100 rounded-lg px-4 py-3 mb-6 flex items-center gap-4">

            <a href="/orden-produccion"
                class="text-sm text-gray-600 hover:text-black">
                ← Volver
            </a>

            <h2 class="text-lg font-semibold">
                Orden de Producción (desde OP)
            </h2>

        </div>


        <!-- Información General -->
        <div class="grid grid-cols-2 gap-6 text-sm mb-6">

            <div>
                <div class="text-gray-500">Documento OP</div>
                <div class="font-medium">
                    <?= htmlspecialchars($op['documento']) ?>
                </div>
            </div>

            <div>
                <div class="text-gray-500">Cliente</div>
                <div class="font-medium">
                    <?= htmlspecialchars($op['nombrecli']) ?>
                </div>
            </div>

            <div>
                <div class="text-gray-500">Fecha OP</div>
                <div class="font-medium">
                    <?= date('d/m/Y', strtotime($op['fecha'])) ?>
                </div>
            </div>

            <div>
                <div class="text-gray-500">Fecha Entrega</div>
                <div class="font-medium">
                    <?= date('d/m/Y', strtotime($op['fechent'])) ?>
                </div>
            </div>

            <div>
                <div class="text-gray-500">Total Ítems</div>
                <div class="font-medium">
                    <?= $totalItems ?>
                </div>
            </div>

            <div>
                <div class="text-gray-500">Cantidad Total</div>
                <div class="font-medium">
                    <?= number_format($cantidadTotal, 2) ?>
                </div>
            </div>

        </div>

        <!-- Observaciones generales -->
        <?php if (!empty($op['comentario'])): ?>
            <div class="mb-6 text-sm">
                <div class="text-gray-500 mb-1">Observaciones OP</div>
                <div class="p-3 bg-gray-50 rounded border text-gray-700">
                    <?= nl2br(htmlspecialchars($op['comentario'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tabla resumen -->
        <div class="mb-6">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="p-2">Referencia</th>
                        <th class="p-2">Descripción</th>
                        <th class="p-2 text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="p-2"><?= htmlspecialchars($it['codr']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($it['descripcion']) ?></td>
                            <td class="p-2 text-right">
                                <?= number_format($it['cantidad'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Formulario -->
        <form method="POST" action="/orden-produccion/store/<?= $op['documento'] ?>">

            <input type="hidden"
                name="documento"
                value="<?= htmlspecialchars($op['documento']) ?>">

            <div class="mb-4">
                <label class="text-sm font-medium block mb-1">
                    Observaciones para producción
                </label>

                <textarea
                    name="obs_produccion"
                    rows="3"
                    class="w-full border rounded p-2 text-sm text-gray-800"
                    placeholder="Observaciones adicionales..."></textarea>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm shadow">
                    Generar OPR
                </button>
            </div>

        </form>

    </div>
</div>