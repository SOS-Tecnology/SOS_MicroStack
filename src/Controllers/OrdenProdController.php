<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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

}
