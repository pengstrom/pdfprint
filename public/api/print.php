<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");


define('PDFPRINT_ROOT', realpath('../../'));

require PDFPRINT_ROOT . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use PEngstrom\PdfPrintLib\PdfUploadHandler;
use PEngstrom\PdfPrintLib\PrintSSH;
use Symfony\Component\Yaml\Parser;


$yaml = new Parser();
$config = $yaml->parse(
  file_get_contents(PDFPRINT_ROOT . DIRECTORY_SEPARATOR . 'siteconfig.yml')
);

$uploadFolder = PDFPRINT_ROOT . DIRECTORY_SEPARATOR . $config['uploadFolder'];
$sshServer = $config['ssh']['server'];

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
    } elseif (count($copies) !== count($files)) {
        throw new \RuntimeException(
            "You need to supply the number of copies for each file");
    }

    $jsonfile = '../../printer-options.json';
    $contents = file_get_contents($jsonfile);
    $printerData = json_decode($contents, true);

    if ($contents === false) {
        error_log("Printer data not found at $jsonfile");
        http_response_code(500);
        exit("Internal server error");
    }

    if (array_key_exists($printer, $printerData) === false) {
        throw new \RuntimeException("Printer $printer not found");
    }

    $availableOptions = (array) $printerData[$printer];

    foreach ($options as $optionName => $optionValue) {
        if (!array_key_exists($optionName, $availableOptions)) {
            throw new \RuntimeException(
                "Option $optionName not available for printer $printer");
        } elseif (!in_array($optionValue, $availableOptions[$optionName])) {
            throw new \RuntimeException(
                "Value $optionValue not available for
                option $optionName on printer $printer");
        }
    }

    $uploadDir = realpath($uploadFolder);
    if ($uploadDir === false) {
        error_log("Could not establish uplad folder $uploadFolder");
        http_response_code(500);
        exit("Internal server error");
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
        echo json_encode($errors);
        exit;
    }

    for ($i=0; $i < count($files); $i++) {
        $result = $results[$i];
        $filename = $result['filename'];
        $copy = $copies[$i];
        $printer->printFile($filename, $printer, $options, $copy, true);
    }

    $response = $_POST;
    http_response_code(200);

    echo json_encode($response);
    exit;

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode($e->getMessage());
    exit;
} 

?>
