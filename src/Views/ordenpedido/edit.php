<?php $title = "Editar OP # " . $p['documento']; ?>

<div class="max-w-7xl mx-auto my-4 px-4">
    <div class="flex items-center justify-between mb-3">
        <a href="/orden-pedido" class="flex items-center text-xs font-bold text-blue-600 hover:underline">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            VOLVER AL LISTADO
        </a>
    </div>

    <form action="/orden-pedido/update/<?= $p['documento'] ?>" method="POST" id="orderForm">
        <input type="hidden" name="codcp" value="<?= $p['codcp'] ?>">

        <div class="bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">
            
            <div class="bg-gray-800 p-4 text-white">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-[9px] font-bold text-gray-400 uppercase">Documento</label>
                        <div class="text-lg font-black text-white">OP-<?= $p['documento'] ?></div>
                    </div>
<div class="md:col-span-2">
    <label class="block text-[9px] font-bold text-gray-400 uppercase italic">Cliente</label>
    <input type="text" 
           value="<?= $p['codcp'] ?> - <?= !empty($p['cliente']) ? $p['cliente'] : 'Nombre no encontrado' ?>" 
           class="w-full bg-gray-100 border-none rounded text-xs p-1.5 text-gray-900 font-bold" 
           readonly>
</div>
                    <div>
                        <label class="block text-[9px] font-bold text-gray-400 uppercase">Sucursal</label>
                        <select name="codsuc" id="codsuc" class="w-full bg-gray-700 border-none rounded text-xs p-1 text-white focus:ring-1 focus:ring-blue-400">
                            <option value="<?= $p['codsuc'] ?>"><?= $p['codsuc'] ?> - Cargando...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-gray-400 uppercase">Estado</label>
                        <span class="text-[10px] bg-blue-500 px-2 py-0.5 rounded font-black">PENDIENTE</span>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-[9px] font-bold text-blue-300 uppercase italic">Fecha OP</label>
                        <input type="date" name="fecha" value="<?= $p['fecha'] ?>" class="w-full bg-gray-700 border-none rounded text-xs p-1 text-white">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-green-400 uppercase italic">Fecha Entrega</label>
                        <input type="date" name="fechent" value="<?= $p['fechent'] ?>" class="w-full bg-gray-700 border-none rounded text-xs p-1 text-white">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-[9px] font-bold text-gray-400 uppercase">Comentario General</label>
                        <input type="text" name="comen" value="<?= $p['comen'] ?>" class="w-full bg-gray-700 border-none rounded text-xs p-1 text-white" placeholder="Observaciones...">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto" style="max-height: 400px;">
                <table class="w-full text-left border-collapse" id="itemsTable">
                    <thead class="bg-gray-100 border-b sticky top-0 z-10">
                        <tr class="text-[9px] font-black text-gray-500 uppercase">
                            <th class="px-3 py-2">Referencia</th>
                            <th class="px-2 py-2 w-24 text-center">Talla</th>
                            <th class="px-2 py-2 w-32 text-center">Color</th>
                            <th class="px-2 py-2 w-20 text-center">Cant.</th>
                            <th class="px-2 py-2 w-28 text-right">Precio</th>
                            <th class="px-3 py-2 w-32 text-right">Subtotal</th>
                            <th class="px-2 py-2 w-8"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($detalles as $index => $d): ?>
                        <tr class="item-row bg-white hover:bg-blue-50/50" data-index="<?= $index ?>">
                            <td class="p-1 px-3">
                                <select name="items[<?= $index ?>][codr]" class="product-select w-full" required>
                                    <?php foreach ($productos as $prod): ?>
                                        <option value="<?= $prod['codr'] ?>" <?= $prod['codr'] == $d['codr'] ? 'selected' : '' ?>><?= $prod['descr'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="p-1 px-2">
                                <select name="items[<?= $index ?>][codtalla]" class="w-full border-gray-200 rounded text-[11px] p-0.5">
                                    <?php foreach ($tallas as $t): ?>
                                        <option value="<?= $t ?>" <?= $t == $d['codtalla'] ? 'selected' : '' ?>><?= $t ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="p-1 px-2">
                                <select name="items[<?= $index ?>][codcolor]" class="w-full border-gray-200 rounded text-[11px] p-0.5">
                                    <?php foreach ($colores as $c): ?>
                                        <option value="<?= $c ?>" <?= $c == $d['codcolor'] ? 'selected' : '' ?>><?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="p-1 px-2 text-center">
                                <input type="number" name="items[<?= $index ?>][cantidad]" value="<?= (int)$d['cantidad'] ?>" class="qty w-full border-gray-200 rounded text-[11px] p-0.5 text-center font-bold">
                            </td>
                            <td class="p-1 px-2 text-right">
                                <input type="number" name="items[<?= $index ?>][valor]" value="<?= (int)$d['valor'] ?>" class="price w-full border-gray-200 rounded text-[11px] p-0.5 text-right font-medium">
                            </td>
                            <td class="p-1 px-3 text-right font-bold text-[11px] text-gray-600 line-total">
                                $ <?= number_format($d['cantidad'] * $d['valor'], 2) ?>
                            </td>
                            <td class="p-1 px-2 text-center">
                                <button type="button" class="remove-row text-red-300 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                            </td>
                        </tr>
                        <tr class="comment-row" data-index="<?= $index ?>">
                            <td colspan="7" class="p-0 px-3 pb-1 border-b border-gray-100">
                                <input type="text" name="items[<?= $index ?>][comencpo]" value="<?= $d['comencpo'] ?>" class="w-full bg-transparent border-none text-[9px] text-blue-500 italic p-0 focus:ring-0">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="bg-blue-50 border-t border-blue-100 px-6 py-2 flex justify-between items-center text-blue-800">
                <div class="flex gap-6">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black uppercase text-blue-400">Total Ítems</span>
                        <span id="resTotalItems" class="text-sm font-black"><?= count($detalles) ?></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black uppercase text-blue-400">Cantidades Totales</span>
                        <span id="resTotalQty" class="text-sm font-black">0</span>
                    </div>
                </div>
                <div class="text-right">
                    <span id="resGrandTotal" class="text-xl font-black text-blue-700">$ 0.00</span>
                </div>
            </div>
            
            <div class="p-2 bg-white border-t">
                <button type="button" id="addRow" class="text-[10px] font-black text-blue-600 hover:text-blue-800 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                    INSERTAR NUEVA LÍNEA
                </button>
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-10 py-2.5 rounded text-xs font-black shadow-lg">
                ACTUALIZAR Y GUARDAR CAMBIOS
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('.product-select').select2();
    let rowIdx = <?= count($detalles) ?>;

    // --- LÓGICA DE SUCURSAL PARA EDICIÓN ---
    function cargarSucursalesEdicion() {
        const codcli = '<?= $p['codcp'] ?>';
        const sucursalActual = '<?= $p['codsuc'] ?>';
        const $sucSelect = $('#codsuc');

        if (!codcli) return;

        $.ajax({
            url: '/orden-pedido/sucursales/' + codcli,
            type: 'GET',
            success: function(response) {
                let options = '';
                const data = typeof response === 'string' ? JSON.parse(response) : response;

                if (data && data.length > 0) {
                    data.forEach(s => {
                        const selected = (s.codsuc == sucursalActual) ? 'selected' : '';
                        options += `<option value="${s.codsuc}" ${selected}>${s.codsuc} - ${s.nombresuc}</option>`;
                    });
                } else {
                    options = '<option value="01">01 - SEDE PRINCIPAL</option>';
                }
                $sucSelect.html(options);
            },
            error: function() {
                $sucSelect.html('<option value="01">01 - SEDE PRINCIPAL</option>');
            }
        });
    }

    cargarSucursalesEdicion(); // Ejecutar al cargar la página
    updateTotals(); // Calcular totales iniciales

    // El resto del JS (addRow, updateTotals, remove-row) se mantiene igual que tu versión funcional...
    $('#addRow').click(function() {
        const row = `
        <tr class="item-row bg-white" data-index="${rowIdx}">
            <td class="p-1 px-3">
                <select name="items[${rowIdx}][codr]" class="product-select w-full" required>
                    <option value="">Buscar...</option>
                    <?php foreach ($productos as $prod): ?>
                        <option value="<?= $prod['codr'] ?>"><?= $prod['descr'] ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td class="p-1 px-2">
                <select name="items[${rowIdx}][codtalla]" class="w-full border-gray-200 rounded text-[11px] p-0.5">
                    <?php foreach ($tallas as $t): ?> <option value="<?= $t ?>"><?= $t ?></option> <?php endforeach; ?>
                </select>
            </td>
            <td class="p-1 px-2">
                <select name="items[${rowIdx}][codcolor]" class="w-full border-gray-200 rounded text-[11px] p-0.5">
                    <?php foreach ($colores as $c): ?> <option value="<?= $c ?>"><?= $c ?></option> <?php endforeach; ?>
                </select>
            </td>
            <td class="p-1 px-2 text-center"><input type="number" name="items[${rowIdx}][cantidad]" value="1" class="qty w-full border-gray-200 rounded text-[11px] p-0.5 text-center font-bold"></td>
            <td class="p-1 px-2 text-right"><input type="number" name="items[${rowIdx}][valor]" value="0" class="price w-full border-gray-200 rounded text-[11px] p-0.5 text-right"></td>
            <td class="p-1 px-3 text-right font-bold text-[11px] text-gray-600 line-total">$ 0.00</td>
            <td class="p-1 px-2 text-center"><button type="button" class="remove-row text-red-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button></td>
        </tr>
        <tr class="comment-row" data-index="${rowIdx}">
            <td colspan="7" class="p-0 px-3 pb-1 border-b border-gray-100">
                <input type="text" name="items[${rowIdx}][comencpo]" placeholder="Nota..." class="w-full bg-transparent border-none text-[9px] text-blue-500 italic p-0 focus:ring-0">
            </td>
        </tr>`;
        $('#itemsTable tbody').append(row);
        $(`tr[data-index="${rowIdx}"] .product-select`).select2();
        rowIdx++;
        updateTotals();
    });

    $(document).on('change keyup', '.qty, .price', function() {
        const $row = $(this).closest('.item-row');
        const qty = parseFloat($row.find('.qty').val()) || 0;
        const price = parseFloat($row.find('.price').val()) || 0;
        $row.find('.line-total').text('$ ' + (qty * price).toLocaleString('en-US', {minimumFractionDigits: 2}));
        updateTotals();
    });

    $(document).on('click', '.remove-row', function() {
        const idx = $(this).closest('tr').data('index');
        $(`tr[data-index="${idx}"]`).remove();
        updateTotals();
    });

    function updateTotals() {
        let grandTotal = 0; let totalQty = 0; let totalItems = 0;
        $('.item-row').each(function() {
            const qty = parseFloat($(this).find('.qty').val()) || 0;
            const sub = parseFloat($(this).find('.line-total').text().replace(/[$,]/g, '')) || 0;
            totalQty += qty; grandTotal += sub; totalItems++;
        });
        $('#resTotalItems').text(totalItems);
        $('#resTotalQty').text(totalQty);
        $('#resGrandTotal').text('$ ' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2}));
    }
});
</script>