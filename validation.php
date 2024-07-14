<?php

require 'vendor/autoload.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
    if (isset($data->name) && isset($data->tel) && isset($data->service)) {
        // Procesar los datos como desees
        $name = $data->name;
        $tel = $data->tel;
        $service = $data->service;
        $time = date('Y-m-d H:i:s');
        $filePath = 'datos.xlsx';
        if (file_exists($filePath)) {
            // Cargar el archivo existente
            $spreadsheet = IOFactory::load($filePath);
        } else {
            // Crear un nuevo archivo si no existe
            $spreadsheet = new Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();
            $worksheet->setCellValue('A1', 'Nombre');
            $worksheet->setCellValue('B1', 'Teléfono');
            $worksheet->setCellValue('C1', 'Servicio');
            $worksheet->setCellValue('D1', 'Día y Hora');

            // Aplicar color de fondo a los títulos
            $worksheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00'); // Color amarillo

            // Aplicar bordes a los títulos
            $worksheet->getStyle('A1:D1')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'], // Color negro
                    ],
                ],
            ]);
        }

        $worksheet = $spreadsheet->getActiveSheet();
        $row = $worksheet->getHighestRow() + 1;

        $worksheet->setCellValue('A' . $row, $name);
        $worksheet->setCellValue('B' . $row, $tel);
        $worksheet->setCellValue('C' . $row, $service);
        $worksheet->setCellValue('D' . $row, $time);

        // Aplicar color de fondo a los datos
        $worksheet->getStyle('A' . $row . ':D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF'); // Color blanco

        // Aplicar bordes a los datos
        $worksheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'], // Color negro
                ],
            ],
        ]);

        // Ajustar el ancho de las columnas automáticamente
        foreach (range('A', $worksheet->getHighestColumn()) as $column) {
            $worksheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Preparar una respuesta (por ejemplo, un mensaje de confirmación)
        $response = array(
            "status" => "ok",
            "message" => "Datos recibidos correctamente"
        );

        // Devolver la respuesta como JSON
        header("Content-Type: application/json");
        echo json_encode($response);
    } else {
        // Si faltan campos o los datos no están en el formato esperado
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "Error: Campos requeridos faltantes"));
    }
} else {
    // Si no es una solicitud POST
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "Error: Metodo no permitido"));
}
?>
