<?php

require '../cors.php';

require '../../vendor/autoload.php';

define('PDFPRINT_ROOT', realpath('../../'));

require PDFPRINT_ROOT . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use PEngstrom\PdfPrintLib\PrintSSH;

require '../yaml.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$username = $data['username']) {
        throw new \RuntimeException("You must specify a username");
    }
    if (!$password = $data['password']) {
        throw new \RuntimeException("You must specify a password");
    }

    $printer = new PrintSSH($sshServer, $username, $password);

    http_response_code(200);
    echo json_encode(['errors' => [], 'payload' => $data]);

} catch (Exception $e) {
    http_response_code(400);
    error_log($e->getMessage());
    echo json_encode(['errors' => [$e->getMessage()], 'payload' => []]);
    exit;
} 
