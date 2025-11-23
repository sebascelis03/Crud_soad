<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actividad - Inicio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen relative bg-cover bg-center" style="background-image: url('img/fondoXD.jpg');">

    <div class="absolute inset-0 bg-black opacity-50"></div>

    <div class="relative z-10 bg-white p-8 rounded-lg shadow-2xl text-center max-w-md w-full border-t-4 border-blue-600">

        <h1 class="text-3xl font-bold text-gray-800 mb-2">CLÍNICA - SOAP</h1>
        <p class="text-gray-500 mb-8 font-medium">Gestor Interno de Pacientes</p>

        <div class="space-y-4">
            <a href="crear.php" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded shadow transition transform hover:-translate-y-1">
                ➕ Registrar Nuevo Paciente
            </a>

            <a href="listar.php" class="block w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded shadow transition transform hover:-translate-y-1">
                📋 Ver Lista de Pacientes
            </a>
        </div>
        
        <p class="mt-8 text-xs text-gray-400">Clínica Salud Total &copy; 2025</p>
    </div>

</body>
</html>