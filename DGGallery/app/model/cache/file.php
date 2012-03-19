<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

class model_cache_file
{

    /**
     * @var string
     */
    private static $dir;
    /**
     * @var string
     */
    private static $dirJson;
    /**
     *
     */
    const CACHE_SYSTEM = '/DGGallery/cache/system/';
    /**
     *
     */
    const CAHCHE_JSON = '/DGGallery/cache/json/';

    /**
     *
     */
    public function __construct()
    {
        self::$dir = ROOT_DIR . self::CACHE_SYSTEM;
        self::$dirJson = ROOT_DIR . self::CAHCHE_JSON;
    }

    /**
     * @static
     * @param $name
     * @return mixed|null
     */
    public static function get($name)
    {
        return (file_exists(self::$dir . $name . '.php')) ? unserialize(file_get_contents(self::$dir . $name . '.php')) : null;
    }

    /**
     * @static
     * @param $name
     */
    public static function delete($name)
    {
        if ((file_exists(self::$dir . $name . '.php'))) {
            unlink(self::$dir . $name . '.php');
        }
        if (file_exists(self::$dirJson . $name . '.json')) {
            unlink(self::$dirJson . $name . '.json');
        }
    }

    /**
     * @static
     * @param $name
     * @return null|string
     */
    public static function getJson($name)
    {
        return (file_exists(self::$dirJson . $name . '.json')) ? file_get_contents(self::$dirJson . $name . '.json') : null;
    }

    /**
     * @param $name
     * @param $data
     */
    public function setCache($name, $data)
    {
        $file = fopen(self::$dir . $name . '.php', "wb+");
        fwrite($file, serialize($data));
        fclose($file);
        @chmod($file, '666');
    }

    /**
     * @param $name
     * @param $data
     */
    public function setCacheJson($name, $data)
    {
        $mod_json = new module_json ();
        $file = fopen(self::$dirJson . $name . '.json', "wb+");
        fwrite($file, $mod_json->getJson($data));
        fclose($file);
        @chmod($file, '666');
    }

}

?>