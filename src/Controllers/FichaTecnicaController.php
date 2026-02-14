<?php

namespace App\Controllers;

use App\Models\FichaTecnicaModel;
use App\Models\FichaTecnicaFotoModel;
use App\Models\FichaTecnicaDetalleModel;

class FichaTecnicaController
{
    protected $db;
    protected $fichaModel;
    protected $fotoModel;
    protected $detalleModel;

    public function __construct($db)
    {
        $this->db = $db; // <--- AQUÍ se asigna para que funcione $this->db->select()
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
            'user_create'        => $_SESSION['user']['name'] ?? 'system',
            'user_update'        => $_SESSION['user']['name'] ?? 'system',
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

        // ==========================================
        // 4. GUARDAR PROCESOS DE FABRICACIÓN
        // ==========================================
        if (!empty($data['procesos'])) {

            foreach ($data['procesos'] as $i => $proc) {

                if (empty($proc['proceso_id'])) continue;

                $this->db->insert("ficha_tecnica_procesos", [
                    "id_ficha_tecnica" => $idFicha,   // 👈 tu variable real
                    "proceso_id"       => $proc['proceso_id'],
                    "tiempo_minutos"   => $proc['tiempo'],
                    "comentario"       => $proc['comentario'],
                    "orden"            => $i
                ]);
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

    public function edit($request, $response, $args)
    {
        $id = $args['id'];

        // 1. Obtener los datos principales de la ficha
        $ficha = $this->fichaModel->find($id);

        if (!$ficha) {
            // Podrías redirigir con un mensaje de error
            return $response->withHeader('Location', '/fichas-tecnicas')->withStatus(302);
        }

        // 2. Obtener datos para los combos (Select2)
        $productosBase = $this->db->select("inrefinv", ["codr", "descr"]);
        $clientes      = $this->db->select("geclientes", ["codcli", "nombrecli"]);
        // 3. Definir 'referencias' (que son los mismos productos para los detalles)
        $referencias = $productosBase;

        // 4. Obtener fotos y detalles relacionados
        $fotos    = $this->fotoModel->byFicha($id);
        $detalles = $this->detalleModel->byFicha($id);

        // 5. Pasar todo a la vista de edición
        return renderView(
            $response,
            __DIR__ . '/../Views/fichas-tecnicas/edit.php',
            "Editar Ficha Técnica: " . $ficha['nombre_ficha'],
            [
                'ficha'         => $ficha,
                'productosBase' => $productosBase,
                'clientes'      => $clientes,
                'fotos'         => $fotos,
                'detalles'      => $detalles,
                'referencias'   => $referencias // Importante para el bucle de detalles            
            ]
        );
    }
    public function update($request, $response, $args)
    {
        $idFicha = $args['id'];
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        // 1. Actualizar datos básicos de la Ficha
        $this->db->update("fichas_tecnicas", [
            "id_producto_base"   => $data['id_producto_base'],
            "id_cliente"         => $data['id_cliente'],
            "nombre_ficha"       => $data['nombre_ficha'],
            "adicionales"        => $data['adicionales'],
            "tiempo_corte"       => $data['tiempo_corte'],
            "tiempo_confeccion"  => $data['tiempo_confeccion'],
            "tiempo_alistamiento" => $data['tiempo_alistamiento'],
            "tiempo_remate"      => $data['tiempo_remate'],
            "user_update" => $_SESSION['user']['name'] ?? 'system',
            "updated_at"         => date('Y-m-d H:i:s')
        ], ["id" => $idFicha]);

        // 2. Gestionar Detalles (Insumos)
        // Sincronización simple: Borramos los actuales e insertamos los nuevos
        $this->db->delete("ficha_tecnica_detalles", ["id_ficha_tecnica" => $idFicha]);

        if (!empty($data['referencias'])) {
            foreach ($data['referencias'] as $ref) {
                if (!empty($ref['codr'])) {
                    $this->detalleModel->create([
                        "id_ficha_tecnica" => $idFicha,
                        "codr"             => $ref['codr'],
                        "cantidad"         => $ref['cantidad'],
                        "talla"            => $ref['talla'],
                        "color"            => $ref['color']
                    ]);
                }
            }
        }

        // 3. Gestionar Nuevas Fotos
        if (!empty($files['fotos'])) {
            $uploadPath = __DIR__ . "/../../public/uploads/fichas/";
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

            foreach ($files['fotos'] as $foto) {
                if ($foto->getError() === UPLOAD_ERR_OK) {
                    $nombreArchivo = uniqid() . "_" . $foto->getClientFilename();
                    $foto->moveTo($uploadPath . $nombreArchivo);

                    $this->fotoModel->create([
                        'id_ficha_tecnica' => $idFicha,
                        'ruta_imagen'      => "uploads/fichas/" . $nombreArchivo,
                        'created_at'       => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
        // eliminar procesos anteriores
        $this->db->delete("ficha_tecnica_procesos", [
            "id_ficha_tecnica" => $idFicha
        ]);

        // volver a insertar
        if (!empty($data['procesos'])) {

            foreach ($data['procesos'] as $i => $proc) {

                if (empty($proc['proceso_id'])) continue;

                $this->db->insert("ficha_tecnica_procesos", [
                    "id_ficha_tecnica" => $idFicha,
                    "proceso_id"       => $proc['proceso_id'],
                    "tiempo_minutos"   => $proc['tiempo'],
                    "comentario"       => $proc['comentario'],
                    "orden"            => $i
                ]);
            }
        }

        return $response->withHeader('Location', '/fichas-tecnicas')->withStatus(302);
    }
    public function show($request, $response, $args)
    {
        $id = $args['id'];

        // Medoo requiere especificar columnas individuales al usar JOIN
        $ficha = $this->db->get("fichas_tecnicas", [
            "[>]geclientes" => ["id_cliente" => "codcli"]
        ], [
            // Especificamos los campos de la ficha técnica
            "fichas_tecnicas.id",
            "fichas_tecnicas.nombre_ficha",
            "fichas_tecnicas.id_cliente",
            "fichas_tecnicas.adicionales",
            "fichas_tecnicas.tiempo_corte",
            "fichas_tecnicas.tiempo_confeccion",
            "fichas_tecnicas.tiempo_alistamiento",
            "fichas_tecnicas.tiempo_remate",
            // Campo de la tabla unida
            "geclientes.nombrecli(nombre_cliente)"
        ], [
            "fichas_tecnicas.id" => $id
        ]);

        if (!$ficha) {
            return $response->withHeader('Location', '/fichas-tecnicas')->withStatus(302);
        }

        $fotos    = $this->fotoModel->byFicha($id);
        $detalles = $this->detalleModel->byFicha($id);

        return renderView($response, __DIR__ . '/../Views/fichas-tecnicas/show.php', "Consulta de Ficha", [
            'ficha'    => $ficha,
            'fotos'    => $fotos,
            'detalles' => $detalles
        ]);
    }
    public function delete($request, $response, $args)
    {
        $id = $args['id'];

        // 1. Obtener las fotos para eliminarlas físicamente del disco
        $fotos = $this->fotoModel->byFicha($id);
        foreach ($fotos as $foto) {
            $rutaFisica = __DIR__ . "/../../public/" . $foto['ruta_imagen'];
            if (file_exists($rutaFisica) && is_file($rutaFisica)) {
                unlink($rutaFisica);
            }
        }

        // 2. Eliminar registros relacionados (Fotos y Detalles)
        $this->db->delete("ficha_tecnica_fotos", ["id_ficha_tecnica" => $id]);
        $this->db->delete("ficha_tecnica_detalles", ["id_ficha_tecnica" => $id]);

        // 3. Eliminar la ficha principal
        $this->db->delete("fichas_tecnicas", ["id" => $id]);

        return $response->withHeader('Location', '/fichas-tecnicas')->withStatus(302);
    }
public function create($request, $response)
{
    // 🔹 1. Traer datos para selects (igual que hacías en index.php)
    // die("ENTRÉ AL CONTROLLER CREATE");
    //echo "ANTES DEL RENDER";
    //exit;
    $productosBase = $this->db->select("inrefinv", ["codr", "descr", "unid"]);
    $clientes      = $this->db->select("geclientes", ["codcli", "nombrecli"]);
    $referencias   = $this->db->select("inrefinv", ["codr", "descr", "unid"]);

    // 🔹 2. Traer procesos desde catalogo procesos_ft
    $procesosCatalogo = $this->db->select("procesos_ft", [
        "id",
        "nombre"
    ], [
        "activo" => 1,
        "ORDER" => ["nombre" => "ASC"]
    ]);

    // 🔹 3. Renderizar vista con TODA la data
    return renderView(
        $response,
        __DIR__ . '/../Views/fichas-tecnicas/create.php',
        "Nueva Ficha Técnica",
        [
            'productosBase' => $productosBase,
            'clientes'      => $clientes,
            'referencias'   => $referencias,
            'procesosCatalogo' => $procesosCatalogo
        ]
    );
}

}
