<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */


class view_comments extends view_template {

    /**
     *
     * @var array
     */
    protected $_user;

    /**
     *
     * @var
     */
    protected $_db;

    /**
     *
     */
    public function __construct() {
        parent::__construct();
        if (class_exists('assistant')) {// adminpanel
            $this->_user = assistant::$_user;
            $this->_tpl->dir = ROOT_DIR . '/DGGallery/admin/theme/default/';
        } else {
            $this->_user = model_gallery::$user;
        }
        $this->setView('comments.tpl');
        $this->_db = model_gallery::getRegistry('module_db');
    }

    /**
     * @param array $param
     * @param array $row
     * @param $id
     * @return mixed
     */
    public function renderComments(array $param, array $row, $id) {

        global $lang, $user_group, $is_logged;
        $config_albom = null;
        $last_comm = false;
        $status = $param['status'];
        $go_page = '';
        $tpl = $this->_tpl;
        $config = model_gallery::getRegistry('config_cms');
        $tpl->set('{comment-id}', $id);
        $tpl->set('{THEME}', HOME_URL . 'templates/' . $this->config_dle['skin']);
        $tpl->set('{level}', $row['ns_level']);
        $tpl->set('{parent_id}', $row['ns_id']);
        $tpl->set('{id}', $row['id']);
        if ($last_comm) {
            $tpl->set_block("'\\[fast\\](.*?)\\[/fast\\]'si", "");
        }
        if ($status == 'all') {
            $tpl->set_block("'\\[fast\\](.*?)\\[/fast\\]'si", "");
            if (!class_exists('gallery_cache')) {
                include_once (ROOT_DIR . '/engine/classes/gallery_cache.class.php');
            }
            $config_albom = model_gallery::getClass('model_albom')->getInfo();

            if ($row['file_id'] == '0') {
                $tpl->set('[link_albom]', '<a href="' . HOME_URL . 'gallery/albom/' . $row['albom_id'] . '-' . $config_albom['albom']['alt_title'] . '.html">');
            } else {
                $tpl->set('[link_albom]', '<a href="' . HOME_URL . 'gallery/albom/' . $row['albom_id'] . '-' . $row['alt_title'] . '.html#/0-' . $row['file_id'] . '">');
            }
            $title = $config_albom['albom']['title'];
            $tpl->set('{title_target}', $title);
            $tpl->set('[/link_albom]', '</a>');
        } else {
            $tpl->set('{title_target}', '');
            $tpl->set_block("'\\[link_albom\\](.*?)\\[/link_albom\\]'si", "");
        }
        if ($row['is_register'] == '1') {
            $tpl->set('{author}', stripslashes($row['name']));
            $go_page = HOME_URL . "user / " . urlencode($row['name']) . " / ";
            $tpl->set('[profile]', " <a href = \"" . HOME_URL . "user/" . urlencode($row['name']) . "/\">");
            $tpl->set('[/profile]', "</a>");
            $tpl->set('{login}', $row['name']);

            if ($this->_isAdmin()) {
                $tpl->set('{ip}', "IP: <a href=\"javascript:\" onclick=\"return dropdownmenu(this, event, IPMenu('" . $row['ip'] . "', '" . $lang['ip_info'] . "', '" . $lang['ip_tools'] . "', '" . $lang['ip_ban'] . "'), '190px')\" onmouseout=\"delayhidemenu()\" href=\"https://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a>");
            } else {
                $tpl->set('{ip}', '');
            }
            if (!$user_group) {
                $user_group = get_vars('usergroup');
            }
            if ($this->_editAccess($row, 1)) {
                $tpl->set('[com-edit]', '<a href="javascript:" onclick="gallery.comments.edit(\'' . $row['id'] . '\'); return false;" >');
                $tpl->set('[/com-edit]', "</a>");
                $tpl->set('[com-del]', "<a href=\"javascript:\" onclick=\"gallery.comments._delete('{$row['id']}'); return false;\">");
                $tpl->set('[/com-del]', "</a>");
            } else {
                $tpl->set_block("'\\[com-edit\\](.*?)\\[/com-edit\\]'si", "");
                $tpl->set_block("'\\[com-del\\](.*?)\\[/com-del\\]'si", "");
            }
            $tpl->set('{mail}', $row['email']);
            $tpl->set('{id}', $row['id']);
            $tpl->set('{date}', model_gallery::dateFormat($row['date']));
            $tpl->set('{registration}', ($row['reg_date'] != '') ? langdate("j.m.Y", $row['reg_date']) : '');
            $tpl->set('{icq}', ($row['icq'] != '') ? stripslashes($row['icq']) : '---');
            $tpl->set('{land}', ($row['land'] != '') ? stripslashes($row['land']) : '---');
            $tpl->set('{fullname}', ($row['fullname'] != '') ? stripslashes($row['fullname']) : '---');
            if ($row['signature'] != '') {
                $tpl->set_block("'\\[signature\\](.*?)\\[/signature\\]'si", "\\1");
                $tpl->set('{signature}', stripslashes($row['signature']));
            } else {
                $tpl->set_block("'\\[signature\\](.*?)\\[/signature\\]'si", "");
            }
            $tpl->set('[fast]', "<a onmouseover=\"gallery.comments.dle_copy_quote('" . str_replace(array(" ", "&#039;"), array("&nbsp;", "&amp;#039;"), $row['autor']) . "')\" onclick=\"gallery.comments.copyQuote('" . str_replace(array(" ", "&#039;"), array("&nbsp;", "&amp;#039;"), $row['autor']) . "'); return false;\" href=\"javascript:\"  >");
            $tpl->set('[/fast]', "</a>");
        } else {
            $tpl->set('{date}', model_gallery::dateFormat($row['date']));
            $tpl->set('[fast]', "<a onmouseover=\"gallery.comments.dle_copy_quote('" . str_replace(array(" ", "&#039;"), array("&nbsp;", "&amp;#039;"), $row['autor']) . "');\"  onclick=\"gallery.comments.copyQuote('" . str_replace(array(" ", "&#039;"), array("&nbsp;", "&amp;#039;"), $row['name']) . "'); return false;\"return false;\" href=\"javascript:\"  >");
            $tpl->set('[/fast]', "</a>");
            $tpl->set('{author}', "<a href=\"mailto:" . htmlspecialchars($row['email'], ENT_QUOTES) . "\">" . $row['autor'] . "</a>");
            if ($this->_editAccess($row, 0)) {
                $tpl->set('[com-edit]', '<a href="javascript:" onclick="gallery.comments.edit(\'' . $row['id'] . '\'); return false;" >');
                $tpl->set('[/com-edit]', "</a>");
                $tpl->set('[com-del]', "<a href=\"javascript:\" onclick=\"gallery.comments._delete('{$row['id']}'); return false;\">");
                $tpl->set('[/com-del]', "</a>");
            } else {
                $tpl->set_block("'\\[com-edit\\](.*?)\\[/com-edit\\]'si", "");
                $tpl->set_block("'\\[com-del\\](.*?)\\[/com-del\\]'si", "");
            }
            $tpl->set('[profile]', '---');
            $tpl->set('[/profile]', '---');
            $tpl->set('{login}', '---');
            $tpl->set('{ip}', '---');
            $tpl->set('{registration}', '---');
            $tpl->set('{icq}', '---');
            $tpl->set('{land}', '---');
            $tpl->set('{fullname}', '---');
            $tpl->set_block("'\\[signature\\](.*?)\\[/signature\\]'si", "");
        }
        if ($row['foto'])
            $tpl->set('{foto}', HOME_URL . "uploads/fotos/" . $row['foto']);
        else
            $tpl->set('{foto}', HOME_URL . "templates/" . $config['skin'] . "/images/noavatar.png");
        if ($user_group[$row['user_group']]['icon'])
            $tpl->set('{group-icon}', "<img src=\"" . $user_group[$row['user_group']]['icon'] . "\" border=\"0\" alt=\"\" />");
        else
            $tpl->set('{group-icon}', "");
        $tpl->set('{group-name}', $user_group[$row['user_group']]['group_prefix'] . $user_group[$row['user_group']]['group_name'] . $user_group[$row['user_group']]['group_suffix']);
        if ($user_group[$this->_user['user_group']]['allow_hide'])
            $row['text'] = preg_replace("'\[hide\](.*?)\[/hide\]'si", "\\1", $row['text']);
        else
            $row['text'] = preg_replace("'\[hide\](.*?)\[/hide\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>", $row['text']);
        $tpl->set('{comment}', '<div id="comm_id_' . $row['id'] . '">' . stripslashes($row['text']) . '</div>');
        $tpl->set('{news-num}', intval($row['news_num']));
        $tpl->set('{comm-num}', intval($row['comm_num']));


        if ($this->_isLogged) {
            $tpl->set('[complaint]', '<a href="javascript:void(0);" onclick="gallery.comments.complaint(\'' . $row['id'] . '\')">');
            $tpl->set('[/complaint]', '</a>');
        } else {
            $tpl->set_block("'\\[complaint\\](.*?)\\[/complaint\\]'si", "");
        }
        if ($this->_isAdmin()) {
            $tpl->set('{mass-action}', '<input name="selected_comments[]" value="' . $row['id'] . '" type="checkbox" />');
        } else {
            $tpl->set('{mass-action}', '');
        }
        $tpl->copy_template = '<div id="box_comm_' . $row['id'] . '">' . $tpl->copy_template . '</div>';
        $tpl->compile('comm');
        return $this->_tpl->result['comm'];
    }

    /**
     *
     * @param array $row
     * @param int $is_reg
     * @return bool
     */
    private function _editAccess($row, $is_reg) {
        global $user_group;
        if ($is_reg)
            return ((true === $this->_isLogged ) && (($this->_user['name'] == $row['name']) && (1 == $row['is_register'])
                || (1 == $user_group[$this->_user['user_group']]['allow_editc'])) ||
                (1 == $user_group[$this->_user['user_group']]['edit_allc']) || (1 == $this->_user['user_group'])) ? true : false;
        else
            return
                (((true === $this->_isLogged ) && (1 == $user_group[$this->_user['user_group']]['allow_editc']))
                    || (1 == $user_group[$this->_user['user_group']]['edit_allc']) || (1 == $this->_user['user_group'])) ? true : false;
    }

}