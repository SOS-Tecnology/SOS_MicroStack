<?php
namespace App\Controllers;

class SateliteController
{
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function index($request, $response) {
        $satelites = $this->db->select("satelites", [
            "[>]provee" => ["id_proveedor" => "codp"]
        ], [
            "satelites.id",
            "satelites.tipo",
            "satelites.especialidad",
            "satelites.calificacion",
            "satelites.estado",
            "provee.nombre(nombre_proveedor)",
            "provee.ciud(ciudad)",
            "provee.contacto"
        ]);

        return renderView($response, __DIR__ . '/../Views/Satelites/index.php', "Control de Satélites", [
            'satelites' => $satelites
        ]);
    }

    public function create($request, $response) {
        $proveedores = $this->db->select("provee", [
            "codp", "nombre", "direcc", "ciud", "contacto", "tels"
        ]);
        
        return renderView($response, __DIR__ . '/../Views/Satelites/create.php', "Nuevo Satélite", [
            'proveedores' => $proveedores
        ]);
    }

public function store($request, $response) {
    $data = $request->getParsedBody();

    // VALIDACIÓN DE DUPLICADOS: Verificar si el proveedor ya está registrado como satélite activo
    $existe = $this->db->has("satelites", [
        "AND" => [
            "id_proveedor" => $data['id_proveedor'],
            "estado" => "Activo"
        ]
    ]);

    if ($existe) {
        // Podrías redirigir con un mensaje de error (usando sesiones flash)
        $_SESSION['error'] = "Este proveedor ya está registrado como un satélite activo.";
        return $response->withHeader('Location', '/satelites/create')->withStatus(302);
    }

    $this->db->insert("satelites", [
        "id_proveedor" => $data['id_proveedor'],
        "tipo"         => $data['tipo'],
        "especialidad" => $data['especialidad'],
        "capacidad_produccion" => $data['capacidad_produccion'],
        "calificacion" => $data['calificacion'],
        "comentarios"  => $data['comentarios'],
        "estado"       => "Activo",
        "created_at"   => date('Y-m-d H:i:s')
    ]);

    return $response->withHeader('Location', '/satelites')->withStatus(302);
}

    public function show($request, $response, $args) {
        $id = $args['id'];
        $satelite = $this->db->get("satelites", [
            "[>]provee" => ["id_proveedor" => "codp"]
        ], [
            // Campos de Satélites
            "satelites.id",
            "satelites.id_proveedor",
            "satelites.tipo",
            "satelites.especialidad",
            "satelites.capacidad_produccion",
            "satelites.calificacion",
            "satelites.comentarios",
            "satelites.estado",
            // Campos de Proveedor
            "provee.nombre",
            "provee.direcc",
            "provee.ciud",
            "provee.contacto",
            "provee.tels"
        ], ["satelites.id" => $id]);

        return renderView($response, __DIR__ . '/../Views/Satelites/show.php', "Detalle Satélite", [
            's' => $satelite
        ]);
    }

    public function edit($request, $response, $args) {
        $id = $args['id'];
        
        // Obtenemos solo los campos necesarios de la tabla base
        $satelite = $this->db->get("satelites", [
            "id", "id_proveedor", "tipo", "especialidad", 
            "capacidad_produccion", "calificacion", "comentarios"
        ], ["id" => $id]);

        $proveedores = $this->db->select("provee", [
            "codp", "nombre", "direcc", "ciud", "contacto", "tels"
        ]);

        return renderView($response, __DIR__ . '/../Views/Satelites/edit.php', "Editar Satélite", [
            's' => $satelite,
            'proveedores' => $proveedores
        ]);
    }

    public function update($request, $response, $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();

        $this->db->update("satelites", [
            "id_proveedor" => $data['id_proveedor'],
            "tipo"         => $data['tipo'],
            "especialidad" => $data['especialidad'],
            "capacidad_produccion" => $data['capacidad_produccion'],
            "calificacion" => $data['calificacion'],
            "comentarios"  => $data['comentarios'],
            "updated_at"   => date('Y-m-d H:i:s')
        ], ["id" => $id]);

        return $response->withHeader('Location', '/satelites')->withStatus(302);
    }

    public function anular($request, $response, $args) {
        $id = $args['id'];
        $this->db->update("satelites", ["estado" => "Bloqueado"], ["id" => $id]);
        return $response->withHeader('Location', '/satelites')->withStatus(302);
    }
}