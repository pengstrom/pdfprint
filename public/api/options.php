<?php

require '../cors.php';

require '../../vendor/autoload.php';

$response = [];
$errorcode = 200;

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$printer = $data['printer']) {
        $errorcode = 400;
        throw new \RuntimeException("A printer must be provided");
    }

    $jsonfile = '../../printer-options.json';

    $options = json_decode($contents = file_get_contents($jsonfile), true);

    if ($contents === false) {
        $errorcode = 500;
        throw new \RuntimeException("JSON file not found");
    }
    if ($options === null) {
        $errorcode = 500;
        throw new \RuntimeException("Printer file $printer contains invalid json");
    }

    if (!array_key_exists($printer, $options)) {
        $errorcode = 500;
        throw new \RuntimeException("Malformed json: $options key not found in $printer");
    }

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
} catch (Exception $e) {
    http_response_code($errorcode);
    error_log($e->getMessage());
    echo json_encode([ 'errors' => [$e->getMessage()], 'payload' => []]);
    exit;
} 

?>
