<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OP #<?= $pedido['documento'] ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 24px;
            max-width: 900px;
            margin: 0 auto;
        }

        /* ---- BOTÓN IMPRIMIR (solo en pantalla) ---- */
        .print-bar {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 16px;
        }
        .btn-print {
            background: #1a56db;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-back {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
        }

        /* ---- ENCABEZADO ---- */
        .header {
            border-bottom: 2px solid #1a56db;
            padding-bottom: 12px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .doc-title { font-size: 20px; font-weight: bold; color: #1a56db; }
        .doc-fecha { font-size: 11px; color: #6b7280; }

        /* ---- DATOS GENERALES ---- */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 24px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 16px;
        }
        .info-row { display: flex; gap: 6px; }
        .info-label { font-weight: bold; color: #374151; min-width: 80px; }
        .info-value { color: #111827; }
        .info-full { grid-column: 1 / -1; }

        /* ---- TABLA ÍTEMS ---- */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .items-table th {
            background: #1a56db;
            color: white;
            padding: 7px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .items-table tr:nth-child(even) td { background: #f9fafb; }
        .ref { font-weight: bold; }
        .nota { font-size: 9px; color: #6b7280; font-style: italic; margin-top: 2px; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        /* ---- TOTALES ---- */
        .totals {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #1a56db;
            padding-top: 10px;
            margin-bottom: 24px;
        }
        .totals-left { font-size: 11px; color: #6b7280; }
        .totals-left span { font-weight: bold; color: #111827; }
        .total-final { font-size: 16px; font-weight: bold; color: #1a56db; }

        /* ---- FOOTER ---- */
        .footer {
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }

        /* ---- MEDIA PRINT ---- */
        @media print {
            .print-bar { display: none !important; }
            body { padding: 0; }
            .items-table th { background: #1a56db !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .items-table tr:nth-child(even) td { background: #f9fafb !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <!-- Barra de acciones (solo pantalla) -->
    <div class="print-bar">
        <a href="javascript:history.back()" class="btn-back">← Volver</a>
        <button class="btn-print" onclick="window.print()">🖨 Imprimir / Guardar PDF</button>
    </div>

    <!-- Encabezado -->
    <div class="header">
        <div>
            <div class="doc-title">ORDEN DE PEDIDO &nbsp;#<?= htmlspecialchars($pedido['documento']) ?></div>
        </div>
        <div class="doc-fecha">
            Fecha: <strong><?= date('d/m/Y', strtotime($pedido['fecha'])) ?></strong>
        </div>
    </div>

    <!-- Datos generales -->
    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">Cliente:</span>
            <span class="info-value"><?= htmlspecialchars($pedido['codcp']) ?> — <?= htmlspecialchars($pedido['cliente']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Entrega:</span>
            <span class="info-value"><?= date('d/m/Y', strtotime($pedido['fechent'])) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Sucursal:</span>
            <span class="info-value"><?= htmlspecialchars($pedido['codsuc']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Estado:</span>
            <span class="info-value">PENDIENTE</span>
        </div>
        <?php if (!empty($pedido['comen'])): ?>
        <div class="info-row info-full">
            <span class="info-label">Observación:</span>
            <span class="info-value"><?= nl2br(htmlspecialchars($pedido['comen'])) ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tabla de ítems -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Referencia / Producto</th>
                <th class="text-center" width="50">Talla</th>
                <th class="text-center" width="80">Color</th>
                <th class="text-center" width="45">Cant.</th>
                <th class="text-right"  width="80">Precio</th>
                <th class="text-right"  width="90">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalQty = 0;
            $totalVal = 0;
            foreach ($detalles as $d):
                $sub = $d['cantidad'] * $d['valor'];
                $totalQty += $d['cantidad'];
                $totalVal += $sub;
            ?>
            <tr>
                <td>
                    <div class="ref"><?= htmlspecialchars($d['codr']) ?></div>
                    <div><?= htmlspecialchars($d['producto_nombre']) ?></div>
                    <?php if (!empty(trim($d['comencpo']))): ?>
                        <div class="nota">Nota: <?= htmlspecialchars($d['comencpo']) ?></div>
                    <?php endif; ?>
                </td>
                <td class="text-center"><?= htmlspecialchars($d['codtalla']) ?></td>
                <td class="text-center"><?= htmlspecialchars($d['codcolor']) ?></td>
                <td class="text-center"><strong><?= number_format($d['cantidad'], 0) ?></strong></td>
                <td class="text-right">$ <?= number_format($d['valor'], 0) ?></td>
                <td class="text-right"><strong>$ <?= number_format($sub, 0) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totales -->
    <div class="totals">
        <div class="totals-left">
            Ítems: <span><?= count($detalles) ?></span> &nbsp;&nbsp;
            Unidades: <span><?= number_format($totalQty, 0) ?></span>
        </div>
        <div class="total-final">
            TOTAL: $ <?= number_format($pedido['valortotal'] ?: $totalVal, 0) ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        SOS MicroStack &nbsp;·&nbsp; Generado el <?= date('d/m/Y H:i') ?>
    </div>

</body>
</html>