<div class="p-6">
  <h1 class="text-2xl font-bold mb-4">Usuarios</h1>
  <a href="/usuarios/create" class="px-4 py-2 bg-green-600 text-white rounded">Nuevo Usuario</a>
  <table class="mt-4 w-full border-collapse">
    <thead>
      <tr class="bg-gray-200">
        <th class="p-2">Nombre</th>
        <th class="p-2">Email</th>
        <th class="p-2">Rol</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($usuarios as $u): ?>
      <tr>
        <td class="p-2"><?= htmlspecialchars($u['nombre']) ?></td>
        <td class="p-2"><?= htmlspecialchars($u['email']) ?></td>
        <td class="p-2"><?= htmlspecialchars($u['rol']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
