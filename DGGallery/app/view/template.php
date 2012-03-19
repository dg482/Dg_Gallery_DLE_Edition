<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

class view_template {

    /**
     *
     * @var object
     */
    protected $_tpl;
    /**
     *
     * @var dle_template
     */
    public $tpl;
    /**
     *
     * @var array
     */
    protected $_action;
    /**
     *
     * @var string
     */
    public $action;
    /**
     *
     * @var type
     */
    protected $_dir;
    /**
     *
     * @var array
     */
    protected $_config;
    /**
     *
     * @var array
     */
    public static $CATEGORY;
    /**
     *
     * @var array
     */
    protected $_user;
    /**
     *
     * @var array
     */
    protected $_lang;
    /**
     *
     * @var db
     */
    protected $_db;

    /**
     *
     */
    public function __construct() {
        global $tpl, $config;
        $this->tpl = $tpl;
        if (!class_exists('dle_template')) {
            return false;
        }

        $this->_tpl = new dle_template();
        $this->_dir = ROOT_DIR . '/templates/' . $config['skin'] . '/gallery/';
        $this->_tpl->dir = $this->_dir;
        $this->_config = model_gallery::getRegistry('config');
        $this->_lang = model_gallery::getRegistry('lang');
        $this->_user = model_gallery::$user;
    }

    /**
     *
     * @return dle_template
     */
    public function getView() {
        return $this->_tpl;
    }

    /**
     *
     * @param type $tpl
     * @return  void
     */
    public function setView($tpl) {
        if (null === $this->_tpl) {
            return;
        }
        $this->_tpl->load_template($tpl);
    }

    /**
     *
     * @param string $name
     * @return string
     */
    public function compile($name) {
        $this->_tpl->set('{info}', model_gallery::$INFO);
        $this->_tpl->compile($name);
        if (strpos($this->_tpl->result[$name], "[aviable-gallery=") !== false) { // support aviable tag
            $this->_tpl->result[$name] = preg_replace("#\\[aviable-gallery=(.+?)\\](.*?)\\[/aviable-gallery\\]#ies", "\$this->checkModule('\\1', '\\2')", $this->_tpl->result[$name]);
        }
        if (strpos($this->_tpl->result[$name], "[not-aviable-gallery=") !== false) { // support aviable tag
            $this->_tpl->result[$name] = preg_replace("#\\[not-aviable-gallery=(.+?)\\](.*?)\\[/not-aviable-gallery\\]#ies", "\$this->checkModule('\\1', '\\2',false)", $this->_tpl->result[$name]);
        }
        return $this->_tpl->result[$name];
    }

    /**
     * DLE
     * dle_template::check_module
     *
     * @param string $aviable
     * @param string $block
     * @param bool $action
     * @return string
     */
    public function checkModule($aviable, $block, $action = true) {
        $aviable = explode('|', $aviable);
        $module = model_gallery::getRegistry('view_action');
        #var_dump($module);
        $block = str_replace('\"', '"', $block);
        if ($action) {
            if (!(in_array($module, $aviable)) and ($aviable[0] != "global"))
                return "";
            else
                return $block;
        } else {

            if ((in_array($module, $aviable)))
                return "";
            else
                return $block;
        }
    }

    /**
     *
     * @param string $name
     * @param string $str
     * @return void
     */
    public function globalSet($name, $str) {
        $this->tpl->set($name, $str);
    }

    /**
     *
     * @global array $config
     * @param type $title
     * @param type $text
     * @return string
     */
    public function msgbox($title, $text) {
        global $config;
        $tpl = new dle_template( );
        $tpl->dir = ROOT_DIR . '/templates/' . $config['skin'] . '/gallery/';
        $tpl->load_template('info.tpl');
        $tpl->set('{error}', $text);
        $tpl->set('{title}', $title);
        $tpl->compile('info');
        $tpl->clear();
        model_gallery::$INFO .= $tpl->result['info'];
        return $tpl->result['info'];
    }

    /**
     * Вспомогательный метод, выводит информацию о авторе альбома.
     * Метод устанавливает значения тегов в контексте переданного объекта шаблонизатора.
     *
     * @global array $user_group
     * @global array $lang
     * @global string $PHP_SELF
     * @global bool $is_logged
     * @global array $member_id
     * @param array $row
     * @param dle_template $tpl
     * @return void
     */
    protected function _setInfoUser($row, dle_template $tpl) {
        global $user_group, $lang, $PHP_SELF, $is_logged, $member_id;
        $tpl->set('{login}', $row ['autor']);
        $go_page = HOME_URL . 'user/' . urlencode($row ['name']) . '/';
        $tpl->set('{author}', "<a onclick=\"ShowProfile('" . urlencode($row ['name']) . "', '" . $go_page . "', '" . $user_group [$member_id ['user_group']] ['admin_editusers'] . "'); return false;\" href=\"" . $go_page . "\">" . $row ['name'] . "</a>");

        if ($row ['banned'] == 'yes')
            $user_group [$row ['user_group']] ['group_name'] = $lang ['user_ban'];

        if ($row ['allow_mail']) {
            if (!$user_group [$member_id ['user_group']] ['allow_feed'] and $row ['user_group'] != 1)
                $tpl->set('{email}', $lang ['news_mail']);
            else
                $tpl->set('{email}', '<a href="' . $PHP_SELF . '?do=feedback&amp;user=' . $row ['user_id'] . '">' . $lang ['news_mail'] . '</a>');
        } else {
            $tpl->set('{email}', $lang ['news_mail']);
        }
        if ($user_group [$member_id ['user_group']] ['allow_pm'])
            $tpl->set('{pm}', '<a href="' . $PHP_SELF . '?do=pm&amp;doaction=newpm&amp;user=' . $row ['user_id'] . '">' . $lang ['news_pmnew'] . '</a>');
        else
            $tpl->set('{pm}', $lang ['news_pmnew']);
        if ($row ['foto'] and (file_exists(ROOT_DIR . "/uploads/fotos/" . $row ['foto'])))
            $tpl->set('{foto}', HOME_URL . "uploads/fotos/" . $row ['foto']);
        else
            $tpl->set('{foto}', "{THEME}/images/noavatar.png");

        $tpl->set('{usertitle}', stripslashes($row ['name']));
        $tpl->set('{fullname}', stripslashes($row ['fullname']));
        $tpl->set('{icq}', stripslashes($row ['icq']));
        $tpl->set('{land}', stripslashes($row ['land']));
        $tpl->set('{info}', stripslashes($row ['info']));
        $tpl->set('{editmail}', stripslashes($row ['email']));
        $tpl->set('{comm_num}', $row ['comm_num']);
        $tpl->set('{news_num}', $row ['news_num']);
        $tpl->set('{status}', $user_group [$row ['user_group']] ['group_prefix'] . $user_group [$row ['user_group']] ['group_name'] . $user_group [$row ['user_group']] ['group_suffix']);
        if (function_exists('userrating')) {
            $tpl->set('{rate}', userrating($row ['name']));
        } else {
            $tpl->set('{rate}', '');
        }

        $tpl->set('{registration}', model_gallery::dateFormat($row ['reg_date'], FALSE));
        $tpl->set('{lastdate}', model_gallery::dateFormat($row ['lastdate'], FALSE));

        if ($user_group [$row ['user_group']] ['icon'])
            $tpl->set('{group-icon}', "<img src=\"" . $user_group [$row ['user_group']] ['icon'] . "\" border=\"0\" />");
        else
            $tpl->set('{group-icon}', "");

        if ($is_logged and $user_group [$row ['user_group']] ['time_limit'] and ($member_id ['user_id'] == $row ['user_id'] or $member_id ['user_group'] < 3)) {
            $tpl->set_block("'\\[time_limit\\](.*?)\\[/time_limit\\]'si", "\\1");
            if ($row ['time_limit']) {
                $tpl->set('{time_limit}', model_gallery::dateFormat($row ['time_limit']));
            } else {
                $tpl->set('{time_limit}', $lang ['no_limit']);
            }
        } else {
            $tpl->set_block("'\\[time_limit\\](.*?)\\[/time_limit\\]'si", "");
        }
        if ($row ['comm_num']) {
            $tpl->set('{last_comments}', '<a href="' . $PHP_SELF . '?do=lastcomments&amp;userid=' . $row ['user_id'] . '">' . $lang ['last_comm'] . '</a>');
        } else {
            $tpl->set('{last_comments}', $lang ['last_comm']);
        }
    }

    /**
     * Установка значений глобальной переменной используемой для вывода заголовка, описания, ключевых слов.
     * @global array $metatags
     * @param array $meta
     */
    protected function _setMetaTag($meta) {
        global $metatags;
        $metatags ['title'] = $meta ['meta_title'];
        $metatags ['description'] = $meta ['meta_descr'];
        $metatags ['keywords'] = $meta ['meta_keywords'];
    }

    /**
     *
     * @return bool
     */
    protected function _isAdmin() {
        if (class_exists('assistant')) {// adminpanel
            return (assistant::$_user['user_group'] == 1);
        }
        return (model_gallery::$user['user_group'] == 1);
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function _setSpeedbar(array $data) {
        if (stripos($this->_tpl->copy_template, '{speedbar}') === false) {
            return;
        }
        $home = ($this->_config ['title_speedbar']) ? stripslashes($this->_config ['title_speedbar']) : 'Home';
        if (count($data)) {
            $home = '<a href="' . HOME_URL . 'gallery/">' . $home . '</a> &raquo; ';
        }
        $this->_tpl->set('{speedbar}', $home . implode(' &raquo; ', $data));
    }

}

