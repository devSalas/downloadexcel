<?php

require 'vendor/autoload.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}

// Obtener los datos POST
$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'];
$password = $data['password'];

$valid_username = 'esteban';
$valid_password = 'g1g4m4$_20';

$archivo = 'datos.xlsx';

if ($name === $valid_username && $password === $valid_password) {
    // Verificar si el archivo existe
    if (file_exists($archivo)) {
        // Configurar las cabeceras para forzar la descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.basename($archivo).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($archivo));
        
        // Leer el archivo y enviarlo al navegador
        readfile($archivo);
        exit;
    } else {
        // Manejar el caso en que el archivo no exista
        http_response_code(404);
        echo json_encode(['error' => 'El archivo no existe.']);
    }
} else {
    // Credenciales incorrectas
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales incorrectas.']);
}
?>
