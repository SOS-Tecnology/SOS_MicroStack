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
            'h.estadorm',
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
}
