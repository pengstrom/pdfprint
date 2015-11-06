<?php

/**
 * Document handler and result page
 *
 * Upload the document via POST, validtes it
 * and transfers it via SFTP for printing.
 *
 * Printing not yet implemented.
 *
 * PHP version 5
 *
 * @category PdfPrint
 * @package  PdfPrint
 * @author   Per Engström <per.olov.engstrom@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License 
 * @link     https://github.com/pengstrom/pdfprint
 */

// The main project directory relative to this document
define('PROJECT_ROOT', '../');

require "../vendor/autoload.php";

require 'Net/SSH2.php';
require 'Crypt/RSA.php';
require 'Net/SFTP.php';

use Sirius\Upload\Handler as UploadHandler;
use Symfony\Component\Yaml\Parser;

// Parse the config file
$yaml = new Parser();
$config = $yaml->parse(file_get_contents(PROJECT_ROOT . 'siteconfig.yaml'));

// Retrieve relevant information from the config
$uploadFolder = PROJECT_ROOT . $config['uploadFolder'];
$sshServer = $config['ssh']['server'];
$sshUsername = $config['ssh']['username'];
$sshRsaKey = PROJECT_ROOT . $config['ssh']['rsaKeyLocation'];
$baseurl = $config['baseurl'];

// Initiate the upload handler
$uploadHandler = new UploadHandler($uploadFolder);

// Initiate the sftp client, connecting to $sshServer
// and rsa key
$sftp = new Net_SFTP($sshServer);
$key = new Crypt_RSA();

// Only accept .pdf files
$uploadHandler->addRule(
    'extension',
    ['allowed' => 'pdf'],
    '{label} should be a valid pdf file',
    'document'
);

// Fetch the uploaded file
$result = $uploadHandler->process($_FILES['document']);

// Validate it
if ($result->isValid()) {
    try {

        $name = $result->name;

        // If successful, load the rsa key and log in
        $key->loadKey(file_get_contents($sshRsaKey));
        if (!$sftp->login($sshUsername, $key)) {
            exit('Login failed');
        }

        // Upload the file to the server using the local name
        $sftp->put($name, $name, NET_SFTP_LOCAL_FILE);

        $title = 'Lyckades!';
        $message = 'Uppladdningen lyckades! Du kan nu hämta din utskrift.';

    } catch (\Exception $e0) {
        // If there were an error, delete the file and rethrow
        $result->clear();
        throw $e;
    }
} else {
    $title = 'Lyckades inte ;(';
    $message
        = 'Uppladdningen misslyckades. '
        . 'Försäkra dig om att det är en giltig pdf-fil.';
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
            <h1><span class="fa fa-file-pdf-o"></span> <?=$title?></h1>
            <p class="lead"><?=$message?></p>
        </div>
        <a href="/" class="btn btn-default"><span class="fa fa-repeat"i></span> Tillbaka</a>
    </div>
</body>
</html>
