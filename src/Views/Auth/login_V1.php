<<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ingreso al Sistema</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body class="bg-gradient-to-r from-blue-50 to-blue-100 min-h-screen flex items-center justify-center">

  <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
    <div class="text-center mb-6">
      <h1 class="text-3xl font-bold text-gray-700">Acceso al Sistema</h1>
      <p class="text-gray-500 text-sm">Ingrese sus credenciales para continuar</p>
    </div>

    <form method="POST" action="/login" class="space-y-4">
      <!-- Correo -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-600">Correo</label>
        <input id="email" type="email" name="email" required
               class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-300">
      </div>

      <!-- Contraseña -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-600">Contraseña</label>
        <input id="password" type="password" name="password" required
               class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-300">
      </div>

      <!-- Botón -->
      <div>
        <button type="submit"
                class="w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
          Ingresar
        </button>
      </div>
    </form>

    <!-- Enlace opcional -->
    <div class="mt-4 text-center">
      <a href="#" class="text-sm text-blue-600 hover:underline">¿Olvidó su contraseña?</a>
    </div>
  </div>

</body>
</html>
