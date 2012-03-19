<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

class model_user extends model_gallery {

    public function __construct() {
        if (null === $this->_db) {
            $this->_db = model_gallery::getRegistry('module_db');
        }
    }

    /**
     * Получение альбомов определенного пользователя.
     * @param string $name
     * @return array
     */
    public function getAlbom($name) {

        $sql = 'SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE author='{$name}'";
        return model_gallery::getClass('model_albom')->getAlbomResult($sql);
    }

    /**
     * Обновление кол-ва альбомов в таблице статистики
     * @param string $set
     * @param int $id
     */
    public function updateAlbom($set, $id) {
        $this->_db->query('UPDATE ' . DBNAME . '.' . PREFIX . "_dg_gallery_user SET albom=albom{$set} WHERE user_id='{$id}' LIMIT 1");
    }

    /**
     * Обновление кол-ва комментариев таблице статистики
     * @param string $set
     * @param int $id
     */
    public function updateComm($set, $id) {
        $this->_db->query('UPDATE ' . DBNAME . '.' . PREFIX . "_dg_gallery_user SET comments=comments{$set} WHERE user_id='{$id}' LIMIT 1");
    }

    public function getInfo($id) {
        return $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_user WHERE user_id='{$id}' LIMIT 1");
    }

    /**
     *
     * @global array $lang
     * @param string $text
     * @param string $name
     * @param string $field
     * @return string
     */
    public function getEditor($text = '', $name='gallery_add_form', $field='descr') {
        global $lang;
        $config = model_gallery::getRegistry('config_cms');

        if (!empty($_POST['config']['descr']))
            $text = trim(stripslashes(strip_tags($_POST['config']['descr'])));
        $code = '';
        if ($config['allow_comments_wysiwyg'] == 'no') {

        }

        $code = <<< HTML
<script type="text/javascript">
//<![CDATA[
var text_enter_url       = "$lang[bb_url]";
var text_enter_size      = "$lang[bb_flash]";
var text_enter_flash     = "$lang[bb_flash_url]";
var text_enter_page      = "$lang[bb_page]";
var text_enter_url_name  = "$lang[bb_url_name]";
var text_enter_page_name = "$lang[bb_page_name]";
var text_enter_image    = "$lang[bb_image]";
var text_enter_email    = "$lang[bb_email]";
var text_code           = "$lang[bb_code]";
var text_quote          = "$lang[bb_quote]";
var error_no_url        = "$lang[bb_no_url]";
var error_no_title      = "$lang[bb_no_title]";
var error_no_email      = "$lang[bb_no_email]";
var prompt_start        = "$lang[bb_prompt_start]";
var img_title   	= "$lang[bb_img_title]";
var email_title  	= "$lang[bb_email_title]";
var text_pages  	= "$lang[bb_bb_page]";
var image_align  	= "{$config['image_align']}";
var bb_t_emo  	        = "{$lang['bb_t_emo']}";
var bb_t_col  	        = "{$lang['bb_t_col']}";
var text_enter_list     = "{$lang['bb_list_item']}";
var selField  = '{$field}';
var fombj    = '{$name}';
//]]>
</script>

<div style="width:465px;border:1px solid #BBB;">
<div style="width:100%; height:25px;border-bottom:1px solid #BBB;background-image:url('{THEME}/bbcodes/bg.gif')">
<div id="b_b" class="editor_button" onclick="simpletag('b')"><img title="$lang[bb_t_b]" src="{THEME}/bbcodes/b.gif" width="23" height="25" border="0" alt="" /></div>
<div id="b_i" class="editor_button" onclick="simpletag('i')"><img title="$lang[bb_t_i]" src="{THEME}/bbcodes/i.gif" width="23" height="25" border="0" alt="" /></div>
<div id="b_u" class="editor_button" onclick="simpletag('u')"><img title="$lang[bb_t_u]" src="{THEME}/bbcodes/u.gif" width="23" height="25" border="0" alt="" /></div>
<div id="b_s" class="editor_button" onclick="simpletag('s')"><img title="$lang[bb_t_s]" src="{THEME}/bbcodes/s.gif" width="23" height="25" border="0" alt="" /></div>
<div class="editor_button"><img src="{THEME}/bbcodes/brkspace.gif" width="5" height="25" border="0" alt="" /></div>
<div id="b_left" class="editor_button" onclick="simpletag('left')"><img title="$lang[bb_t_l]" src="{THEME}/bbcodes/l.gif" width="23" height="25" border="0" alt="" /></div>
<div id="b_center" class="editor_button" onclick="simpletag('center')"><img title="$lang[bb_t_c]" src="{THEME}/bbcodes/c.gif" width="23" height="25" border="0" alt="" /></div>
<div id="b_right" class="editor_button" onclick="simpletag('right')"><img title="$lang[bb_t_r]" src="{THEME}/bbcodes/r.gif" width="23" height="25" border="0" alt="" /></div>
<div class="editor_button"><img src="{THEME}/bbcodes/brkspace.gif" width="5" height="25" border="0" alt="" /></div>
<div id="b_color" class="editor_button" onclick="ins_color(this);"><img src="{THEME}/bbcodes/color.gif" width="23" height="25" border="0" alt="" /></div>
<div class="editor_button"><img src="{THEME}/bbcodes/brkspace.gif" width="5" height="25" border="0" alt="" /></div>
<div id="b_hide" class="editor_button" onclick="simpletag('hide')"><img title="$lang[bb_t_hide]" src="{THEME}/bbcodes/hide.gif" width="23" height="25" border="0" alt="" /></div>
<div id="b_quote" class="editor_button" onclick="simpletag('quote')"><img title="$lang[bb_t_quote]" src="{THEME}/bbcodes/quote.gif" width="23" height="25" border="0" alt="" /></div>
<div id="b_spoiler" class="editor_button" onclick="simpletag('spoiler')"><img src="{THEME}/bbcodes/spoiler.gif" width="23" height="25" border="0" alt="" /></div>
</div>
<textarea name="{$field}" id="$field" cols="" rows="" style="width:465px;height:156px;border:0px;margin: 0px 1px 0px 0px;padding: 0px;" onclick="setFieldName(this.name); fombj =  document.getElementById( '{$name}' );">{$text}</textarea>
</div>
HTML;

        return $code;
    }

}

