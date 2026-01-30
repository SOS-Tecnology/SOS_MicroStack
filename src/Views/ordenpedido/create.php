<?php $title = "Nueva Orden de Pedido OP"; ?>

<form action="/orden-pedido/store" method="POST" id="orderForm" class="max-w-6xl mx-auto my-8">

    <div class="max-w-6xl mx-auto mb-4 flex items-center justify-between">
        <a href="/orden-pedido" class="flex items-center text-sm font-bold text-gray-400 hover:text-blue-600 transition-colors">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Regresar al Listado
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">

        <div class="bg-gray-50 border-b p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="bg-blue-600 text-white px-3 py-1 rounded-md text-xs font-black tracking-widest uppercase">Orden de Pedido</span>
                    <h1 class="text-3xl font-black text-gray-800 mt-2">OP # <span class="text-gray-400">NUEVO</span></h1>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Cliente</label>
                    <select name="codcp" id="codcli" class="select2-cliente w-full" required>
                        <option value="">Buscar cliente...</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?= $c['codcli'] ?>"><?= $c['nombrecli'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Sucursal</label>
                    <select name="codsuc" id="codsuc" class="w-full border-gray-300 rounded-lg text-sm">
                        <option value="">Seleccione cliente...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Fecha Solicitud</label>
                    <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" class="w-full border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Fecha Entrega</label>
                    <input type="date" name="fechent" class="w-full border-gray-300 rounded-lg text-sm">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Observaciones Generales</label>
                    <input type="text" name="comen" class="w-full border-gray-300 rounded-lg text-sm" placeholder="Notas del documento...">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="itemsTable">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr class="text-[10px] font-bold text-gray-600 uppercase">
                        <th class="px-4 py-3 w-1/3">Producto</th>
                        <th class="px-4 py-3">Talla</th>
                        <th class="px-4 py-3">Color</th>
                        <th class="px-4 py-3 text-center">Cant.</th>
                        <th class="px-4 py-3">Precio</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                        <th class="px-4 py-3 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-white border-t">
            <button type="button" id="addRow" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 shadow-md flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                AGREGAR ÍTEM
            </button>
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-4">
        <button type="submit" class="bg-green-600 text-white px-10 py-3 rounded-xl font-black shadow-lg hover:bg-green-700 transition-all">GUARDAR ORDEN</button>
    </div>
</form>
<script>
    $(document).ready(function() {
        $('.select2-cliente').select2();

        // SUCURSALES: Conexión reforzada
        $('#codcli').on('change', function() {
            const codcli = $(this).val();
            const $sucSelect = $('#codsuc');

            $sucSelect.html('<option value="">Cargando sucursales...</option>');

            if (!codcli) return;

            $.ajax({
                url: '/orden-pedido/sucursales/' + codcli,
                type: 'GET',
                success: function(response) {
                    let options = '';
                    // Si la respuesta es string, la convertimos a JSON
                    const data = typeof response === 'string' ? JSON.parse(response) : response;

                    if (data && data.length > 0) {
                        data.forEach(s => {
                            options += `<option value="${s.codsuc}">${s.codsuc} - ${s.nombresuc}</option>`;
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
        });
        let rowIdx = 0;

        $('#addRow').click(function() {
            const row = `
        <tr class="item-row bg-white border-t border-gray-100" data-index="${rowIdx}">
            <td class="p-3">
                <select name="items[${rowIdx}][codr]" class="product-select w-full" required>
                    <option value="">Buscar referencia...</option>
                    <?php foreach ($productos as $p): ?>
                        <option value="<?= $p['codr'] ?>"><?= $p['descr'] ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td class="p-3">
                <select name="items[${rowIdx}][codtalla]" class="w-full border-gray-300 rounded text-xs">
                    <?php foreach ($tallas as $t): ?> <option value="<?= $t ?>"><?= $t ?></option> <?php endforeach; ?>
                </select>
            </td>
            <td class="p-3">
                <select name="items[${rowIdx}][codcolor]" class="w-full border-gray-300 rounded text-xs">
                    <?php foreach ($colores as $c): ?> <option value="<?= $c ?>"><?= $c ?></option> <?php endforeach; ?>
                </select>
            </td>
            <td class="p-3">
                <input type="number" name="items[${rowIdx}][cantidad]" class="qty w-full border-gray-300 rounded text-sm text-center font-bold" value="1" min="1">
            </td>
            <td class="p-3">
                <input type="number" name="items[${rowIdx}][valor]" class="price w-full border-gray-300 rounded text-sm text-right" placeholder="0">
            </td>
            <td class="p-3 text-right font-black text-gray-700 line-total">$ 0.00</td>
            <td class="p-3 text-center" rowspan="2">
                <button type="button" class="remove-row text-red-300 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </td>
        </tr>
        <tr class="comment-row bg-gray-50/50" data-index="${rowIdx}">
            <td colspan="6" class="px-3 pb-3 pt-1 border-b border-gray-200">
                <input type="text" name="items[${rowIdx}][comencpo]" placeholder="Escriba aquí observaciones específicas para este producto..." 
                       class="w-full bg-transparent border-b border-dashed border-gray-300 text-[11px] text-blue-500 focus:ring-0 italic p-1">
            </td>
        </tr>`;

            $('#itemsTable tbody').append(row);

            // Inicializar select2 en el nuevo elemento
            $(`tr[data-index="${rowIdx}"] .product-select`).select2();

            rowIdx++;
            updateTotals();
        });

        // MOTOR DE CÁLCULO REFORZADO
        $(document).on('change keyup', '.qty, .price', function() {
            // Buscamos la fila "item-row" más cercana
            const $row = $(this).closest('.item-row');
            const qty = parseFloat($row.find('.qty').val()) || 0;
            const price = parseFloat($row.find('.price').val()) || 0;
            const subtotal = qty * price;

            // Inyectamos el subtotal en la celda correspondiente
            $row.find('.line-total').text('$ ' + subtotal.toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));

            updateTotals();
        });

        function updateTotals() {
            let grandTotal = 0;
            let totalQty = 0;
            let itemsCount = 0;

            $('.item-row').each(function() {
                const qty = parseFloat($(this).find('.qty').val()) || 0;
                const lineTotalText = $(this).find('.line-total').text().replace(/[$,]/g, '');
                const lineTotal = parseFloat(lineTotalText) || 0;

                totalQty += qty;
                grandTotal += lineTotal;
                itemsCount++;
            });

            $('#grandTotal').text('$ ' + grandTotal.toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
            $('#totalQty').text(totalQty);
            $('#totalItems').text(itemsCount);
        }

        // Remover ambas filas (Datos + Comentario)
        $(document).on('click', '.remove-row', function() {
            const idx = $(this).closest('tr').data('index');
            $(`tr[data-index="${idx}"]`).remove();
            updateTotals();
        });

        // El resto de funciones de cálculo (updateTotals) permanecen igual
    });
</script>