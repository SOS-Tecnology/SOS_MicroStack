<?php

namespace App\Controllers;

class EppController
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // =========================
    // LISTADO
    // =========================
    public function index($request, $response)
    {

        $movimientos = $this->db->select("cabezamov", [
            "[>]provee" => ["codcp" => "codp"]
        ], [
            "cabezamov.id",
            "cabezamov.documento",
            "cabezamov.fecha",
            "cabezamov.fechent",
            "cabezamov.estado",
            "cabezamov.codp",
            "provee.nombre(proveedor)"
        ], [
            "cabezamov.tm" => "EPP",
            "ORDER" => ["cabezamov.id" => "DESC"]
        ]);

        return renderView($response, __DIR__ . '/../Views/Epp/index.php', "Envíos a Proceso (EPP)", [
            'movimientos' => $movimientos
        ]);
    }

    // =========================
    // CREAR
    // =========================
    public function create($request, $response)
    {

        // Obtener consecutivo
        $ultimo = $this->db->get("inconsemov", "ultmov", [
            "prefijo" => "EPP",
            "tipomv"      => "EPP"
        ]);

        $nuevoConsecutivo = $ultimo ? $ultimo + 1 : 1;
        $nextEpp = $this->db->query("
            SELECT IFNULL(MAX(documento)+1,1) AS num
            FROM cabezamov
            WHERE tm='EPP'
            ")->fetch()['num'];
        $nextEpp = ($max ?? 0) + 1;

        $oprs = $this->db->select("cabezamov", [
            "[>]geclientes" => ["codcp" => "codcli"]
        ], [
            "cabezamov.documento",
            "cabezamov.fecha",
            "cabezamov.fechent",
            "cabezamov.estado",
            "cabezamov.codcp",
            "geclientes.nombrecli(cliente)"
        ], [
            "cabezamov.tm" => "OPR",
            "OR" => [
                "cabezamov.estado" => null,
                "cabezamov.estado[!]" => ["A", "T"]
            ],
            "ORDER" => ["cabezamov.documento" => "DESC"]
        ]);

        $satelites = $this->db->select("satelites", [
            "[>]provee" => ["id_proveedor" => "codp"]
        ], [
            "satelites.id",
            "satelites.especialidad",
            "provee.codp",
            "provee.nombre"
        ], [
            "satelites.estado" => "Activo"
        ]);

        $procesos = $this->db->select("procesos_ft", [
            "id",
            "nombre"
        ]);

        $responsables = $this->db->select("personal", [
            "id",
            "nombres",
            "apellidos",
            "cargo"
        ]);

        return renderView($response, __DIR__ . '/../Views/Epp/create.php', "Nuevo Envío a Proceso", [
            'consecutivo' => $nuevoConsecutivo,
            'satelites'   => $satelites,
            'procesos'    => $procesos,
            'responsables' => $responsables,
            'oprs'        => $oprs,
            'nextEpp'     => $nextEpp
        ]);
    }

    // =========================
    // GUARDAR
    // =========================
    public function store($request, $response)
    {

        $data = $request->getParsedBody();

        // =========================
        // INSERTAR CABECERA
        // =========================

        $this->db->insert("cabezamov", [

            "tm"        => "EPP",
            "prefijo"   => "EPP",
            "documento" => $data['documento'],
            "fecha"     => $data['fecha'],
            "fechent"   => $data['fechent'],
            "codp"      => $data['codp'],
            "comen"     => $data['comen'] ?? '',
            "estado"    => "Abierto",
            "fechacrea" => date('Y-m-d H:i:s'),
            "usucrea"   => $_SESSION['usuario'] ?? 'sistema'

        ]);

        $idMovimiento = $this->db->id();

        // =========================
        // INSERTAR DETALLE
        // =========================

        if (!empty($data['detalle']) && is_array($data['detalle'])) {

            foreach ($data['detalle'] as $item) {

                $this->db->insert("cuerpomov", [

                    "idcabezamov" => $idMovimiento,
                    "codr"        => $item['codr'] ?? null,
                    "codtalla"    => $item['codtalla'] ?? null,
                    "proceso_id"  => $item['proceso_id'] ?? null,
                    "cantidad"    => $item['cantidad'] ?? 0,
                    "cantent"     => 0,
                    "tipo_registro" => $item['tipo_registro'] ?? 'META',
                    "unidad"      => $item['unidad'] ?? null

                ]);
            }
        }

        // =========================
        // ACTUALIZAR CONSECUTIVO
        // =========================

        $this->db->update("inconsemov", [
            "ultmov[+]" => 1
        ], [
            "prefijo" => "EPP",
            "tipomv"  => "EPP"
        ]);

        return $response
            ->withHeader('Location', '/epp')
            ->withStatus(302);
    }
    // =========================
    // VER
    // =========================
    public function show($request, $response, $args)
    {

        $id = $args['id'];

        $cab = $this->db->get("cabezamov", "*", [
            "id" => $id
        ]);

        $det = $this->db->select("cuerpomov", "*", [
            "idcabezamov" => $id
        ]);

        return renderView($response, __DIR__ . '/../Views/Epp/show.php', "Detalle EPP", [
            'cab' => $cab,
            'det' => $det
        ]);
    }

    // =========================
    // IMPRIMIR
    // =========================
    public function print($request, $response, $args)
    {

        $id = $args['id'];

        $cab = $this->db->get("cabezamov", "*", [
            "id" => $id
        ]);

        $det = $this->db->select("cuerpomov", "*", [
            "idcabezamov" => $id
        ]);

        return renderView($response, __DIR__ . '/../Views/Epp/print.php', "Impresión EPP", [
            'cab' => $cab,
            'det' => $det
        ]);
    }

    public function getDataByOpr($request, $response, $args)
    {
        $opr_id = $args['id'];

        // META (productos)
        $meta = $this->db->select("cuerpomov", [
            "codr",
            "codtalla",
            "cantidad"
        ], [
            "prefijo" => "OPR",
            "documento" => $opr_id,
            "tipo_registro" => "META"
        ]);

        // MP (materiales)
        $mp = $this->db->select("cuerpomov", [
            "codr",
            "unidad",
            "cantidad"
        ], [
            "prefijo" => "OPR",
            "documento" => $opr_id,
            "tipo_registro" => "MP"
        ]);

        $response->getBody()->write(json_encode([
            "meta" => $meta,
            "mp"   => $mp
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
    public function getDataFromOpr($request, $response, $args)
    {
        $documento = $args['documento'];
        $prefijo   = "OP";

        // ===============================
        // 1. DETALLE OPR
        // ===============================
        $items = $this->db->select("cuerpomov", [
            "[>]inrefinv" => ["codr" => "codr"]
        ], [
            "cuerpomov.codr",
            "inrefinv.descr(producto_nombre)",
            "inrefinv.ref_fabrica(id_producto_base)",
            "cuerpomov.codtalla",
            "cuerpomov.cantidad"
        ], [
            "cuerpomov.documento" => $documento,
            "cuerpomov.prefijo"   => $prefijo,
            "cuerpomov.tm"        => "OPR"
        ]);

        $meta = [];
        $materiales = [];

        foreach ($items as $it) {

            // META (productos reales a fabricar)
            $meta[] = [
                "codr" => $it['codr'],
                "producto" => $it['producto_nombre'],
                "codtalla" => $it['codtalla'],
                "cantidad" => $it['cantidad']
            ];

            $id_producto_base = $it['id_producto_base'];

            $ft_id = $this->db->get("fichas_tecnicas", "id", [
                "id_producto_base" => $id_producto_base
            ]);

            if (!$ft_id) continue;

            $detalles_ft = $this->db->select("ficha_tecnica_detalles", "*", [
                "id_ficha_tecnica" => $ft_id
            ]);

            foreach ($detalles_ft as $det) {

                $codr = $det['codr'];

                if (!isset($materiales[$codr])) {

                    $prod = $this->db->get("inrefinv", [
                        "descr",
                        "unid"
                    ], [
                        "codr" => $codr
                    ]);

                    $materiales[$codr] = [
                        "codr" => $codr,
                        "nombre" => $prod['descr'],
                        "unidad" => $prod['unid'],
                        "cantidad" => 0
                    ];
                }

                $materiales[$codr]['cantidad'] +=
                    $det['cantidad'] * $it['cantidad'];
            }
        }

        $response->getBody()->write(json_encode([
            "meta" => $meta,
            "mp"   => array_values($materiales)
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
    public function getOprData($request, $response, $args)
    {

        $documento = $args['documento'];

        // META
        $meta = $this->db->select("cuerpomov", [
            "codr",
            "codtalla",
            "cantidad"
        ], [
            "prefijo" => "OP",
            "documento" => $documento,
            "tm" => "OPR",
            "tipo_registro" => "META"
        ]);

        // MP
        $mp = $this->db->select("cuerpomov", [
            "codr",
            "unidad",
            "cantidad"
        ], [
            "prefijo" => "OP",
            "documento" => $documento,
            "tm" => "OPR",
            "tipo_registro" => "MP"
        ]);

        $response->getBody()->write(json_encode([
            "meta" => $meta,
            "mp"   => $mp
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
