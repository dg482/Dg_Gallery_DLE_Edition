<?php

/*
 * Модель комментариев (толстая)
 * Все методы по работе с комментариями, добавление, удаление, редактирование, вывод и т.д.
 *
 * @package gallery
 * @author Dark Ghost
 * @copyright 2011
 * @access public
 * @since 1.5.6 (08.2011)
 *
 */

class model_comments extends model_gallery
{

    /**
     *
     * @var array
     */
    protected $_config;

    /**
     *
     * @var obj
     */
    protected $_db;

    /**
     *
     * @var bool
     */
    protected $_isLogged;

    /**
     * @deprecated
     * @var string
     */
    public $area;

    /**
     *
     * @var ParseFilter
     */
    private $_parse;

    /**
     *
     * @var aray
     */
    public $_user;

    public function __construct()
    {
        $this->_db = model_gallery::getClass('module_db');
        $this->_user = model_gallery::$user;
        global $is_logged;
        $this->_isLogged = $is_logged;
    }

    /**
     * Подсчет комм. по ключу 'parent_id'
     * @param int $id
     * @param string $status
     * @return array
     */
    public function count($id, $status)
    {
        return $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments WHERE parent_id='{$id}' AND status='{$status}' AND approve='1'");
    }

    /**
     *
     * @param int $parent_id
     * @return string
     */
    public function getAddForm($parent_id)
    {
        return model_gallery::getClass('view_addComments')->render($parent_id);
    }

    /**
     * Добавление комментария в б.д.
     * В методе производится валидация данных поступивших из формы добавления комментариев.
     * В зависимоти от метода передачи данных будет произведена соответствующая обработка строк.
     * В зависимости от настроек скрипта буду произведены дополнительные действия:
     * 1 - обновление счетчиков.
     * 2 - обновление кэша альбома.
     * 3 - добавление записи о произведенном деиствие (flood)
     * Возвращаемые значения могут быть двух типов
     * 1 - строка для вывода в шаблоне main.tpl
     * 2 - json объект для разбора методами javascript
     *
     * @global array $user_group
     * @global array $lang
     * @global bool $is_logged
     * @return mixed
     */
    public function add()
    {
        global $user_group, $lang, $is_logged;
        $this->user = model_gallery::$user;
        if (null === $this->_config)
            $this->_config = model_gallery::getRegistry('config');
        if (!in_array($this->user['user_group'], explode(',', $this->_config['accessCommFile']))) {
            return array('error' => array('error, access denied'));
        }

        $this->_isLogged = $is_logged;
        include ROOT_DIR . '/language/Russian/website.lng';
        $stop = array();
        $CN_HALT = false;
        $name = null;
        $mail = null;
        #require_once ROOT_DIR . '/engine/classes/parse.class.php';
        $config = model_gallery::getRegistry('config_cms');

        $this->_setParse();
        $_TIME = time() + ($config['date_adjust'] * 60);
        $date = date('Y-m-d H:i:s', $_TIME);
        $_IP = $this->_db->safesql($_SERVER['REMOTE_ADDR']);
        $flooder = false;
        ///$this->_config['def_flood'] = 30; //TODO:настроика защиты от флуда перенести в админпанеть
        if ($this->_config['def_flood']) {
            $this_time = ($_TIME - $this->_config['def_flood']);
            $this->_db->query("DELETE FROM " . PREFIX . "_flood where id < '$this_time'");
            $_count = $this->_db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_flood WHERE ip = '$_IP'");
            $flooder = ($_count['count']) ? true : false;
            if ($flooder) {
                $stop[] = $lang['news_err_4'] . " " . $lang['news_err_5'] . " {$this->_config['def_flood']} " . $lang['news_err_6'];
                $CN_HALT = true;
            }
        }
        $parent_id = model_request::getRequest('parent_id');
        if (!$parent_id or $parent_id == 0 or $parent_id == null) {
            $stop[] = $lang['news_err_id'];
            $CN_HALT = true;
        }

        if (model_request::isAjax()) {
            model_request::setParam('comments', module_json::convertToCp(model_request::getRequest('comments')));
        }

        if (!$this->_isLogged) {
            if (model_request::isAjax()) {
                model_request::setParam('name', module_json::convertToCp(model_request::getRequest('name')));
                model_request::setParam('mail', module_json::convertToCp(model_request::getRequest('mail')));
            }
            $name = $this->_db->safesql($this->_parse->process(model_request::getRequest('name')));
            $mail = $this->_db->safesql($this->_parse->process(model_request::getRequest('mail')));
        } else {
            $name = $this->user['name'];
            $mail = $this->user['email'];
        }
        if (intval($config['comments_minlen']) and
            dle_strlen(trim($this->_parse->process($_REQUEST['comments'])), $config['charset']) < $config['comments_minlen']
        ) {
            $stop[] = $lang['news_err_40'];
            $CN_HALT = true;
        }
        if (empty($name)) {
            $stop[] = $lang['news_err_9'];
            $CN_HALT = true;
        }
        if (dle_strlen($name, $config['charset']) > 50) {
            $stop[] = $lang['news_err_1'];
            $CN_HALT = true;
        }
        if (strlen($mail) > 50 and !$this->_isLogged) {
            $stop[] = $lang['news_err_2'];
            $CN_HALT = true;
        }

        $comments = null;
        if ($config['allow_comments_wysiwyg'] === "yes") {
            $comments = $this->_db->safesql($this->_parse->BB_Parse($this->_parse->process(model_request::getRequest('comments'))));
        } else {
            $comments = $this->_db->safesql($this->_parse->BB_Parse($this->_parse->process(model_request::getRequest('comments')), false));
        }
        if ($this->user['restricted'] == 2 || $this->user['restricted'] == 3) {
            $stop[] = $lang['news_info_3'];
            $CN_HALT = true;
        }
        if (dle_strlen($comments, $config['charset']) > $config['comments_maxlen']) {
            $stop[] = $lang['news_err_3'];
            $CN_HALT = true;
        }
        //$user_group[$this->user['user_group']]['captcha'] = 1;
        if ($user_group[$this->user['user_group']]['captcha']) {
            if ($config['allow_recaptcha']) {
                require_once ENGINE_DIR . '/classes/recaptcha.php';
                $resp = false;
                if ($_POST['recaptcha_response_field'] and $_POST['recaptcha_challenge_field']) {
                    $resp = recaptcha_check_answer($config['recaptcha_private_key'], $_SERVER['REMOTE_ADDR'], model_request::getPost('recaptcha_challenge_field'), model_request::getPost('recaptcha_response_field'));
                    if (!$resp->is_valid) {
                        $stop[] = $lang['news_err_30'];
                        $CN_HALT = true;
                    }
                } else {
                    $stop[] = $lang['news_err_30'];
                    $CN_HALT = true;
                }
            } else {
                if ($_REQUEST['sec_code'] != $_SESSION['sec_code_session'] or !$_SESSION['sec_code_session']) {
                    $stop[] = $lang['news_err_30'];
                    $CN_HALT = true;
                }
            }
        }
        if ($comments == '') {
            $stop[] = $lang['news_err_11'];
            $CN_HALT = true;
        }
        if ($parse->not_allowed_tags) {
            $stop[] = $lang['news_err_33'];
            $CN_HALT = true;
        }
        if ($parse->not_allowed_text) {
            $stop[] = $lang['news_err_37'];
            $CN_HALT = true;
        }
        if (!$this->_isLogged and $CN_HALT != true) {
            $this->_db->query("SELECT name from " . USERPREFIX . "_users where LOWER(name) = '" . strtolower($name) . "'");
            if ($this->_db->num_rows() > 0) {
                $stop[] = $lang['news_err_7'];
                $CN_HALT = true;
            }
        }
        if (empty($name) and $CN_HALT != true) {
            $stop[] = $lang['news_err_9'];
            $CN_HALT = true;
        }
        if ($mail != "") {
            if (!preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $mail)) {
                $stop[] = $lang['news_err_10'];
                $CN_HALT = true;
            }
        }

        $this->_autoWrapText($comments);
        $catInfo = null;
        $albInfo = null;
        if (false == $CN_HALT) {
            $update_comments = false;
            //get albom ID
            $check = $this->_db->super_query('SELECT parent_id,id FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE id='{$parent_id}' LIMIT 1");
            if (null == $check) {
                return array('error' => array('checked parent_id filed'));
            }
            $row = null;
            if (1 === GALLERY_MODE) { // Categories + Albom + file Mode
                $cat = model_gallery::getRegistry('model_category');
                $alb = model_gallery::getRegistry('model_albom');
                if ($alb instanceof model_albom) {
                    $alb->setId($check['parent_id']);
                    $alb->openAlbom();
                    $albInfo = $alb->getInfo();
                }
                if ($cat instanceof model_category && is_array($albInfo)) {
                    $catInfo = $cat->getCatInfo($albInfo['parent_id']);
                }
            }


            $config['allow_combine_alb'] = false; //TODO: добавить параметры в админпанель
            if ($config['allow_combine_alb']) {
                $row = $this->_db->super_query("SELECT id, parent_id, user_id, date, text, ip, is_register  FROM "
                                               . DBNAME . '.' . PREFIX . "_dg_gallery_comments WHERE parent_id='{$parent_id}' AND status='file' ORDER BY id DESC LIMIT 0,1");
                if ($row['id'] and $this->_isLogged) {
                    if ($row['user_id'] == $this->user['user_id'] and $row['is_register'])
                        $update_comments = true;
                    elseif ($row['ip'] == $_IP and !$row['is_register'] and !$this->_isLogged)
                        $update_comments = true;
                    $row['date'] = strtotime($row['date']);
                    if (date("Y-m-d", $row['date']) != date("Y-m-d", $_TIME))
                        $update_comments = false;
                    if ($user_group[$this->user['user_group']]['edit_limit'] and (($row['date'] + ($user_group[$this->user['user_group']]['edit_limit'] * 60)) < $_TIME))
                        $update_comments = false;
                    if (((dle_strlen($row['text'], $config['charset']) + dle_strlen($comments, $config['charset'])) > $config['comments_maxlen']) and $update_comments) {
                        $update_comments = false;
                        $stop[] = $lang['news_err_3'];
                        $CN_HALT = true;
                    }
                }
            }
            $_lastId = false;
            $accessId = explode(',', $this->_config['commentsApprove']);
            $this->_config['commentsApprove'] = (in_array($this->_user['user_group'], $accessId));
            $approve = ($this->_config['commentsApprove']) ? 0 : 1;
            if ($update_comments) {
                $comments = $this->_db->safesql($row['text']) . "<br /><br />" . $comments;
                $this->_db->query("UPDATE " . DBNAME . '.' . PREFIX . "_dg_gallery_comments set date='{$date}', text='{$comments}'
                WHERE id='{$row['id']}'");
                $_lastId = $row['id'];
            } else {
                $logged = ($this->_isLogged) ? 1 : 0;
                $this->user['user_id'] = ($this->_isLogged) ? $this->user['user_id'] : 0;
                if ($this->_config['coments_tree']) { //tree
                    require_once ROOT_DIR . '/DGGallery/app/lib/dbtree.class.php';
                    require_once ROOT_DIR . '/DGGallery/app/lib/db_mysql.class.php';
                    $_db = new _db(DBHOST, DBUSER, DBPASS, DBNAME);
                    $dbtree = new dbtree(PREFIX . '_dg_gallery_comments', 'ns', $_db);
                    $_ns_id = (int)model_request::getRequest('ns_parent_id');
                    if ($_ns_id) {
                        $dbtree->Insert($_ns_id, array(
                                                      'and' => array(
                                                          'status' => "status='file'",
                                                          'parent_id' => "parent_id='" . $parent_id . "'"
                                                      )
                                                 ), array(
                                                         'parent_id' => $parent_id,
                                                         'user_id' => $this->user['user_id'],
                                                         'date' => $date,
                                                         'autor' => $name,
                                                         'email' => $mail,
                                                         'text' => $comments,
                                                         'ip' => $_IP,
                                                         'is_register' => $logged,
                                                         'status' => 'file',
                                                         'approve' => $approve
                                                    ));
                        $_lastId = $_db->insert_id();
                    } else {
                        $this->_db->query("INSERT INTO " . DBNAME . '.' . PREFIX . "_dg_gallery_comments
                        (parent_id, user_id, date, autor, email, text, ip, is_register,status,approve,
                                 ns_left, ns_right  ) VALUES
                                ('{$parent_id}', '{$this->user['user_id']}', '{$date}', '{$name}', '{$mail}', '{$comments}', '{$_IP}', '{$logged}', 'file','{$approve}',
                                 '1','2')");
                        $_lastId = $this->_db->insert_id();
                    }
                } else {
                    $this->_db->query("INSERT INTO " . DBNAME . '.' . PREFIX . "_dg_gallery_comments
                        (parent_id, user_id, date, autor, email, text, ip, is_register,status,approve ) VALUES
                                ('{$parent_id}', '{$this->user['user_id']}', '{$date}', '{$name}', '{$mail}', '{$comments}', '{$_IP}', '{$logged}', 'file','{$approve}' )");
                    $_lastId = $this->_db->insert_id();
                }


                // if (!$this->_config['commentsApprove']) {}
                if ($this->_config['countComm']) { // comment count
                    if (1 === GALLERY_MODE) {
                        $this->_db->query("UPDATE " . DBNAME . '.' . PREFIX . "_dg_gallery_albom SET comm=comm+1 where id='{$check['parent_id']}' LIMIT 1");
                    }
                    $this->_db->query("UPDATE " . DBNAME . '.' . PREFIX . "_dg_gallery_file SET comm_num=comm_num+1 where id='{$check['id']}' LIMIT 1");

                    $this->_db->query("UPDATE " . DBNAME . '.' . PREFIX . "_dg_gallery_user SET comments=comments+1 where user_id='{$this->user['user_id']}' LIMIT 1");
                }
                if ($this->_config['update_cache_addcomm']) { // update cache albom
                    model_gallery::getClass('model_albom')->updateAlbom($check['parent_id']);
                }
                if ($this->_config['sendEmailcomm']) { //send e-mail
                    $msg = 'Текст комментария: <br />';
                    $msg .= stripslashes($comments) . '<br /><br />';
                    if (1 === GALLERY_MODE) { // Categories + Albom + file Mode
                        $cat = model_gallery::getRegistry('model_category');
                        $alb = model_gallery::getRegistry('model_albom');
                        if ($alb instanceof model_albom) {
                            $alb->setId($check['parent_id']);
                            $alb->openAlbom();
                            $albInfo = $alb->getInfo();
                        }
                        if ($cat instanceof model_category && is_array($albInfo)) {
                            $catInfo = $cat->getCatInfo($albInfo['parent_id']);
                        }
                        if (is_string($albInfo["meta_data"]))
                            $albInfo["meta_data"] = unserialize($albInfo["meta_data"]);
                        $msg .= '<a href="' . HOME_URL . 'gallery/albom/' . $albInfo['id'] . '-' . $albInfo['meta_data']["meta_title"] .
                                '.' . $parent_id . '">переити к прочтению на сайте</a>';
                    }
                    model_mail::send('Добавлен новый комментарий.', $msg);
                }
            }
            if ($this->_config['def_flood']) {
                $this->_db->query("INSERT INTO " . PREFIX . "_flood (id, ip) values ('$_TIME', '$_IP')");
            }

            if ($_lastId && model_request::isAjax()) { //AJAX
                $view = model_gallery::getRegistry('view_template');
                if ($this->_config['commentsApprove']) {
                    return array(
                        'tpl' => model_gallery::getClass('view_template')->msgbox($lang['add_comm'], $lang['news_err_31'])
                    );
                } else {


                    return array('tpl' => '<div id="blind-animation" style="display:none">' . $this->load(array(
                                                                                                               'id' => $_lastId,
                                                                                                               'status' => 'file',
                                                                                                               'start' => 0,
                                                                                                               'end' => 1,
                                                                                                               'count' => false,
                                                                                                               'where' => 'id'
                                                                                                          ), $view->getView(), false) . '</div>');
                }
            }
        } else {
            //ERROR
            if (model_request::isAjax()) { //AJAX
                return array('error' => $stop);
            } else { //POST
                //                model_gallery::getRegistry('view_template')->
                //                        msgbox($lang['all_err_1'], implode("<br />", $stop) . "<br /><br /><a href=\"javascript:history.go(-1)\">" . $lang['all_prev'] . "</a>");
                //
            }
        }
    }

    /**
     * Метод выводит комментарии для файлов, по сути аналогичен стандартному [DLE].
     *
     * @global array $lang
     * @global array $user_group
     * @global bool $is_logged
     * @global string $dle_login_hash
     * @param array $param
     * @param dle_template $tpl
     * @return mixed
     */
    public function load(array $param, dle_template $tpl, $_isAdmin = false)
    {
        $id = (int)$param['id'];
        $status = $param['status'];
        $count = null;
        $offset = (int)model_request::getRequest('page');
        $param['start'] = ($offset > 1) ? (($offset - 1) * $param['end']) : 0;
        if (false === $param['count']) {
            $count['count'] = 1;
        } else {
            $count['count'] = (int)$param['count']['count'];
        }
        # $config = model_gallery::getRegistry('config_cms');
        $row = null;
        if ($count['count']) {
            global $lang, $user_group, $is_logged;
            if (null === $lang['ip_info']) {
                require ROOT_DIR . '/language/Russian/website.lng';
            }
            $this->_isLogged = $is_logged;
            if (null === $this->_user)
                $this->_user = model_gallery::$user;
            $count_id = $param['start'];
            $param['approve'] = (null != $param['approve']) ? $param['approve'] : 1;
            $order = 'date ASC';
            $limit = 'LIMIT ' . $param['start'] . ',' . $param['end'];
            $this->_config['coments_tree'] = 1;
            if ($this->_config['coments_tree']) {
                $order = 'ns_right DESC ';
                $limit = '';

            }
            if (null === $param['mysqlId']) {
                $param['mysqlId'] = $this->_db->query('SELECT  ' . DBNAME . '.' . PREFIX . '_dg_gallery_comments.*,'
                                                      . DBNAME . '.' . PREFIX . '_users.name,'
                                                      . DBNAME . '.' . PREFIX . '_users.reg_date,'
                                                      . DBNAME . '.' . PREFIX . '_users.reg_date,fullname,'
                                                      . DBNAME . '.' . PREFIX . '_users.icq,'
                                                      . DBNAME . '.' . PREFIX . '_users.user_group,'
                                                      . DBNAME . '.' . PREFIX . '_users.news_num,'
                                                      . DBNAME . '.' . PREFIX . '_users.comm_num,'
                                                      . DBNAME . '.' . PREFIX . '_users.signature  FROM '
                                                      . DBNAME . '.' . PREFIX . '_dg_gallery_comments LEFT JOIN '
                                                      . DBNAME . '.' . PREFIX . '_users ON '
                                                      . DBNAME . '.' . PREFIX . '_users.user_id=' . DBNAME . '.' . PREFIX . '_dg_gallery_comments.user_id  WHERE '
                                                      . DBNAME . '.' . PREFIX . "_dg_gallery_comments.{$param['where']}='{$id}' AND "
                                                      . DBNAME . '.' . PREFIX . "_dg_gallery_comments.approve='{$param['approve']}' AND "
                                                      . DBNAME . '.' . PREFIX . "_dg_gallery_comments.status='{$status}' ORDER BY "
                                                      . DBNAME . '.' . PREFIX . "_dg_gallery_comments.{$order} $limit");
            }

            while ($row = $this->_db->get_row($param['mysqlId'])) {
                $count_id++;
                $tpl->result['comm'] = model_gallery::getClass('view_comments')->renderComments($param, $row, $count_id);
            }
            global $dle_login_hash;
            if (!model_request::isAjax() && $this->_isAdmin()) {
                if (false == $_isAdmin) {
                    return <<<HTML
<form action="#" method="post"  name="gallery_comment_form" id="gallery_comment_form">
{$tpl->result['comm']}
<div id="gallery-ajax-comments"></div>
<div class="mass_comments_action">{$lang['mass_comments']}
             <select name="mass_action">
                <option value="">{$lang['edit_selact']}</option>
              <!--<option value="mass_combine">{$lang['edit_selcomb']}</option>-->
                <option value="mass_delete">{$lang['edit_seldel']}</option>
             </select>
<input type="submit" class="bbcodes" value="{$lang['b_start']}" />
</div>
<input type="hidden" name="dle_allow_hash" value="{$dle_login_hash}" />
<input type="hidden" name="area" value="file" />
</form>
HTML;
                } else {
                    return $tpl->result['comm'];
                }
            } else {
                return $tpl->result['comm'] . '<div id="gallery-ajax-comments"></div>';
            }
        } else {
            return '<div id="gallery-ajax-comments"></div>';
        }
    }

    /**
     * Редактирование комментариев
     *
     * Вернуть объет json для разбора в javascript
     */
    public function edit()
    {
        return model_gallery::getClass('view_addComments')->renderEdit();
    }

    /**
     * Сохранение редактируемого комментария.
     * Метод получает данные, обрабатывает из для хранения в б.д., обновляет комментарий.
     *
     * Вернуть объет json для разбора в javascript
     * @global array $lang
     * @return array
     */
    public function save()
    {
        $id = model_request::getRequest('id');
        if (null == $id)
            return false;
        global $lang;
        $config = model_gallery::getRegistry('config_cms');
        $stop = array();
        $_stop = false;
        $this->_setParse();
        $comm_txt = trim($this->_parse->BB_Parse($this->_parse->process(convert_unicode(model_request::getRequest('comm_txt'), $config['charset'])), false));
        if ($this->_parse->not_allowed_tags) {
            $stop[] = $lang['news_err_33'];
            $_stop = true;
        }
        if ($this->_parse->not_allowed_text) {
            $stop[] = $lang['news_err_37'];
            $_stop = true;
        }
        if ($comm_txt == '') {
            $stop[] = $lang['news_err_11'];
            $_stop = true;
        }
        if (dle_strlen($comm_txt, $config['charset']) > $config['comments_maxlen']) {
            $stop[] = $lang['news_err_11'];
            $_stop = true;
        }
        if (false == $_stop) {
            $comm_update = $this->_db->safesql($this->_autoWrapText($comm_txt));
            $this->_db->query('UPDATE ' . PREFIX . "_dg_gallery_comments SET text='{$comm_update}' WHERE id='{$id}' LIMIT 1 ");
            $comm_txt = preg_replace("'\[hide\](.*?)\[/hide\]'si", "\\1", $comm_txt);
            $buffer = stripslashes($comm_txt);
            $buffer = str_replace('{THEME}', HOME_URL . 'templates/' . $config['skin'], $buffer);
            return array('tpl' => $buffer);
        } else {
            return array('error' => $stop);
        }
    }

    /**
     *  Удаление комментария из б.д., обновлление счетчика у альбома.
     * @param null $id
     * @return void
     */
    public function delete($id = null)
    {
        global $user_group;
        if (((true === $this->_isLogged) && (1 == $user_group[$this->_user['user_group']]['allow_editc']))
            || (1 == $user_group[$this->_user['user_group']]['edit_allc']) || (1 == $this->_user['user_group'])
        ) {
            if (null === $id)
                $id = (int)model_request::getPost('id');
            if ($id) {
                $comm = $this->_db->super_query('SELECT parent_id FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments WHERE id='{$id}' AND status='file' LIMIT 1");
                $parent_id = $comm['parent_id'];
                $_check = $this->_db->super_query('SELECT parent_id FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE id='{$parent_id}' LIMIT 1");
                $check = $this->_db->super_query('SELECT id,comm FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE id='{$_check['parent_id']}' LIMIT 1");
                $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments WHERE id='{$id}' LIMIT 1");
                if ($check['comm']) {
                    $this->_db->query("UPDATE " . DBNAME . '.' . PREFIX . "_dg_gallery_albom set comm=comm-1 where id='{$check['id']}' LIMIT 1");
                }
                $this->_config['update_cache_addcomm'] = 1; //TODO: ддобавить параметр обновления кеша в раздел оптимизации
                if ($this->_config['update_cache_addcomm']) {
                    model_gallery::getClass('model_albom')->updateAlbom($check['parent_id']);
                }
            }
        }
    }

    /**
     * Массовое удаление комментариев из б.д.
     */
    public function massAction()
    {
        $action = model_request::getPost('mass_action');
        $parent_id = null;
        $this->_user = model_gallery::$user;
        if ($this->_isAdmin() && $action) {
            $albInfo = null;
            $catInfo = null;
            $cat = model_gallery::getRegistry('model_category');
            $alb = model_gallery::getRegistry('model_albom');
            if ($alb instanceof model_albom) {
                $albInfo = $alb->getInfo();
            }
            if ($cat instanceof model_category && is_array($albInfo)) {
                $catInfo = $cat->getCatInfo($albInfo['parent_id']);
            }
            switch ($action) {
                case 'mass_delete':
                    if (!empty($_POST['selected_comments']) && is_array($_POST['selected_comments'])) {
                        foreach ($_POST['selected_comments'] as $id) {
                            if ($id != '' && null != $id) {
                                $comm = $this->_db->super_query('SELECT parent_id FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments WHERE id='{$id}' AND status='file' LIMIT 1");
                                $parent_id = $comm['parent_id'];
                                if ($parent_id) {
                                    $_check = $this->_db->super_query('SELECT parent_id FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE id='{$parent_id}' LIMIT 1");
                                    $check = $this->_db->super_query('SELECT id,comm FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE id='{$_check['parent_id']}' LIMIT 1");
                                    $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments WHERE id='{$id}' LIMIT 1");
                                    if ($check['comm']) {
                                        $this->_db->query("UPDATE " . DBNAME . '.' . PREFIX . "_dg_gallery_albom set comm=comm-1 where id='{$check['id']}' LIMIT 1");
                                    }
                                }
                            }
                        }
                    }
                    $this->_config['update_cache_addcomm'] = 1; //TODO: ддобавить параметр обновления кеша в раздел оптимизации
                    if ($this->_config['update_cache_addcomm']) {
                        if ($parent_id)
                            model_gallery::getClass('model_albom')->updateAlbom($parent_id);
                    }
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Перенос длинных строк, согласно параметрам конфигурации[DLE]
     * @param string $text
     * @return string
     */
    private function _autoWrapText($text)
    {
        $config = model_gallery::getRegistry('config_cms');
        if (intval($config['auto_wrap'])) {
            $text = preg_split('((>)|(<))', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
            $n = count($text);
            for ($i = 0; $i < $n; $i++) {
                if ($text[$i] == "<") {
                    $i++;
                    continue;
                }
                $text[$i] = preg_replace("#([^\s\n\r]{" . intval($config['auto_wrap']) . "})#i", "\\1<br />", $text[$i]);
            }
            return join("", $text);
        }
    }

    /**
     *
     * @param array $row
     * @param int $is_reg
     * @return bool
     */
    private function _editAccess($row, $is_reg)
    {
        global $user_group;
        if ($is_reg)
            return ((true === $this->_isLogged) && (($this->_user['name'] == $row['name']) && (1 == $row['is_register'])
                                                    || (1 == $user_group[$this->_user['user_group']]['allow_editc'])) ||
                    (1 == $user_group[$this->_user['user_group']]['edit_allc']) || (1 == $this->_user['user_group']))
                    ? true : false;
        else
            return
                    (((true === $this->_isLogged) && (1 == $user_group[$this->_user['user_group']]['allow_editc']))
                     || (1 == $user_group[$this->_user['user_group']]['edit_allc']) || (1 == $this->_user['user_group']))
                            ? true : false;
    }

//    /**
//     *
//     * @return bool
//     */
//    private function _isAdmin() {
//        return ((true === $this->_isLogged ) && (1 === (int) $this->user['user_group'])) ? true : false;
//    }

    /**
     * @return void
     */
    private function _setParse()
    {
        require_once ROOT_DIR . '/engine/classes/parse.class.php';
        $config = model_gallery::getRegistry('config_cms');
        if ($config['allow_comments_wysiwyg'] === "yes") {
            $this->_parse = new ParseFilter(Array('div', 'span', 'p', 'br', 'strong', 'em', 'ul', 'li', 'ol'), Array(), 0, 1);
            $this->_parse->wysiwyg = true;
            $this->_parse->safe_mode = true;
        } else {
            $this->_parse = new ParseFilter(array(), array(), 1, 1);
            $this->_parse->safe_mode = true;
        }
    }

}

