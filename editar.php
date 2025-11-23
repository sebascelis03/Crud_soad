<?php
if (!isset($_SERVER['HTTP_REFERER'])) {
    header("Location: index.php");
    exit();
}
$wsdl = "http://localhost/actividad_final/servicio.wsdl";
$client = new SoapClient($wsdl);

// A. Si llegamos aquí para GUARDAR cambios (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST['cedula'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    // Llamamos a la función de actualizar (RF-04)
    $client->actualizarPaciente($cedula, $nombres, $apellidos, $telefono, $fecha_nacimiento);
    
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
$jsonResponse = $client->buscarPaciente($cedula_editar);
$paciente = json_decode($jsonResponse, true); // Convertimos JSON a Array

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
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">Actualizar Datos</button>
            </div>
        </form>
    </div>

</body>
</html>