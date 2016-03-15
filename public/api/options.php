<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");

require '../../vendor/autoload.php';

$response = [];

try {
    $printer = $_POST['printer'];

    $jsonfile = '../../printer-options.json';

    $options = json_decode($contents = file_get_contents($jsonfile), true);

    if ($contents === false) {
        d($contents);
        throw new \RuntimeException("JSON file not found");
    }
    if ($options === null) {
        d($options);
        throw new \RuntimeException("Printer file $printer contains invalid json");
    }

    if (array_key_exists($printer, $options)) {
        $printerOptions = $options[$printer];

        if ($printerOptions == []) {
            http_response_code(418);
            echo json_encode([ 'error' => 'Printer not reachable']);
            exit;
        }

        ksort($printerOptions);

        http_response_code(200);

        $response['printer'] = $printer;
        $response['options'] = $printerOptions;

        echo json_encode($response);
        exit;
    } else {
        http_response_code(404);
        echo json_encode([ 'error' => 'Printer not found']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([ 'error' => $e->getMessage()]);
    exit;
} 

?>
