<?php

/**
 * @author Dark Ghost
 * @copyright 15.3.2011
 * @package dle92
 * Назначение:
 */
class model_cache_file {

    private static $dir;
    private static $dirJson;
    const CACHE_SYSTEM = '/DGGallery/cache/system/';
    const CAHCHE_JSON = '/DGGallery/cache/json/';

    /**
     * model_cache_file::__construct()
     *
     * @return void
     */
    public function __construct() {
        self::$dir = ROOT_DIR . self::CACHE_SYSTEM;
        self::$dirJson = ROOT_DIR . self::CAHCHE_JSON;
    }

    /**
     * model_cache_file::get()
     *
     * @param mixed $name
     * @return
     */
    public static function get($name) {
        return (file_exists(self::$dir . $name . '.php')) ? unserialize(file_get_contents(self::$dir . $name . '.php')) : null;
    }

    public static function delete($name) {
        if ((file_exists(self::$dir . $name . '.php'))) {
            unlink(self::$dir . $name . '.php');
        }
        if (file_exists(self::$dirJson . $name . '.json')) {
            unlink(self::$dirJson . $name . '.json');
        }
    }

    /**
     * model_cache_file::getJson()
     *
     * @return
     */
    public static function getJson($name) {
        return (file_exists(self::$dirJson . $name . '.json')) ? file_get_contents(self::$dirJson . $name . '.json') : null;
    }

    /**
     * model_cache_file::setCache()
     *
     * @param mixed $name
     * @param mixed $data
     * @return void
     */
    public function setCache($name, $data) {
        $file = fopen(self::$dir . $name . '.php', "wb+");
        fwrite($file, serialize($data));
        fclose($file);
        @chmod($file, '666');
    }

    /**
     * model_cache_file::setCacheJson()
     *
     * @param mixed $name
     * @param mixed $data
     * @return void
     */
    public function setCacheJson($name, $data) {
        $mod_json = new module_json ();
        $file = fopen(self::$dirJson . $name . '.json', "wb+");
        fwrite($file, $mod_json->getJson($data));
        fclose($file);
        @chmod($file, '666');
    }

}

?>