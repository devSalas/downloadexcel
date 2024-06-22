<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}
// Verificar que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y decodificar los datos JSON enviados desde el cliente
    $data = json_decode(file_get_contents("php://input"));

    // Verificar si se recibieron datos y que contienen los campos esperados
    if (isset($data->name) && isset($data->tel)) {
        // Procesar los datos como desees
        $name = $data->name;
        $tel = $data->tel;

        // Aquí puedes realizar cualquier acción con los datos recibidos
        // Por ejemplo, guardar en una base de datos, generar un archivo, etc.

        // Preparar una respuesta (por ejemplo, un mensaje de confirmación)
        $response = array(
            "status" =>"ok",
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
