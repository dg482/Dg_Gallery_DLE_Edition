<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

class install {

    protected $_action;
    protected $_db;
    public $lang;

    /**
     *
     */
    public function __construct() {
        $this->_action = (isset($_REQUEST ['action'])) ? trim($_REQUEST ['action']) : 'index';

        $this->lang = include ROOT_DIR . '/DGGallery/app/lang/gallery.php';
    }

    /**
     * @return string
     */
    public function init() {
        $result = $this->setHeader();
        $result .= '<div class="step">';
        $result .= $this->_getLic();
        $result .= '</div>';
        $result .= $this->_getInfo();
        $result .= $this->_configure();
        $result .= $this->setFooter();
        return $result;
    }

    /**
     * @return bool|string
     */
    protected function _getLic() {
        return (file_exists(ROOT_DIR . '/DGGallery/cache/system/page/lic.tmp')) ?
            file_get_contents(ROOT_DIR . '/DGGallery/cache/system/page/lic.tmp') : false;
    }

    /**
     * @return string
     */
    protected function GetOsServer() {
        return @php_uname("s") . " " . @php_uname("r");
    }

    /**
     * install::GetRevInfo()
     *
     * @return
     */
    protected function GetRevInfo() {
        if (function_exists('apache_get_modules')) {
            if (array_search('mod_rewrite', apache_get_modules())) {
                return $this->lang ['info'] ['enable'];
            } else {
                return $this->lang ['info'] ['disable'];
            }
        } else {
            return $this->lang ['info'] ['undefined'];
        }
    }

    /**
     * @return string
     */
    protected function GetGDInfo() {
        if (function_exists('gd_info')) {
            $array = gd_info();
            $info = "";
            foreach ($array as $key => $val) {
                if ($val === true) {
                    $val = "Enabled";
                }
                if ($val === false) {
                    $val = "Disabled";
                }
                $info .= $key . ":&nbsp;{$val}, ";
            }
            return $info;
        } else {
            return $this->lang ['info'] ['undefined'];
        }
    }

    /**
     * @return string
     */
    protected function checkDir() {
        $file_status = '';
        $html = '';
        $files = array(
            ROOT_DIR . '/DGGallery/app/config/',
            ROOT_DIR . '/DGGallery/app/config/adminForms/',
            ROOT_DIR . '/DGGallery/app/config/config_gallery.php',
            ROOT_DIR . '/DGGallery/cache/',
            ROOT_DIR . '/DGGallery/cache/json/',
            ROOT_DIR . '/DGGallery/cache/system/',
            ROOT_DIR . '/uploads/gallery/'
        );
        foreach ($files as $file) {
            if (!file_exists($file)) {
                $file_status = "<font color=red>не найден!</font>";
            } elseif (is_writable($file)) {
                $file_status = "<font color=green>разрешено</font>";
            } else {
                @chmod($file, 0777);
                if (is_writable($file)) {
                    $file_status = "<font color=green>разрешено</font>";
                } else {
                    @chmod("$file", 0755);
                    if (is_writable($file)) {
                        $file_status = "<font color=green>разрешено</font>";
                    } else {
                        $file_status = "<font color=red>запрещено</font>";
                    }
                }
            }
            $chmd = @decoct(@fileperms($file)) % 1000;
            $file_ = str_replace(ROOT_DIR, '.', $file);
            $html .= <<< HTML
<tr>
<td>{$file_}</td>
<td>{$file_status}: ({$chmd})</td>
</tr>
HTML;
        }
        return $html;
    }

    /**
     * install::checkFFMpeg()
     *
     * @return
     */
    protected function checkFFMpeg() {
        return (extension_loaded('ffmpeg')) ? $this->lang ['info'] ['enable'] : $this->lang ['info'] ['disable'];
    }


    /**
     * @return string
     */
    protected function _getInfo() {
        $php = phpversion();
        $os = $this->GetOsServer();
        $mr = $this->GetRevInfo();
        $gd = $this->GetGDInfo();
        $fc = $this->checkDir();
        $ffmpeg = $this->checkFFMpeg();
        return <<< HTML
<div class="check b-4 step">
<table width="100%">
<tr>
<td width="200">Версия php:</td>
<td>$php</td>
</tr>
<tr>
<td>Операционная система:</td>
<td>{$os}</td>
</tr>
<tr>
<td>Module mod_rewrite:</td>
<td>$mr</td>
</tr>
<tr>
<td>Extension FFMPEG:</td>
<td>$ffmpeg</td>
</tr>
<tr>
<td>Информация о GD:</td>
<td>{$gd}</td>
</tr>
{$fc}
</table>
</div>
HTML;
    }

    /**
     * @return bool
     */
    protected function checkOld() {
        $this->_setDbAdapter();
        $this->_db->query('SHOW TABLES');
        $file = false;
        while ($row = $this->_db->get_row()) {
            if (in_array(PREFIX . '_gallery_file', $row)) {
                $file = true;
            }
        }
        return $file;
    }

    /**
     * install::_setDbAdapter()
     *
     * @return void
     */
    protected function _setDbAdapter() {
        if (file_exists(ROOT_DIR . '/engine/classes/mysql.php')) {
            define('DATALIFEENGINE', true);
            define('ENGINE_DIR', ROOT_DIR . '/engine');
            include ROOT_DIR . '/engine/classes/mysql.php';
            include ROOT_DIR . '/engine/data/dbconfig.php';
            unset($db);
            require ROOT_DIR . '/DGGallery/app/module/db.php';
            $this->_db = new module_db ();
        } else {
            //no DLE
        }
    }

    /**
     * @return string
     */
    protected function _configure() {
        $old = '';
        $dle = '';
        $config = null;
        if (file_exists(ROOT_DIR . '/engine/data/config.php')) {
            include ROOT_DIR . '/engine/data/config.php';
            $dle_version = $config ['version_id'];
            $dle = <<< HTML
<tr>
<td width="200">Версия DLE:</td>
<td>{$dle_version}</td>
</tr>
HTML;
            if ($this->checkOld())
                $old = <<< HTML
<div class="warning b-4 ">
<p>{$this->lang['info']['old_version']}</p>
<a class="close" href="javascript:">close</a>
</div>
<div class="error b-4">
<p>{$this->lang['info']['verion_limit_1']}</p>
<a class="close" href="javascript:">close</a>
</div>
<table width="100%" border="1">
<thead>
<tr>
<td colspan="2">{$this->lang['restore']['title_import']}</td>
</tr>
</thead>
<tr><td width="250">{$this->lang['restore']['file']}&nbsp;</td>
<td height="20"><p class="switch">
<label class="label"> </label>
<label for="file" class="on active"></label>
<label for="file" class="off"></label><input type="checkbox" name="file" checked="checked" id="file" value="1" />
</p></td></tr><tr>
<td>{$this->lang['restore']['cat']}&nbsp;</td><td>
<p class="switch">
<label class="label"> </label>
<label for="cat" class="on active"></label>
<label for="cat" class="off"></label>
<input type="checkbox" name="cat" checked="checked" id="cat" value="1" /></p>
</td></tr>
</table>
HTML;
        } else {
            //no DLE CMS
        }

        return <<< HTML
<div class="step">
<form name="install" id="install" action="#" method="post">
<div class="check b-4">
<table width="100%">
{$dle}
<tr>
<td>Конфигурация: </td>
<td><select name="GALLERY_MODE">
<option value="1">Портал</option>
<option value="2">Архив</option>
</select>
</td>
</tr>
</table>
{$old}
<input type="hidden" name="action" value="install" />
<input type="hidden" name="type" value="json" />
<button class="buttons b-4" onclick="installStart(); return false;">продолжить</button>
<div id="proccess">
<div class="info b-4 hidden">
<p>{$this->lang['info']['process']}: <b id="count"></b></p>
<a class="close" href="javascript:">close</a>
</div>
<div class="success b-4 hidden">
<p>{$this->lang['info']['install_ok']}</p>
<a class="close" href="javascript:">close</a>
</div>
</div>
</div></form>  </div>
HTML;
    }

    /**
     * @return string
     */
    protected function setHeader() {
        return <<< HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Install D.G. Gallery 1.5</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/block.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="js/assets.js"></script>
</head>
<body>
<div style="overflow:hidden; width:100%">
<div id="top"><a class="log"></a> </div>
</div>
<div id="content" style="left:200px; right:200px;">
<div class="block b-10 edit-images" style="height:600px;">
<div id="work-area-top-bar">
<ul class="nav">
<li class="logo"></li>
<li class="loading"></li>
</ul>
</div>
<div id="work-area-side-bar">
<ul class="nav">
<li class="selected"><a href="#"  class="license"></a></li>
<li><a href="#"  class="caution"></a></li>
<li><a href="#"  class=" command"></a></li>
</ul>
</div>
<div id="work-area">
HTML;
    }

    /**
     * @return string
     */
    protected function setFooter() {
        return <<< HTML
</div></div></div></body>
</html>
HTML;
    }

    /**
     * @return string
     */
    public function InstallGallery() {
        $inf = array();
        $sql = false;
        if (isset($_POST['GALLERY_MODE'])) {
            $_POST['GALLERY_MODE'] = intval($_POST['GALLERY_MODE']);
            $config = new model_config();
            $config->saveToArray(
                array(
                    'mode' => $_POST['GALLERY_MODE']
                )
            );
        }
        $this->_setDbAdapter();
        $query = file_get_contents(ROOT_DIR . '/installGallery/install.sql');
        $query = str_replace('##', PREFIX, $query);
        $sql ['system'] = explode(';', $query);

        if ($sql)
            foreach ($sql as $v) {
                foreach ($v as $query) {
                    $this->_db->query($query);
                }
            }

        $inf ['stopimport'] = 1;
        require ROOT_DIR . '/DGGallery/app/module/json.php';
        $json = new module_json ();
        return $json->getJson($inf);
    }

}