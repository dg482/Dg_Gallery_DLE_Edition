<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

class model_metaTag
{
    /**
     * @var array
     *
     * */
    private $_config;

    /**
     * @param model_config $config
     */
    public function __construct(model_config $config)
    {
        $this->_config = $config->getSection('metatag');
    }
    /**
     * model_metaTag::set()
     *
     * @return void
     */
    public function set()
    {
        if ($this->_config['dle'])
        {
            global $metatags;
        }
    }
    /**
     * @static
     * @param $str
     * @param int $l
     * @param int $n
     * @return array|string
     */
    public static function getKeyword($str, $l = 10, $n = 20)
    {
        $str = str_replace(array('<br />', '<br>', '<br/>'), ' ', $str);
        $fastquotes = array("\x22", "\x60", "\t", "\n", "\r", '"', '\r', '\n', "$", "{",
            "}", "[", "]", "<", ">", ',', '.', '&nbsp;');
        $str = str_replace($fastquotes, '', stripslashes(strip_tags($str)));
        $keyword_ = array();
        $key_ = explode(' ', trim($str));
        if (count($key_) > 2)
        {
            foreach ($key_ as $keyword)
            {
                if (strlen($keyword) >= $l)
                {
                    $keyword_[] = trim($keyword);
                }
            }
            if(is_array($keyword_)){
                $key_word = array_count_values($keyword_);
                arsort($key_word);
                $key_word = array_keys($key_word);
                $key_word = array_slice($key_word, 0, $n);
                return $key_word;
            }else{
                return '';
            }

        }
    }
    /**
     * @static
     * @return string
     */
    public static function getDescr()
    {
        $_REQUEST['text'] = str_replace('&nbsp;', '', $_REQUEST['text']);
        return substr(trim(strip_tags(stripslashes($_REQUEST['text']))), 0, 300);
    }
    /**
     * @static
     * @param $var
     * @return mixed|string
     */
    public static function totranslit($var)
    {
        $langtranslit = array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' =>
            'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' =>
            's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' =>
            'yu', 'я' => 'ya', "ї" => "yi", "є" => "ye", 'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' =>
            'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' =>
            'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' =>
            'C', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya', "Ї" => "yi", "Є" => "ye", );
        $var = str_replace(".php", "", $var);
        $var = trim(strip_tags($var));
        $var = preg_replace("/\s+/ms", "-", $var);
        $var = strtr($var, $langtranslit);
        $var = preg_replace("/[^a-z0-9\_\-.]+/mi", "", $var);
        $var = preg_replace('#[\-]+#i', '-', $var);
        $var = strtolower($var);
        if (strlen($var) > 200)
        {
            $var = substr($var, 0, 200);
            if (($temp_max = strrpos($var, '-')))
                $var = substr($var, 0, $temp_max);
        }
        return $var;
    }
}
