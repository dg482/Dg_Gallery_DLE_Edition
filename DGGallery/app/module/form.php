<?php
/**
 * @package dg_r
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

/**
 * Генератор форм из массивов по заданным параметрам,
 * есть зависимости от глобальных переменных DLE и Register Globals.
 * Подходит для узкого применения в рамках одного скрипта.
 */
class module_form {

    /**
     *
     * @var array
     */
    protected $_config;
    /**
     * Конфигурационный массив формы
     * @var array
     */
    protected $_forms;
    /**
     * Искать значения в setValue
     * @var bool
     */
    public $setconfig;
    /**
     * action=""
     * @var sting
     */
    public $actionPath;
    /**
     * Массив устанавливаемых значений
     * @var array
     */
    public $setValue;

    /**
     * @param null $option
     */
    public function __construct($option = null) {
        if (null === $option) {
            $this->_forms = require_once ROOT_DIR . '/DGGallery/app/config/adminForms/forms.php';
        } else {
            if (is_array($option) and array_key_exists('form', $option)) {
                $this->_forms = $option;
            } else {
                #throw new  Exception('error array format');
            }
        }
        $this->_config = model_gallery::getRegistry('model_config');
    }

    /**
     * @param string $name
     * @param string $nameForm
     * @param bool $part
     * @param bool $block
     * @return string
     */
    public function getForm($name = 'setting', $nameForm = '', $part = false, $block = false) {
        $form = $this->_forms;
        if (is_array($form ['form'] [$nameForm])) {
            $a = str_replace('?', $this->actionPath, $form ['form'] ['action']);
            $name = $form ['form'] ['name'];
            $result = '<form name="' . $form ['form'] ['name'] . '" method="post" action="' . $a . '">';
            $result .= ( $part == true) ? '<div id="part-' . $nameForm . '">' : '';
            $result .= '<fieldset class="b-4"><legend class="b-4">' . $form ['form'] [$nameForm] ['legend'] . '</legend>';

            $result .= '<table width="100%" border="0"  class="table-form">';
            foreach ($form ['form'] [$nameForm] as $key => $row) {
                if (!empty($row) && is_array($row)) {
                    foreach ($row as $value) {
                        $result .= '<tr> <td class="td-label"><label>' . $value ['label'] . '</label></td>
                        <td class="td-content">';
                        $result .= $this->setElement($value ['type'], $value ['key'], $value ['values']);
                        if ($value ['subelement']) {

                            $result .= $this->setElement($value ['subelement'] ['type'], $value ['subelement'] ['key'], $value ['subelement'] ['values']);
                        }
                        $result .= '</td></tr>';
                    }
                }
            }
            $result .= '</table></fieldset>';
            $result .= ( $block == true) ? '</div>' : '';
            return $result;
        }
    }

    /**
     * @param $nameForm
     * @param bool $startPart
     * @param bool $endPatr
     * @return string
     */
    public function setPatr($nameForm, $startPart = false, $endPatr = false) {
        $form = $this->_forms;
        $a = str_replace('?', $this->actionPath, $form ['form'] [$nameForm] ['action']);
        $name = $form ['form'] ['name'];
        $result = ($startPart) ? '<div id="part-' . $nameForm . '">' : '';
        $result .= '<fieldset  class="b-4"><legend  class="b-4">' . $form ['form'] [$nameForm] ['legend'] . '</legend>';
        $element = '';
        $result .= '<table width="100%" border="0" class="table-form">';
        if (is_array($form ['form'] [$nameForm]))
            foreach ($form ['form'] [$nameForm] as $key => $row) {
                if (!empty($row) && is_array($row)) {
                    foreach ($row as $value) {
                        if ($value['type'] === 'hidden') {
                            $result .= $this->setElement($value ['type'], $value ['key'], $value ['values']);
                        } else {
                            $result .= '<tr> <td class="td-label"><label>' . $value ['label'] . '</label></td>
                        <td class="td-content">';
                            $result .= $this->setElement($value ['type'], $value ['key'], $value ['values']);
                            if ($value ['subelement']) {
                                $result .= $this->setElement($value ['subelement'] ['type'], $value ['subelement'] ['key'], $value ['subelement'] ['values']);
                            }
                            $result .= '</td></tr>';
                        }
                    }
                }
            }
        $result .= '</table></fieldset>';
        $result .= ( $endPatr) ? '</div>' : '';
        return $result;
    }

    /**
     * @param $type
     * @param $key
     * @param null $value
     * @return string
     */
    protected function setElement($type, $key, $value = null) {
        $result = '';
        switch ($type) {
            case 'checkbox' :
                return $this->setCheckbox($key);
            case 'text' :
                return $this->setTextfield($key);
            case 'textarea' :
                return $this->setTextarea($key);
            case 'select' :
                return $this->setSelect($key, $value);
            case 'multiple' :
                return $this->setSelectMultiple($key, $value);
            case 'textareawysiwyg' :

                return $this->setTextarea($key, 'jwysiwyg');
            case 'uploadify' :
                return '<div id="view-image" class="cover"></div><div id="fileQueue"></div><input type="file" name="uploadify" id="uploadify"/>';
            case 'submit':
                return '<input type="submit" value="применить" />';
            case 'bbcode':
                return $this->getEditor($value, $key);
            case 'hidden':

                return '<input type="hidden" name="' . $key . '" value="' . $this->setconfig[$key] . '" />';
        }
        return $result;
    }

    /**
     * @param string $n
     * @param null $v
     * @return string
     */
    protected function setSelectMultiple($n = '', $v = null) {
        $s = '<div><select name="config[' . $n . '][]"  multiple="multiple" >';
        global $$v ['data'];
        $val = $$v ['data'];
        $conf = (!empty($this->setconfig [$n])) ? explode(',', $this->setconfig [$n]) : null;

        if (is_array($val))
            foreach ($val as $key => $value) {
                if (is_array($conf))
                    $selected = (in_array($value [$v ['key']], $conf)) ? 'selected' : '';
                $s .= '<option value="' . $value [$v ['key']] . '" ' . $selected . ' >' . $value [$v ['label']] . '</option>';
            }
        $s .= '</select></div>';
        return $s;
    }

    /**
     * @param string $n
     * @param null $v
     * @return string
     */
    protected function setSelect($n = '', $v = null) {
        $s = '<div><select name="config[' . $n . ']" >';
        if (key_exists('data', $v)) {
            global $$v ['data'];
            $val = $$v ['data'];
            $parent_id = ($v ['check']) ? $this->setconfig [$v ['check']] : 0;

            $s .= $this->selectCategories($val, '', $parent_id);
        } else {
            foreach ($v as $k => $val) {
                $ss = ($this->setconfig[$n] == $k) ? 'selected="selected"' : '';
                $s .= '<option value="' . $k . '" ' . $ss . ' >' . $val . '</option>';
            }
        }
        $s .= '</select></div>';
        return $s;
    }

    /**
     * @param $current_arr
     * @param string $trigger
     * @param int $current_id
     * @param bool $r_id
     * @return string
     */
    public function selectCategories($current_arr, $trigger = '', $current_id = 0, $r_id = true) {
        $global_cat = $current_arr;
        settype($trigger, "string");
        if (!strstr($current_id, ',')) {
            $select = '';
            if (!is_array($current_arr)) {
                if (is_array($global_cat)) {
                    foreach ($global_cat as & $rec) {
                        if ($current_id == $rec ['id']) {
                            if ($r_id) {
                                $select .= '<option value="' . $rec ['id'] . '"  selected="selected">' . $rec ['title'] . '</option>';
                            } else {
                                $select .= '<option value="' . $rec ['parent_id'] . '"  selected="selected">' . $rec ['title'] . '</option>';
                            }
                        } else {
                            $select .= '<option value="' . $rec ['id'] . '" >' . $rec ['title'] . '</option>';
                        }
                        if (is_array($rec ['children'])) {
                            $trigger .= $rec ['title'] . '--';
                            $select .= $this->selectCategories($rec ['children'], $trigger, $current_id, $r_id);
                        }
                    }
                } else {
                    $select .= '<option value="0" ></option>';
                }
            } else {
                foreach ($current_arr as $val) {
                    if ($val ['parent_id'] == 0) {
                        $trigger = '';
                    }
                    if ($current_id == $val ['id']) {
                        if ($r_id) {
                            $select .= '<option value="' . $val ['id'] . '"  selected="selected">' . $trigger . $val ['title'] . '</option>';
                        } else {
                            $select .= '<option value="' . $val ['parent_id'] . '"  selected="selected">' . $trigger . $val ['title'] . '</option>';
                        }
                    } else {
                        $select .= '<option value="' . $val ['id'] . '"> ' . $trigger . $val ['title'] . '</option>';
                    }
                    if (is_array($val ['children'])) {
                        $trigger .= "-";
                        $select .= $this->selectCategories($val ['children'], $trigger, $current_id, $r_id);
                    }
                }
            }
            return $select;
        } else {
            $trigger_str = $current_id;
            $current_id = explode(',', $current_id);
            if (!is_array($current_arr)) {
                //.........................
            } else {
                 $select  = '';
                foreach ($current_arr as $val) {
                    if ($val ['parent_id'] == 0) {
                        $trigger = '';
                    }
                    if (in_array($val ['id'], $current_id)) {
                        if ($r_id) {
                            $select .= '<option value="' . $val ['id'] . '"  selected="selected">' . $trigger . $val ['title'] . '</option>';
                        } else {
                            $select .= '<option value="' . $val ['parent_id'] . '"  selected="selected">' . $trigger . $val ['title'] . '</option>';
                        }
                    } else {
                        $select .= '<option value="' . $val ['id'] . '"> ' . $trigger . $val ['title'] . '</option>';
                    }
                    if (is_array($val ['children'])) {
                        $trigger .= "--";
                        $select .= $this->selectCategories($val ['children'], $trigger, $trigger_str);
                    }
                }
            }
            return $select;
        }
    }

    /**
     * @param string $n
     * @return string
     */
    protected function setCheckbox($n = '') {
        if (intval($this->setconfig [$n])) {
            return '<input type="checkbox" checked="checked" name="config[' . $n . ']" value="1" />';
        } else {
            return '<input type="checkbox"   name="config[' . $n . ']" value="0" />';
        }
    }

    /**
     * @param string $n
     * @return string
     */
    protected function setTextfield($n = '') {
        if ($this->setValue) {
            return '<input type="text" class="input-text b-4" name="config[' . $n . ']" value="' . $this->setconfig [$n] . '" />';
        } else {
            return '<input type="text" name="config[' . $n . ']" />';
        }
    }

    /**
     * @param string $n
     * @param string $w
     * @return string
     */
    protected function setTextarea($n = '', $w = '') {
        if ($this->setValue) {
            return '<textarea rows="" cols="" name="config[' . $n . ']"  id="' . $n . '" class="b-4 ' . $w . '">' . $this->setconfig [$n] . '</textarea>';
        } else {
            return '<textarea rows="" cols="" name="config[' . $n . ']" id="' . $n . '" class="b-4 ' . $w . '"></textarea>';
        }
    }

    /**
     * @param string $html
     * @return string
     */
    public function closeForm($html = '') {
        return $html . '</form>';
    }

    /**
     * @param string $text
     * @param string $name
     * @param string $field
     * @return string
     */
    public function getEditor($text = '', $name='gallery_add_form', $field='config[descr]') {
        global $lang;
        $config = model_gallery::getRegistry('config_cms');

        if (!empty($_POST['config']['descr']))
            $text = trim(stripslashes(strip_tags($_POST['config']['descr'])));
        $code = '';
        if ($config['allow_comments_wysiwyg'] == 'no') {

        }
        $text = $this->setconfig ['description'];

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
var selField  = 'descr';
var fombj    = 'gallery_add_form';
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
<textarea name="descr" id="descr" cols="" rows="" style="width:465px;height:156px;border:0px;margin: 0px 1px 0px 0px;padding: 0px;" onclick="setFieldName(this.name); fombj =  document.getElementById( 'gallery_add_form' );">{$text}</textarea>
</div>
HTML;

        return $code;
    }

}
