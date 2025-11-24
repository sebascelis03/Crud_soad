<?php
class ServicioPacientes {
    
    private $archivoXml = 'pacientes.xml';

    // Función auxiliar para guardar cambios en el XML
    private function guardarXML($xml) {
        $dom = new DOMDocument("1.0");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save($this->archivoXml);
    }
    

    // RF-03: Listar Pacientes
    public function listarPacientes() {
        if (!file_exists($this->archivoXml)) return json_encode([]);
        
        $xml = simplexml_load_file($this->archivoXml);
        $lista = [];
        
        foreach ($xml->paciente as $paciente) {
            $lista[] = [
                'cedula' => (string)$paciente->cedula,
                'nombres' => (string)$paciente->nombres,
                'apellidos' => (string)$paciente->apellidos,
                'telefono' => (string)$paciente->telefono,
                'fecha_nacimiento' => (string)$paciente->fecha_nacimiento
            ];
        }
        return json_encode($lista);
    }

    // RF-01: Crear Paciente
    public function registrarPaciente($cedula, $nombres, $apellidos, $telefono, $fecha_nacimiento) {
        $xml = simplexml_load_file($this->archivoXml);

        // Validar si ya existe
        foreach ($xml->paciente as $paciente) {
            if ((string)$paciente->cedula == $cedula) {
                return json_encode(["status" => "error", "mensaje" => "El paciente ya existe."]);
            }
        }

        // Agregar nuevo paciente
        $nuevo = $xml->addChild('paciente');
        $nuevo->addChild('cedula', $cedula);
        $nuevo->addChild('nombres', $nombres);
        $nuevo->addChild('apellidos', $apellidos);
        $nuevo->addChild('telefono', $telefono);
        $nuevo->addChild('fecha_nacimiento', $fecha_nacimiento);

        $this->guardarXML($xml);
        return json_encode(["status" => "ok", "mensaje" => "Paciente registrado con éxito."]);
    }

    // RF-02: Buscar Paciente
    public function buscarPaciente($cedula) {
        $xml = simplexml_load_file($this->archivoXml);
        
        foreach ($xml->paciente as $paciente) {
            if ((string)$paciente->cedula == $cedula) {
                $datos = [
                    'cedula' => (string)$paciente->cedula,
                    'nombres' => (string)$paciente->nombres,
                    'apellidos' => (string)$paciente->apellidos,
                    'telefono' => (string)$paciente->telefono,
                    'fecha_nacimiento' => (string)$paciente->fecha_nacimiento
                ];
                return json_encode($datos);
            }
        }
        return json_encode(null);
    }

    // RF-04: Actualizar Paciente
    public function actualizarPaciente($cedula, $nombres, $apellidos, $telefono, $fecha_nacimiento) {
        $xml = simplexml_load_file($this->archivoXml);
        $encontrado = false;

        foreach ($xml->paciente as $paciente) {
            if ((string)$paciente->cedula == $cedula) {
                $paciente->nombres = $nombres;
                $paciente->apellidos = $apellidos;
                $paciente->telefono = $telefono;
                $paciente->fecha_nacimiento = $fecha_nacimiento;
                $encontrado = true;
                break;
            }
        }

        if ($encontrado) {
            $this->guardarXML($xml);
            return json_encode(["status" => "ok", "mensaje" => "Paciente actualizado."]);
        }
        return json_encode(["status" => "error", "mensaje" => "Paciente no encontrado."]);
    }

    // RF-05: Eliminar Paciente
    public function eliminarPaciente($cedula) {
        $xml = simplexml_load_file($this->archivoXml);
        $i = 0;
        $encontrado = false;

        foreach ($xml->paciente as $paciente) {
            if ((string)$paciente->cedula == $cedula) {
                unset($xml->paciente[$i]);
                $encontrado = true;
                break;
            }
            $i++;
        }

        if ($encontrado) {
            $this->guardarXML($xml);
            return json_encode(["status" => "ok", "mensaje" => "Paciente eliminado."]);
        }
        return json_encode(["status" => "error", "mensaje" => "No se pudo eliminar."]);
    }
}
