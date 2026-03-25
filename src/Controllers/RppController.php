<?php

namespace App\Controllers;

class RppController
{

    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($request, $response, $args)
    {
        $epp = $args['epp'];

        // ===============================
        // 1. CABECERA EPP + PROCESO + SATELITE
        // ===============================
        $cab = $this->db->get("cabezamov", [

            "[>]procesos_ft" => ["proceso_id" => "id"],
            "[>]satelites"   => ["satelite_id" => "id"],
            "[>]provee"      => ["satelites.id_proveedor" => "codp"]

        ], [

            // ===============================
            // CABEZAMOV
            // ===============================
            "cabezamov.tm",
            "cabezamov.prefijo",
            "cabezamov.documento",
            "cabezamov.fecha",
            "cabezamov.estado",
            "cabezamov.proceso_id",
            "cabezamov.codcp",
            "cabezamov.docaux",
            "cabezamov.prefaux",
            "cabezamov.tmaux",
            "cabezamov.satelite_id",

            // ===============================
            // PROCESO
            // ===============================
            "procesos_ft.nombre(proceso_nombre)",

            // ===============================
            // SATELITE / PROVEEDOR 🔥
            // ===============================
            "provee.nombre(satelite_nombre)"

        ], [
            "cabezamov.tm"        => "EPP",
            "cabezamov.prefijo"   => "EPP",
            "cabezamov.documento" => $epp
        ]);

        if (!$cab) {
            $_SESSION['error'] = "EPP no encontrado";
            return $response->withHeader('Location', '/orden-produccion')->withStatus(302);
        }

        // ===============================
        // 2. DETALLE EPP
        // ===============================
        $det = $this->db->select("cuerpomov", "*", [
            "tm"        => "EPP",
            "prefijo"   => "EPP",
            "documento" => $epp
        ]);

        $items = $this->db->select('cuerpomov (cm)', [
            '[>]inrefinv (r)' => ['codr' => 'codr']
        ], [
            'cm.codr(coditem)',
            'cm.codcolor(color)',
            'cm.codtalla(talla)',
            'cm.cantidad',
            'cm.valor',
            'r.descr(descripcion)'
        ], [
            "cm.tm"        => "EPP",
            "cm.prefijo"   => "EPP",
            "cm.documento" => $epp
        ]);

        // ===============================
        // 3. SEPARAR MP vs METIS 🔥
        // (AJUSTA EL CAMPO SEGÚN TU BD)
        // ===============================
        $mp = [];
        $metis = [];

        foreach ($items as $d) {

            // EJEMPLO: si no tiene talla → es MP
            if (empty($d['codtalla'])) {
                $mp[] = $d;
            } else {
                $metis[] = $d;
            }
        }
        // ===============================
        // 4. FORMATO OPR
        // ===============================
        $oprFormateado = str_pad($cab['docaux'], 6, "0", STR_PAD_LEFT);

        // ===============================
        // 5. CONSECUTIVO RPP
        // ===============================
        $last = $this->db->max("cabezamov", "documento", [
            "tm" => "RPP",
            "prefijo" => "RPP"
        ]);

        $numero = $last ? intval(substr($last, 3)) + 1 : 1;
        $nextRpp = str_pad($numero, 8, "0", STR_PAD_LEFT);

        // ===============================
        // 6. ENVIAR A VISTA
        // ===============================
        return renderView(
            $response,
            __DIR__ . '/../Views/rpp/create.php',
            "Recepción Proceso (RPP)",
            [
                'cab' => $cab,
                'mp'  => $mp,
                'metis' => $metis,
                'nextRpp' => $nextRpp,
                'opr' => $oprFormateado
            ]
        );
    }

    public function store($request, $response)
    {
        $data = $request->getParsedBody();

        $documento = str_pad($data['documento'], 8, "0", STR_PAD_LEFT);

        // CABECERA
        $this->db->insert("cabezamov", [

            "tm"        => "RPP",
            "prefijo"   => "RPP",
            "documento" => $documento,

            "fecha"     => $data['fecha'],

            "codcp"     => $data['codcp'] ?? null,
            "comen"     => $data['comen'] ?? '',

            "docaux"    => $data['epp'],
            "prefaux"   => "EPP",
            "tmaux"     => "EPP",

            "opr_id"     => $data['opr'] ?? null,
            "proceso_id" => $data['proceso'] ?? null,
            "satelite_id" => $data['satelite'] ?? null,

            "fechacrea" => date('Y-m-d H:i:s'),
            "usuacrea"  => $_SESSION['usuario'] ?? 'sistema'

        ]);

        // DETALLE
        if (!empty($data['detalle'])) {

            foreach ($data['detalle'] as $d) {

                $this->db->insert("cuerpomov", [

                    "tm"        => "RPP",
                    "prefijo"   => "RPP",
                    "documento" => $documento,

                    "codr"      => $d['codr'],
                    "codtalla"  => $d['codtalla'] ?? null,
                    "codcolor"  => $d['codcolor'] ?? null,

                    "cantidad"  => $d['cantidad'],
                    "cantent"   => $d['cantent'] ?? 0,

                    "unidad"    => $d['unidad'] ?? null,
                    "tipo_registro" => $d['tipo_registro'] ?? 'META',

                    "docaux" => $data['epp'],
                    "prefaux" => "EPP",
                    "tmaux"  => "EPP"

                ]);
            }
        }

        return $response
            ->withHeader('Location', '/orden-produccion/avance')
            ->withStatus(302);
    }
}
