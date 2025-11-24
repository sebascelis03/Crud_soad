<?php
// Configuración del Cliente SOAP (usar WSDL local)
$wsdl = __DIR__ . '/servicio.wsdl';
$client = null;
if (class_exists('SoapClient')) {
    try {
        $client = new SoapClient($wsdl);
    } catch (Exception $e) {
        error_log("SoapClient error: " . $e->getMessage());
        $client = null;
    }
}

// A. Si llegamos aquí para GUARDAR cambios (POST)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = filter_var($_POST['cedula'], FILTER_SANITIZE_NUMBER_INT);
    $nombres = htmlspecialchars(trim($_POST['nombres']));
    $apellidos = htmlspecialchars(trim($_POST['apellidos']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    // Llamamos a la función de actualizar (RF-04)
    if (isset($client) && $client) {
        $client->actualizarPaciente($cedula, $nombres, $apellidos, $telefono, $fecha_nacimiento);
    } else {
        // Fallback local para actualizar
        require_once __DIR__ . '/ServicioPacientes.php';
        $svc = new ServicioPacientes();
        $svc->actualizarPaciente($cedula, $nombres, $apellidos, $telefono, $fecha_nacimiento);
    }

    try{
    $client->actualizarPaciente($cedula, $nombres, $apellidos, $telefono, $fecha_nacimiento);
    } catch (Exception $e) {
        die("Error actualizar paciente: " . $e->getMessage());
    }
    
    header("Location: listar.php");
    exit();
}

// B. Si llegamos aquí para VER el formulario (GET)
// Verificamos que nos hayan pasado una cédula
if (!isset($_GET['cedula'])) {
    header("Location: listar.php");
    exit();
}

$cedula_editar = $_GET['cedula'];

// Buscamos los datos actuales de ese paciente (RF-02)
// Buscamos los datos actuales de ese paciente (RF-02)
$paciente = null;

// Intentamos buscar por SOAP con manejo de excepciones
if (isset($client) && $client) {
    try {
        $jsonResponse = $client->buscarPaciente($cedula_editar);
        $paciente = json_decode($jsonResponse, true);
    } catch (Exception $e) {
        // Si SOAP falla, mostrar error y permitir fallback
        error_log("Error SOAP buscarPaciente: " . $e->getMessage());
        $paciente = null;
    }
}

// Si SOAP no funcionó o no devolvió resultado, usar fallback local
if (!$paciente) {
    require_once __DIR__ . '/ServicioPacientes.php';
    $svc = new ServicioPacientes();
    $jsonResponse = $svc->buscarPaciente($cedula_editar);
    $paciente = $jsonResponse ? json_decode($jsonResponse, true) : null;
}

// Si no existe, volvemos
if (!$paciente) {
    header("Location: listar.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Paciente</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Editar Paciente: <?php echo $paciente['nombres']; ?></h2>

        <form method="POST" action="editar.php" class="space-y-4">
            <div>
                <label class="block text-gray-700">Cédula (No modificable):</label>
                <input type="text" name="cedula" value="<?php echo $paciente['cedula']; ?>" readonly class="w-full border p-2 rounded bg-gray-200 cursor-not-allowed">
            </div>
            
            <div>
                <label class="block text-gray-700">Nombres:</label>
                <input type="text" name="nombres" value="<?php echo $paciente['nombres']; ?>" required class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div>
                <label class="block text-gray-700">Apellidos:</label>
                <input type="text" name="apellidos" value="<?php echo $paciente['apellidos']; ?>" required class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div>
                <label class="block text-gray-700">Teléfono:</label>
                <input type="text" name="telefono" value="<?php echo $paciente['telefono']; ?>" required class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div>
                <label class="block text-gray-700">Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" value="<?php echo $paciente['fecha_nacimiento']; ?>" required class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div class="flex justify-between pt-4">
                <a href="listar.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Cancelar</a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">Actualizar Datos</button>
            </div>
        </form>
    </div>

</body>
</html>