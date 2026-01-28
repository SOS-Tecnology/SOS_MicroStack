<?php
namespace App\Models;

class FichaTecnicaModel
{
    protected $db;
    public function __construct($db) { $this->db = $db; }

    public function create($data)
    {
        $this->db->insert("fichas_tecnicas", $data);
        return $this->db->id();
    }

    public function all()
    {
        return $this->db->select("fichas_tecnicas", [
            "[>]geclientes" => ["id_cliente" => "codcli"]
        ], [
            "fichas_tecnicas.id",
            "fichas_tecnicas.nombre_ficha",
            "fichas_tecnicas.id_cliente",
            "geclientes.nombrecli(nombre_cliente)"
        ]);
    }

    public function find($id)
    {
        return $this->db->get("fichas_tecnicas", "*", ["id" => $id]);
    }
}