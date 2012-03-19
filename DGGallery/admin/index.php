<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

if (!defined('DATALIFEENGINE')) {
    define('DATALIFEENGINE', true);
}
if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', $_SERVER ["DOCUMENT_ROOT"]);
}
 /**
 * @var bool
 */
$dgGallery = true;

require_once ROOT_DIR . '/DGGallery/admin/assistant.php';
//global
$sort_list = array();
//global
$gallery_cat = array();
$ass = new assistant ();

//ajax
if (model_request::isAjax()) {
    model_request::setHeaderJson();
    if ('json' === model_request::getRequest('type')) {
        echo $ass->runAjax();
        die();
    } else {
        //  model_request::setHeader();
        echo $ass->runAjax();
        die();
    }
} else {
    echo $ass->run();
}

function __autoload($className) {
    $className = ROOT_DIR . '/DGGallery/app/' . str_replace('_', '/', $className);
    if (file_exists($className . '.php'))
        include_once $className . '.php';
}
