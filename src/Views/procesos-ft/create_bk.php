<div class="flex justify-between items-center mb-6">

    <div>
        <h2 class="text-2xl font-extrabold text-gray-800">
            <?= $title ?>
        </h2>
        <p class="text-sm text-gray-500">
            Gestión de procesos de fabricación
        </p>
    </div>

    <a href="/procesos-ft"
       class="flex items-center text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors">

        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8"/>
        </svg>

        Volver
    </a>

</div>
<div class="max-w-3xl mx-auto bg-white shadow rounded-xl p-6">

    <h1 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">
        <?= isset($proceso) ? 'Editar Proceso' : 'Nuevo Proceso de Fabricación' ?>
    </h1>

    <form method="POST"
          action="<?= isset($proceso) ? '/procesos-ft/update/' . $proceso['id'] : '/procesos-ft/store' ?>"
          class="space-y-4">

        <!-- NOMBRE -->
        <div>
            <label class="block text-sm text-gray-600 mb-1">Nombre del proceso</label>
            <input type="text"
                   name="nombre"
                   required
                   value="<?= $proceso['nombre'] ?? '' ?>"
                   class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200">
        </div>

        <!-- TIPO -->
        <div>
            <label class="block text-sm text-gray-600 mb-1">Tipo</label>
            <select name="tipo"
                    class="w-full border rounded px-3 py-2">
                <option value="ACUMULADO"
                    <?= (($proceso['tipo'] ?? '') == 'ACUMULADO') ? 'selected' : '' ?>>
                    ACUMULADO
                </option>
                <option value="INDIVIDUAL"
                    <?= (($proceso['tipo'] ?? '') == 'INDIVIDUAL') ? 'selected' : '' ?>>
                    INDIVIDUAL
                </option>
            </select>
        </div>

        <!-- ORDEN -->
        <div>
            <label class="block text-sm text-gray-600 mb-1">Orden en producción</label>
            <input type="number"
                   name="orden"
                   value="<?= $proceso['orden'] ?? 0 ?>"
                   class="w-full border rounded px-3 py-2">
        </div>

        <!-- RESPONSABLE -->
        <div>
            <label class="block text-sm text-gray-600 mb-1">Responsable</label>
            <select name="responsable"
                    class="w-full border rounded px-3 py-2">
                <option value="INTERNO"
                    <?= (($proceso['responsable'] ?? '') == 'INTERNO') ? 'selected' : '' ?>>
                    Interno
                </option>
                <option value="SATELITE"
                    <?= (($proceso['responsable'] ?? '') == 'SATELITE') ? 'selected' : '' ?>>
                    Satélite / Tercero
                </option>
            </select>
        </div>

        <!-- BOTONES -->
        <div class="flex justify-end space-x-3 pt-4">

            <a href="/procesos-ft"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Cancelar
            </a>

            <button type="submit"
                    class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Guardar
            </button>

        </div>

    </form>

</div>