<?php
namespace App\Models;

class FichaTecnicaDetalleModel {
    protected $db;
    public function __construct($db) { $this->db = $db; }

    public function create($data) {
        // Asegúrate que la tabla se llame 'ficha_tecnica_detalles' en tu DB
        $this->db->insert("ficha_tecnica_detalles", $data);
        return $this->db->id();
    }

    public function byFicha($idFicha) {
        return $this->db->select("ficha_tecnica_detalles", [
            "[>]inrefinv" => ["codr" => "codr"]
        ], [
            "ficha_tecnica_detalles.codr",
            "inrefinv.descr",
            "inrefinv.unid",
            "ficha_tecnica_detalles.cantidad",
            "ficha_tecnica_detalles.talla",
            "ficha_tecnica_detalles.color"
        ], [
            "id_ficha_tecnica" => $idFicha
        ]);
    }
}