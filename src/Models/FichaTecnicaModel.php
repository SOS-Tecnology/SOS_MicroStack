<?php
namespace App\Models;

class FichaTecnicaModel {
    protected $db;
    public function __construct($db) { $this->db = $db; }

    public function create($data) {
        $this->db->insert("fichas_tecnicas", $data);
        return $this->db->id();
    }

    public function all() {
        return $this->db->select("fichas_tecnicas", "*");
    }
}
