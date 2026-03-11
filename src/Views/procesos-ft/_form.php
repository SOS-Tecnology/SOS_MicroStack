<?php
// Variables esperadas:
// $proceso (array|null)
// $action (string)
// $method (POST|PUT)
?>

<form method="POST" action="<?= $action ?>" class="space-y-6">

    <?php if ($method === 'PUT'): ?>
        <input type="hidden" name="_METHOD" value="PUT">
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- Nombre -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" name="nombre"
                value="<?= $proceso['nombre'] ?? '' ?>"
                class="mt-1 block w-full border rounded p-2">
        </div>

        <!-- Descripción -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Descripción</label>
            <input type="text" name="descripcion"
                value="<?= $proceso['descripcion'] ?? '' ?>"
                class="mt-1 block w-full border rounded p-2">
        </div>

        <!-- Activo -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Activo</label>
            <select name="activo" class="mt-1 block w-full border rounded p-2">
                <option value="1" <?= ($proceso['activo'] ?? 1) == 1 ? 'selected' : '' ?>>Sí</option>
                <option value="0" <?= ($proceso['activo'] ?? 1) == 0 ? 'selected' : '' ?>>No</option>
            </select>
        </div>

        <!-- Bodega Origen -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Bodega Origen</label>
            <input type="text" name="bod_origen"
                value="<?= $proceso['bod_origen'] ?? '' ?>"
                class="mt-1 block w-full border rounded p-2">
        </div>

        <!-- Bodega Destino -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Bodega Destino</label>
            <input type="text" name="bod_destino"
                value="<?= $proceso['bod_destino'] ?? '' ?>"
                class="mt-1 block w-full border rounded p-2">
        </div>

        <!-- Tipo mov salida -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo Movimiento Salida</label>
            <input type="text" name="tipo_mov_salida"
                value="<?= $proceso['tipo_mov_salida'] ?? '' ?>"
                class="mt-1 block w-full border rounded p-2">
        </div>

        <!-- Tipo mov entrada -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo Movimiento Entrada</label>
            <input type="text" name="tipo_mov_entrada"
                value="<?= $proceso['tipo_mov_entrada'] ?? '' ?>"
                class="mt-1 block w-full border rounded p-2">
        </div>

        <!-- Requiere satélite -->
        <div>
            <label class="block text-sm font-medium text-gray-700">¿Requiere Satélite?</label>
            <select name="requiere_satelite" class="mt-1 block w-full border rounded p-2">
                <option value="1" <?= ($proceso['requiere_satelite'] ?? 0) == 1 ? 'selected' : '' ?>>Sí</option>
                <option value="0" <?= ($proceso['requiere_satelite'] ?? 0) == 0 ? 'selected' : '' ?>>No</option>
            </select>
        </div>

        <!-- Nombre actor -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Nombre Actor</label>
            <input type="text" name="nombre_actor"
                value="<?= $proceso['nombre_actor'] ?? '' ?>"
                class="mt-1 block w-full border rounded p-2">
        </div>

        <!-- Tipo de proceso -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo de Proceso</label>
            <select name="tipo" class="border rounded p-2 w-full">

                <option value="MP">MP → Producto</option>
                <option value="INTER">Producto intermedio</option>
                <option value="SERVICIO">Servicio (misma pieza)</option>
                <option value="PT">Producto terminado</option>

            </select>
        </div>
        <!-- Es reproceso -->
        <div>
            <label class="block text-sm font-medium text-gray-700">¿Es reproceso?</label>
            <select name="es_reproceso" class="mt-1 block w-full border rounded p-2">
                <option value="1" <?= ($proceso['es_reproceso'] ?? 0) == 1 ? 'selected' : '' ?>>Sí</option>
                <option value="0" <?= ($proceso['es_reproceso'] ?? 0) == 0 ? 'selected' : '' ?>>No</option>
            </select>
        </div>

        <!-- Modo tiempo -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Modo de Tiempo</label>
            <select name="modo_tiempo" class="mt-1 block w-full border rounded p-2">
                <option value="POR_UNIDAD" <?= ($proceso['modo_tiempo'] ?? '') == 'POR_UNIDAD' ? 'selected' : '' ?>>Por Unidad</option>
                <option value="TIEMPO_FIJO" <?= ($proceso['modo_tiempo'] ?? '') == 'TIEMPO_FIJO' ? 'selected' : '' ?>>Tiempo Fijo</option>
            </select>
        </div>

    </div>

    <div class="flex justify-end gap-2">
        <a href="/procesos-ft" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</a>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
    </div>

</form>