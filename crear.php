<?php
if (!isset($_SERVER['HTTP_REFERER'])) {
    header("Location: index.php");
    exit();
}
// Lógica para GUARDAR el nuevo paciente
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Conexión al Cliente SOAP
    $wsdl = "http://localhost/actividad_final/servicio.wsdl";
    $client = new SoapClient($wsdl);

    // 2. Recogemos los datos del formulario
    $cedula = $_POST['cedula'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    // 3. Enviamos la orden al servidor (RF-01)
    $respuesta = $client->registrarPaciente($cedula, $nombres, $apellidos, $telefono, $fecha_nacimiento);
    
    // 4. Volvemos a la lista para ver el resultado
    header("Location: listar.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Paciente</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Registrar Nuevo Paciente</h2>

        <form method="POST" action="crear.php" class="space-y-4">
            <div>
                <label class="block text-gray-700">Cédula:</label>
                <input type="text" name="cedula" required class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            
            <div>
                <label class="block text-gray-700">Nombres:</label>
                <input type="text" name="nombres" required class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700">Apellidos:</label>
                <input type="text" name="apellidos" required class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700">Teléfono:</label>
                <input type="text" name="telefono" required class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700">Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" required class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="flex justify-between pt-4">
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Cancelar</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Guardar Paciente</button>
            </div>
        </form>
    </div>

</body>
</html>