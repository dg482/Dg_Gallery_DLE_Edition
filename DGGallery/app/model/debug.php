<?php

/**
 * @package gallery
 * @author Dark Ghost
 * @copyright 2011
 * @access public
 * @since 1.5.2 (06.2011)
 */
class model_debug {

    protected static $_mysql;
    protected static $_query;
    public static $time;
    public static $MySQL_time_taken;
    public static $m;

    public static function mysql($num) {
        self::$_mysql += $num;
    }

    public static function setQuery($query) {
        self::$_query[] = $query;
    }

    public static function get() {
        return array(
            'mysql' => array(
                'num' => self::$_mysql,
                'query' => self::$_query
            )
        );
    }

    public static function show() {
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
                    '����� ���������� �������' => $t . '���',
                    '��������� ������' => $m . 'mb',
                    'MySQL ��������' => $q,
                    '����� ����������' => $tm . '���',
                    'query' => $qq,
                    '������' => $reg,
                    'request' => $re,
                    '������������ �����' => $inc
                )
            );
        } else {
            return <<<HTML
����� ���������� �������: ~ $t ���<br />
��������� ������: ~ $m mb<br />
MySQL ��������: $q<br />
����� ����������: ~ $tm ���<br />
<pre>  $qq </pre>
������:
<pre>  $reg </pre>
\$_REQUEST
 <pre>  $re </pre>
������������ �����:
<pre> $inc </pre>
HTML;
        }
    }

}

