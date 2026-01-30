<?php

namespace App\Controllers;

class OrdenPedidoController
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    public function index($request, $response)
    {
        $params = $request->getQueryParams();
        $filtro = $params['estado'] ?? 'PENDIENTE';

        // Base de la consulta
        $where = ["cabezamov.tm" => "OP"];

        // Lógica de filtrado por estados específicos
        if ($filtro === 'PENDIENTE') {
            $where["cabezamov.estado[!]"] = ['C', 'A']; // Diferente de Cerrada y Anulada
        } elseif ($filtro === 'C') {
            $where["cabezamov.estado"] = 'C';
        } elseif ($filtro === 'A') {
            $where["cabezamov.estado"] = 'A';
        }
        // Si es 'ALL', no añadimos restricción de estado

        $pedidos = $this->db->select("cabezamov", [
            "[>]geclientes" => ["codcp" => "codcli"]
        ], [
            "cabezamov.documento",
            "cabezamov.prefijo",
            "cabezamov.fecha",
            "cabezamov.fechent",
            "geclientes.nombrecli(cliente)",
            "cabezamov.valortotal",
            "cabezamov.estado"
        ], [
            "AND" => $where,
            "ORDER" => ["cabezamov.fecha" => "DESC"]
        ]);

        return renderView($response, __DIR__ . '/../../src/Views/OrdenPedido/index.php', "Órdenes de Pedido", [
            'pedidos' => $pedidos,
            'filtroActual' => $filtro
        ]);
    }

    public function create($request, $response)
    {
        $clientes = $this->db->select("geclientes", ["codcli", "nombrecli"]);
        // Filtramos solo productos de clase 'V'
        $productos = $this->db->select("inrefinv", [
            "codr",
            "descr"
        ], [
            "tipoprod" => "V"
        ]);
        // Listas manuales para Talla y Color como pediste
        $tallas = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '6', '8', '10', '12', '14', '16'];
        $colores = ['Blanco', 'Negro', 'Azul Navy', 'Rojo', 'Gris', 'Kaki', 'Verde Militar'];

        return renderView($response, __DIR__ . '/../../src/Views/OrdenPedido/create.php', "Nueva Orden de Pedido", [
            'clientes' => $clientes,
            'productos' => $productos,
            'tallas' => $tallas,
            'colores' => $colores
        ]);
    }

// En OrdenPedidoController.php

public function store($request, $response) {
    $data = $request->getParsedBody();
    
    // Consecutivo para el documento OP
    $ultimo = $this->db->max("cabezamov", "documento", ["tm" => "OP"]) ?: 0;
    $nuevoDoc = str_pad((int)$ultimo + 1, 8, "0", STR_PAD_LEFT);

    // Insertar Cabecera
// En OrdenPedidoController.php -> store()
$this->db->insert("cabezamov", [
    "tm" => "OP",
    "documento" => $nuevoDoc,
    "prefijo" => "OP",
    "codcp" => $data['codcp'],
    "codsuc" => $data['codsuc'] ?: '01', // Aseguramos sucursal
    "fecha" => $data['fecha'],
    "fechent" => $data['fechent'],
    "comen" => $data['comen'] ?? '',
    "valortotal" => $total,
    "estado" => " ", // Espacio en blanco para Pendiente
    "usuario" => $_SESSION['user_id'] ?? 'ADMIN',
    "fechacrea" => date('Y-m-d'),
    "horacrea" => date('H:i:s'),
    "bodega" => "01", // Campo común en estos sistemas
    "vendedor" => "01",
    "moneda" => "PESOS"
]);

    // Insertar Detalle
    foreach ($data['items'] as $index => $item) {
        if (empty($item['codr'])) continue;

        $this->db->insert("cuerpomov", [
            "tm" => "OP",
            "documento" => $nuevoDoc,
            "prefijo" => "OP",
            "codr" => $item['codr'],
            "codtalla" => $item['codtalla'],
            "codcolor" => $item['codcolor'],
            "cantidad" => $item['cantidad'],
            "valor" => $item['valor'],
            "comencpo" => $item['comencpo'], // Comentario del item
            "item" => $index + 1
        ]);
    }

    return $response->withHeader('Location', '/orden-pedido')->withStatus(302);
}
    // En OrdenPedidoController.php
    public function getSucursales($request, $response, $args)
    {
        $codcli = $args['codcli'];
        $sucursales = $this->db->select("geclientesaux", ["codsuc", "nombresuc"], ["codcli" => $codcli]);

        $response->getBody()->write(json_encode($sucursales));
        return $response->withHeader('Content-Type', 'application/json');
    }
    // Función auxiliar para sumar el total en el servidor
    private function calcularTotal($items)
    {
        $total = 0;
        foreach ($items as $item) {
            $total += ($item['cantidad'] * $item['valor']);
        }
        return $total;
    }
}
