<?php

/**
 * @author Dark Ghost
 * @copyright 2011
 */
abstract class model_request {

    /**
     * model_request::getPost()
     *
     * @param mixed $n
     * @return
     */
    public static function getPost($n) {
        return (isset($_POST [$n])) ? $_POST [$n] : null;
    }

    /**
     * model_request::getGet()
     *
     * @param string $n
     * @return mixed
     */
    public static function getGet($n) {
        return (isset($_GET [$n])) ? $_GET [$n] : null;
    }

    /**
     * model_request::getRequest()
     *
     * @param string $n
     * @return mixed
     */
    public static function getRequest($n) {
        return (isset($_REQUEST [$n])) ? $_REQUEST [$n] : null;
    }

    /**
     * model_request::isAjax()
     *
     * @return bool
     */
    public static function isAjax() {
        return ($_SERVER ['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }

    /**
     * model_request::setHeader()
     *
     * @return void
     */
    public static function setHeader() {
        global $config;
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1990 05:00:00 GMT');
        header("Content-type: text/css; charset=" . $config ['charset']);
    }

    /**
     * model_request::setHeaderJson()
     *
     * @return void
     */
    public static function setHeaderJson() {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1990 05:00:00 GMT');
        header('Content-type: application/json');
        global $config;
        header("Content-type: text/css; charset=" . $config ['charset']);
    }

    /**
     *
     * @global array $config
     * @param string $to
     */
    public static function _redirect($to) {
        global $config;
        if (null == $config) {
            require_once ROOT_DIR . 'engine/data/config.php';
        }
        header('Location: ' . $config ['http_home_url'] . $config ['admin_path'] . $to);
    }

    public static function redirect($to) {
        global $config;
        if (null == $config) {
            require_once ROOT_DIR . 'engine/data/config.php';
        }
        header('Location: ' . $config ['http_home_url'] . $to, false, 307);
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function setParam($name, $value) {
        self::setGet($name, $value);
        self::setPost($name, $value);
        self::setRequest($name, $value);
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function setPost($name, $value) {
        $_POST[$name] = $value;
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function setGet($name, $value) {
        $_GET[$name] = $value;
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function setRequest($name, $value) {
        $_REQUEST[$name] = $value;
    }

}
