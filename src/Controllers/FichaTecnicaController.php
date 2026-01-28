<?php

namespace App\Controllers;

use App\Models\FichaTecnicaModel;
use App\Models\FichaTecnicaFotoModel;
use App\Models\FichaTecnicaDetalleModel;

class FichaTecnicaController
{
    protected $fichaModel;
    protected $fotoModel;
    protected $detalleModel;

    public function __construct($db)
    {
        $this->fichaModel   = new FichaTecnicaModel($db);
        $this->fotoModel    = new FichaTecnicaFotoModel($db);
        $this->detalleModel = new FichaTecnicaDetalleModel($db);
    }

    public function store($request, $response)
    {
        $data  = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        // 1. Guardar ficha técnica
        $idFicha = $this->fichaModel->create([
            'id_producto_base'   => $data['id_producto_base'],
            'id_cliente'         => $data['id_cliente'],
            'nombre_ficha'       => $data['nombre_ficha'],
            'adicionales'        => $data['adicionales'],
            'tiempo_corte'       => $data['tiempo_corte'],
            'tiempo_confeccion'  => $data['tiempo_confeccion'],
            'tiempo_alistamiento' => $data['tiempo_alistamiento'],
            'tiempo_remate'      => $data['tiempo_remate'],
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s')
        ]);


// 2. Guardar fotos
if (!empty($files['fotos'])) {
    $uploadPath = __DIR__ . "/../../public/uploads/fichas/";
    
    // Crear carpeta si no existe
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    foreach ($files['fotos'] as $foto) {
        if ($foto->getError() === UPLOAD_ERR_OK) {
            $nombreArchivo = uniqid() . "_" . $foto->getClientFilename();
            $rutaRelativa = "uploads/fichas/" . $nombreArchivo;
            
            $foto->moveTo($uploadPath . $nombreArchivo);
            
            $this->fotoModel->create([
                'id_ficha_tecnica' => $idFicha,
                'ruta_imagen'      => $rutaRelativa,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s')
            ]);
        }
    }
}

        // 3. Guardar referencias
        if (!empty($data['referencias'])) {
            foreach ($data['referencias'] as $ref) {
                if (!empty($ref['codr'])) {
                    $this->detalleModel->create([
                        'id_ficha_tecnica' => $idFicha,
                        'codr'             => $ref['codr'],
                        'cantidad'         => $ref['cantidad'],
                        'talla'            => $ref['talla'],
                        'color'            => $ref['color'],
                        'created_at'       => date('Y-m-d H:i:s'),
                        'updated_at'       => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }




        return $response
            ->withHeader('Location', '/fichas-tecnicas')
            ->withStatus(302);
    }
    public function index($request, $response)
    {
        // Obtenemos todas las fichas desde el modelo
        $fichas = $this->fichaModel->all();

        // Llamamos a la función global renderView pasando los datos
        return renderView(
            $response,
            __DIR__ . '/../Views/fichas-tecnicas/index.php',
            "Fichas Técnicas",
            ['fichas' => $fichas] // Aquí enviamos la variable a la vista
        );
    }
}
