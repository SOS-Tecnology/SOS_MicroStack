<div class="max-w-7xl mx-auto text-gray-800">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-700">
            Seguimiento OPR
        </h2>

        <a href="/orden-produccion/create"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow">
            + Nueva OPR
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">Documento</th>
                    <th class="px-6 py-3">Fecha</th>
                    <th class="px-6 py-3">Cliente</th>
                    <th class="px-6 py-3">Fecha Entrega</th>
                    <th class="px-6 py-3 text-center">Estado</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">

                <?php if (!empty($oprs)) : ?>
                    <?php foreach ($oprs as $opr) : ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold">
                                <?= htmlspecialchars($opr['documento']) ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= $opr['fecha'] ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= htmlspecialchars($opr['nombrecli'] ?? 'N/A') ?>
                            </td>

                            <td class="px-6 py-4">
                                <?= $opr['fechent'] ?>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <?php
                                $estado = $opr['estadorm'] ?? 'P';

                                switch ($estado) {
                                    case 'P':
                                        echo '<span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs">Planeada</span>';
                                        break;

                                    case 'A':
                                        echo '<span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs">Aprobada</span>';
                                        break;

                                    case 'E':
                                        echo '<span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-xs">En Producción</span>';
                                        break;

                                    case 'T':
                                        echo '<span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs">Terminada</span>';
                                        break;

                                    case 'C':
                                        echo '<span class="bg-gray-300 text-gray-800 px-3 py-1 rounded-full text-xs">Cerrada</span>';
                                        break;

                                    case 'X':
                                        echo '<span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs">Cancelada</span>';
                                        break;

                                    default:
                                        echo '<span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs">Sin Estado</span>';
                                }
                                ?>

                            </td>

                            <td class="px-6 py-4 text-center">
                                <a href="/orden-produccion/ver/<?= $opr['documento'] ?>"
                                    class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-500">
                            No hay OPR registradas.
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>