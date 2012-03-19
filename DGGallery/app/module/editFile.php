<?php

/**
 * Простои файовый редактор, зависимости module_json
 *
 * @package dg_r
 * @author Dark Ghost
 * @copyright 2010
 * @access public
 */
class module_editFile extends assistant {

    /**
     * @var bool
     *
     * */
    protected $_access;
    /**
     * @var string
     *
     * */
    public $dir;
    /**
     * @var string
     *
     * */
    protected $_tplDir;
    /**
     * @var array
     *
     * */
    protected $_list;
    /**
     * @var array
     *
     * */
    protected $_editExt;
    /**
     * Директория шаблонов
     */
    const DIR = 'templates/';

    /**
     *
     */
    public function __construct() {
        $this->_config = parent::$_registry['config'];

        $this->_access = (intval(parent::$_user['user_group']) === 1) ? true : false;

        if (!$this->_access) {
            die('ACCESS DENIED !');
        }

        global $config;
        $this->_tplDir = ROOT_DIR .'/'. self::DIR . $config['skin'] . '/gallery/';
        $this->_editExt = array('tpl', 'css', 'js', 'html', 'png', 'gif', 'jpg',
            /* 'php','tmp','json' */                );
    }

    /**
     * Чтение директории
     * set  array
     * @return void
     */
    protected function getInfoDir() {
        if (null === $this->dir) {
            $this->_list['currentDir'] = self::DIR;
        }
        $dDir = opendir($this->_tplDir);

        while ($sFileName = readdir($dDir)) {

            if ($sFileName != '.' && $sFileName != '..') {
                if (is_dir($this->_tplDir . $sFileName)) {
                    if ($this->dir) {
                        $this->_list['dir'][] = $this->dir . '/' . $sFileName;
                    } else {
                        $this->_list['dir'][] = $sFileName;
                    }
                }
                if (is_file($this->_tplDir . $sFileName)) {
                    $ext = end(explode('.', $sFileName));
                    if (in_array($ext, $this->_editExt)) {
                        $this->_list['file'][] = $this->dir . '/' . $sFileName;
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getStart() {
        $this->getInfoDir();
        return module_json::getJson($this->_list);
    }

    /**
     * @return string (json obj)
     */
    protected function _setCurrentDir() {
        $this->dir = model_request::getRequest('dir');
        if ($this->dir) {
            $this->dir = trim(strip_tags($this->dir));
            $replace = array(".php", "\\", '.htaccess');
            $this->dir = str_replace($replace, '', $this->dir);
            $this->dir = preg_replace("/[^a-z0-9\/\_\-]+/mi", "", $this->dir);
            if (is_dir($this->_tplDir . $this->dir . DIRECTORY_SEPARATOR)) {
                $this->_tplDir = $this->_tplDir . $this->dir . DIRECTORY_SEPARATOR;
                return $this->getStart();
            }
        }
    }

    /**
     * @param $file
     * @return bool
     */
    protected function _openFileEdit($file) {
        if (file_exists($this->_tplDir . $file)) {
            $info = pathinfo($this->_tplDir . $file);
            if (!$this->_check($info))
                return false;
            if (file_exists($this->_tplDir . $file) && is_writable($this->_tplDir . $file)) {
                $this->_list['fileedit']['content'] = file_get_contents($this->_tplDir . $file);
                $this->_list['fileedit']['comment'] = 'ok';
                $this->_list['fileedit']['extension'] = $info['extension'];
            } else {
                $this->_list['fileedit']['content'] = file_get_contents($this->_tplDir . $file);
                $this->_list['fileedit']['comment'] = 'only read';
                $this->_list['fileedit']['extension'] = $info['extension'];
            }
            $this->_list['fileedit']['tag'] = $this->_getTemplateTag($info['filename']);
            return true;
        }
    }

    /**
     * @param null $info
     * @return bool
     */
    protected function _check($info = null) {
        if (null == $info) {
            die('');
        }
        //check ext
        if (!in_array($info['extension'], $this->_editExt)) {
            $this->_list['fileedit']['content'] = 'access denied';
            $this->_list['fileedit']['comment'] = 'access denied';
            return false;
        }
        $info['dirname'] = str_replace(ROOT_DIR . '/', '', $info['dirname']);
        $dir = explode('/', $info['dirname']);
        //check dir$dir[0] != 'DGGallery' ||
        if ( $dir[0] != 'templates') {
            $this->_list['fileedit']['content'] = 'access denied';
            $this->_list['fileedit']['comment'] = 'access denied';
            return false;
        }
        return true;
    }

    /**
     *
     * @param mixed $key
     * @return array
     */
    protected function _getTemplateTag($key) {
        if (!file_exists(ROOT_DIR . '/DGGallery/app/config/templateTag.php')) {
            return null;
        }
        $tag = require_once ROOT_DIR . '/DGGallery/app/config/templateTag.php';
        return $tag[$key];
    }

    /**
     * @param $file
     */
    protected function _saveFile($file) {
        $info = pathinfo($this->_tplDir . $file);
        if ($this->_check($info)) {
            if (is_writable($this->_tplDir . $file)) {
                $data = model_request::getPost('data');
                if ($data == '') {
                    die();
                }
                $data = stripslashes(module_json::convertToCp($data));
                $file = fopen($this->_tplDir . $file, "wb+");
                fwrite($file, $data);
                fclose($file);
            }
        }
    }

    /**
     * @return string|void
     */
    public function go() {
        $action = model_request::getRequest('cmd');
        $file = model_request::getPost('file');
        switch ($action) {
            case 'open':
                if (null === $file) {
                    $this->_setCurrentDir();
                } else {
                    $this->_openFileEdit($file);
                }
                return module_json::getJson($this->_list);
            case 'save':
                return $this->_saveFile($file);
        }
    }

}
