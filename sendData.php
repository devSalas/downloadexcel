<?php

require 'vendor/autoload.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

date_default_timezone_set('America/Lima'); // Establecer la zona horaria

$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}

// Verificar que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y decodificar los datos JSON enviados desde el cliente
    $data = json_decode(file_get_contents("php://input"));
    
    // Verificar si se recibieron datos y que contienen los campos esperados
    if (isset($data->username) && isset($data->password)) {
        $username = $data->username;
        $password = $data->password;
        
        // Validar usuario y contraseña (puedes cambiar esto por una validación más segura)
        $validUsername = "esteban";
        $validPassword = "zq1p6CIv27";

        if ($username === $validUsername && $password === $validPassword) {
            $filePath = 'datos.xlsx';
            if (file_exists($filePath)) {
                // Cargar el archivo existente
                $spreadsheet = IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();

                // Leer títulos y datos del archivo Excel
                $titles = [];
                $data = [];
                $rowIndex = 0;
                foreach ($worksheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false); // This loops through all cells, even if a cell value is not set.
                    $row_data = [];
                    foreach ($cellIterator as $cell) {
                        $row_data[] = $cell->getValue();
                    }
                    if ($rowIndex == 0) {
                        $titles = $row_data;
                    } else {
                        $data[] = $row_data;
                    }
                    $rowIndex++;
                }

                // Preparar una respuesta con los títulos y datos del Excel
                $response = [
                    "status" => "ok",
                    "titles" => $titles,
                    "data" => $data
                ];

                // Devolver la respuesta como JSON
                header("Content-Type: application/json");
                echo json_encode($response);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["message" => "Error: El archivo no existe"]);
            }
        } else {
            http_response_code(401); // Unauthorized
            echo json_encode(["message" => "Error: Usuario o contraseña incorrectos"]);
        }
    } else {
        // Si faltan campos o los datos no están en el formato esperado
        http_response_code(400); // Bad Request
        echo json_encode(["message" => "Error: Campos requeridos faltantes"]);
    }
} else {
    // Si no es una solicitud POST
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Error: Metodo no permitido"]);
}
?>
