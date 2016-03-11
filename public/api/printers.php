<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");

try {

    $jsonfile = '../../printer-options.json';

    $printerOptions = (array) json_decode($content = file_get_contents($jsonfile));
    if ($content === false) {
        throw new \RuntimeException("Printer info file not found");
    }
    $printers = array_keys($printerOptions);
    sort($printers);

    http_response_code(200);
    echo json_encode($printers);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode($e->getMessage());
}

?>
