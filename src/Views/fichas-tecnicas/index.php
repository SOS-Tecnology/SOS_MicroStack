<?php
$title = "Fichas Técnicas";
ob_start();
?>

<!-- Botón para crear nueva ficha técnica -->
<div class="flex justify-end mb-4">
    <a href="/fichas-tecnicas/create" 
       class="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-4 py-2 rounded-lg shadow hover:opacity-90 transition flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M12 4v16m8-8H4"/>
        </svg>
        Nueva Ficha Técnica
    </a>
</div>

<!-- Lista de fichas técnicas -->
<div class="space-y-4">
    <?php 
    // Ejemplo de registros (en práctica vendrán de la BD)
    $fichas = [
        ["titulo" => "Ficha Técnica #001", "descripcion" => "Confección de camisa básica"],
        ["titulo" => "Ficha Técnica #002", "descripcion" => "Producción de pantalón jean"],
        ["titulo" => "Ficha Técnica #003", "descripcion" => "Alistamiento de chaqueta deportiva"],
    ];

    $colors = ["bg-gray-50", "bg-white"];
    $i = 0;

    foreach ($fichas as $ficha): 
        $color = $colors[$i % 2]; 
    ?>
        <div class="<?= $color ?> shadow rounded-lg p-4 flex justify-between items-center">
            <!-- Contenido -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700"><?= $ficha['titulo'] ?></h3>
                <p class="text-sm text-gray-500"><?= $ficha['descripcion'] ?></p>
            </div>
            <!-- Botones de acción -->
            <div class="flex space-x-2">
                <!-- Editar -->
                <a href="/fichas-tecnicas/edit/<?= $i+1 ?>" 
                   class="bg-green-500 text-white p-2 rounded-full hover:bg-green-600 transition" title="Editar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M11 4h2m-1 1v14m-7-7h14"/>
                    </svg>
                </a>
                <!-- Anular -->
                <a href="/fichas-tecnicas/delete/<?= $i+1 ?>" 
                   class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition" title="Anular">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            </div>
        </div>
    <?php 
        $i++;
    endforeach; 
    ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . "/../layouts/dashboard.php";
?>
