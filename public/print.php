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

$printerName = $_POST['printer'];
$legalOptions = (array) json_decode(file_get_contents($jsonfile));
$legalOptions = (array) $legalOptions[$printerName];

$files = $_FILES['documents'];

$options = $_POST;

$username = $_POST['username'];
$password = $_POST['password'];
$copies = (int) $_POST['copies'];

// Remove non-options
unset($options['printer']);
unset($options['copies']);
unset($options['username']);
unset($options['password']);

// Validate printer options in POST
foreach ($options as $optionName => $data) {
    if (!in_array($optionName, array_keys($legalOptions))) {
      exit('Invalid request');
    }

    $legalValues = (array) $legalOptions[$optionName];
    $legalValues = (array) $legalValues['values'];
    if (!in_array($data, $legalValues)) {
      exit('Invalid request');
    }
}

$absStorage = realpath($uploadFolder);
$uploader = new PdfUploadHandler($absStorage);
$results = $uploader->upload($files);

$errors = [];

foreach ($results as $result) {

    if ($result['message']) {
        $errors[] = $result;
    }

}

if (!$errors) {

    $printer = new PrintSSH($sshServer, $username, $password);

    if ($copies < 1) {
        $copies = 1;
    }

    foreach ($results as $result) {
        $filename = $result['filename'];
        $printer->printFile($filename, 'pr2402', $options, $copies, true);
    }

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
            <?php if ($errors): ?>
                <h1><span class="fa fa-exclamation-triangle"></span> Det blev fel :(</h1>
                <p class="lead">Det förekom fel, se nedan.</p>
            <?php else: ?>
                <h1><span class="fa fa-print"></span> Utskriften lyckades!</h1>
                <p class="lead">Utskriften kan hämtas på pr2404!</p>
            <?php endif; ?>
        </div>          
        
        <?php if ($errors): ?>
           <ul>
             <?php foreach($errors as $error): ?>
               <li><?=$error['original']?> :: <strong>Error</strong> : <em><?=$error['message']?></em></li>
             <?php endforeach; ?>
           </ul> 
        <?php endif; ?>

        <a class="btn btn-default pull-right" href="/printer.php"><span class="fa fa-refresh"></span> Tillbaka</a>
      </div>
    <?php
      require 'foot.php';
    ?>
  </body>
</html>
