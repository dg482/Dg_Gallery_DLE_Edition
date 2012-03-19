<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

@session_start();
define('DATALIFEENGINE', true);
define('FILE_DIR', '../uploads/files/');
define('ROOT_DIR', '..');
define('ENGINE_DIR', ROOT_DIR . '/engine');
@error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_NOTICE);
$config = null;
$user_group = null;
require ENGINE_DIR . '/data/config.php';
if ($config['http_home_url'] == "") {
    $config['http_home_url'] = explode("engine/download.php", $_SERVER['PHP_SELF']);
    $config['http_home_url'] = reset($config['http_home_url']);
    $config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];
}
require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';
require_once ENGINE_DIR . '/modules/functions.php';
require_once ENGINE_DIR . '/modules/sitelogin.php';
$user_group = get_vars("usergroup");
if (!$user_group) {
    $user_group = array();
    $db->query("SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC");
    while ($row = $db->get_row()) {
        $user_group[$row['id']] = array();
        foreach ($row as $key => $value) {
            $user_group[$row['id']][$key] = $value;
        }
    }
    set_vars("usergroup", $user_group);
    $db->free();
}
if (!$is_logged) {
    $member_id['user_group'] = 5;
}
$id = intval($_REQUEST['id']);
if ($id == 0)
    exit;
$row = $db->super_query('SELECT id,title,path,original FROM ' . PREFIX . "_dg_gallery_file WHERE id='{$id}' LIMIT 1");
if ($row) {
    $_config = require_once ROOT_DIR . '/DGGallery/app/config/config_gallery.php';
    if ($_config['coutnDownloadFile'])
        $db->query('UPDATE ' . PREFIX . "_dg_gallery_file SET download=download+1 WHERE id='{$row['id']}' LIMIT 1");
    $arr_name = explode('/', $row['path']);
    $ext = explode('.', end($arr_name));
    $type = end($ext);
    $allowed_extensions = array("gif", "jpg", "png", "jpe", "jpeg");
    if (!in_array($type, $allowed_extensions)) {
        exit;
    }
    $faile_title = ($row['title'] == '') ? end($arr_name) : totranslit($row['title'], true, true) . '.' . $type;

    $path = str_replace('%replace%/', 'original/', $row['path']);
}
$filename = ROOT_DIR . $path;
if (!file_exists($filename)) {
    header("HTTP/1.0 404 Not Found");
    exit;
}
$fsize = filesize($filename);
$ftime = date("D, d M Y H:i:s T", filemtime($filename));
$fd = @fopen($filename, "rb");
if (!$fd) {
    header("HTTP/1.0 403 Forbidden");
    exit;
}
if ($HTTP_SERVER_VARS["HTTP_RANGE"]) {
    $range = $HTTP_SERVER_VARS["HTTP_RANGE"];
    $range = str_replace("bytes=", "", $range);
    $range = str_replace("-", "", $range);
    if ($range) {
        fseek($fd, $range);
    }
}
$content = fread($fd, filesize($filename));
fclose($fd);
if ($range) {
    header("HTTP/1.1 206 Partial Content");
} else {
    header("HTTP/1.1 200 OK");
}
header("Content-Disposition: attachment; filename=$faile_title");
header("Last-Modified: $ftime");
header("Accept-Ranges: bytes");
header("Content-Length: " . ($fsize - $range));
header("Content-Range: bytes $range-" . ($fsize - 1) . "/" . $fsize);
header('Content-type: application/' . $type);
print $content;
exit;
?>
