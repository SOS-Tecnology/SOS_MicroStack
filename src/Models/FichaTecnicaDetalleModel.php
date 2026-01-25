<?php
namespace App\Models;

class FichaTecnicaDetalleModel {
    protected $db;
    public function __construct($db) { $this->db = $db; }

    public function create($data) {
        $this->db->insert("ficha_tecnica_detalles", $data);
        return $this->db->id();
    }

    public function byFicha($idFicha) {
        return $this->db->query("
            SELECT d.codr, i.descr, d.cantidad, d.talla, d.color
            FROM ficha_tecnica_detalles d
            JOIN inrefinv i ON d.codr = i.codr
            WHERE d.id_ficha_tecnica = {$idFicha}
        ")->fetchAll();
    }
}
