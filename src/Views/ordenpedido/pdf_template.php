<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; color: #1a56db; }
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 4px; vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { background: #f3f4f6; padding: 8px; border: 1px solid #ddd; text-align: left; font-size: 9px; }
        .items-table td { padding: 6px; border: 1px solid #ddd; }
        .total-box { margin-top: 20px; text-align: right; border-top: 2px solid #444; padding-top: 10px; }
        .text-bold { font-weight: bold; }
        .text-blue { color: #1a56db; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td class="title">ORDEN DE PEDIDO # <?= $pedido['documento'] ?></td>
                <td align="right" class="text-bold">Fecha: <?= date('d/m/Y', strtotime($pedido['fecha'])) ?></td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%" class="text-bold">CLIENTE:</td>
            <td width="45%"><?= $pedido['codcp'] ?> - <?= $pedido['cliente'] ?></td>
            <td width="15%" class="text-bold">ENTREGA:</td>
            <td width="25%"><?= date('d/m/Y', strtotime($pedido['fechent'])) ?></td>
        </tr>
        <tr>
            <td class="text-bold">SUCURSAL:</td>
            <td><?= $pedido['codsuc'] ?> - Principal</td>
            <td class="text-bold">ESTADO:</td>
            <td>PENDIENTE</td>
        </tr>
        <tr>
            <td class="text-bold">OBSERVACIÓN:</td>
            <td colspan="3"><?= $pedido['comen'] ?: 'Ninguna' ?></td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>REF / PRODUCTO</th>
                <th width="50" align="center">TALLA</th>
                <th width="80" align="center">COLOR</th>
                <th width="40" align="center">CANT.</th>
                <th width="70" align="right">PRECIO</th>
                <th width="80" align="right">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalQty = 0;
            foreach($detalles as $d): 
                $sub = $d['cantidad'] * $d['valor'];
                $totalQty += $d['cantidad'];
            ?>
            <tr>
                <td>
                    <span class="text-bold"><?= $d['codr'] ?></span><br>
                    <small><?= $d['producto_nombre'] ?></small>
                    <?php if(!empty($d['comencpo'])): ?>
                        <br><i style="font-size: 8px; color: #666;">Nota: <?= $d['comencpo'] ?></i>
                    <?php endif; ?>
                </td>
                <td align="center"><?= $d['codtalla'] ?></td>
                <td align="center"><?= $d['codcolor'] ?></td>
                <td align="center" class="text-bold"><?= number_format($d['cantidad'], 0) ?></td>
                <td align="right">$ <?= number_format($d['valor'], 0) ?></td>
                <td align="right" class="text-bold">$ <?= number_format($sub, 0) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-box">
        <table width="100%">
            <tr>
                <td align="left">TOTAL ÍTEMS: <span class="text-bold"><?= count($detalles) ?></span></td>
                <td align="left">TOTAL UNIDADES: <span class="text-bold"><?= number_format($totalQty, 0) ?></span></td>
                <td align="right" class="text-blue" style="font-size: 16px;">
                    TOTAL: <span class="text-bold">$ <?= number_format($pedido['valortotal'], 0) ?></span>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generado automáticamente por SOS MicroStack - <?= date('d/m/Y H:i') ?>
    </div>
</body>
</html>