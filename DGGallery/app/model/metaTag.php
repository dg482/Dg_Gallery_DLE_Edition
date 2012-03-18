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
        $langtranslit = array('�' => 'a', '�' => 'b', '�' => 'v', '�' => 'g', '�' => 'd',
            '�' => 'e', '�' => 'e', '�' => 'zh', '�' => 'z', '�' => 'i', '�' => 'y', '�' =>
            'k', '�' => 'l', '�' => 'm', '�' => 'n', '�' => 'o', '�' => 'p', '�' => 'r', '�' =>
            's', '�' => 't', '�' => 'u', '�' => 'f', '�' => 'h', '�' => 'c', '�' => 'ch',
            '�' => 'sh', '�' => 'sch', '�' => '', '�' => 'y', '�' => '', '�' => 'e', '�' =>
            'yu', '�' => 'ya', "�" => "yi", "�" => "ye", '�' => 'A', '�' => 'B', '�' => 'V',
            '�' => 'G', '�' => 'D', '�' => 'E', '�' => 'E', '�' => 'Zh', '�' => 'Z', '�' =>
            'I', '�' => 'Y', '�' => 'K', '�' => 'L', '�' => 'M', '�' => 'N', '�' => 'O', '�' =>
            'P', '�' => 'R', '�' => 'S', '�' => 'T', '�' => 'U', '�' => 'F', '�' => 'H', '�' =>
            'C', '�' => 'Ch', '�' => 'Sh', '�' => 'Sch', '�' => '', '�' => 'Y', '�' => '',
            '�' => 'E', '�' => 'Yu', '�' => 'Ya', "�" => "yi", "�" => "ye", );
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
