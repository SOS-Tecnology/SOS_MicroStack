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
            'h.docaux',
            'h.tmaux',
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
            "cabezamov.codcp",
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
                "id_producto_base" => $id_producto_base,
                "id_cliente" => $opr['codcp']
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
            "h.codcp",
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
                "id_producto_base" => $id_producto_base,
                "id_cliente" => $opr['codcp']
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
    public function generarPdf(Request $request, Response $response, array $args): Response
    {
        $documento = $args['documento'];
        $prefijo   = "OPR";

        $opr = $this->db->get("cabezamov", [
            "[>]geclientes" => ["codcp" => "codcli"]
        ], [
            "cabezamov.documento",
            "cabezamov.prefijo",
            "cabezamov.fecha",
            "cabezamov.codcp",
            "cabezamov.fechent",
            "cabezamov.estado",
            "cabezamov.comen",
            "cabezamov.docaux",
            "geclientes.nombrecli(cliente)"
        ], [
            "cabezamov.tm"        => "OPR",
            "cabezamov.documento" => $documento
        ]);

        if (!$opr) {
            die("OPR no encontrada");
        }

        $items = $this->db->select("cuerpomov", [
            "[>]inrefinv" => ["codr" => "codr"]
        ], [
            "cuerpomov.item",
            "cuerpomov.codr",
            "inrefinv.descr(producto_nombre)",
            "cuerpomov.codtalla",
            "cuerpomov.codcolor",
            "cuerpomov.cantidad"
        ], [
            "cuerpomov.tm"        => "OPR",
            "cuerpomov.documento" => $documento
        ]);

        // Procesos desde FT
        $procesos = [];
        $fts_vistos = [];

        foreach ($items as $it) {
            $ft = $this->db->get("fichas_tecnicas", "id", [
                "id_producto_base" => $this->db->get("inrefinv", "ref_fabrica", ["codr" => $it['codr']]),
                "id_cliente" => $opr['codcp']
            ]);
            if (!$ft || in_array($ft, $fts_vistos)) continue;
            $fts_vistos[] = $ft;

            $procs = $this->db->select("ficha_tecnica_procesos (ftp)", [
                "[>]procesos_ft (p)" => ["codigo_proceso" => "id"]
            ], [
                "ftp.id",
                "ftp.orden",
                "ftp.nombre_proceso",
                "ftp.ejecutable_en",
                "ftp.tiempo_minutos",
                "p.entrada_tipo",
                "p.salida_tipo"
            ], [
                "ftp.id_ficha_tecnica" => $ft,
                "ftp.activo"           => 1,
                "ORDER" => ["ftp.orden" => "ASC"]
            ]);

            foreach ($procs as $p) $procesos[] = $p;
        }

        $total_piezas = array_sum(array_column($items, 'cantidad'));

        // Fotos de todas las FTs de esta OPR
        $fotos = [];
        foreach ($fts_vistos as $ft_id) {
            $imgs = $this->db->select("ficha_tecnica_fotos", [
                "ruta_imagen",
                "descripcion"
            ], [
                "id_ficha_tecnica" => $ft_id
            ]);
            foreach ($imgs as $img) $fotos[] = $img;
        }
        ob_start();
        require __DIR__ . '/../Views/orden-produccion/pdf_opr.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
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
        $documento  = $args['documento'];
        $proceso_id = (int) $args['proceso'];

        // -------------------------------------------------------
        // 1. DATOS DEL PROCESO desde procesos_ft
        //    proceso_id = procesos_ft.id (confirmado)
        // -------------------------------------------------------
        $procesoData = $this->db->get("procesos_ft", [
            "id",
            "nombre",
            "entrada_tipo",
            "salida_tipo"
        ], [
            "id" => $proceso_id
        ]);

        if (!$procesoData) {
            return $response
                ->withHeader('Location', "/orden-produccion/avance/ver/{$documento}")
                ->withStatus(302);
        }

        // -------------------------------------------------------
        // 2. COMENTARIO ESPECÍFICO DE LA FT PARA ESTE PROCESO
        // -------------------------------------------------------
        $opr_codcp = $this->db->get("cabezamov", "codcp", [
            "tm"        => "OPR",
            "documento" => $documento
        ]);

        $primer_item = $this->db->get("cuerpomov (cm)", [
            "[>]inrefinv (r)" => ["codr" => "codr"]
        ], [
            "r.ref_fabrica"
        ], [
            "cm.tm"        => "OPR",
            "cm.documento" => $documento
        ]);

        $comentario_proceso = null;
        if ($primer_item && $opr_codcp) {
            $ft_id = $this->db->get("fichas_tecnicas", "id", [
                "id_producto_base" => $primer_item['ref_fabrica'],
                "id_cliente"       => $opr_codcp
            ]);
            if ($ft_id) {
                $ftp = $this->db->get("ficha_tecnica_procesos", ["comentario"], [
                    "id_ficha_tecnica" => $ft_id,
                    "codigo_proceso"   => $proceso_id
                ]);
                $comentario_proceso = $ftp['comentario'] ?? null;
            }
        }

        // -------------------------------------------------------
        // 3. ¿YA EXISTEN EPP para este OPR + proceso?
        // -------------------------------------------------------
        $existenEpp = $this->db->has("cabezamov", [
            "tm"         => "EPP",
            "tmaux"      => "OPR",
            "docaux"     => $documento,
            "proceso_id" => $proceso_id
        ]);

        // -------------------------------------------------------
        // 4. LISTADO DE MOVIMIENTOS EPP + RPP
        // -------------------------------------------------------
        $movimientos = $this->db->query("
        -- EPPs vinculados a esta OPR + proceso
        SELECT
            h.tm,
            h.tm        AS tipo_fila,
            h.documento,
            h.documento AS epp_doc,
            h.fecha,
            h.estado,
            h.proceso_id,
            p.nombre AS proveedor,
            COALESCE(SUM(CASE WHEN d.tipo_registro = 'META' THEN d.cantidad ELSE 0 END), 0) AS cantidad
        FROM cabezamov h
        LEFT JOIN provee p ON h.codcp = p.codp
        LEFT JOIN cuerpomov d
            ON  d.tm        = h.tm
            AND d.prefijo   = h.prefijo
            AND d.documento = h.documento
            AND d.tipo_registro = 'META'
        WHERE h.tm       = 'EPP'
          AND h.tmaux    = 'OPR'
          AND h.docaux   = :documento
          AND h.proceso_id = :proceso_id
        GROUP BY h.tm, h.documento, h.fecha, h.estado, h.proceso_id, p.nombre

        UNION ALL

        -- RPPs cuyo docaux apunta a alguno de esos EPPs
        SELECT
            h.tm,
            h.tm        AS tipo_fila,
            h.documento,
            h.docaux    AS epp_doc,
            h.fecha,
            h.estado,
            h.proceso_id,
            p.nombre AS proveedor,
            COALESCE(SUM(CASE WHEN d.tipo_registro = 'META' THEN d.cantidad ELSE 0 END), 0) AS cantidad
        FROM cabezamov h
        LEFT JOIN provee p ON h.codcp = p.codp
        LEFT JOIN cuerpomov d
            ON  d.tm        = h.tm
            AND d.prefijo   = h.prefijo
            AND d.documento = h.documento
            AND d.tipo_registro = 'META'
        WHERE h.tm    = 'RPP'
          AND h.tmaux = 'EPP'
          AND h.docaux IN (
              SELECT documento FROM cabezamov
              WHERE  tm        = 'EPP'
                AND  tmaux     = 'OPR'
                AND  docaux    = :documento2
                AND  proceso_id = :proceso_id2
          )
        GROUP BY h.tm, h.documento, h.docaux, h.fecha, h.estado, h.proceso_id, p.nombre

        ORDER BY epp_doc DESC, tipo_fila ASC, documento DESC
        ", [
                ':documento'   => $documento,
                ':proceso_id'  => $proceso_id,
                ':documento2'  => $documento,
                ':proceso_id2' => $proceso_id
            ])->fetchAll(\PDO::FETCH_ASSOC);

            // -------------------------------------------------------
            // 5. MÉTRICAS RESUMEN
            // -------------------------------------------------------

            // META: total de prendas en la OPR
            $meta = (int) $this->db->query("
            SELECT COALESCE(SUM(d.cantidad), 0)
            FROM cuerpomov d
            WHERE d.tm        = 'OPR'
            AND d.documento = :doc
        ", [':doc' => $documento])->fetchColumn();

            // EPP acumulado META para este proceso
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
            AND d.tipo_registro = 'META'
        ", [':doc' => $documento, ':pid' => $proceso_id])->fetchColumn();

            // RPP acumulado META para este proceso
            // ↓ FIX: tmaux = 'EPP' porque RPP referencia al EPP, no a la OPR
            $rpp_total = (int) $this->db->query("
            SELECT COALESCE(SUM(d.cantidad), 0)
            FROM cuerpomov d
            INNER JOIN cabezamov h
                ON  d.tm        = h.tm
                AND d.prefijo   = h.prefijo
                AND d.documento = h.documento
            WHERE h.tm         = 'RPP'
            AND h.tmaux      = 'EPP'
            AND h.proceso_id = :pid
            AND d.tipo_registro = 'META'
        ", [':pid' => $proceso_id])->fetchColumn();

        // -------------------------------------------------------
        // 6. RENDER
        // -------------------------------------------------------
        return renderView(
            $response,
            __DIR__ . '/../Views/orden-produccion/procesos.php',
            "Gestión de Proceso — OPR {$documento}",
            [
                'documento'          => $documento,
                'proceso'            => $procesoData['nombre'],
                'proceso_id'         => $proceso_id,
                'comentario_proceso' => $comentario_proceso,
                'existenEpp'         => $existenEpp,
                'movimientos'        => $movimientos,
                'meta'               => $meta,
                'epp_total'          => $epp_total,
                'rpp_total'          => $rpp_total,
            ]
        );
    }
}
