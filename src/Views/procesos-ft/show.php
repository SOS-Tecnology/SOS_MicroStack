<?php $title = "Detalle Proceso FT"; ?>

<div class="max-w-5xl mx-auto p-6 bg-white shadow rounded">

    <h1 class="text-2xl font-bold mb-6"><?= $title ?></h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><strong>Nombre:</strong> <?= $proceso['nombre'] ?></div>
        <div><strong>Descripción:</strong> <?= $proceso['descripcion'] ?></div>
        <div><strong>Activo:</strong> <?= $proceso['activo'] ? 'Sí' : 'No' ?></div>
        <div><strong>Bodega Origen:</strong> <?= $proceso['bod_origen'] ?></div>
        <div><strong>Bodega Destino:</strong> <?= $proceso['bod_destino'] ?></div>
        <div><strong>Tipo Mov Salida:</strong> <?= $proceso['tipo_mov_salida'] ?></div>
        <div><strong>Tipo Mov Entrada:</strong> <?= $proceso['tipo_mov_entrada'] ?></div>
        <div><strong>Requiere Satélite:</strong> <?= $proceso['requiere_satelite'] ? 'Sí' : 'No' ?></div>
        <div><strong>Actor:</strong> <?= $proceso['nombre_actor'] ?></div>
        <div><strong>Es Reproceso:</strong> <?= $proceso['es_reproceso'] ? 'Sí' : 'No' ?></div>
        <div><strong>Modo Tiempo:</strong> <?= $proceso['modo_tiempo'] ?></div>
    </div>

    <div class="mt-6 flex gap-2">
        <a href="/procesos-ft/edit/<?= $proceso['id'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded">
            Editar
        </a>
    </div>

    <?php
    $url = "/procesos-ft";
    require __DIR__ . '/../components/_back_button.php';
    ?>

</div>