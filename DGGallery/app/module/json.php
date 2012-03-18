<?php

/**
 * @author Dark Ghost
 * @copyright 25.3.2011
 * @package dle92
 * Назначение:
 */
class module_json
{
    public function __construct()
    {
    }
    public static function getJson( $arr)
    {
        return json_encode(self::convert('cp1251', 'utf-8', $arr));
    }
    protected static function convert($from, $to, $var)
    {
        if (is_array($var))
        {
            $new = array();
            foreach ($var as $key => $val)
            {
                $new[self::convert($from, $to, $key)] = self::convert($from, $to, $val);
            }
            $var = $new;
        } else
            if (is_string($var))
            {
                $var = iconv($from, $to, $var);
            }
        return $var;
    }
    public static function convertToCp($arr)
    {
        return self::convert('utf-8', 'cp1251', $arr);
    }
}
