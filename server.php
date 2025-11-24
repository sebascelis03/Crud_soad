<?php
// 1. Configuración inicial: Desactivar caché WSDL para desarrollo
ini_set("soap.wsdl_cache_enabled", "0");

// 2. Definición de la clase con la lógica del negocio
require_once __DIR__ . '/ServicioPacientes.php';

// 3. Inicialización del Servidor SOAP 
// Si la extensión SOAP NO está disponible, exponemos un wrapper REST simple
if (!class_exists('SoapServer')) {
    // Servicio REST mínimo para permitir que la aplicación funcione sin SOAP
    $svc = new ServicioPacientes();
    header('Content-Type: application/json; charset=utf-8');

    $action = $_REQUEST['action'] ?? '';
    switch ($action) {
        case 'listar':
            echo $svc->listarPacientes();
            break;
        case 'registrar':
            $cedula = $_REQUEST['cedula'] ?? '';
            $nombres = $_REQUEST['nombres'] ?? '';
            $apellidos = $_REQUEST['apellidos'] ?? '';
            $telefono = $_REQUEST['telefono'] ?? '';
            $fecha_nacimiento = $_REQUEST['fecha_nacimiento'] ?? '';
            echo $svc->registrarPaciente($cedula, $nombres, $apellidos, $telefono, $fecha_nacimiento);
            break;
        case 'buscar':
            $cedula = $_REQUEST['cedula'] ?? '';
            echo $svc->buscarPaciente($cedula);
            break;
        case 'actualizar':
            $cedula = $_REQUEST['cedula'] ?? '';
            $nombres = $_REQUEST['nombres'] ?? '';
            $apellidos = $_REQUEST['apellidos'] ?? '';
            $telefono = $_REQUEST['telefono'] ?? '';
            $fecha_nacimiento = $_REQUEST['fecha_nacimiento'] ?? '';
            echo $svc->actualizarPaciente($cedula, $nombres, $apellidos, $telefono, $fecha_nacimiento);
            break;
        case 'eliminar':
            $cedula = $_REQUEST['cedula'] ?? '';
            echo $svc->eliminarPaciente($cedula);
            break;
        default:
            echo json_encode(["status" => "error", "mensaje" => "No action specified"]);
            break;
    }
    exit();
}

// Si llegamos aquí, la extensión SOAP está disponible: arrancamos el SoapServer
try {
    // Usamos la ruta absoluta o relativa al WSDL
    $server = new SoapServer("servicio.wsdl", [
        'uri' => "http://localhost/ginpac"
    ]);
    $server->setClass("ServicioPacientes");
    $server->handle();
} catch (SoapFault $f) {
    echo "Error SOAP: " . $f->getMessage();
}
?>