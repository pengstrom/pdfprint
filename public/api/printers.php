<?php

require '../cors.php';

try {

    $jsonfile = '../../printer-options.json';

    $printerOptions = json_decode($contents = file_get_contents($jsonfile), true);

    if ($contents === false) {
        throw new \RuntimeException("Printer info file not found");
    }
    if ($printerOptions === null) {
        throw new \RuntimeException("Printer file $printer contains invalid json");
    }

    $printers = array_keys($printerOptions);
    sort($printers);

    http_response_code(200);
    echo json_encode([ 'errors' => [], 'payload' => $printers]);

} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode([ 'errors' => [$e->getMessage()], 'payload' => []]);
}

?>
