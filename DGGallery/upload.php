<?php

#@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
#@ini_set('display_errors', true);
#@ini_set('html_errors', false);
define('ROOT_DIR', $_SERVER ["DOCUMENT_ROOT"]);
#@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);
$is_logged = false;
if (isset($_POST ["sessid"])) {
    session_id($_POST ["sessid"]);
} else {
    #   exit();
}
session_start();

if (file_exists(ROOT_DIR . '/engine/data/config.php')) {
    define('DATALIFEENGINE', true);
    define('ENGINE_DIR', ROOT_DIR . '/engine');
    require_once ROOT_DIR . '/engine/data/config.php';
    require_once ROOT_DIR . '/engine/classes/mysql.php';
    require_once ROOT_DIR . '/engine/data/dbconfig.php';
    require_once ROOT_DIR . '/engine/inc/include/functions.inc.php';
    $db = new db ();
    $user_group = get_vars("usergroup");
    if (!$user_group) {
        $user_group = array();
        $db->query("SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC");
        while ($row = $db->get_row()) {
            $user_group [$row ['id']] = array();
            foreach ($row as $key => $value) {
                $user_group [$row ['id']] [$key] = $value;
            }
        }
        set_vars("usergroup", $user_group);
        $db->free();
    }
    require_once ROOT_DIR . '/engine/modules/sitelogin.php';
}

if ($is_logged) {
    require_once ROOT_DIR . '/DGGallery/admin/assistant.php';
    $upload = new model_upload ();
    echo $upload->MoveFile();
}

function __autoload($className) {
    $className = ROOT_DIR . '/DGGallery/app/' . str_replace('_', '/', $className);
    include_once $className . '.php';
}

