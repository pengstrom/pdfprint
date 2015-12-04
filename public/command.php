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

$jsonfile = '../printer-options.json';

$printer = $_POST['printer'];
$legalOptions = (array) json_decode(file_get_contents($jsonfile));
$legalOptions = (array) $legalOptions[$printer];

$options = $_POST;

// Remove non-options
unset($options['printer']);

// Get non-default options
foreach ($options as $optionName => $optionValue) {
    if (!$legalOptions[$optionName] || $optionValue === $legalOptions[$optionName]->default) {
        unset($options[$optionName]);
    }
}

// Construct command
$command = 'lpr -P ' . $printer;
foreach ($options as $optionName => $optionValue) {
    $option = ' -o ' . $optionName . '=' . $optionValue;
    $command = $command . $option;
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
            <h1><span class="fa fa-terminal"></span> Printer-kommando</h1>
            <p class="lead">Ditt kommando är klart!</p>
        </div>          

        <pre><?=$command?></pre>
        
        <a class="btn btn-default pull-right" href="/"><span class="fa fa-refresh"></span> Tillbaka</a>
      </div>
  </body>
</html>
