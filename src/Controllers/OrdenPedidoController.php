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
  //  "valortotal" => $total,
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
echo json_encode($sucursales);
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

public function show($request, $response, $args)
{
    $id = $args['id'];

    $pedido = $this->db->get("cabezamov", [
        "[>]geclientes" => ["codcp" => "codcli"]
    ], [
        "cabezamov.documento", 
        "cabezamov.codcp", 
        "cabezamov.codsuc", 
        "cabezamov.fecha", 
        "cabezamov.fechent", 
        "cabezamov.comen", 
        "cabezamov.estado",
        "cabezamov.valortotal", // <--- AGREGAR ESTA LÍNEA
        "geclientes.nombrecli(cliente)" 
    ], [
        "cabezamov.documento" => $id,
        "cabezamov.tm" => "OP"
    ]);

    // 2. Detalles con JOIN para ver el nombre del producto
    $detalles = $this->db->select("cuerpomov", [
        "[>]inrefinv" => ["codr" => "codr"]
    ], [
        "cuerpomov.codr",
        "inrefinv.descr(producto_nombre)",
        "cuerpomov.codtalla",
        "cuerpomov.codcolor",
        "cuerpomov.cantidad",
        "cuerpomov.valor",
        "cuerpomov.comencpo"
    ], [
        "cuerpomov.documento" => $id,
        "cuerpomov.tm" => "OP"
    ]);

    // 3. Enviamos 'pedido' (que es lo que tu vista busca)
    return renderView($response, __DIR__ . '/../../src/Views/OrdenPedido/show.php', "Consulta OP #" . $id, [
        'pedido' => $pedido,
        'detalles' => $detalles
    ]);
}


public function edit($request, $response, $args)
{
    $id = $args['id'];

    // 1. Cabecera con JOIN para traer el nombre del cliente (Evita el error de nombre vacío)
    $pedido = $this->db->get("cabezamov", [
        "[>]geclientes" => ["codcp" => "codcli"]
    ], [
        "cabezamov.documento", 
        "cabezamov.codcp", 
        "cabezamov.codsuc", 
        "cabezamov.fecha", 
        "cabezamov.fechent", 
        "cabezamov.comen", 
        "geclientes.nombrecli(cliente)" // Esto llena $p['cliente']
    ], [
        "cabezamov.documento" => $id, 
        "cabezamov.tm" => "OP"
    ]);

    // 2. Detalles (cuerpomov) - Traemos los campos específicos
    // Asegúrate que los nombres coincidan con los de tu tabla
    $detalles = $this->db->select("cuerpomov", [
        "codr",
        "codtalla",
        "codcolor",
        "cantidad",
        "valor",
        "comencpo"
    ], [
        "documento" => $id, 
        "tm" => "OP"
    ]);

    // 3. Listas para los selects
    $clientes = $this->db->select("geclientes", ["codcli", "nombrecli"]);
    $productos = $this->db->select("inrefinv", ["codr", "descr"], ["tipoprod" => "V"]);
    
    $tallas = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '6', '8', '10', '12', '14', '16'];
    $colores = ['Blanco', 'Negro', 'Azul Navy', 'Rojo', 'Gris', 'Kaki', 'Verde Militar'];

    return renderView($response, __DIR__ . '/../../src/Views/OrdenPedido/edit.php', "Editar OP #" . $id, [
        'p' => $pedido,
        'detalles' => $detalles,
        'clientes' => $clientes,
        'productos' => $productos,
        'tallas' => $tallas,
        'colores' => $colores
    ]);
}
public function update($request, $response, $args)
{
    $id = $args['id'];
    $data = $request->getParsedBody();

    // 1. Calculamos el nuevo total
    $nuevoTotal = $this->calcularTotal($data['items']);

    // 2. Actualizamos la cabecera
    $this->db->update("cabezamov", [
        "fechent" => $data['fechent'],
        "comen" => $data['comen'],
        "valortotal" => $nuevoTotal,
        "fechamod" => date('Y-m-d')
    ], [
        "documento" => $id,
        "tm" => "OP"
    ]);

    // 3. Para los items, la política más sencilla es borrar los anteriores y re-insertar
    $this->db->delete("cuerpomov", ["documento" => $id, "tm" => "OP"]);

    foreach ($data['items'] as $index => $item) {
        if (empty($item['codr'])) continue;
        $this->db->insert("cuerpomov", [
            "tm" => "OP",
            "documento" => $id,
            "prefijo" => "OP",
            "codr" => $item['codr'],
            "codtalla" => $item['codtalla'],
            "codcolor" => $item['codcolor'],
            "cantidad" => $item['cantidad'],
            "valor" => $item['valor'],
            "comencpo" => $item['comencpo'],
            "item" => $index + 1
        ]);
    }

    return $response->withHeader('Location', '/orden-pedido')->withStatus(302);
}


public function generarPdf($request, $response, $args)
{
    $id = $args['id'];

    // 1. Obtener cabecera con el nombre del cliente
    $pedido = $this->db->get("cabezamov", [
        "[>]geclientes" => ["codcp" => "codcli"]
    ], [
        "cabezamov.documento", 
        "cabezamov.codcp", 
        "cabezamov.codsuc", 
        "cabezamov.fecha", 
        "cabezamov.fechent", 
        "cabezamov.comen", 
        "cabezamov.valortotal",
        "geclientes.nombrecli(cliente)" 
    ], [
        "cabezamov.documento" => $id,
        "cabezamov.tm" => "OP"
    ]);

    // 2. Obtener detalles con el nombre del producto
    $detalles = $this->db->select("cuerpomov", [
        "[>]inrefinv" => ["codr" => "codr"]
    ], [
        "cuerpomov.codr",
        "inrefinv.descr(producto_nombre)",
        "cuerpomov.codtalla",
        "cuerpomov.codcolor",
        "cuerpomov.cantidad",
        "cuerpomov.valor",
        "cuerpomov.comencpo"
    ], [
        "cuerpomov.documento" => $id,
        "cuerpomov.tm" => "OP"
    ]);

    // 3. Configurar Dompdf
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true); // Para cargar logos o imágenes
    $dompdf = new \Dompdf\Dompdf($options);

    // 4. Renderizar la vista HTML (crearemos este archivo a continuación)
    ob_start();
    require __DIR__ . '/../../src/Views/OrdenPedido/pdf_template.php';
    $html = ob_get_clean();

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // 5. Enviar al navegador
    $response->getBody()->write($dompdf->output());
    return $response
        ->withHeader('Content-Type', 'application/pdf')
        ->withHeader('Content-Disposition', 'inline; filename="OP-' . $id . '.pdf"');
}
}
