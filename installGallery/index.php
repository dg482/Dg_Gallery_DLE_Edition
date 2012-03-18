<?php
define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);
require_once ROOT_DIR . '/installGallery/install.php';
$install = new install();
if (empty($_REQUEST['action'])) {
    echo $install->init();
} elseif ($_POST['action'] == 'install') {
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1990 05:00:00 GMT');
    header('Content-type: application/json');
    echo $install->InstallGallery();
} elseif ($_POST['action'] == 'updateFile') {
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1990 05:00:00 GMT');
    header('Content-type: application/json');
    echo $install->updateFile();
}

function __autoload($className) {
    $className = ROOT_DIR . '/DGGallery/app/' . str_replace('_', '/', $className);
    include_once $className . '.php';
}
