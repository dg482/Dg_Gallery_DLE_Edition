<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */


class model_search extends model_gallery {

    /**
     *
     * @var string
     */
    public static $DEF_LANG = 'rus';
    /**
     *
     * @var string
     */
    public static $DEF_ORDER = 'ORDER_DATE';

    /**
     *
     */
    public function __construct() {
        $this->_db = model_gallery::getRegistry('module_db');
    }

    /**
     * @param null $data
     * @return bool
     */
    public function addKeywordsFile($data = null) {
        $key = ($data) ? $data : model_request::getPost('tag');
        $parent_id = (int) model_request::getPost('id');
        if (null === $key || !$parent_id) {
            return;
        }
        $check = model_gallery::getClass('model_file')->getFile($parent_id);
        if (null === $check) {
            return false;
        }
        if (null == $this->_db) {
            $this->_db = model_gallery::getClass('module_db');
        }
        require_once ROOT_DIR . '/engine/classes/parse.class.php';
        $parse = new ParseFilter(array(), array(), 1, 1);
        $key = htmlspecialchars(strip_tags(stripslashes(trim($key))), ENT_QUOTES);
        $key = explode(',', $key);
        $ins = array();
        $_key = array();
        $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_tags WHERE parent_id='{$check['id']}'");
        foreach ($key as $val) {
            if ($val != '' && null != $val) {
                $val = iconv('utf-8', 'cp1251', $val);
                $_key[] = $val;
                $ins[] = "('" . $parent_id . "','" . $this->_db->safesql(trim($parse->process($val))) . "','file')";
            }
        }
        $this->_db->query('INSERT INTO ' . DBNAME . '.' . PREFIX . '_dg_gallery_tags (parent_id,tag,status) VALUE ' . implode(',', $ins));
        $check['other_dat'] = (is_string($check['other_dat'])) ? unserialize($check['other_dat']) : $check['other_dat'];
        $check['other_dat']['tag'] = implode(', ', $_key);
        model_gallery::getClass('model_file')->updateFile($check['id'], array(
            'other_dat' => serialize($check['other_dat'])
        ));
        return model_gallery::getClass('model_albom')->updateAlbom($check['parent_id']);
    }

    /**
     * DLE
     * @param string $text
     * @return string
     */
    public static function strip_data($text) {
        $quotes = array("\x27", "\x22", "\x60", "\t", "\n", "\r", "'", ",", "/", ";", ":", "@", "[", "]", "{", "}", "=", ")", "(", "*", "&", "^", "%", "$", "<", ">", "?", "!", '"');
        $goodquotes = array("-", "+", "#");
        $repquotes = array("\-", "\+", "\#");
        $text = stripslashes($text);
        $text = trim(strip_tags($text));
        $text = str_replace($quotes, '', $text);
        $text = str_replace($goodquotes, $repquotes, $text);
        return $text;
    }

    /**
     * @param array $param
     * @param int $pageLimit
     * @return mixed
     */
    public function get(array $param, $pageLimit = 3) {
        $offset = (int) model_request::getRequest('page');
        $offset = ($offset) ? (($offset - 1) * $pageLimit) : 0;
        $param['search'] = self::strip_data($param['search']);

        if (GALLERY_MODE === 1) {
            if ($param['where'] == 'color')
                return $this->_db->query('SELECT ' . PREFIX . '_dg_gallery_file.*,' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_albom.meta_data, ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_albom.access_data FROM ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_file LEFT JOIN ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_albom ON ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_albom.id = ' . DBNAME . '.' . PREFIX . '_dg_gallery_file.parent_id  WHERE ' .
                    DBNAME . '.' . PREFIX . "_dg_gallery_file.other_dat REGEXP  '[[:<:]]{$param['search']}[[:>:]]' AND " .
                    DBNAME . '.' . PREFIX . "_dg_gallery_file.status='albom' LIMIT {$offset},{$pageLimit}");
            if ($param['where'] == 'keyword') {
                return $this->_db->query('SELECT ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_tags.parent_id, ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_file. * , ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_albom.access_data  FROM  ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_tags  JOIN ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_file ON ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_file.id= ' . DBNAME . '.' . PREFIX . '_dg_gallery_tags.parent_id  JOIN ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_albom ON ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_albom.id= ' . DBNAME . '.' . PREFIX . '_dg_gallery_file.parent_id  WHERE ' .
                    DBNAME . '.' . PREFIX . "_dg_gallery_tags.tag='{$param['search']}' LIMIT {$offset},{$pageLimit}");
            }
        } elseif (GALLERY_MODE === 2) {
            if ($param['where'] == 'keyword') {
                return $this->_db->query('SELECT ' . DBNAME . '.' . PREFIX . '_dg_gallery_tags.*,' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_file.* FROM ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_tags LEFT JOIN ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_file ON ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_file.id =' . DBNAME . '.' . PREFIX . '_dg_gallery_tags.parent_id  WHERE ' .
                    DBNAME . '.' . PREFIX . "_dg_gallery_tags.tag='{$param['search']}'");
            }
            if ($param['where'] == 'color')
                return $this->_db->query('SELECT ' . PREFIX . '_dg_gallery_file.*,' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_albom.meta_data, ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_albom.access_data FROM ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_file LEFT JOIN ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_albom ON ' .
                    DBNAME . '.' . PREFIX . '_dg_gallery_albom.id = ' . DBNAME . '.' . PREFIX . '_dg_gallery_file.parent_id  WHERE ' .
                    DBNAME . '.' . PREFIX . "_dg_gallery_file.other_dat REGEXP  '[[:<:]]{$param['search']}[[:>:]]' AND " .
                    DBNAME . '.' . PREFIX . "_dg_gallery_file.status='catfile' LIMIT {$offset},{$pageLimit}");
        }
        if ($param['where'] == 'letter') {
            $where = "symbol='{$param['search']}'";

            if ($param['search'] == '\#') {
                $where = 'symbol  REGEXP  \'[0-9]\' ';
            }


            return $this->_db->query('SELECT * FROM ' . PREFIX . "_dg_gallery_albom WHERE $where  LIMIT {$offset},{$pageLimit}");
        }
    }

    /**
     * @static
     * @param $letter
     * @return mixed
     */
    public static function addLetter($letter) {
        $_config = model_gallery::getRegistry('config');
        if (!$_config['statusAlfavit']) {
            return;
        }
        $letter = strtoupper($letter);
        if (intval($letter)) {
            $letter = '#';
        }
        $alf = include ROOT_DIR . '/DGGallery/app/config/alfavite.php';
        if (!in_array($letter, $alf)) {
            $alf[$letter] = $letter;
            $file = fopen(ROOT_DIR . '/DGGallery/app/config/alfavite.php', "wb+");
            fwrite($file, "<?\r\nreturn array(\r\n");
            foreach ($alf as $k => $v) {
                fwrite($file, "  '{$k}' => '{$v}',\r\n");
            }
            fwrite($file, "\r\n);");
        }
    }

    /**
     * @static
     * @return string
     */
    public static function getAlfa() {
        $_config = model_gallery::getRegistry('config');
        if (!$_config['statusAlfavit']) {
            return;
        }
        $_lang = self::getRegistry('lang');
        $alfa = include ROOT_DIR . '/DGGallery/app/config/alfavite.php';
        $sw = array('rus' => 'rus', 'eng' => 'eng');
        unset($sw[self::$DEF_LANG]);
        $lng = current($sw);
        $html = '<span class="search-letter">' . self::$DEF_LANG . '</span>';
        $html .= '<a href="javascript:void(0)" class="search-letter" rel="' . $lng . '">' . $lng . '</a>';
        foreach ($_lang[self::$DEF_LANG] as $key) {
            $html .= ( in_array($key, $alfa)) ? '<a href="' . HOME_URL . 'gallery/search/letter/' . urlencode(strtolower($key)) .
                '" class="search-letter">' . $key . '</a>' : '<span class="search-letter">' . $key . '</span>';
        }

        return $html;
    }

    /**
     * @static
     * @return string
     */
    public static function getForm() {
        $param = model_gallery::getRegistry('route')->getParam();
        $_lang = self::getRegistry('lang');
        $param['where'] = model_request::getPost('_where');
        $_checked = 'albom';
        $_where = array(
            'albom' => $_lang['search']['albom'],
            'file' => $_lang['search']['file'],
            'category' => $_lang['search']['category']
        );
        switch ($param [0]) {
            case 'keyword' :
            case 'color' :
                $_checked = 'file';
                break;
            case 'letter':
                unset($_where['file']);
                $_checked = 'albom';
        }
        $html = '<form name="search" action="#" method="post" >';
        $html .= '<fieldset><legend>' . $_lang['search']['form']['legend'] . '</legend>';
        //        $html .='<label for="_where">' . $_lang['search']['form']['legend'] . '</label>';
        //        $html .='<select id="_where" name="_where">';
        //        foreach ($_where as $key => $value) {
        //            if ($key == $_checked) {
        //                $html .= '<option value="' . $key . '" selected="selected">' . $value . '</option>';
        //            } else {
        //                $html .= '<option value="' . $key . '">' . $value . '</option>';
        //            }
        //        }
        //        $html .= '</select>';

        $html .='</fieldset>';
        $html .= '<input type="submit" value="Начать поиск" id="dosearch" name="dosearch" style="margin:0px 20px 0 0px;" class="bbcodes">';
        $html .='</form>';
        return $html;
    }

}