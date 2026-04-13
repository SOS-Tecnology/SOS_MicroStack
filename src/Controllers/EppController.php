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
            "cabezamov.documento",
            "cabezamov.fecha",
            "cabezamov.fechent",
            "cabezamov.estado",
            "cabezamov.codcp",
            "provee.nombre(proveedor)"
        ], [
            "cabezamov.tm" => "EPP",
            "ORDER" => ["cabezamov.documento" => "DESC"]
        ]);

        return renderView($response, __DIR__ . '/../Views/Epp/index.php', "Envíos a Proceso (EPP)", [
            'movimientos' => $movimientos
        ]);
    }

    // =========================
    // CREAR
    // =========================
    public function create($request, $response, $args = [])
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
            "nombre",
            "tipo",
            "modo_tiempo",
            "entrada_tipo",
            "salida_tipo"
        ]);

        $personal = $this->db->select("personal", [
            "id",
            "nombres",
            "apellidos",
            "cargo"
        ]);

        // Parámetros desde Avance OPR
        $opr_param = $args['documento'] ?? null;
        $ft_param = $args['ft_id'] ?? null;
        $proceso_param = isset($args['proceso']) ? (int)$args['proceso'] : null;

        // Resolver datos del proceso desde ficha_tecnica_procesos
        $proceso_data = null;
        if ($proceso_param) {
            $proceso_data = $this->db->get("procesos_ft", [
                "id",
                "nombre",
                "entrada_tipo",
                "salida_tipo",
                "requiere_satelite"
            ], [
                "id" => $proceso_param
            ]);

            // TRACE TEMPORAL — borrar después


        }
// Obtener comentario específico de la FT para este proceso
$comentario_proceso = null;
if ($proceso_param && $opr_param) {
    // Obtener FT de la OPR
    $opr_data = $this->db->get("cabezamov", ["codcp", "documento"], [
        "tm"        => "OPR",
        "documento" => $opr_param
    ]);

    if ($opr_data) {
        // Primer item de la OPR para resolver la FT
        $primer_item = $this->db->get("cuerpomov (cm)", [
            "[>]inrefinv (r)" => ["codr" => "codr"]
        ], [
            "r.ref_fabrica"
        ], [
            "cm.tm"        => "OPR",
            "cm.documento" => $opr_param
        ]);

        if ($primer_item) {
            $ft_id = $this->db->get("fichas_tecnicas", "id", [
                "id_producto_base" => $primer_item['ref_fabrica'],
                "id_cliente"       => $opr_data['codcp']
            ]);

            if ($ft_id) {
                $ftp = $this->db->get("ficha_tecnica_procesos", [
                    "comentario"
                ], [
                    "id_ficha_tecnica" => $ft_id,
                    "codigo_proceso"   => $proceso_param
                ]);
                $comentario_proceso = $ftp['comentario'] ?? null;
            }
        }
    }
}
        return renderView($response, __DIR__ . '/../Views/Epp/create.php', "Nuevo Envío a Proceso", [
            'consecutivo'  => $nuevoConsecutivo,
            'satelites'    => $satelites,
            'procesos'     => $procesos,
            'personal'     => $personal,
            'oprs'         => $oprs,
            'nextEpp'      => $nextEpp,
            'opr_param'    => $opr_param,
            'comentario_proceso' => $comentario_proceso,
            'proceso_param' => $proceso_param,   // ficha_tecnica_procesos.id (int)
            'proceso_data'  => $proceso_data,    // datos completos del proceso
        ]);
    }

    // =====================================================
    // GUARDAR DOCUMENTO EPP (ENVÍO A PROCESO)
    // =====================================================
    public function store($request, $response)
    {

        // -------------------------------------------------
        // 1. RECIBIR DATOS DEL FORMULARIO
        // -------------------------------------------------

        $data = $request->getParsedBody();
        $data['detalle'] = json_decode($data['detalle_json'], true);

        if (empty($data['fecha']) || empty($data['fechent'])) {
            $_SESSION['error'] = "Debe ingresar las fechas";

            return $response
                ->withHeader('Location', '/epp/create')
                ->withStatus(302);
        }

        if ($data['fechent'] < $data['fecha']) {
            $_SESSION['error'] = "Debe ingresar las fechas";

            return $response
                ->withHeader('Location', '/epp/create')
                ->withStatus(302);
        }
        //  echo "<pre>";
        //  print_r($request->getParsedBody());
        //  exit;

        // -------------------------------------------------
        // 2. FORMATEAR DOCUMENTO (00000001)
        // -------------------------------------------------

        $documento = str_pad($data['documento'], 8, "0", STR_PAD_LEFT);

        $codcp_opr = $this->db->get("cabezamov", "codcp", [
            "tm"        => "OPR",
            "prefijo"   => "OP",
            "documento" => $data['opr']
        ]);
        // -------------------------------------------------
        // 3. INSERTAR CABECERA
        // -------------------------------------------------

        $this->db->insert("cabezamov", [

            "tm"        => "EPP",
            "prefijo"   => "EPP",
            "documento" => $documento,

            "fecha"     => $data['fecha'],
            "fechent"   => $data['fechent'],

            "codcp"     => $codcp_opr,  // cliente o proveedor
            "comen"     => $data['comen'] ?? '',

            "tmaux"     => "OPR",
            "prefaux"   => "OP",
            "docaux"    => $data['opr'],

            "proceso_id"   => $data['proceso'] ?? 0,
            "satelite_id"  => $data['satelite'] ?? 0,
            "responsable_id" => $data['personal'] ?? 0,
            "comen"        => $data['observaciones'] ?? '',

            "estado"    => "",

            "fechacrea" => date('Y-m-d H:i:s'),
            "usuacrea"  => $_SESSION['usuario'] ?? 'sistema',

            "opr_id"     => $data['opr'] ?? null,
            "proceso_id" => $data['proceso'] ?? null,
            "satelite_id" => $data['satelite'] ?? null

        ]);

        $idMovimiento = $this->db->id();


        // -------------------------------------------------
        // 4. INSERTAR DETALLE
        // -------------------------------------------------

        if (!empty($data['detalle']) && is_array($data['detalle'])) {

            foreach ($data['detalle'] as $item) {

                // Evitar registros vacíos
                if (($item['cantidad'] ?? 0) <= 0) {
                    continue;
                }

                $this->db->insert("cuerpomov", [

                    "tm"        => "EPP",
                    "prefijo"   => "EPP",
                    "documento" => $documento,

                    // RELACIÓN CON OPR
                    "tmaux"     => "OPR",
                    "prefaux"   => "OP",
                    "docaux"    => $data['opr'],

                    "codr"        => $item['codr'] ?? null,
                    "codtalla"    => $item['codtalla'] ?? null,
                    "codcolor"    => $item['codcolor'] ?? null,
                    "unidad"       => $item['unidad'] ?? 'UND',
                    "proceso_id"  => $item['proceso_id'] ?? $data['proceso'] ?? null,

                    "cantidad"    => $item['cantidad'],
                    "cantent"     => 0,

                    "valor" => $item['valor_unitario'] ?? 0,

                    "tipo_registro" => $item['tipo_registro'] ?? 'META'
                ]);
            }
        }


        // -------------------------------------------------
        // 5. ACTUALIZAR CONSECUTIVO
        // -------------------------------------------------
        $consecutivo = $this->db->get("inconsemov", "ultmov", [
            "prefijo" => "EPP",
            "tipomv"  => "EPP"
        ]);

        $numero = intval($consecutivo) + 1;

        $nuevoConsecutivo = str_pad($numero, 8, "0", STR_PAD_LEFT);

        $this->db->update("inconsemov", [
            "ultmov" => $nuevoConsecutivo
        ], [
            "prefijo" => "EPP",
            "tipomv"  => "EPP"
        ]);

        // -------------------------------------------------
        // 6. REDIRECCIONAR
        // -------------------------------------------------

        return $response
            ->withHeader('Location', '/orden-produccion/avance/ver/' . $data['opr'])
            ->withStatus(302);
    }


    // =========================
    // VER
    // =========================
    public function show($request, $response, $args)
    {

        $documento = $args['documento'];

        // CABECERA + SATÉLITE + RESPONSABLE
        $cab = $this->db->get("cabezamov", [

            "[>]satelites"   => ["satelite_id" => "id"],
            "[>]provee"      => ["satelites.id_proveedor" => "codp"],
            "[>]personal"    => ["responsable_id" => "id"],
            "[>]procesos_ft" => ["proceso_id" => "id"]

        ], [

            // CABECERA
            "cabezamov.documento",
            "cabezamov.fecha",
            "cabezamov.fechent",
            "cabezamov.estado",
            "cabezamov.comen",
            "cabezamov.docaux",
            "cabezamov.proceso_id",

            // SATÉLITE (CORRECTO)
            "provee.nombre(satelite)",

            // OTROS
            "personal.nombres(responsable)",
            "procesos_ft.nombre(proceso_nombre)"

        ], [
            "cabezamov.tm"        => "EPP",
            "cabezamov.prefijo"   => "EPP",
            "cabezamov.documento" => $documento
        ]);

        // DETALLE
        $det = $this->db->select("cuerpomov", "*", [
            "tm"        => "EPP",
            "prefijo"   => "EPP",
            "documento" => $documento
        ]);


        $procesoNombre = $this->db->get("procesos_ft", "nombre", [
            "id" => $cab['proceso_id']
        ]);

        return renderView($response, __DIR__ . '/../Views/Epp/show.php', "Detalle EPP", [
            'cab' => $cab,
            'det' => $det,
            'documento' => $documento,
            'procesoNombre' => $procesoNombre
        ]);
    }

    // =========================
    // IMPRIMIR
    // =========================
    public function print($request, $response, $args)
    {

        $id = $args['documento'];
        $tm        = "EPP";
        $prefijo   = "EPP";
        $cab = $this->db->get("cabezamov", "*", [
            "documento" => $id,
            "tm" => $tm,
            "prefijo" => $prefijo
        ]);

        $det = $this->db->select("cuerpomov", "*", [
            "documento" => $id,
            "tm" => $tm,
            "prefijo" => $prefijo
        ]);

        return renderView($response, __DIR__ . '/../Views/Epp/print.php', "Impresión EPP", [
            'cab' => $cab,
            'det' => $det
        ]);
    }

    public function getDataByOpr($request, $response, $args)
    {
        $opr_id = $args['documento'];

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
            "cuerpomov.codcolor",
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
                "codcolor" => $it['codcolor'],
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
        $prefijo   = "OP";

        // ======================
        // CLIENTE
        // ======================

        $cliente = $this->db->get("cabezamov", [
            "[>]geclientes" => ["codcp" => "codcli"]
        ], [
            "geclientes.nombrecli"
        ], [
            "cabezamov.tm"        => "OPR",
            "cabezamov.documento" => $documento,
            "cabezamov.prefijo"   => $prefijo
        ]);

        // ======================
        // ITEMS META (PRODUCTOS)
        // ======================

        $items = $this->db->select("cuerpomov", [
            "[>]inrefinv" => ["codr" => "codr"]
        ], [
            "cuerpomov.codr",
            "inrefinv.descr(producto)",
            "inrefinv.ref_fabrica(id_producto_base)",
            "cuerpomov.codtalla",
            "cuerpomov.codcolor",
            "cuerpomov.cantidad"
        ], [
            "cuerpomov.documento" => $documento,
            "cuerpomov.prefijo"   => $prefijo,
            "cuerpomov.tm"        => "OPR"
        ]);

        $meta = [];
        $materiales = [];
        $codcp_opr = $this->db->get("cabezamov", "codcp", [
            "tm"        => "OPR",
            "documento" => $documento
        ]);
        foreach ($items as $it) {

            // ======================
            // META
            // ======================

            $meta[] = [
                "codr" => $it['codr'],
                "descr" => $it['producto'],
                "codtalla" => $it['codtalla'],
                "codcolor" => $it['codcolor'],
                "cantidad" => $it['cantidad']
            ];

            // ======================
            // BUSCAR FICHA TECNICA
            // ======================



            $ft_id = $this->db->get("fichas_tecnicas", "id", [
                "id_producto_base" => $it['id_producto_base'],
                "id_cliente"       => $codcp_opr   // ← regla FT siempre por cliente
            ]);
            if (!$ft_id) continue;

            // ======================
            // MATERIALES FT
            // ======================

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
                        "descr" => $prod['descr'] ?? '',
                        "unidad" => $prod['unid'] ?? '',
                        "cantidad" => 0
                    ];
                }

                // cantidad FT * cantidad OPR
                $materiales[$codr]['cantidad'] +=
                    $det['cantidad'] * $it['cantidad'];
            }
        }

        $response->getBody()->write(json_encode([

            "cliente"    => $cliente['nombrecli'] ?? '',
            "meta"       => $meta,
            "materiales" => array_values($materiales)

        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
