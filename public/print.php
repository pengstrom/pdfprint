<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo 'Go away.';
    exit;
}

define('PDFPRINT_ROOT', realpath('../'));

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
$sshUsername = $config['ssh']['username'];
$sshRsaKey = PDFPRINT_ROOT . DIRECTORY_SEPARATOR . $config['ssh']['rsaKeyLocation'];
$baseurl = $config['baseurl'];


$file = $_FILES['document'];

$absStorage = realpath($uploadFolder);
$uploader = new PdfUploadHandler($absStorage);
$result = $uploader->upload($file);

if ($result['message']) {

    $title = '<span class="fa fa-exclamation-triangle"></span> Det blev fel :(';
    $message = 'Error: ' . $result['message'];

} else if ($filename = $result['filename']) {

    $key = file_get_contents($sshRsaKey);
    $printer = new PrintSSH($sshServer, $sshUsername, $key);

    $printer->printFile($filename);

    $title = '<span class="fa fa-print"></span> Utskriften lyckades!';
    $message = 'Utskriften kan nu hämtas.';
}

?>

<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pelles PDF-printer</title>

    <link
        rel="stylesheet"
        href="vendor/bootstrap/dist/css/bootstrap.min.css"
        type="text/css"
        media="all"
    />
    <link
        rel="stylesheet"
        href="assets/style/main.css"
        type="text/css"
        media="all"
    />
    <link
        rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"
    >

  </head>
  <body>
      <div class="container">
        <div class="page-header">
            <h1><?=$title?></h1>
            <p class="lead"><?=$message?></p>
        </div>          
        
        <a class="btn btn-default pull-right" href="/"><span class="fa fa-refresh"></span> Tillbaka</a>
      </div>
  </body>
</html>


