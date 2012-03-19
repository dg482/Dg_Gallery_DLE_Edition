<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */


class view_related extends view_template {

    public function render($result) {
        $cat = null;
        $_cat = model_gallery::getRegistry('model_category')->getAllCategory();
        $this->setView('related.tpl');
        foreach ($result as $value) {
            $cat = $_cat[$value['parent_id']];
            $this->_tpl->set('{preview-path}', model_file::getThumb($value['path']));
            $this->_tpl->set('[link]', '<a href="' . $value['parent_id'] . '-' . $cat['meta_data']['meta_title'] . '.' . $value['id'] . '">');
            $this->_tpl->set('[/link]', '</a>');
            $this->compile('rel');
        }
        return $this->_tpl->result['rel'];
    }

}

