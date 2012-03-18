<?php

/**
 * Маршрутизатор,то еще ..............................
 *
 * @package gallery
 * @author Dark Ghost
 * @copyright 2011
 * @access public
 * @since 1.5.3 (07.2011)
 *
 */
class model_route extends model_gallery {

    /**
     *
     * @var string
     */
    protected $_controller;
    /**
     *
     * @var array
     */
    protected $_params;
    /**
     *
     * @var string
     */
    protected $_action;
    /**
     *
     * @var string
     */
    protected $_result;
    /**
     *
     * @var string
     */
    public static $action;
    /**
     *
     * @var string
     */
    protected $_uri;

    public function __construct() {
        $this->_uri = trim($_SERVER['REQUEST_URI'], '/');
        $uri = explode('/', $this->_uri);
        $this->_controller = ($controller = array_shift($uri)) ? $controller : 'gallery'; // only gallery or ajax_gallery
        if (stripos($_SERVER['REQUEST_URI'], 'ajax') !== false) {
            $this->_controller = 'ajax_gallery';
            $this->_action = strtolower(model_request::getRequest('action'));
        } else {
            $this->_action = ($action = array_shift($uri)) ? $action : 'index';
            $this->_action = strtolower($this->_action);
        }
        if (stripos($_SERVER['REQUEST_URI'], 'user') !== false) {
            $this->_controller = 'user';
            $this->_action = strtolower(array_shift($uri));
        }
        $this->_params = array();
        while (count($uri)) {
            $n = array_shift($uri);
            $this->_params[] = $n;
        }
        $this->_result = null;
        model_gallery::setRegistry('route', $this);

    }

    public function getServerURI() {
        return $this->_uri;
    }

    /**
     * Определение параметров запроса.
     * @param string $str
     */
    private function _setParam($str) {
        $s = null;
        if (preg_match("#page,(\d+)#", $str, $s)) {
            model_request::setParam('page', $s[1]);
        }
        if (preg_match("#(\d+)-([^/]*)#", $str, $s)) {
            model_request::setParam('id', $s[1]);
            model_request::setParam('url_title', $s[2]);
        }
        if (preg_match("#(\d+)-([^.]*)[.](\d+)#", $str, $s)) {
            model_request::setParam('id', $s[1]);
            model_request::setParam('url_title', $s[1]);
            model_request::setParam('id_file', $s[3]);
        }
    }

    /**
     * Получение параметов запроса.
     * @return array
     */
    public function getParam() {
        return $this->_params;
    }

    public function getAction() {
        return $this->_action;
    }

    /**
     * Вычисление маршрута, определение запрошенного контроллера, вызов деиствия,
     * если деиствия не существует в контроллере вызов indexAction() контроллера по умолчанию
     * @return mixed
     */
    public function route() {
        if (class_exists('controller_' . $this->_controller)) {
            $ctrl = 'controller_' . $this->_controller;
            $method = get_class_methods($ctrl);
            $action = 'index';
            if (is_array($this->_params)) {
                foreach ($this->_params as $p) {
                    $this->_setParam($p);
                }
            }
            if (is_array($method) && in_array($this->_action . 'Action', $method)) {
                $controller = new $ctrl;
                model_gallery::setRegistry($ctrl, $controller);
                $action = $this->_action . 'Action';
                self::$action = $this->_action;
                if (model_request::isAjax()) {
                    return $controller->$action();
                } else {
                    $this->_result = $controller->$action();
                }
            } else {
                #  throw new Exception('not method exists');
                $ctrl = new controller_gallery;
                $this->_result = $ctrl->indexAction();
            }
        }
        # var_dump($this->_action, $this->_controller, $this->_params);
        return $this->_result;
    }

}

