<div class="max-w-6xl mx-auto bg-white shadow rounded-xl p-6">

    <div class="bg-gray-50 border-b p-6 flex justify-between items-start">

        <!-- IZQUIERDA -->
        <div>
            <button onclick="window.print()"
                class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-black transition">
                🖨 Imprimir
            </button>

            <!-- BOTÓN VOLVER -->
            <div class="mb-2">
                <a href="/fichas-tecnicas"
                    class="inline-flex items-center text-xs text-gray-500 hover:text-black border px-2 py-1 rounded">
                    ← Volver
                </a>
            </div>

            <!-- NOMBRE FICHA -->
            <h1 class="text-3xl font-extrabold text-gray-900 leading-tight uppercase">
                <?= htmlspecialchars($ficha['nombre_ficha']) ?>
            </h1>

            <!-- CLIENTE -->
            <p class="text-blue-600 font-bold tracking-widest text-sm mt-1">
                CLIENTE: <?= htmlspecialchars($ficha['nombre_cliente'] ?? 'N/A') ?>
            </p>

            <p class="text-sm text-gray-600 mt-1">
                Producto base:
                <span class="font-semibold">
                    <?= htmlspecialchars($ficha['producto_codigo'] ?? '') ?>
                    - <?= htmlspecialchars($ficha['producto_descr'] ?? '') ?>
                </span>
            </p>


            <!-- FECHA CREACIÓN -->
            <p class="text-xs text-gray-400 mt-1">
                Creado: <?= htmlspecialchars($ficha['created_at'] ?? '') ?>
            </p>

            <!-- COMENTARIOS -->
            <?php if (!empty($ficha['adicionales'])): ?>
                <div class="mt-3 text-xs bg-yellow-50 border border-yellow-200 p-2 rounded text-gray-700 italic">
                    <?= nl2br(htmlspecialchars($ficha['adicionales'])) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- DERECHA: DOCUMENTO -->
        <div class="text-right">
            <span class="text-xs text-gray-400 uppercase font-bold block">
                Ficha Técnica
            </span>

            <span class="text-3xl font-mono text-gray-800">
                #<?= str_pad($ficha['id'], 6, "0", STR_PAD_LEFT) ?>
            </span>
        </div>

    </div>


    <!-- INSUMOS -->
    <h2 class="font-bold text-lg mb-2">Materia Prima / Insumos</h2>

    <table class="w-full text-sm mb-6">
        <thead class="bg-gray-200">
            <tr>
                <th>Referencia</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Color</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($insumos as $i): ?>
                <tr>
                    <td><?= $i['codr'] ?> - <?= $i['descr'] ?></td>
                    <td><?= $i['unid'] ?></td>
                    <td><?= $i['cantidad'] ?></td>
                    <td><?= $i['color'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- PROCESOS -->
    <h2 class="font-bold text-lg mb-2">Procesos de Fabricación</h2>

    <table class="w-full text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th>Proceso</th>
                <th>Tiempo</th>
                <th>Comentario</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($procesos as $proc): ?>
                <tr class="border-t">
                    <td class="p-2 font-semibold text-gray-700">
                        <?= htmlspecialchars($proc['nombre_proceso']) ?>
                    </td>
                    <td class="text-center">
                        <?= (int)$proc['tiempo_minutos'] ?> min
                    </td>
                    <td class="text-gray-600 text-sm">
                        <?= htmlspecialchars($proc['comentario'] ?? '') ?>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
    <!-- FOTOS -->

    <section>
        <h3 class="text-xs font-black text-gray-400 uppercase border-b mb-4 text-right">
            Galería de Referencia
        </h3>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php foreach ($fotos ?? [] as $f): ?>
                <div class="border rounded-lg overflow-hidden bg-gray-50 h-28 flex items-center justify-center">
                    <img
                        src="/<?= $f['ruta_imagen'] ?>"
                        class="object-contain w-full h-full cursor-pointer hover:scale-105 transition"
                        onclick="openImageModal(this.src)">
                </div>
            <?php endforeach; ?>
        </div>
    </section>


    <!-- MODAL -->
    <div id="visorImagen" class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-50">
        <img id="imgGrande" class="max-h-[90%] max-w-[90%] rounded shadow-2xl">
    </div>

</div>
<script>
    function abrirImagen(src) {
        document.getElementById('imgGrande').src = src;
        document.getElementById('visorImagen').classList.remove('hidden');
    }

    document.getElementById('visorImagen').addEventListener('click', function() {
        this.classList.add('hidden');
    });
</script>


<!-- VISOR DE IMAGEN -->
<div id="imageModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50">
    <div class="bg-white p-2 rounded-lg max-w-3xl w-full relative">
        <button onclick="closeImageModal()" class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded">X</button>
        <img id="modalImage" src="" class="w-full h-auto object-contain max-h-[80vh]">
    </div>
</div>

<script>
    function openImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('imageModal').classList.add('flex');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }
</script>