<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");


define('PDFPRINT_ROOT', realpath('../../'));

require PDFPRINT_ROOT . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use PEngstrom\PdfPrintLib\PdfUploadHandler;
use PEngstrom\PdfPrintLib\PrintSSH;

require '../yaml.php';

try {
    if (!$printer = $_POST['printer']) {
        throw new \RuntimeException("You must specify a printer");
    }

    if (!$options = $_POST['options']) {
        throw new \RuntimeException("You must specify the printer options");
    }

    if (!$copies = $_POST['copies']) {
        throw new \RuntimeException("You must specify the number of copies");
    }

    if (!$username = $_POST['username']) {
        throw new \RuntimeException("You must specify a username");
    }

    if (!$password = $_POST['password']) {
        throw new \RuntimeException("You must specify a password");
    }

    if (!$files = $_FILES['documents']) {
        throw new \RuntimeException("You must specify the files");
    } elseif (count($copies) !== count($files['name'])) {
        throw new \RuntimeException(
            "You need to supply the number of copies for each file");
    }

    $jsonfile = '../../printer-options.json';
    $contents = file_get_contents($jsonfile);
    $printerData = json_decode($contents, true);

    if (!$contents) {
        error_log("Printer data not found at $jsonfile");
        http_response_code(500);
        exit;
    }

    if (!array_key_exists($printer, $printerData)) {
        throw new \RuntimeException("Printer $printer not found");
    }

    $availableOptions = $printerData[$printer];

    foreach ($options as $optionName => $optionValue) {
        if (!array_key_exists($optionName, $availableOptions)) {
            throw new \RuntimeException(
                "Option $optionName not available for printer $printer");
        } elseif (!in_array($optionValue, $availableOptions[$optionName]['values'])) {
            throw new \RuntimeException(
                "Value $optionValue not available for
                option $optionName on printer $printer");
        }
    }

    $uploadDir = realpath($uploadFolder);
    if (!$uploadDir) {
        error_log("Could not establish uplad folder $uploadFolder");
        http_response_code(500);
        exit;
    }

    $printer = new PrintSSH($sshServer, $username, $password);
    $uploader = new PdfUploadHandler($uploadDir);

    $results = $uploader->upload($files);
    $errors = [];

    foreach ($results as $result) {
        if ($result['message']) {
            $errors[] = $result;
        }
    }

    if (count($errors) !== 0) {
        http_response_code(400);
        echo json_encode(['error' => $errors, 'payload' => []]);
        exit;
    }

    for ($i=0; $i < count($copies); $i++) {
        $result = $results[$i];
        $filename = $result['filename'];
        $copy = $copies[$i];
        $printer->printFile($filename, $printer, $options, $copy, $live);
    }

    $response = $_POST;
    http_response_code(200);

    echo json_encode(['error' => [], 'payload' => $response]);
    exit;

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage(), 'payload' => []]);
    exit;
} 

?>
