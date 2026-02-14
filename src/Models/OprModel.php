<?php

namespace App\Models;

use Medoo\Medoo;

class OprModel
{
    private $db;

    public function __construct(Medoo $db)
    {
        $this->db = $db;
    }

    /**
     * Obtener cabecera de la OPR
     */
    public function getCabecera($documento, $prefijo)
    {
        return $this->db->get("cabezamov", "*", [
            "TM" => "OPR",
            "prefijo" => $prefijo,
            "documento" => $documento
        ]);
    }

    /**
     * Obtener detalle de la OPR
     */
    public function getDetalle($documento, $prefijo)
    {
        return $this->db->select("cuerpomov", "*", [
            "TM" => "OPR",
            "prefijo" => $prefijo,
            "documento" => $documento
        ]);
    }

    /**
     * Obtener OPR completa (cabecera + detalle)
     */
    public function getOprCompleta($documento, $prefijo)
    {
        $cabecera = $this->getCabecera($documento, $prefijo);

        if (!$cabecera) {
            return null;
        }

        $detalle = $this->getDetalle($documento, $prefijo);

        return [
            "cabecera" => $cabecera,
            "detalle" => $detalle
        ];
    }
}
