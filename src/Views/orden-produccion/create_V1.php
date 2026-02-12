<?php $title = "Generar Orden de Producción"; ?>

<div class="max-w-5xl mx-auto space-y-6">

    <!-- ENCABEZADO OP -->
    <div class="bg-white border rounded-xl p-5 shadow-sm">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            Orden de Pedido <?= $op['prefijo'] ?>-<?= $op['documento'] ?>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-bold text-gray-600">Cliente:</span><br>
                <?= htmlspecialchars($op['cliente_nombre']) ?> (<?= $op['codcp'] ?>)
            </div>

            <div>
                <span class="font-bold text-gray-600">Fecha OP:</span><br>
                <?= date('d/m/Y', strtotime($op['fecha'])) ?>
            </div>

            <div>
                <span class="font-bold text-gray-600">Fecha Entrega:</span><br>
                <?= $op['fechent'] ?>
            </div>
        </div>
    </div>

    <!-- COMENTARIO -->
    <form method="POST" action="/orden-produccion/store/<?= $op['documento'] ?>">
        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                    Comentario General Producción
                </label>
                <textarea name="comentario"
                          rows="3"
                          class="w-full border rounded-md p-2 text-sm"></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2 rounded-lg">
                    Generar OPR
                </button>
            </div>
        </div>
    </form>

</div>
