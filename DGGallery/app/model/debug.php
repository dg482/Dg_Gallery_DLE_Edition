<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

class model_debug
{

    /**
     * @var
     */
    protected static $_mysql;
    /**
     * @var
     */
    protected static $_query;

    /**
     * @var
     */
    public static $time;

    /**
     * @var
     */
    public static $MySQL_time_taken;

    /**
     * @var
     */
    public static $m;

    /**
     * @static
     * @param $num
     */
    public static function mysql($num)
    {
        self::$_mysql += $num;
    }

    /**
     * @static
     * @param $query
     */
    public static function setQuery($query)
    {
        self::$_query[] = $query;
    }

    /**
     * @static
     * @return array
     */
    public static function get()
    {
        return array(
            'mysql' => array(
                'num' => self::$_mysql,
                'query' => self::$_query
            )
        );
    }

    /**
     * @static
     * @return array|string
     */
    public static function show()
    {
        $config = model_gallery::getRegistry('config');
        $user = model_gallery::$user;
        if ($config['debugAccessGroup'] != '') {
            $config['debugAccessGroup'] = explode(',', $config['debugAccessGroup']);
        } else {
            $config['debugAccessGroup'] = array(1);
        }

        if (!$config['debug'])
            return;
        if (!in_array($user['user_group'], $config['debugAccessGroup']))
            return;

        $t = round(self::$time, 5);
        $db = null;
        $m = round((((memory_get_peak_usage() / 1024) / 1024) - self::$m), 2);
        $db = model_gallery::getRegistry('module_db');
        if (is_object($db))
            $q = $db->query_num;


        $qq = print_r(self::$_query, true);
        $inc = print_r(get_included_files(), true);
        $re = print_r($_REQUEST, true);
        $tm = round(self::$MySQL_time_taken, 6);
        $reg = print_r(array_keys(model_gallery::getAllRegistry()), true);
        if (model_request::isAjax()) {
            return array(
                'debug' => array(
                    'Время выполнения скрипта' => $t . 'сек',
                    'Затрачено памяти' => $m . 'mb',
                    'MySQL запросов' => $q,
                    'Время выполнения' => $tm . 'сек',
                    'query' => $qq,
                    'реестр' => $reg,
                    'request' => $re,
                    'Подключенные файлы' => $inc
                )
            );
        } else {
            return <<<HTML
Время выполнения скрипта: ~ $t сек<br />
Затрачено памяти: ~ $m mb<br />
MySQL запросов: $q<br />
Время выполнения: ~ $tm сек<br />
<pre>  $qq </pre>
Реестр:
<pre>  $reg </pre>
\$_REQUEST
 <pre>  $re </pre>
Подключенные файлы:
<pre> $inc </pre>
HTML;
        }
    }

}