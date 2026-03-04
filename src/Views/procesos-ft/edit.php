<?php $title = "Editar Proceso FT"; ?>

<div class="max-w-5xl mx-auto p-6 bg-white shadow rounded">
    <h1 class="text-2xl font-bold mb-6"><?= $title ?></h1>

    <?php
    $action = "/procesos-ft/update/" . $proceso['id'];
    $method = "PUT";

    require __DIR__ . '/_form.php';
    ?>

    <?php
    $url = "/procesos-ft";
    require __DIR__ . '/../components/_back_button.php';
    ?>
</div>