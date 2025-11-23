# Clínica -SOAP: Gestor Interno de Pacientes
**Clínica Salud Total**

## 1. Descripción del Proyecto
Este sistema es una solución de software tipo intranet desarrollada para la **Clínica Salud Total**. Su objetivo es centralizar y gestionar el registro de pacientes mediante una arquitectura distribuida **SOAP** (Simple Object Access Protocol).

El sistema permite realizar un **CRUD completo** (Crear, Leer, Actualizar y Eliminar) sobre un archivo de datos XML, garantizando la persistencia de la información sin necesidad de bases de datos SQL tradicionales.

---

## 2. Integrantes del Grupo
* Jhoan Sebastiab Celis Pabon
* Melanny Yilyan Guate Restrepo
* Zharick Nicolle Acevedo Ascanio

---

## 3. Arquitectura del Sistema
El proyecto sigue estrictamente el patrón de diseño Cliente-Servidor bajo el protocolo SOAP:

1.  **Persistencia (XML):** Los datos se almacenan físicamente en `pacientes.xml`.
2.  **Contrato (WSDL):** El archivo `servicio.wsdl` define las 5 operaciones disponibles.
3.  **Backend (Servidor SOAP):** `server.php` implementa la lógica de negocio y modifica el XML.
4.  **Frontend (Cliente SOAP):** Vistas en PHP/HTML que consumen el servicio para interactuar con el usuario.

---

## 4. Requisitos Previos e Instalación

### Requisitos del Entorno
* **Servidor Web:** Apache (XAMPP recomendado).
* **Lenguaje:** PHP 7.4 o superior.
* **Extensión SOAP:** Debe estar habilitada en el archivo `php.ini`.

### Pasos para Habilitar SOAP en XAMPP
1.  Abrir el archivo de configuración `php.ini`.
2.  Buscar la línea: `;extension=soap`
3.  Quitar el punto y coma (`;`) inicial para que quede: `extension=soap`
4.  Guardar y reiniciar Apache.

### Instalación
1.  Copiar la carpeta del proyecto (`actividad_final`) dentro del directorio `htdocs` de XAMPP.
2.  Verificar que el archivo `pacientes.xml` tenga permisos de escritura.
3.  Acceder desde el navegador a: `http://localhost/actividad_final/index.php`

---

## 5. Estructura de Archivos

| Archivo | Tipo | Descripción |
| :--- | :--- | :--- |
| **pacientes.xml** | Base de Datos | Almacena la información de los pacientes (Cédula, Nombres, etc.). |
| **servicio.wsdl** | Contrato | Define las operaciones SOAP disponibles (Registrar, Buscar, Listar, etc.). |
| **server.php** | Backend | Servidor que procesa las peticiones y manipula el XML. |
| **index.php** | Frontend | Vista principal con el menú de navegación. |
| **listar.php** | Frontend | Tabla que consume el servicio para mostrar, editar y eliminar pacientes. |
| **crear.php** | Frontend | Formulario para registrar nuevos pacientes. |
| **editar.php** | Frontend | Formulario que busca datos existentes y permite actualizarlos. |

---

## 6. Funcionalidades (CRUD)

El sistema implementa las siguientes operaciones requeridas:

* **Registrar Paciente (RF-01):** Agrega un nuevo nodo `<paciente>` al XML.
* **Buscar Paciente (RF-02):** Localiza un paciente por cédula para precargar el formulario de edición.
* **Listar Pacientes (RF-03):** Recupera todos los registros para mostrarlos en tabla.
* **Actualizar Paciente (RF-04):** Modifica los datos de un nodo existente.
* **Eliminar Paciente (RF-05):** Elimina el nodo correspondiente del archivo XML.

---

## 7. Notas Adicionales
* **Diseño:** Se utilizó **Tailwind CSS** vía CDN para una interfaz limpia y profesional.
* **Seguridad:** Se implementó validación de navegación (`HTTP_REFERER`) para evitar acceso directo a formularios sin pasar por el flujo correcto.

---
**Fecha de Entrega:** 24 de Noviembre de 2025