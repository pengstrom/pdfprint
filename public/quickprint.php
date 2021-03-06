<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(400);
    exit;
}


define('PDFPRINT_ROOT', realpath('../'));

require PDFPRINT_ROOT . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use PEngstrom\PdfPrintLib\PdfUploadHandler;
use PEngstrom\PdfPrintLib\PrintSSH;

require 'yaml.php';

$errors = [];

if (isset($_FILES['documents']) === true) {
    $files = $_FILES['documents'];
} else {
    $errors[] = ['message' => 'No file uploaded!'];
}

$absStorage = realpath($uploadFolder);
$uploader = new PdfUploadHandler($absStorage);

$results = $uploader->upload($files);

foreach ($results as $result) {

    if ($result['message']) {
        $errors[] = $result;
    }

}

if (!$errors) {
    $options = ['ColorModel' => 'Gray', 'Duplex' => 'None'];

    if ($_POST['color'] === "on") {
        $options['ColorModel'] = 'CMYK';
    }

    if ($_POST['duplex'] === "on") {
        $options['Duplex'] = 'DuplexNoTumble';
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $copies = (int) $_POST['copies'];
    if ($copies < 1) {
        $copies = 1;
    }

    try {
        $printer = new PrintSSH($sshServer, $username, $password);

        foreach ($results as $result) {
            $filename = $result['filename'];
            if ($filename) {
                $printer->printFile($filename, 'pr2402', $options, $copies, $live);
            }
        }
    } catch (Exception $e) {
        $errors[] = ['message' => $e->getMessage()];
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

        <a class="btn btn-default pull-right" href="/"><span class="fa fa-refresh"></span> Tillbaka till förstasidan</a>
      </div>
<?php
require 'foot.php';
?>
  </body>
</html>


