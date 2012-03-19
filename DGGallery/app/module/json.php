<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

class module_json
{
    /**
     * @static
     * @param $arr
     * @return string
     */
    public static function getJson($arr)
    {
        return json_encode(self::convert('cp1251', 'utf-8', $arr));
    }

    /**
     * @static
     * @param $from
     * @param $to
     * @param $var
     * @return array|string
     */
    protected static function convert($from, $to, $var)
    {
        if (is_array($var)) {
            $new = array();
            foreach ($var as $key => $val)
            {
                $new[self::convert($from, $to, $key)] = self::convert($from, $to, $val);
            }
            $var = $new;
        } else
            if (is_string($var)) {
                $var = iconv($from, $to, $var);
            }
        return $var;
    }

    /**
     * @static
     * @param $arr
     * @return array|string
     */
    public static function convertToCp($arr)
    {
        return self::convert('utf-8', 'cp1251', $arr);
    }
}
