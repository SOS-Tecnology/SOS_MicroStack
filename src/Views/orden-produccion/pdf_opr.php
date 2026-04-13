<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>OPR #<?= $opr['documento'] ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; padding: 24px; max-width: 900px; margin: 0 auto; }

        .print-bar { display: flex; justify-content: flex-end; margin-bottom: 16px; }
        .btn-print { background: #1e3a5f; color: white; border: none; padding: 8px 20px; border-radius: 6px; font-size: 13px; cursor: pointer; }

        .header { border-bottom: 2px solid #1e3a5f; padding-bottom: 12px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-end; }
        .doc-title { font-size: 20px; font-weight: bold; color: #1e3a5f; }
        .doc-meta  { font-size: 11px; color: #6b7280; text-align: right; line-height: 1.6; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 24px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 16px; margin-bottom: 16px; }
        .info-row  { display: flex; gap: 6px; }
        .info-label { font-weight: bold; color: #374151; min-width: 80px; }
        .info-full  { grid-column: 1 / -1; }

        .section-title { font-size: 12px; font-weight: bold; color: #1e3a5f; text-transform: uppercase; letter-spacing: .5px; margin: 16px 0 6px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #1e3a5f; color: white; padding: 7px 8px; text-align: left; font-size: 10px; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        tr:nth-child(even) td { background: #f9fafb; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-planta   { background: #dbeafe; color: #1e40af; }
        .badge-satelite { background: #fef3c7; color: #92400e; }
        .badge-ambos    { background: #d1fae5; color: #065f46; }
        .badge-mp   { background: #fee2e2; color: #991b1b; }
        .badge-inter{ background: #fef3c7; color: #92400e; }
        .badge-pt   { background: #d1fae5; color: #065f46; }

        .totals { display: flex; justify-content: space-between; border-top: 2px solid #1e3a5f; padding-top: 10px; margin-bottom: 24px; }
        .total-final { font-size: 15px; font-weight: bold; color: #1e3a5f; }

        /* Fotos */
        .fotos-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px; }
        .foto-item { border: 1px solid #e5e7eb; border-radius: 4px; overflow: hidden; }
        .foto-item img { width: 100%; height: 160px; object-fit: cover; display: block; }
        .foto-caption { padding: 4px 6px; font-size: 9px; color: #6b7280; text-align: center; background: #f9fafb; }

        .footer { text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; margin-top: 16px; }

        @media print {
            .print-bar { display: none !important; }
            body { padding: 0; }
            th { background: #1e3a5f !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tr:nth-child(even) td { background: #f9fafb !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .foto-item { break-inside: avoid; }
            .fotos-grid { grid-template-columns: repeat(3, 1fr); }
        }
    </style>
</head>
<body>

<div class="print-bar">
    <button class="btn-print" onclick="imprimirConFotos()">🖨 Imprimir / Guardar PDF</button>
</div>

<div class="header">
    <div>
        <div class="doc-title">ORDEN DE PRODUCCIÓN &nbsp;#<?= htmlspecialchars($opr['documento']) ?></div>
        <div style="font-size:11px;color:#6b7280;margin-top:4px;">OP origen: #<?= htmlspecialchars($opr['docaux']) ?></div>
    </div>
    <div class="doc-meta">
        Fecha OPR: <strong><?= date('d/m/Y', strtotime($opr['fecha'])) ?></strong><br>
        Entrega: <strong><?= date('d/m/Y', strtotime($opr['fechent'])) ?></strong>
    </div>
</div>

<div class="info-grid">
    <div class="info-row">
        <span class="info-label">Cliente:</span>
        <span><?= htmlspecialchars($opr['cliente']) ?></span>
    </div>
    <div class="info-row">
        <span class="info-label">Estado:</span>
        <span><?= htmlspecialchars($opr['estado'] ?: 'En proceso') ?></span>
    </div>
    <?php if (!empty($opr['comen'])): ?>
    <div class="info-row info-full">
        <span class="info-label">Observaciones:</span>
        <span><?= nl2br(htmlspecialchars($opr['comen'])) ?></span>
    </div>
    <?php endif; ?>
</div>

<!-- PRENDAS -->
<div class="section-title">Prendas a fabricar</div>
<table>
    <thead>
        <tr>
            <th>Referencia</th>
            <th>Descripción</th>
            <th class="text-center" width="50">Talla</th>
            <th class="text-center" width="70">Color</th>
            <th class="text-center" width="50">Cant.</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $d): ?>
        <tr>
            <td><strong><?= htmlspecialchars($d['codr']) ?></strong></td>
            <td><?= htmlspecialchars($d['producto_nombre']) ?></td>
            <td class="text-center"><?= htmlspecialchars($d['codtalla']) ?></td>
            <td class="text-center"><?= htmlspecialchars($d['codcolor']) ?></td>
            <td class="text-center"><strong><?= number_format($d['cantidad'], 0) ?></strong></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="totals">
    <span style="color:#6b7280">Ítems: <strong style="color:#111"><?= count($items) ?></strong></span>
    <span class="total-final">Total piezas: <?= number_format($total_piezas, 0) ?></span>
</div>

<!-- PROCESOS -->
<?php if (!empty($procesos)): ?>
<div class="section-title">Procesos de fabricación</div>
<table>
    <thead>
        <tr>
            <th width="40">Orden</th>
            <th>Proceso</th>
            <th width="90">Ejecución</th>
            <th width="70">Entrada</th>
            <th width="70">Salida</th>
            <th class="text-center" width="60">Min/und</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($procesos as $p): ?>
        <tr>
            <td class="text-center"><?= $p['orden'] + 1 ?></td>
            <td><strong><?= htmlspecialchars($p['nombre_proceso']) ?></strong></td>
            <td>
                <?php $badgeEj = match($p['ejecutable_en']) {
                    'PLANTA'   => 'badge-planta',
                    'SATELITE' => 'badge-satelite',
                    default    => 'badge-ambos'
                }; ?>
                <span class="badge <?= $badgeEj ?>"><?= $p['ejecutable_en'] ?></span>
            </td>
            <td>
                <?php $badgeEnt = match($p['entrada_tipo'] ?? '') {
                    'MP'    => 'badge-mp',
                    'INTER' => 'badge-inter',
                    'PT'    => 'badge-pt',
                    default => ''
                }; ?>
                <span class="badge <?= $badgeEnt ?>"><?= $p['entrada_tipo'] ?? '—' ?></span>
            </td>
            <td>
                <?php $badgeSal = match($p['salida_tipo'] ?? '') {
                    'INTER' => 'badge-inter',
                    'PT'    => 'badge-pt',
                    default => ''
                }; ?>
                <span class="badge <?= $badgeSal ?>"><?= $p['salida_tipo'] ?? '—' ?></span>
            </td>
            <td class="text-center"><?= $p['tiempo_minutos'] ?? '—' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<!-- FOTOS -->
<?php if (!empty($fotos)): ?>
<div class="section-title">Fotos de referencia</div>
<div class="fotos-grid">
    <?php foreach ($fotos as $foto): ?>
    <div class="foto-item">
        <img src="<?= BASE_URL . '/' . ltrim($foto['ruta_imagen'], '/') ?>"
             alt="Foto ficha técnica"
             onerror="this.parentElement.style.display='none'">
        <?php if (!empty($foto['descripcion'])): ?>
            <div class="foto-caption"><?= htmlspecialchars($foto['descripcion']) ?></div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="footer">
    SOS MicroStack &nbsp;·&nbsp; Generado el <?= date('d/m/Y H:i') ?>
</div>

<script>
function imprimirConFotos() {
    const imgs = document.querySelectorAll('img');
    if (imgs.length === 0) { window.print(); return; }

    let pendientes = Array.from(imgs).filter(img => !img.complete).length;
    if (pendientes === 0) { window.print(); return; }

    let cargadas = 0;
    imgs.forEach(img => {
        if (!img.complete) {
            img.addEventListener('load',  () => { cargadas++; if (cargadas >= pendientes) window.print(); });
            img.addEventListener('error', () => { cargadas++; if (cargadas >= pendientes) window.print(); });
        }
    });
}
</script>

</body>
</html>