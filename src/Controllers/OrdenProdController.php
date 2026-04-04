<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\OprModel;

class OrdenProdController
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /*
    |--------------------------------------------------------------------------
    | LISTADO OP
    |--------------------------------------------------------------------------
    */
    public function index(Request $request, Response $response)
    {
        $sql = "
            SELECT 
                h.documento,
                h.prefijo,
                h.fecha      AS fecha_op,
                h.fechent    AS fecha_entrega,
                h.codcp      AS cliente_id,
                c.nombrecli  AS cliente_nombre
            FROM cabezamov h
            INNER JOIN geclientes c ON c.codcli = h.codcp
            WHERE h.tm = 'OP'
            ORDER BY h.fecha DESC
        ";


        $ordenes = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($ordenes as &$orden) {
            $orden['tiene_opr'] = $this->db->count('cabezamov', [
                'tm'     => 'OPR',
                'docaux' => $orden['documento']
            ]);
        }

        return renderView(
            $response,
            __DIR__ . '/../Views/orden-produccion/index.php',
            'Ordenes de Producción',
            ['ordenes' => $ordenes]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(Request $request, Response $response, array $args)
    {
        $documento = $args['documento'];

        $op = $this->db->get('cabezamov (h)', [
            '[>]geclientes (c)' => ['codcp' => 'codcli']
        ], [
            'h.documento',
            'h.prefijo',
            'h.fecha',
            'h.fechent',
            'h.codcp',
            'h.comen',
            'c.nombrecli(nombrecli)'
        ], [
            'h.tm'        => 'OP',
            'h.documento' => $documento
        ]);

        if (!$op) {
            throw new \Exception("Orden OP no encontrada");
        }

        $items = $this->db->select('cuerpomov (cm)', [
            '[>]inrefinv (r)' => ['codr' => 'codr']
        ], [
            'cm.ID',
            'cm.codr',
            'cm.cantidad',
            'cm.codcolor',
            'cm.codtalla',
            'cm.comencpo',
            'cm.valor',
            'r.descr(descripcion)'
        ], [
            'cm.tm'        => 'OP',
            'cm.documento' => $documento
        ]);



        // Totales
        $totalItems = count($items);

        $cantidadTotal = 0;
        foreach ($items as $it) {
            $cantidadTotal += (float)$it['cantidad'];
        }
        // var_dump($items);
        // exit;

        return renderView(
            $response,
            __DIR__ . '/../Views/orden-produccion/create.php',
            'Crear OPR',
            [
                'op'             => $op,
                'items'          => $items,
                'totalItems'     => $totalItems,
                'cantidadTotal'  => $cantidadTotal

            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, Response $response, array $args)
    {
        $documentoOP = $args['documento'];
        $data        = $request->getParsedBody();

        $op = $this->db->get('cabezamov', '*', [
            'tm'        => 'OP',
            'documento' => $documentoOP
        ]);

        if (!$op) {
            throw new \Exception("OP no encontrada");
        }

        $ultimo = $this->db->query("
            SELECT MAX(CAST(documento AS UNSIGNED)) as max_doc
            FROM cabezamov
            WHERE tm = 'OPR'
        ")->fetch();

        $numero = (int) ($ultimo['max_doc'] ?? 0);

        $nuevoDocumento = str_pad($numero + 1, 8, '0', STR_PAD_LEFT);



        $this->db->pdo->beginTransaction();

        try {

            $this->db->insert('cabezamov', [
                'tm'        => 'OPR',
                'documento' => $nuevoDocumento,
                'prefijo'   => $op['prefijo'],
                'fecha'     => date('Y-m-d'),
                'codcp'     => $op['codcp'],
                'tmaux'     => 'OP',
                'docaux'    => $op['documento'],
                'prefaux'   => $op['prefijo'],
                'fechent'   => $op['fechent'],
                'comen'     => $data['comentario'] ?? $op['comen']
            ]);


            $items = $this->db->select('cuerpomov (cm)', [
                '[>]inrefinv (r)' => ['codr' => 'codr']
            ], [
                'cm.codr',
                'cm.cantidad',
                'cm.codcolor',
                'cm.codtalla',
                'cm.comencpo',
                'cm.valor'
            ], [
                'cm.tm'        => 'OP',
                'cm.documento' => $documentoOP,
                'r.prototipo'  => 0
            ]);


            foreach ($items as $item) {
                $this->db->insert('cuerpomov', [
                    'tm'        => 'OPR',
                    'documento' => $nuevoDocumento,
                    'prefijo'   => $op['prefijo'],
                    'codr'      => $item['codr'],
                    'cantidad'  => $item['cantidad'],
                    'cantent'   => 0,
                    'codcolor'  => $item['codcolor'],
                    'codtalla'  => $item['codtalla'],
                    'comencpo'  => $item['comencpo'],
                    'valor'     => $item['valor']
                ]);
            }


            $this->db->pdo->commit();
        } catch (\Exception $e) {
            $this->db->pdo->rollBack();
            throw $e;
        }

        return $response
            ->withHeader('Location', '/orden-produccion')
            ->withStatus(302);
    }

    public function seguimiento(Request $request, Response $response, array $args)
    {
        $oprs = $this->db->select('cabezamov (h)', [
            '[>]geclientes (c)' => ['codcp' => 'codcli']
        ], [
            'h.documento',
            'h.fecha',
            'h.fechent',
            'h.estado',
            'c.nombrecli(nombrecli)'
        ], [
            'h.tm' => 'OPR',
            'ORDER' => ['h.fecha' => 'DESC']
        ]);

        return renderView(
            $response,
            __DIR__ . '/../Views/orden-produccion/seguimiento.php',
            'Seguimiento OPR',
            [
                'oprs' => $oprs
            ]
        );
    }
    public function ver($request, $response, $args)
    {
        $documento = $args['documento'];
        $prefijo   = "OP";

        // ===============================
        // 1. CABECERA OPR
        // ===============================
        $opr = $this->db->get("cabezamov", [
            "[>]geclientes" => ["codcp" => "codcli"]
        ], [
            "cabezamov.documento",
            "cabezamov.prefijo",
            "cabezamov.fecha",
            "cabezamov.fechent",
            "cabezamov.estado",
            "cabezamov.comen",
            "cabezamov.tmaux",
            "cabezamov.docaux",
            "cabezamov.prefaux",
            "geclientes.nombrecli(cliente)"
        ], [
            "cabezamov.tm"        => "OPR",
            "cabezamov.documento" => $documento,
            "cabezamov.prefijo"   => $prefijo
        ]);

        if (!$opr) {
            die("No existe la OPR");
        }
        // ===============================
        // OP ORIGEN (desde tmaux/docaux/prefaux)
        // ===============================
        $op = $this->db->get("cabezamov", [
            "[>]geclientes" => ["codcp" => "codcli"]
        ], [
            "cabezamov.documento",
            "cabezamov.prefijo",
            "cabezamov.fecha",
            "cabezamov.fechent",
            "geclientes.nombrecli(cliente)"
        ], [
            "cabezamov.tm"        => $opr['tmaux'],
            "cabezamov.documento" => $opr['docaux'],
            "cabezamov.prefijo"   => $opr['prefaux']
        ]);
        // ===============================
        // 2. DETALLE + FT BASE
        // ===============================
        $items = $this->db->select("cuerpomov", [
            "[>]inrefinv" => ["codr" => "codr"]
        ], [
            "cuerpomov.item",
            "cuerpomov.codr",
            "inrefinv.descr(producto_nombre)",
            "inrefinv.ref_fabrica(id_producto_base)",
            "cuerpomov.codtalla",
            "cuerpomov.codcolor",
            "cuerpomov.comencpo",
            "cuerpomov.cantidad"
        ], [
            "cuerpomov.documento" => $documento,
            "cuerpomov.prefijo"   => $prefijo,
            "cuerpomov.tm"        => "OPR"
        ]);

        // ===============================
        // 3. AGRUPAR POR FICHA TECNICA
        // ===============================
        $fts = [];
        $materiales = [];

        foreach ($items as $it) {

            $id_producto_base = $it['id_producto_base'];

            // Buscar la ficha técnica real
            $ft = $this->db->get("fichas_tecnicas", "id", [
                "id_producto_base" => $id_producto_base
            ]);

            if (!$ft) continue;

            $ft_id = $ft;

            if (!isset($fts[$ft_id])) {
                $fts[$ft_id] = [
                    'cantidad_total' => 0,
                    'procesos' => [],
                    'fotos' => []
                ];
            }

            $fts[$ft_id]['cantidad_total'] += $it['cantidad'];

            // ===============================
            // MATERIALES DESDE FT
            // ===============================
            $detalles_ft = $this->db->select("ficha_tecnica_detalles", "*", [
                "id_ficha_tecnica" => $ft_id
            ]);

            foreach ($detalles_ft as $det) {

                $codr = $det['codr'];

                if (!isset($materiales[$codr])) {

                    $prod = $this->db->get("inrefinv", [
                        "descr",
                        "codprov"
                    ], [
                        "codr" => $codr
                    ]);

                    $prov = $this->db->get("provee", "nombre", [
                        "codp" => $prod['codprov']
                    ]);

                    $materiales[$codr] = [
                        'nombre' => $prod['descr'],
                        'cantidad' => 0,
                        'proveedor' => $prov
                    ];
                }

                // cantidad FT * cantidad OPR
                $materiales[$codr]['cantidad'] += $det['cantidad'] * $it['cantidad'];
            }
        }

        // ===============================
        // 4. PROCESOS + FOTOS POR FT
        // ===============================
        foreach ($fts as $ft_id => &$ft) {

            // PROCESOS
            $procesos = $this->db->select("ficha_tecnica_procesos (ftp)", [
                "[>]procesos_ft (pft)" => [
                    "codigo_proceso" => "id"
                ]
            ], [
                "ftp.orden",
                "ftp.nombre_proceso",
                "ftp.ejecutable_en",
                "ftp.tiempo_minutos",
                "ftp.comentario",
                "pft.modo_tiempo"
            ], [
                "ftp.id_ficha_tecnica" => $ft_id,
                "ftp.activo"           => 1,
                "ORDER" => ["ftp.orden" => "ASC"]
            ]);

            foreach ($procesos as $p) {

                $tiempo_unit = $p['tiempo_minutos'] ?? 0;
                $modo_tiempo = $p['modo_tiempo'] ?? 'POR_UNIDAD';

                if ($modo_tiempo === 'TIEMPO_FIJO') {
                    $tiempo_total = $tiempo_unit;
                    $cantidad_mostrar = '-';
                } else {
                    $tiempo_total = $tiempo_unit * $ft['cantidad_total'];
                    $cantidad_mostrar = $ft['cantidad_total'];
                }

                $ft['procesos'][] = [
                    'orden' => $p['orden'],
                    'proceso' => $p['nombre_proceso'],
                    'ejecutable_en' => $p['ejecutable_en'],
                    'tiempo_unit' => $tiempo_unit,
                    'cantidad' => $cantidad_mostrar,
                    'tiempo_total' => $tiempo_total,
                    'ft_id' => $ft_id,
                    'modo_tiempo' => $modo_tiempo,
                    'comentario' => $p['comentario']
                ];
            }

            // FOTOS
            $fotos = $this->db->select("ficha_tecnica_fotos", [
                "ruta_imagen",
                "descripcion"
            ], [
                "id_ficha_tecnica" => $ft_id
            ]);

            $ft['fotos'] = $fotos;
        }

        // ===============================
        // TOTALES
        // ===============================
        $total_items = count($items);
        $total_piezas = array_sum(array_column($items, 'cantidad'));

        return renderView(
            $response,
            __DIR__ . '/../Views/orden-produccion/ver.php',
            "OPR #" . $documento,
            [
                'opr' => $opr,
                'op'  => $op,   // 👈 AGREGAR ESTO
                'detalles' => $items,
                'materiales' => $materiales,
                'fts' => $fts,
                'total_items' => $total_items,
                'total_piezas' => $total_piezas
            ]
        );
    }
    public function crearEPP($request, $response, $args)
    {
        $documento = $args['documento'];
        $prefijo   = "OP";

        // OPR
        $opr = $this->db->get("cabezamov", "*", [
            "tm" => "OPR",
            "prefijo" => $prefijo,
            "documento" => $documento
        ]);

        // PROCESOS DISPONIBLES
        $procesos = $this->db->select("procesos_ft", "*", [
            "activo" => 1,
            "ORDER" => "nombre"
        ]);

        // RESPONSABLES
        $personal = $this->db->select("personal", [
            "id",
            "nombre"
        ]);

        // SATELITES
        $satelites = $this->db->select("satelites", [
            "id",
            "nombre"
        ]);

        // MATERIALES DESDE OPR
        $materiales = $this->db->select("cuerpomov", [
            "[>]inrefinv" => ["codr" => "codr"]
        ], [
            "cuerpomov.codr",
            "inrefinv.descr",
            "cuerpomov.cantidad"
        ], [
            "tm" => "OPR",
            "prefijo" => $prefijo,
            "documento" => $documento
        ]);

        return renderView(
            $response,
            __DIR__ . '/../Views/epp/create.php',
            "Crear EPP",
            [
                "opr" => $opr,
                "procesos" => $procesos,
                "personal" => $personal,
                "satelites" => $satelites,
                "materiales" => $materiales
            ]
        );
    }

    public function guardarEPP($request, $response)
    {
        $data = $request->getParsedBody();

        $prefijo = "EP";
        $tm      = "EPP";

        $numero = $this->db->max("cabezamov", "documento", [
            "tm" => $tm
        ]) + 1;

        // CABECERA
        $this->db->insert("cabezamov", [
            "tm" => $tm,
            "prefijo" => $prefijo,
            "documento" => $numero,
            "fecha" => $data['fecha'],
            "fechent" => $data['fechent'],
            "codresp" => $data['responsable'],
            "codsat" => $data['satelite'],
            "proceso" => $data['proceso'],
            "comen" => $data['observaciones'],

            "tmaux" => "OPR",
            "prefaux" => "OP",
            "docaux" => $data['opr']
        ]);

        // ENTREGA
        if (!empty($data['entrega_codr'])) {
            foreach ($data['entrega_codr'] as $i => $codr) {
                $this->db->insert("cuerpomov", [
                    "tm" => $tm,
                    "prefijo" => $prefijo,
                    "documento" => $numero,
                    "tipo" => "ENTREGA",
                    "codr" => $codr,
                    "cantidad" => $data['entrega_cant'][$i]
                ]);
            }
        }

        // META
        if (!empty($data['meta_codr'])) {
            foreach ($data['meta_codr'] as $i => $codr) {
                $this->db->insert("cuerpomov", [
                    "tm" => $tm,
                    "prefijo" => $prefijo,
                    "documento" => $numero,
                    "tipo" => "META",
                    "codr" => $codr,
                    "cantidad" => $data['meta_cant'][$i]
                ]);
            }
        }

        return $response
            ->withHeader("Location", "/epp/ver/" . $numero)
            ->withStatus(302);
    }


    public function avance(Request $request, Response $response, array $args)
    {
        // ===============================
        // 1. WHERE dinámico
        // ===============================
        $where = [
            'h.tm' => 'OPR',

            'GROUP' => [
                'h.documento',
                'h.fecha',
                'h.fechent',
                'h.estado',
                'c.nombrecli'
            ],

            'ORDER' => [
                'h.documento' => 'DESC'
            ]
        ];

        if (!empty($_GET['cliente'])) {
            $where['c.nombrecli[~]'] = $_GET['cliente'];
        }

        // ===============================
        // 2. CONSULTA PRINCIPAL
        // ===============================
        $oprs = $this->db->select('cabezamov (h)', [

            '[>]geclientes (c)' => ['codcp' => 'codcli'],

            '[>]cuerpomov (d)' => [
                'tm' => 'tm',
                'prefijo' => 'prefijo',
                'documento' => 'documento'
            ],

            // 👇 relación EPP
            '[>]cabezamov (epp)' => [
                'documento' => 'docaux'
            ]

        ], [

            'h.documento',
            'h.fecha',
            'h.fechent',
            'h.estado',

            // 👇 CLIENTE
            'c.nombrecli(nombrecli)',

            // 👇 OP (robusto con agregación)
            'op' => $this->db->raw('MAX(h.docaux)'),

            // 👇 TOTAL PRENDAS
            'total_prendas' => $this->db->raw('COALESCE(SUM(d.cantidad),0)'),

            // 👇 ESTADO EPP
            'tiene_epp' => $this->db->raw("
            COUNT(DISTINCT CASE 
                WHEN epp.tmaux = 'OPR' THEN epp.documento 
            END)
        ")

        ], $where);
        // echo '<pre>'; 
        // print_r($oprs); 
        // exit;
        // ===============================
        // 3. RENDER
        // ===============================
        return renderView(
            $response,
            __DIR__ . '/../Views/orden-produccion/avance.php',
            'Avance en OPR',
            [
                'oprs' => $oprs
            ]
        );
    }

    public function verAvance(Request $request, Response $response, array $args)
    {
        $documento = $args['documento'];
        $prefijo   = "OP";

        // ===============================
        // CABECERA OPR
        // ===============================
        $opr = $this->db->get("cabezamov (h)", [
            "[>]geclientes (c)" => ["codcp" => "codcli"]
        ], [
            "h.documento",
            "h.prefijo",
            "h.fecha",
            "h.fechent",
            "h.estado",
            "c.nombrecli(nombrecli)"
        ], [
            "h.tm"        => "OPR",
            "h.documento" => $documento,
            "h.prefijo"   => $prefijo
        ]);

        if (!$opr) {
            die("No existe la OPR");
        }

        // ===============================
        // DETALLE OPR
        // ===============================
        $items = $this->db->select("cuerpomov (d)", [
            "[>]inrefinv (i)" => ["codr" => "codr"]
        ], [
            "d.codr",
            "d.cantidad",
            "i.ref_fabrica(id_producto_base)"
        ], [
            "d.tm"        => "OPR",
            "d.documento" => $documento,
            "d.prefijo"   => $prefijo
        ]);

        // ===============================
        // AGRUPAR POR FT
        // ===============================
        $fts = [];

        foreach ($items as $it) {

            $id_producto_base = $it['id_producto_base'];

            $ft = $this->db->get("fichas_tecnicas", "id", [
                "id_producto_base" => $id_producto_base
            ]);

            if (!$ft) continue;

            if (!isset($fts[$ft])) {
                $fts[$ft] = [
                    'ft_id' => $ft,
                    'cantidad_total' => 0,
                    'procesos' => []
                ];
            }

            $fts[$ft]['cantidad_total'] += $it['cantidad'];
        }

        // ===============================
        // PROCESOS POR FT + AVANCE
        // ===============================
        foreach ($fts as $ft_id => &$ft) {

            $procesos = $this->db->select("ficha_tecnica_procesos (ftp)", [
                "[>]procesos_ft (p)" => ["codigo_proceso" => "id"]
            ], [
                "p.id(id_proceso)", // 👈 CLAVE
                "ftp.orden",
                "ftp.nombre_proceso",
                "ftp.ejecutable_en",
                "ftp.tiempo_minutos",
                "p.modo_tiempo"
            ], [
                "ftp.id_ficha_tecnica" => $ft_id,
                "ftp.activo" => 1,
                "ORDER" => ["ftp.orden" => "ASC"]
            ]);

            $ft['procesos'] = [];

            foreach ($procesos as &$p) {

                $tiempo_unit = $p['tiempo_minutos'] ?? 0;
                $modo_tiempo = $p['modo_tiempo'] ?? 'POR_UNIDAD';

                if ($modo_tiempo === 'TIEMPO_FIJO') {
                    $tiempo_total = $tiempo_unit;
                    $cantidad = 0;
                } else {
                    $tiempo_total = $tiempo_unit * $ft['cantidad_total'];
                    $cantidad = $ft['cantidad_total'];
                }

                // ===============================
                // EPP
                // ===============================
                // $p['epp'] = $this->db->sum("cuerpomov (d)", "d.cantidad", [

                //     "[>]cabezamov (h)" => [
                //         "d.tm"        => "h.tm",
                //         "d.prefijo"   => "h.prefijo",
                //         "d.documento" => "h.documento"
                //     ]

                // ], [
                //     "h.tm"         => "EPP",
                //     "h.tmaux"      => "OPR",
                //     "h.docaux"     => $documento,
                //     "h.proceso_id" => $p['id_proceso']
                // ]) ?? 0;


                $sql = "
                    SELECT SUM(d.cantidad) as total
                    FROM cuerpomov d
                    INNER JOIN cabezamov h 
                        ON d.tm = h.tm 
                        AND d.prefijo = h.prefijo 
                        AND d.documento = h.documento
                    WHERE 
                        h.tm = 'EPP'
                        AND h.tmaux = 'OPR'
                        AND h.docaux = :documento
                        AND h.proceso_id = :proceso
                ";

                $epp = $this->db->query($sql, [
                    ':documento' => $documento,
                    ':proceso'   => $p['id_proceso']
                ])->fetchColumn() ?? 0;

                // ===============================
                // RPP
                // ===============================

                $sql = "
                    SELECT SUM(d.cantidad) as total
                    FROM cuerpomov d
                    INNER JOIN cabezamov h 
                        ON d.tm = h.tm 
                        AND d.prefijo = h.prefijo 
                        AND d.documento = h.documento
                    WHERE 
                        h.tm = 'RPP'
                        AND h.tmaux = 'OPR'
                        AND h.docaux = :documento
                        AND h.proceso_id = :proceso
                ";

                $rpp = $this->db->query($sql, [
                    ':documento' => $documento,
                    ':proceso'   => $p['id_proceso']
                ])->fetchColumn() ?? 0;
                // ===============================
                // OTROS DATOS ÚTILES
                // ===============================
                $p['epp'] = (float) $epp;  // ← línea nueva
                $p['rpp'] = (float) $rpp;  // ← línea nueva
                $p['cantidad_proceso'] = $cantidad;
                $p['tiempo_total']     = $tiempo_total;

                // Guardar proceso
                $ft['procesos'][] = $p;
            }
        }

        $total_prendas = array_sum(array_column($items, 'cantidad'));

        return renderView(
            $response,
            __DIR__ . '/../Views/orden-produccion/ver_avance.php',
            "Avance OPR #" . $documento,
            [
                'opr' => $opr,
                'fts' => $fts,
                'total_prendas' => $total_prendas
            ]
        );
    }

 // ==============================================================
// REEMPLAZAR el método procesos() completo en OrdenProdController
// Cambios respecto a la versión anterior:
//   1. Busca proceso por ID (no por nombre) — evita el bug de null
//   2. Calcula meta, epp_total, rpp_total para las tarjetas resumen
//   3. Envía 'proceso' como nombre (para el título de la pantalla)
//   4. Firma con tipado completo (consistente con index/avance/verAvance)
// ==============================================================

public function procesos(Request $request, Response $response, array $args): Response
{
    $documento  = $args['documento'];           // número OPR ej: 00000005
    $proceso_id = (int) $args['proceso'];       // ID numérico del proceso ej: 110
// DEBUG TEMPORAL — borrar después
    $procesoData = $this->db->get("procesos_ft", ["id", "nombre"], [
        "id" => $proceso_id
    ]);

// die(json_encode([
//     'proceso_id_buscado' => $proceso_id,
//     'epp_en_bd' => $this->db->query("
//         SELECT documento, proceso_id 
//         FROM cabezamov 
//         WHERE tm = 'EPP' AND docaux = :doc
//         LIMIT 5
//     ", [':doc' => $documento])->fetchAll(\PDO::FETCH_ASSOC)
// ]));

    // -------------------------------------------------------
    // 1. DATOS DEL PROCESO (nombre para mostrar en el título)
    // -------------------------------------------------------
    $procesoData = $this->db->get("ficha_tecnica_procesos", ["id", "nombre_proceso AS nombre"], [
    "id" => $proceso_id   
    ]);

    // Si el proceso no existe redirigimos con error limpio
    if (!$procesoData) {
        return $response
            ->withHeader('Location', "/orden-produccion/avance/ver/{$documento}")
            ->withStatus(302);
    }

    // -------------------------------------------------------
    // 2. ¿YA EXISTEN EPP para este OPR + proceso?
    //    Controla si el botón RPP se habilita en la vista
    // -------------------------------------------------------
    $existenEpp = $this->db->has("cabezamov", [
        "tm"         => "EPP",
        "tmaux"      => "OPR",
        "docaux"     => $documento,
        "proceso_id" => $proceso_id
    ]);

    // -------------------------------------------------------
    // 3. LISTADO DE MOVIMIENTOS EPP + RPP
    // -------------------------------------------------------
    $movimientos = $this->db->select("cabezamov (h)", [
        "[>]provee (p)" => ["codcp" => "codp"]
    ], [
        "h.tm",
        "h.documento",
        "h.fecha",
        "h.estado",
        "h.proceso_id",
        "p.nombre(proveedor)"
    ], [
        "h.tm"         => ["EPP", "RPP"],
        "h.tmaux"      => "OPR",
        "h.docaux"     => $documento,
        "h.proceso_id" => $proceso_id,
        "ORDER"        => ["h.documento" => "DESC"]
    ]);

    // -------------------------------------------------------
    // 4. MÉTRICAS RESUMEN (meta / epp acumulado / rpp acumulado)
    //    Se usan en las 4 tarjetas de la vista
    // -------------------------------------------------------

    // META: total de prendas en la OPR
    $meta = (int) $this->db->query("
        SELECT COALESCE(SUM(d.cantidad), 0)
        FROM cuerpomov d
        WHERE d.tm        = 'OPR'
          AND d.documento = :doc
    ", [':doc' => $documento])->fetchColumn();

    // EPP acumulado para este proceso
    $epp_total = (int) $this->db->query("
        SELECT COALESCE(SUM(d.cantidad), 0)
        FROM cuerpomov d
        INNER JOIN cabezamov h
            ON  d.tm        = h.tm
            AND d.prefijo   = h.prefijo
            AND d.documento = h.documento
        WHERE h.tm         = 'EPP'
          AND h.tmaux      = 'OPR'
          AND h.docaux     = :doc
          AND h.proceso_id = :pid
    ", [':doc' => $documento, ':pid' => $proceso_id])->fetchColumn();

    // RPP acumulado para este proceso
    $rpp_total = (int) $this->db->query("
        SELECT COALESCE(SUM(d.cantidad), 0)
        FROM cuerpomov d
        INNER JOIN cabezamov h
            ON  d.tm        = h.tm
            AND d.prefijo   = h.prefijo
            AND d.documento = h.documento
        WHERE h.tm         = 'RPP'
          AND h.tmaux      = 'OPR'
          AND h.docaux     = :doc
          AND h.proceso_id = :pid
    ", [':doc' => $documento, ':pid' => $proceso_id])->fetchColumn();

    // -------------------------------------------------------
    // 5. RENDER
    // -------------------------------------------------------

    return renderView(
        $response,
        __DIR__ . '/../Views/orden-produccion/procesos.php',
        "Gestión de Proceso — OPR {$documento}",
        [
            'documento'   => $documento,
            'proceso'     => $procesoData['nombre'],  // nombre legible para el título
            'proceso_id'  => $proceso_id,
            'existenEpp'  => $existenEpp,
            'movimientos' => $movimientos,
            'meta'        => $meta,
            'epp_total'   => $epp_total,
            'rpp_total'   => $rpp_total,
        ]
    );

}   
}
