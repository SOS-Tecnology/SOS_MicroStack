<?php

namespace App\Controllers;

class RppController
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // =============================================================
    // CREATE — Cargar formulario de nueva RPP a partir de una EPP
    // =============================================================
    public function create($request, $response, $args)
    {
        $epp = $args['epp'];

        // -------------------------------------------------------
        // 1. CABECERA EPP: proceso, satélite, OPR
        // -------------------------------------------------------
        $cab = $this->db->get("cabezamov", [
            "[>]procesos_ft" => ["proceso_id" => "id"],
            "[>]satelites"   => ["satelite_id" => "id"],
            "[>]provee"      => ["satelites.id_proveedor" => "codp"]
        ], [
            "cabezamov.tm",
            "cabezamov.prefijo",
            "cabezamov.documento",
            "cabezamov.fecha",
            "cabezamov.estado",
            "cabezamov.proceso_id",
            "cabezamov.codcp",
            "cabezamov.docaux",       // número OPR
            "cabezamov.prefaux",
            "cabezamov.tmaux",
            "cabezamov.satelite_id",
            "cabezamov.opr_id",
            "procesos_ft.nombre(proceso_nombre)",
            "provee.nombre(satelite_nombre)"
        ], [
            "cabezamov.tm"        => "EPP",
            "cabezamov.prefijo"   => "EPP",
            "cabezamov.documento" => $epp
        ]);

        if (!$cab) {
            $_SESSION['error'] = "EPP no encontrada: $epp";
            return $response->withHeader('Location', '/orden-produccion')->withStatus(302);
        }

        // -------------------------------------------------------
        // 2. CLIENTE — leer de la OPR referenciada en la EPP
        // -------------------------------------------------------
        $cliente = '';
        if (!empty($cab['docaux'])) {
            $opr_cab = $this->db->get("cabezamov", [
                "[>]geclientes" => ["codcp" => "codcli"]
            ], [
                "geclientes.nombrecli(cliente)"
            ], [
                "cabezamov.tm"        => "OPR",
                "cabezamov.documento" => $cab['docaux']
            ]);
            $cliente = $opr_cab['cliente'] ?? '';
        }

        // -------------------------------------------------------
        // 3. DETALLE EPP
        //    cantent = acumulador de recibos anteriores (todas las RPPs previas)
        //    pendiente = cantidad - cantent
        //    Solo se muestran líneas con pendiente > 0
        // -------------------------------------------------------
        $items = $this->db->select("cuerpomov (cm)", [
            "[>]inrefinv (r)" => ["cm.codr" => "codr"]
        ], [
            "cm.ID",
            "cm.codr(coditem)",
            "cm.codcolor(color)",
            "cm.codtalla(talla)",
            "cm.cantidad",
            "cm.cantent",
            "cm.tipo_registro",
            "r.descr(descripcion)"
        ], [
            "cm.tm"        => "EPP",
            "cm.prefijo"   => "EPP",
            "cm.documento" => $epp
        ]);

        $mp    = [];  // Materia Prima — en la RPP es retorno de sobrante
        $metis = [];  // META — producción terminada que devuelve el satélite

        foreach ($items as $d) {
            $recibido  = floatval($d['cantent'] ?? 0);
            $pendiente = floatval($d['cantidad']) - $recibido;

            if ($pendiente <= 0) continue;

            $d['recibido']  = $recibido;
            $d['pendiente'] = $pendiente;

            if ($d['tipo_registro'] === 'MP') {
                $mp[] = $d;
            } else {
                // META y SOBRANTE van a la tabla de producción
                $metis[] = $d;
            }
        }

        // -------------------------------------------------------
        // 4. CONSECUTIVO RPP
        // -------------------------------------------------------
        $last    = $this->db->max("cabezamov", "documento", [
            "tm"      => "RPP",
            "prefijo" => "RPP"
        ]);
        $numero  = $last ? intval($last) + 1 : 1;
        $nextRpp = str_pad($numero, 8, "0", STR_PAD_LEFT);

        // -------------------------------------------------------
        // 5. OPR formateado para mostrar
        // -------------------------------------------------------
        $oprFormateado = str_pad($cab['docaux'] ?? '', 8, "0", STR_PAD_LEFT);

        return renderView(
            $response,
            __DIR__ . '/../Views/rpp/create.php',
            "Recepción Proceso (RPP)",
            [
                'cab'     => $cab,
                'mp'      => $mp,
                'metis'   => $metis,
                'nextRpp' => $nextRpp,
                'opr'     => $oprFormateado,
                'cliente' => $cliente
            ]
        );
    }

    // =============================================================
    // STORE — Guardar la RPP
    // =============================================================
    public function store($request, $response)
    {
        $data      = $request->getParsedBody();
        $documento = str_pad($data['documento'], 8, "0", STR_PAD_LEFT);
        $epp       = $data['epp'];

        if (empty($data['fecha'])) {
            $_SESSION['error'] = "Debe ingresar la fecha de recibo";
            return $response
                ->withHeader('Location', '/rpp/create/' . $epp)
                ->withStatus(302);
        }

        // -------------------------------------------------------
        // 1. CABECERA RPP
        // -------------------------------------------------------
        $this->db->insert("cabezamov", [
            "tm"          => "RPP",
            "prefijo"     => "RPP",
            "documento"   => $documento,
            "fecha"       => $data['fecha'],
            "codcp"       => $data['codcp']    ?? null,
            "comen"       => $data['comen']    ?? '',
            "docaux"      => $epp,
            "prefaux"     => "EPP",
            "tmaux"       => "EPP",
            "opr_id"      => $data['opr']      ?? null,
            "proceso_id"  => $data['proceso']  ?? null,
            "satelite_id" => $data['satelite'] ?? null,
            "estado"      => "",
            "fechacrea"   => date('Y-m-d H:i:s'),
            "usuacrea"    => $_SESSION['usuario'] ?? 'sistema'
        ]);

        // -------------------------------------------------------
        // 2. DETALLE RPP + acumular cantent en EPP
        // -------------------------------------------------------
        if (!empty($data['detalle']) && is_array($data['detalle'])) {
            foreach ($data['detalle'] as $d) {
                $cantidad = floatval($d['cantidad'] ?? 0);
                if ($cantidad <= 0) continue;

                $codr     = $d['codr']         ?? null;
                $codtalla = $d['codtalla']      ?? '';
                $codcolor = $d['codcolor']      ?? '';
                $tipo     = $d['tipo_registro'] ?? 'META';

                // Insertar línea en RPP
                $this->db->insert("cuerpomov", [
                    "tm"            => "RPP",
                    "prefijo"       => "RPP",
                    "documento"     => $documento,
                    "codr"          => $codr,
                    "codtalla"      => $codtalla,
                    "codcolor"      => $codcolor,
                    "cantidad"      => $cantidad,
                    "cantent"       => 0,
                    "tipo_registro" => $tipo,
                    "docaux"        => $epp,
                    "prefaux"       => "EPP",
                    "tmaux"         => "EPP"
                ]);

                // Acumular en cantent de la EPP
                // cantent es el acumulador oficial de recibos parciales
                $this->db->update("cuerpomov", [
                    "cantent[+]" => $cantidad
                ], [
                    "tm"        => "EPP",
                    "prefijo"   => "EPP",
                    "documento" => $epp,
                    "codr"      => $codr,
                    "codtalla"  => $codtalla,
                    "codcolor"  => $codcolor
                ]);
            }
        }

        // -------------------------------------------------------
        // 3. Volver al avance de la OPR
        // -------------------------------------------------------
        $opr = str_pad($data['opr'] ?? '', 8, "0", STR_PAD_LEFT);

        return $response
            ->withHeader('Location', '/orden-produccion/avance/ver/' . $opr)
            ->withStatus(302);
    }
}
