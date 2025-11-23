<?php
if (!isset($_SERVER['HTTP_REFERER'])) {
    header("Location: index.php");
    exit();
}
// Configuración del Cliente SOAP
// OJO: Asegúrate que la ruta sea correcta hacia TU archivo wsdl
$wsdl = "http://localhost/actividad_final/servicio.wsdl";
$client = new SoapClient($wsdl);

// Lógica para ELIMINAR (si se presionó el botón de borrar)
if (isset($_GET['eliminar_cedula'])) {
    $cedula = $_GET['eliminar_cedula'];
    // Llamamos a la función del servidor
    $respuesta = $client->eliminarPaciente($cedula);
    // Recargamos la página para ver el cambio
    header("Location: listar.php");
    exit();
}

// Lógica para LISTAR (Pedimos los datos al servidor)
// El servidor nos devuelve un JSON (texto), así que lo convertimos a array PHP
$jsonResponse = $client->listarPacientes();
$pacientes = json_decode($jsonResponse, true);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Pacientes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Función para confirmar antes de borrar (Requisito RF-10)
        function confirmarEliminacion(cedula) {
            if (confirm("¿Está seguro de que desea eliminar al paciente con cédula " + cedula + "?")) {
                window.location.href = "listar.php?eliminar_cedula=" + cedula;
            }
        }
    </script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Listado de Pacientes</h2>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                🏠 Volver al Inicio
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="py-3 px-4 text-left">Cédula</th>
                        <th class="py-3 px-4 text-left">Nombres</th>
                        <th class="py-3 px-4 text-left">Apellidos</th>
                        <th class="py-3 px-4 text-left">Teléfono</th>
                        <th class="py-3 px-4 text-left">F. Nacimiento</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php if (count($pacientes) > 0): ?>
                        <?php foreach ($pacientes as $p): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4"><?php echo $p['cedula']; ?></td>
                            <td class="py-3 px-4"><?php echo $p['nombres']; ?></td>
                            <td class="py-3 px-4"><?php echo $p['apellidos']; ?></td>
                            <td class="py-3 px-4"><?php echo $p['telefono']; ?></td>
                            <td class="py-3 px-4"><?php echo $p['fecha_nacimiento']; ?></td>
                            <td class="py-3 px-4 text-center space-x-2">
                                <a href="editar.php?cedula=<?php echo $p['cedula']; ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded text-sm">
                                    Editar
                                </a>
                                <button onclick="confirmarEliminacion('<?php echo $p['cedula']; ?>')" class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded text-sm">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">No hay pacientes registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>