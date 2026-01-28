<?php
namespace App\Models;

class FichaTecnicaFotoModel {
    protected $db;
    public function __construct($db) { $this->db = $db; }

    public function create($data) {
        $this->db->insert("ficha_tecnica_fotos", $data);
        return $this->db->id();
    }

    public function byFicha($idFicha) {
        return $this->db->select("ficha_tecnica_fotos", [
            "id",
            "ruta_imagen",
            "id_ficha_tecnica"
        ], [
            "id_ficha_tecnica" => $idFicha
        ]);
    }

    public function delete($id) {
        return $this->db->delete("ficha_tecnica_fotos", ["id" => $id]);
    }
}