<?php
// Configuración del Cliente SOAP (usar WSDL local)
$wsdl = __DIR__ . '/servicio.wsdl';
$client = null;
if (class_exists('SoapClient')) {
    try {
        $client = new SoapClient($wsdl);
    } catch (Exception $e) {
        error_log("SoapClient error: " . $e->getMessage());
        $client = null; // Mantener valor para evitar errores fatales; comprobamos más abajo
    }
}

// Lógica para ELIMINAR (si se presionó el botón de borrar)
if (isset($_GET['eliminar_cedula'])) {
    $cedula = $_GET['eliminar_cedula'];
    // Llamamos a la función del servidor (SOAP o REST fallback)
    if ($client) {
        $respuesta = $client->eliminarPaciente($cedula);
    } else {
        $api = 'http://127.0.0.1:8000/server.php';
        @file_get_contents($api . '?action=eliminar&cedula=' . urlencode($cedula));
    }
    // Recargamos la página para ver el cambio
    header("Location: listar.php");
    exit();
}

// Lógica para LISTAR (Pedimos los datos al servidor)
// El servidor nos devuelve un JSON (texto), así que lo convertimos a array PHP
$pacientes = [];
if ($client) {
    $jsonResponse = $client->listarPacientes();
    $pacientes = json_decode($jsonResponse, true);
} else {
    // Fallback local: instanciamos la clase directamente para evitar llamadas HTTP recursivas
    require_once __DIR__ . '/ServicioPacientes.php';
    $svc = new ServicioPacientes();
    $jsonResponse = $svc->listarPacientes();
    $pacientes = json_decode($jsonResponse, true);
}


// FILTRADO: Buscar por cédula, nombres o apellidos
$busqueda = trim($_GET['q'] ?? '');
if ($busqueda !== '') {
    $pacientes = array_filter($pacientes, function($p) use ($busqueda) {
        $busqueda = mb_strtolower($busqueda);
        return str_contains(mb_strtolower($p['cedula'] ?? ''), $busqueda) ||
            str_contains(mb_strtolower($p['nombres'] ?? ''), $busqueda) ||
            str_contains(mb_strtolower($p['apellidos'] ?? ''), $busqueda);
    });
}
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
                //formilario POST
                const form = document.createElement('input');
                form.method = 'POST';
                form.action = 'listar.php';

                const input = document.createElement('input');
                input.type = 'hiden';
                input.name = 'eliminar_cedula';
                input.value = cedula;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</head>
<body class="bg-gray-50 p-8">


<nav class="bg-blue-600 text-white shadow-md w-full fixed top-0 left-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        <h1 class="text-2xl font-bold">CLINICA-SOAPS</h1>
        <div class="space-x-4">
            <a href="index.php" class="bg-white text-blue-600 font-semibold py-2 px-4 rounded hover:bg-gray-100 transition">
                Inicio
            </a>

            <a href="crear.php" class="bg-white text-blue-600 font-semibold py-2 px-4 rounded hover:bg-gray-100 transition">
                Agregar Paciente
            </a>
        </div>
    </div>
</nav>





<div class="h-20"></div>



    <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-2xl">
        <div class="flex justify-between items-center mb-8 border-b pb-4">
            <h2 class="text-3xl font-extrabold text-gray-900">Listado de Pacientes</h2>
        </div>

<div class="mt-4 mb-4 flex justify-end">
    <form method="GET" action="listar.php" class="flex space-x-2">
        <input 
            type="text" 
            name="q" 
            placeholder="Buscar por cédula, nombre o apellido" 
            value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" 
            class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            🔍 Buscar
        </button>
        <a href="listar.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">
            ❌ Limpiar
        </a>
    </form>
</div>


        <?php if (isset($soap_error)): ?>
            <div role="alert" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
                <strong>Advertencia:</strong> <?php echo $soap_error; ?> Usando modo de fallback.
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_listar)): ?>
            <div role="alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong>Error:</strong> <?php echo $error_listar; ?>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto shadow-lg rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider rounded-tl-lg">Cédula</th>
                        <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Nombres</th>
                        <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Apellidos</th>
                        <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Teléfono</th>
                        <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">F. Nacimiento</th>
                        <th class="py-3 px-4 text-center text-sm font-medium uppercase tracking-wider rounded-tr-lg">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (count($pacientes) > 0): ?>
                        <?php foreach ($pacientes as $p): ?>
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="py-3 px-4 whitespace-nowrap"><?php echo htmlspecialchars($p['cedula'] ?? ''); ?></td>
                            <td class="py-3 px-4 whitespace-nowrap"><?php echo htmlspecialchars($p['nombres'] ?? ''); ?></td>
                            <td class="py-3 px-4 whitespace-nowrap"><?php echo htmlspecialchars($p['apellidos'] ?? ''); ?></td>
                            <td class="py-3 px-4 whitespace-nowrap"><?php echo htmlspecialchars($p['telefono'] ?? ''); ?></td>
                            <td class="py-3 px-4 whitespace-nowrap"><?php echo htmlspecialchars($p['fecha_nacimiento'] ?? ''); ?></td>
                            <td class="py-3 px-4 text-center whitespace-nowrap space-x-2">
                                <a href="editar.php?cedula=<?php echo urlencode($p['cedula'] ?? ''); ?>" class="bg-blue-500 hover:bg-blue-600 text-white py-1.5 px-3 rounded-full text-sm font-medium shadow-sm hover:shadow-md transition duration-150">
                                    ✏️ Editar
                                </a>
                                <button onclick="confirmarEliminacion('<?php echo htmlspecialchars($p['cedula'] ?? ''); ?>')" class="bg-red-500 hover:bg-red-600 text-white py-1.5 px-3 rounded-full text-sm font-medium shadow-sm hover:shadow-md transition duration-150">
                                    🗑️ Eliminar
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500 text-lg">
                                No hay pacientes registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>