<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OrdenProdController
{
    protected $db;
    protected $container;
    protected $view;

    public function __construct($container)
    {
        // Medoo
        $this->db = $container->get('db');

        // Render de vistas (tu helper renderView o similar)
        $this->view = $container->get('view');
    }

    /**
     * Listado de Órdenes de Pedido elegibles para Producción
     */
    public function index(Request $request, Response $response)
    {
        $ordenesPedido = $this->db->query("
            SELECT 
                h.documento,
                h.prefijo,
                h.codcp       AS cliente_id,
                h.nombre      AS cliente_nombre,
                h.fecha       AS fecha_op,
                h.fechent     AS fecha_entrega,
                h.estado,
                h.entregado,

                (
                    SELECT COUNT(*) 
                    FROM cabezamov o
                    WHERE o.tmaux   = h.tm
                      AND o.docaux  = h.documento
                      AND o.prefaux = h.prefijo
                      AND o.tm      = 'OPR'
                ) AS tiene_opr

            FROM cabezamov h
            WHERE h.tm = 'OP'
            ORDER BY h.fecha DESC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        return $this->view->render(
            $response,
            'orden_produccion/index.php',
            [
                'ordenesPedido' => $ordenesPedido,
                'title' => 'Órdenes de Producción'
            ]
        );
    }
}
