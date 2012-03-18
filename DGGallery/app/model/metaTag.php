<?php

/**
 * Description of metaTag
 *
 * @author Dark Ghost
 */
class model_metaTag
{
    /**
     * @var array
     *
     * */
    private $_config;
    /**
     * model_metaTag::__construct()
     *
     * @param mixed $config
     * @return void
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
     * model_metaTag::getKeyword()
     *
     * @param mixed $str
     * @param integer $l
     * @param integer $n
     * @return
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
     * model_metaTag::getDescr()
     *
     * @return
     */
    public static function getDescr()
    {
        $_REQUEST['text'] = str_replace('&nbsp;', '', $_REQUEST['text']);
        return substr(trim(strip_tags(stripslashes($_REQUEST['text']))), 0, 300);
    }
    /**
     * model_metaTag::totranslit()
     *
     * @param mixed $var
     * @return
     */
    public static function totranslit($var)
    {
        $langtranslit = array('à' => 'a', 'á' => 'b', 'â' => 'v', 'ã' => 'g', 'ä' => 'd',
            'å' => 'e', '¸' => 'e', 'æ' => 'zh', 'ç' => 'z', 'è' => 'i', 'é' => 'y', 'ê' =>
            'k', 'ë' => 'l', 'ì' => 'm', 'í' => 'n', 'î' => 'o', 'ï' => 'p', 'ğ' => 'r', 'ñ' =>
            's', 'ò' => 't', 'ó' => 'u', 'ô' => 'f', 'õ' => 'h', 'ö' => 'c', '÷' => 'ch',
            'ø' => 'sh', 'ù' => 'sch', 'ü' => '', 'û' => 'y', 'ú' => '', 'ı' => 'e', 'ş' =>
            'yu', 'ÿ' => 'ya', "¿" => "yi", "º" => "ye", 'À' => 'A', 'Á' => 'B', 'Â' => 'V',
            'Ã' => 'G', 'Ä' => 'D', 'Å' => 'E', '¨' => 'E', 'Æ' => 'Zh', 'Ç' => 'Z', 'È' =>
            'I', 'É' => 'Y', 'Ê' => 'K', 'Ë' => 'L', 'Ì' => 'M', 'Í' => 'N', 'Î' => 'O', 'Ï' =>
            'P', 'Ğ' => 'R', 'Ñ' => 'S', 'Ò' => 'T', 'Ó' => 'U', 'Ô' => 'F', 'Õ' => 'H', 'Ö' =>
            'C', '×' => 'Ch', 'Ø' => 'Sh', 'Ù' => 'Sch', 'Ü' => '', 'Û' => 'Y', 'Ú' => '',
            'İ' => 'E', 'Ş' => 'Yu', 'ß' => 'Ya', "¯" => "yi", "ª" => "ye", );
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
