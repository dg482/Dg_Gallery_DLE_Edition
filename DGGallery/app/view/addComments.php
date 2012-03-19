<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */


class view_addComments extends view_comments {

    /**
     *
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     *
     * @global array $lang
     * @global array $config
     * @global array $user_group
     * @param int $parent_id
     * @return string
     */
    public function render($parent_id) {
        global $lang, $config, $user_group;
        $this->_lang = model_gallery::getRegistry('lang');
        if (null === $this->_config)
            $this->_config = model_gallery::getRegistry('config');
        if (!in_array(model_gallery::$user['user_group'], explode(',', $this->_config['accessCommFile']))) {
            model_gallery::getClass('view_template')->msgbox($lang['add_comm'], sprintf($this->_lang['exception']['access_comment'], $user_group[model_gallery::$user['user_group']]['group_name']));
            return '';
        }
        $view = new view_template();
        $view->setView('addcomments.tpl');
        $tpl = $view->getView();
        if (null === $config)
            $config = model_gallery::getRegistry('config_cms');

        $tpl->set('{THEME}', HOME_URL . 'templates/' . $config['skin']);

        if (model_gallery::$user['user_group'] == 5) {
            $tpl->set('[not-logged]', '');
            $tpl->set('[/not-logged]', '');
        } else {
            $tpl->set_block("#\[not-logged\](.*?)\[\/not-logged\]#si", '');
        }

        if ($user_group[$this->user['user_group']]['captcha']) {
            if ($config['allow_recaptcha']) {
                $tpl->set_block("'\\[sec_code\\](.*?)\\[/sec_code\\]'si", "");
                $tpl->copy_template .= <<< HTML
<script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
<script language="javascript" type="text/javascript">
//<![CDATA[
 Recaptcha.create("{$config['recaptcha_public_key']}", 'dle_recaptchagallery', {
       theme: "{$config['recaptcha_theme']}",
       lang:  "{$lang['wysiwyg_language']}"
       });
  $('form#gallery_comment_form').submit(function(){
  gallery.comments.add();
        return false;
    })
//]]>
</script>
HTML;
                $tpl->set('[recaptcha]', "");
                $tpl->set('[/recaptcha]', "");
                $tpl->set('{recaptcha}', '<div id="dle_recaptchagallery" ></div>');
            } else {
                $tpl->set_block("'\\[recaptcha\\](.*?)\\[/recaptcha\\]'si", "");
                $tpl->set('[sec_code]', "");
                $tpl->set('[/sec_code]', "");
                $src = HOME_URL . 'engine/modules/antibot.php';
                $tpl->set('{sec_code}', "<span id=\"gallery_dle-captcha\"><img src=\"" . $src . "\" border=\"0\" alt=\"${lang['sec_image']}\" /><br /><a onclick=\"reload(); return false;\" href=\"#\">{$lang['reload_code']}</a></span>");
                $tpl->copy_template .= <<< JSS
<script language="javascript" type="text/javascript">
//<![CDATA[
function reload() {
	var rndval = new Date().getTime();
	document.getElementById('gallery_dle-captcha' ).innerHTML = '<img src="{$config['http_home_url']}engine/modules/antibot.php?rndval=' + rndval + '" border="0" width="120" height="50" alt="" /><br /><a onclick="reload(); return false;" href="#">{$lang['reload_code']}</a>';
$('#sec_code').val('');
};
  $('form#gallery_comment_form').submit(function(){
   gallery.comments.add();
        return false;
    })
//]]>
</script>
JSS;
            }
        } else {
            $tpl->copy_template .= <<< JSS
<script language="javascript" type="text/javascript">
//<![CDATA[
  $('form#gallery_comment_form').submit(function(){
   gallery.comments.add();
        return false;
    })
//]]>
</script>
JSS;
            $tpl->set_block("'\\[recaptcha\\](.*?)\\[/recaptcha\\]'si", "");
            $tpl->set_block("'\\[sec_code\\](.*?)\\[/sec_code\\]'si", "");
        }
        $tpl->set('{editor}', $this->_getEditor());
        $str = $view->compile('add');
        $user_id = model_gallery::$user['user_id'];

        return <<< JSS
<form action="#" method="post"  name="gallery_comment_form" id="gallery_comment_form" onsubmit="return false;" >
{$str}
<input name="parent_id"  type="hidden" value="{$parent_id}" />
<input name="ns_parent_id"  type="hidden" value="{$parent_id}" />
<input name="user_id"  type="hidden" value="{$user_id}" />
<input name="action"  type="hidden" value="addcomment" />
</form>
JSS;
    }

    /**
     * Вспомогательный метод устанавливающий содержание тега {editor}
     * В зависимости от настроек скрипта [DLE] буду произведены дополнительные действия:
     * 1 - определен редактор BBCODE или WYSIWYG
     *
     * Вернуть HTML
     *
     * @global array $lang
     * @param string $text
     * @param string $name
     * @param string $field
     * @return string
     */
    protected function _getEditor($text = '', $name='gallery_comment_form', $field='comments') {
        global $lang;
        $config = model_gallery::getRegistry('config_cms');
        $this->area = 'file';
        if (!empty($_POST['comments']))
            $text = model_request::getPost('comments');
        $code = '';
        if ($config['allow_comments_wysiwyg'] == 'no') {
            $i = 0;
            $output = '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>';
            $smilies = explode(",", $config['smilies']);
            foreach ($smilies as $smile) {
                $i++;
                $smile = trim($smile);
                $output .= "<td style=\"padding:2px;\" align=\"center\"><a href=\"#\" onclick=\"dle_smiley(':$smile:'); return false;\"><img style=\"border: none;\" alt=\"$smile\" src=\"" . $config['http_home_url'] . "engine/data/emoticons/$smile.gif\" /></a></td>";
                if ($i % 4 == 0)
                    $output .= "</tr><tr>";
            }
            $output .= "</tr></table>";
            $code = <<< HTML
<script type="text/javascript">
//<![CDATA[
var text_enter_url       = "$lang[bb_url]";
var text_enter_size       = "$lang[bb_flash]";
var text_enter_flash       = "$lang[bb_flash_url]";
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
<div id="b_emo" class="editor_button"  onclick="ins_emo(this);"><img title="$lang[bb_t_emo]" src="{THEME}/bbcodes/emo.gif" width="23" height="25" border="0" alt="" /></div>
<div id="b_color" class="editor_button" onclick="ins_color(this);"><img src="{THEME}/bbcodes/color.gif" width="23" height="25" border="0" alt="" /></div>
<div class="editor_button"><img src="{THEME}/bbcodes/brkspace.gif" width="5" height="25" border="0" alt="" /></div>
<div id="b_hide" class="editor_button" onclick="simpletag('hide')"><img title="$lang[bb_t_hide]" src="{THEME}/bbcodes/hide.gif" width="23" height="25" border="0" alt="" /></div>
<div id="b_quote" class="editor_button" onclick="simpletag('quote')"><img title="$lang[bb_t_quote]" src="{THEME}/bbcodes/quote.gif" width="23" height="25" border="0" alt="" /></div>
<div class="editor_button" onclick="translit()"><img title="$lang[bb_t_translit]" src="{THEME}/bbcodes/translit.gif" width="23" height="25" border="0" alt="" /></div>
<div id="b_spoiler" class="editor_button" onclick="simpletag('spoiler')"><img src="{THEME}/bbcodes/spoiler.gif" width="23" height="25" border="0" alt="" /></div>
</div>
<div id="dle_emos" style="display: none;" title="{$lang['bb_t_emo']}">
<div style="height:150px;overflow: auto;">{$output}</div></div>
<textarea name="{$field}" id="{$field}" cols="" rows="" style="width:465px;height:156px;border:0px;margin: 0px 1px 0px 0px;padding: 0px;" onclick="setFieldName(this.name); fombj =  document.getElementById( '{$name}' );">{$text}</textarea>
</div>
HTML;
        } else {
            $code = <<<HTML
<script type="text/javascript">
//<![CDATA[
$(function(){
	$('#comments').tinymce({
		script_url : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/tiny_mce.js',
		theme : "advanced",
		skin : "cirkuit",
		language : "{$lang['wysiwyg_language']}",
		width : "460",
		height : "220",
		plugins : "safari,emotions,inlinepopups",
		convert_urls : false,
		force_p_newlines : false,
		force_br_newlines : true,
		dialog_type : 'window',
		extended_valid_elements : "div[align|class|style|id|title]",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright, justifyfull,|,bullist,numlist,|,emotions,dle_quote,dle_hide",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		// Example content CSS (should be your site CSS)
		content_css : "{$config['http_home_url']}engine/editor/css/content.css",
		setup : function(ed) {
		        // Add a custom button
			ed.addButton('dle_quote', {
			title : '{$lang['bb_t_quote']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_quote.gif',
			onclick : function() {
				// Add you own code to execute something on click
				ed.execCommand('mceReplaceContent',false,'[quote]' + ed.selection.getContent() + '[/quote]');
			}
	           });
			ed.addButton('dle_hide', {
			title : '{$lang['bb_t_hide']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_hide.gif',
			onclick : function() {
				// Add you own code to execute something on click
				ed.execCommand('mceReplaceContent',false,'[hide]' + ed.selection.getContent() + '[/hide]');
			}
	           });
			ed.addButton('dle_leech', {
			title : '{$lang['bb_t_leech']}',
			image : '{$config['http_home_url']}engine/editor/jscripts/tiny_mce/themes/advanced/img/dle_leech.gif',
			onclick : function() {
                             ed.execCommand('mceReplaceContent',false,"[leech=http://]{\$selection}[/leech]");
			}
	           });
   	   }
	});
});
//]]>
</script>
<textarea name="comments" id="comments" rows="10" cols="50">{$text}</textarea>
HTML;
        }
        return $code;
    }

    /**
     *
     * @global array $config
     * @global array $lang
     * @return array
     */
    public function renderEdit() {
        $id = model_request::getRequest('id');
        if (null == $id)
            return false;
        $row = $this->_db->super_query('SELECT * FROM ' . PREFIX . "_dg_gallery_comments WHERE id='{$id}' LIMIT 1");
        global $config, $lang;
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
        $bb_code = $this->_getEditor($this->_parse->decodeBBCodes($row['text'], false), 'editcomments');
        $buffer = <<< HTML
<form id="editcomments"><div class="editor">{$bb_code}
<div align="right" style="width:99%;padding-top:5px;">
  <input class=bbcodes title="$lang[bb_t_apply]" type=button onclick="gallery.comments.save('{$id}'); return false;" value="$lang[bb_b_apply]" />
  <input class=bbcodes title="$lang[bb_t_cancel]" type=button onclick="gallery.comments.cancel('{$id}'); return false;" value="$lang[bb_b_cancel]" />
</div></div></form>
HTML;
        $buffer = str_replace('{THEME}', HOME_URL . 'templates/' . $config['skin'], $buffer);
        return array('tpl' => $buffer);
    }

}