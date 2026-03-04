<?php

namespace App\Controllers;

class ProcesosFTController
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // ===============================
    // 📄 LISTADO
    // ===============================
    public function index($request, $response)
    {
        $procesos = $this->db->select("procesos_ft", "*");
        $title = "Detalle del Proceso";
        ob_start();
        require __DIR__ . '/../Views/procesos-ft/index.php';
        $content = ob_get_clean();

        $title = "Procesos de Fabricación";

        require __DIR__ . '/../Views/layouts/dashboard.php';

        return $response;
    }

    // ===============================
    // ➕ FORM CREAR
    // ===============================
    public function create($request, $response)
    {
        $proceso = null;
        $title = "Nuevo Proceso de Fabricación";

        ob_start();
        require __DIR__ . '/../Views/procesos-ft/create.php';
        $content = ob_get_clean();

        $title = "Nuevo Proceso";

        require __DIR__ . '/../Views/layouts/dashboard.php';

        return $response;
    }

    // ===============================
    // 💾 GUARDAR
    // ===============================
    public function store($request, $response)
    {
        $data = $request->getParsedBody();

        $this->db->insert("procesos_ft", [
            "nombre" => $data["nombre"],
            "tipo" => $data["tipo"],
            "orden" => $data["orden"],
            "responsable" => $data["responsable"],
            "bod_origen" => $data["bod_origen"],
            "bod_destino" => $data["bod_destino"],
            "tipo_mov_salida" => $data["tipo_mov_salida"],
            "tipo_mov_entrada" => $data["tipo_mov_entrada"],
            "modo_tiempo" => $data["modo_tiempo"],
            "activo" => 1
        ]);

        return $response
            ->withHeader("Location", "/procesos-ft")
            ->withStatus(302);
    }
    // ===============================
    // ✏️ EDITAR
    // ===============================
    public function edit($request, $response, $args)
    {
        $id = $args["id"];

        $proceso = $this->db->get("procesos_ft", "*", ["id" => $id]);

        // 🔴 Validación básica
        if (!$proceso) {
            return $response
                ->withHeader("Location", "/procesos-ft")
                ->withStatus(302);
        }

        $title = "Editar Proceso de Fabricación";

        ob_start();
        require __DIR__ . '/../Views/procesos-ft/edit.php'; // ✅ CORRECTO
        $content = ob_get_clean();

        require __DIR__ . '/../Views/layouts/dashboard.php';

        return $response;
    }

    // ===============================
    // 🔄 ACTUALIZAR
    // ===============================
    public function update($request, $response, $args)
    {
        $id = $args["id"];
        $data = $request->getParsedBody();

        $this->db->update("procesos_ft", [
            "nombre" => $data["nombre"],
            "tipo" => $data["tipo"],
            "orden" => $data["orden"],
            "responsable" => $data["responsable"],
            "bod_origen" => $data["bod_origen"],
            "bod_destino" => $data["bod_destino"],
            "tipo_mov_salida" => $data["tipo_mov_salida"],
            "tipo_mov_entrada" => $data["tipo_mov_entrada"],
            "modo_tiempo" => $data["modo_tiempo"],
            "activo" => 1

        ], [
            "id" => $id
        ]);

        return $response
            ->withHeader("Location", "/procesos-ft")
            ->withStatus(302);
    }

    // ===============================
    // 🗑️ ELIMINAR
    // ===============================
    public function delete($request, $response, $args)
    {
        $id = $args["id"];

        $this->db->delete("procesos_ft", ["id" => $id]);

        return $response
            ->withHeader("Location", "/procesos-ft")
            ->withStatus(302);
    }

    public function show($request, $response, $args)
    {
        $id = $args['id'];

        $proceso = $this->db->get("procesos_ft", "*", ["id" => $id]);

        $title = "Detalle del Proceso";

        ob_start();
        require __DIR__ . '/../Views/procesos-ft/show.php';
        $content = ob_get_clean();

        require __DIR__ . '/../Views/layouts/dashboard.php';
        return $response;
    }
}
