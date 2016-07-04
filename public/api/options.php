<?php

require '../cors.php';

require '../../vendor/autoload.php';

$response = [];

try {
    $data = json_decode(file_get_contents("php://input"), true);

    $printer = $data['printer'];

    $jsonfile = '../../printer-options.json';

    $options = json_decode($contents = file_get_contents($jsonfile), true);

    if ($contents === false) {
        throw new \RuntimeException("JSON file not found");
    }
    if ($options === null) {
        throw new \RuntimeException("Printer file $printer contains invalid json");
    }

    if (array_key_exists($printer, $options)) {
        $printerOptions = $options[$printer];

        if ($printerOptions == []) {
            http_response_code(418);
            echo json_encode([ 'error' => 'Printer not reachable', 'payload' => []]);
            exit;
        }

        ksort($printerOptions);

        http_response_code(200);

        $response['printer'] = $printer;
        $response['options'] = $printerOptions;

        echo json_encode([ 'error' => [], 'payload' => $response]);
        exit;
    } else {
        http_response_code(404);
        echo json_encode([ 'error' => 'Printer not found', 'payload' => []]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([ 'error' => $e->getMessage(), 'payload' => []]);
    exit;
} 

?>
