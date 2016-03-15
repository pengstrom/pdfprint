<?php

require PDFPRINT_ROOT . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use Symfony\Component\Yaml\Parser;

$yaml = new Parser();
$config = $yaml->parse(
    file_get_contents(PDFPRINT_ROOT . DIRECTORY_SEPARATOR . 'siteconfig.yml')
);

$uploadFolder = PDFPRINT_ROOT . DIRECTORY_SEPARATOR . $config['uploadFolder'];
$sshServer = $config['ssh']['server'];
$live = $config['live'];
$debug = $config['debug'];

?>
