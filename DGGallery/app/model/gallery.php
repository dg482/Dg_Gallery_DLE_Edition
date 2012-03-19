<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

class model_gallery
{

    /**
     * @var array
     * */
    protected static $_registry;

    /**
     * @var array
     * */
    protected $_config;

    /**
     *
     * @var string
     * */
    const VERSION = '1.5';
    /**
     *
     * @var object
     */
    protected $_db;

    /**
     *
     * @var object
     */
    protected $_cache;

    /**
     *
     * @var string
     */
    private static $_copy;

    /**
     *
     * @var bool
     */
    public static $debug;

    /**
     *
     * @var int (timestamp)
     */
    public static $start;

    /**
     *
     * @var array
     */
    public static $user;

    /**
     *
     * @var bool
     */
    protected $_isLogged;

    /**
     *
     * @var string info messages
     */
    public static $INFO;
    /**
     * @var string
     */
    public static $ORDER;

    /**
     *
     */
    public function __construct()
    {
        $this->_db = model_gallery::getClass('module_db');
        $this->_cache = model_gallery::getClass('model_cache_file');
        model_gallery::setRegistry('model_cache_file', $this->_cache);
        global $is_logged;
        $this->_isLogged = ($is_logged) ? true : false;
        self::$ORDER = '';
    }

    /**
     * @return bool
     */
    protected function _isAdmin()
    {
        if (class_exists('assistant')) { // adminpanel
            return (assistant::$_user['user_group'] == 1);
        }
        return (self::$user['user_group'] == 1);
    }

    /**
     * @static
     *
     */
    public static function init()
    {
        $config = null;
        if (file_exists(ROOT_DIR . '/DGGallery/app/config/config_gallery.php')) {
            $config = include ROOT_DIR . '/DGGallery/app/config/config_gallery.php';
        } else {
            //error
        }
        self::setRegistry('config', $config);
        if (!model_request::isAjax()) {
            self::setRegistry('view_template', new view_template());
        }
    }

    /**
     * @static
     * @return array|mixed|null|string
     */
    public static function run()
    {
        global $member_id;
        self::$user = $member_id;
        self::$_copy = '<div id="copy">Powered by D.G. Gallery ' . self::VERSION . ' &copy; 2010 - ' . date('Y') . '</div>';
        $Timer = null;
        $result = null;
        if (self::$debug) {
            $Timer = new microTimer ();
            $Timer->start();
        }
        self::init();
        $route = new model_route();
        $result = $route->route();
        if (self::$debug && !model_request::isAjax()) {
            model_debug::$time = $Timer->stop();
            self::$_copy .= model_debug::show();
        }
        if (model_request::isAjax()) {
            self::$_copy = '';
            if (is_array($result)) {
                model_debug::$time = $Timer->stop();
                if (is_array($result)) {
                    $debug = model_debug::show();
                    if (is_array($debug))
                        return array_merge($result, $debug);
                } else {
                    die();
                }
            }
            return $result;
        }
        $_debug = model_debug::show();
        $_debug = (is_array($_debug)) ? implode('', $_debug) : 'error 2';
        return (is_string($result)) ? $result . self::$_copy : 'error !' . $_debug;
    }

    /**
     * @static
     * @param $n
     * @param $obj
     */

    public static function setRegistry($n, $obj)
    {
        self::$_registry[$n] = $obj;
    }

    /**
     * @static
     * @param $n
     * @return mixed
     */
    public static function getRegistry($n)
    {
        if (null === self::$_registry[$n] && !is_object(self::$_registry[$n]) && !is_array(self::$_registry[$n])) {
            if (class_exists($n)) {
                $new = new $n();
                self::setRegistry($n, $new);
                return $new;
            }
        } else {
            return self::$_registry[$n];
        }
        #  return (is_object(self::$_registry[$n])) ? self::$_registry[$n] : null;
    }

    /**
     * @static
     * @return array|null
     */
    public static function getAllRegistry()
    {
        return (null == self::$_registry) ? null : self::$_registry;
    }

    /**
     * @static
     * @param $name
     * @return mixed
     */
    public static function getClass($name)
    {
        if (!is_object(self::$_registry [$name])) {
            $new = new $name ();
            self::setRegistry($name, $new);
            return $new;
        } else {
            return self::$_registry [$name];
        }
    }

    /**
     * @param $num
     * @param array $data
     * @param dle_template $tpl
     * @return bool|string
     */
    public function _nav($num, array $data, dle_template $tpl)
    {
        $data['current_page_d'] = (int)model_request::getRequest('page');
        if (0 === $data['current_page_d']) {
            $data['current_page_d'] = 1;
        }
        if (!$num) {
            return false;
        }
        if (!$data['global_query_end']) {
            return false;
        }
        $_page = ceil($num / $data['global_query_end']);

        if (!$_page) {
            return false;
        }
        $stop = false;
        //        $sp = '';
        //        $pp = '';
        //        $start = 1;
        //        $end = 3;
        //        $np = '';
        //        $ep = '';
        $_nav = '';
        self::$ORDER;
        if ($_page > 1) {
            if ($data['show_all_page']) {
                $start = 1;
                $end = 10;
                if ($data['current_page_d'] >= 6) {
                    $start = $data['current_page_d'] - 4;
                    $end = $data['current_page_d'] + 8;
                }
                if ($end > $_page) {
                    $end = $_page;
                }
            } else {
                $start = 1;
                $end = $_page;
            }

            $tpl->load_template('navigation.tpl');
            if ($data['current_page_d'] > 1) {

                $fn_f = ($data['ajax_nav_func'] != '') ? ' onclick="' . $data['ajax_nav_func'] . '(\'' . 1 . '\'); return false;" ' : '';
                $fn_p = ($data['ajax_nav_func'] != '') ? ' onclick="' . $data['ajax_nav_func'] . '(\'' . ($data['current_page_d'] - 1) . '\'); return false;" ' : '';


                if ($data['nav_prefix'] == 'javascript') {
                    $sp = '<a href="javascript:" ' . $data['prefix_css_nav_first_link'] . $fn_f . '>';
                    $pp = '<a href="javascript:" ' . $data['prefix_css_nav_back_link'] . $fn_p . '>';
                } else {
                    $sp = '<a href="' . HOME_URL . $data['nav_prefix'] . '/page,' . 1 . $data['nav_suffix'] . '"' . $data['prefix_css_nav_first_link'] . $fn_f . '>';
                    $pp = '<a href="' . HOME_URL . $data['nav_prefix'] . '/page,' . ($data['current_page_d'] - 1) . $data['nav_suffix'] . '"' . $data['prefix_css_nav_back_link'] . $fn_p . '>';
                }
                $tpl->set('[start-page]', $sp);
                $tpl->set('[/start-page]', '</a>');
                $tpl->set('[prev-link]', $pp);
                $tpl->set('[/prev-link]', '</a>');
            } else {
                $tpl->set_block("#\\[prev-link\\](.*?)\\[/prev-link\\]#is", '');
                $tpl->set_block("#\\[start-page\\](.*?)\\[/start-page\\]#is", '');
            }
            if ($data['current_page_d'] > 10) {
                $onclick = ($data['ajax_nav_func'] != '') ? ' onclick="' . $data['ajax_nav_func'] . '(\'1\'); return false;" ' : '';
                if ($data['global_prefix'] != '' and $data['nav_suffix'] != '') {
                    $href = ($data['nav_prefix'] == 'javascript') ? 'javascript:' : HOME_URL . $data['global_prefix'] . 1 . $data['nav_suffix'];
                    $_nav .= '<a href="' . $href . '"' . $onclick . $data['prefix_css_nav_link'] . '>' . 1 . '</a>';
                } elseif ($data['global_prefix']) {
                    $data['nav_prefix'] = $data['global_prefix'];
                }
                if (!$data['nav_suffix']) {
                    $href = ($data['nav_prefix'] == 'javascript') ? 'javascript:' : HOME_URL . $data['nav_prefix'] . '/page,' . 1;
                    $_nav .= '<a href="' . $href . '"' . $onclick . $data['prefix_css_nav_link'] . '>' . 1 . '</a>';
                } else {
                    $href = ($data['nav_prefix'] == 'javascript') ? 'javascript:' : HOME_URL . $data['nav_prefix'] . '/page,' . 1 . $data['nav_suffix'];
                    $_nav .= '<a href="' . $href . '"' . $onclick . $data['prefix_css_nav_link'] . '>' . 1 . '</a>';
                }
                $_nav .= '<span ' . $data['prefix_css_nav_link'] . '>...</span>';
            }
            for ($t = $start; $t <= $end; $t++) {
                if ($t <= $end) {
                    $onclick = ($data['ajax_nav_func'] != '') ? ' onclick="' . $data['ajax_nav_func'] . '(\'' . $t . '\'); return false;" ' : '';
                    if ($data['global_prefix'] != '') {
                        if ($data['current_page_d'] == $t) {
                            $_nav .= '<span ' . $data['prefix_css_nav_current'] . ' >' . $t . '</span>';
                        } else {
                            $href = ($data['nav_prefix'] == 'javascript') ? 'javascript:' : HOME_URL . $data['global_prefix'] . $t . $data['nav_suffix'];
                            $_nav .= '<a href="' . $href . '"' . $onclick . $data['prefix_css_nav_link'] . '>' . $t . '</a>';
                        }
                    } elseif ($data['global_prefix']) {
                        $data['nav_prefix'] = $data['global_prefix'];
                    }
                    if (($data['current_page_d'] == $t)) {
                        $_nav .= '<span ' . $data['prefix_css_nav_current'] . '>' . $t . '</span>';
                    } else {
                        if (!$data['nav_suffix']) {
                            $href = ($data['nav_prefix'] == 'javascript') ? 'javascript:' : HOME_URL . $data['nav_prefix'] . '/page,' . $t . '" ';
                            $_nav .= '<a href="' . $href . '"' . $onclick . $data['prefix_css_nav_link'] . ' >' . $t . '</a>';
                        } else {
                            $href = ($data['nav_prefix'] == 'javascript') ? 'javascript:' : HOME_URL . $data['nav_prefix'] . '/page,' . $t . $data['nav_suffix'];
                            $_nav .= '<a href="' . $href . '" ' . $onclick . $data['prefix_css_nav_link'] . '>' . $t . '</a>';
                        }
                    }
                }
            }
            if ($data['current_page_d'] == $_page) {
                $stop = true;
            }
            $tpl->set('{pages}', $_nav);
            /////////////----------------
            $fn_n = ($data['ajax_nav_func'] != '') ? ' onclick="' . $data['ajax_nav_func'] . '(\'' . ($data['current_page_d'] + 1) . '\'); return false;" ' : '';
            $fn_l = ($data['ajax_nav_func'] != '') ? ' onclick="' . $data['ajax_nav_func'] . '(\'' . $_page . '\'); return false;" ' : '';
            if (!$stop) {

                if ($data['nav_prefix'] == 'javascript') {
                    $np = '<a href="javascript:"' . $data['prefix_css_nav_next_link'] . $fn_n . '>';
                    $ep = '<a href="javascript:"' . $data['prefix_css_nav_end_link'] . $fn_l . '>';
                } else {
                    $np = '<a href="' . HOME_URL . $data['nav_prefix'] . '/page,' . ($data['current_page_d'] + 1) . $data['nav_suffix'] . '"' . $data['prefix_css_nav_next_link'] . $fn_n . '>';
                    $ep = '<a href="' . HOME_URL . $data['nav_prefix'] . '/page,' . ($_page) . $data['nav_suffix'] . '"' . $data['prefix_css_nav_end_link'] . $fn_l . '>';
                }
                $tpl->set('[next-link]', $np);
                $tpl->set('[/next-link]', '</a>');
                $tpl->set('[end-page]', $ep);
                $tpl->set('[/end-page]', '</a>');
            } else {
                $tpl->set_block("#\\[next-link\\](.*?)\\[/next-link\\]#is", '');
                $tpl->set_block("#\\[end-page\\](.*?)\\[/end-page\\]#is", '');
            }
            $tpl->compile('nav');
            return $tpl->result['nav'];
        }
        else
            return '';
    }

    /**
     * @static
     * @param $timestamp
     * @param bool $convert
     * @return string
     */
    public static function dateFormat($timestamp, $convert = TRUE)
    {
        global $_TIME, $lang, $config;
        if ($convert)
            $timestamp = strtotime($timestamp);
        if (date(Ymd, $timestamp) == date(Ymd, $_TIME)) {
            return $lang['time_heute'] . langdate(", H:i", $timestamp);
        } elseif (date(Ymd, $timestamp) == date(Ymd, ($_TIME - 86400))) {
            return $lang['time_gestern'] . langdate(", H:i", $timestamp);
        } else {
            return langdate($config['timestamp_comment'], $timestamp);
        }
    }

    /**
     * @static
     * @param $file_size
     * @return string
     */
    public static function formatsize($file_size)
    {
        if ($file_size >= 1073741824) {
            $file_size = round($file_size / 1073741824 * 100) / 100 . " Gb";
        } elseif ($file_size >= 1048576) {
            $file_size = round($file_size / 1048576 * 100) / 100 . " Mb";
        } elseif ($file_size >= 1024) {
            $file_size = round($file_size / 1024 * 100) / 100 . " Kb";
        } else {
            $file_size = $file_size . " b";
        }
        return $file_size;
    }

}