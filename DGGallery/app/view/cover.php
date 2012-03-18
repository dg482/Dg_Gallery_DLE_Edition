<?php

/**
 * Представление cover.tpl
 * Методы вывода категорий, альбомов, файлов.
 *
 *
 * @package gallery
 * @author Dark Ghost
 * @copyright 2011
 * @access public
 * @since 1.5.3 (07.2011)
 *
 */
class view_cover extends view_template {

    public function __construct() {
        parent::__construct();
        $this->setView('cover.tpl');
    }

    /**
     *
     * @param array $result
     * @return string
     */
    public function renderIndexCategory(array $result) {
        $alb = model_gallery::getClass('model_albom');
        foreach ($result ['cat'] as $value) {
            if (0 == $value ['access_cat']) {
                $this->_tpl->set('[access_denied]', '');
                $this->_tpl->set('[/access_denied]', '');
                $this->_tpl->set_block("#\\[access_granted\\](.*?)\\[/access_granted\\]#si", '');
            } else {
                $this->_tpl->set('[access_granted]', '');
                $this->_tpl->set('[/access_granted]', '');
                $this->_tpl->set_block("#\\[access_denied\\](.*?)\\[/access_denied\\]#si", '');
            }
            $this->_tpl->set('[category]', '');
            $this->_tpl->set('[/category]', '');
            $this->_tpl->set_block("#\\[albom\\](.*?)\\[/albom\\]#si", '');
            $this->_tpl->set_block("#\\[file\\](.*?)\\[/file\\]#si", '');
            $this->_tpl->set_block("#\\[delete-link\\](.*?)\\[/delete-link\\]#si", '');
            $meta_data = (is_string($value ['meta_data'])) ? unserialize($value ['meta_data']) : $value ['meta_data'];
            $meta_data ['meta_title'] = stripslashes($meta_data ['meta_title']);
            $this->_tpl->set('{id}', $value ['id']);
            if ($meta_data ['cover'] != '') {
                $cover = str_replace('%replace%/', 'thumbs/', $meta_data ['cover']);
                $this->_tpl->set('{src}', ($cover != '' && file_exists(ROOT_DIR . $cover)) ? $cover : $alb->getRandFile($value ['id']) );
            } else {
                $this->_tpl->set('{src}', $alb->getRandFile($value ['id']));
            }

            $this->_tpl->set('{meta_name}', (isset($meta_data ['meta_title'])) ? $meta_data ['meta_title'] : '');
            $this->_tpl->set('{meta_title}', (isset($meta_data ['meta_title'])) ? $meta_data ['meta_title'] : '');
            $this->_tpl->set('{meta_keyword}', (isset($meta_data ['meta_keywords'])) ? stripslashes($meta_data ['meta_keywords']) : '');

            $this->_tpl->set('[link]', '<a href="' . HOME_URL . 'gallery/show/' . $value ['id'] . '-' . $meta_data ['meta_title'] . '">');
            $this->_tpl->set('[/link]', '</a>');
            $this->_tpl->set('{albom_num}', $value ['albom_num']);
            $this->_tpl->set('{name}', stripslashes($value ['title']));

            $this->_tpl->compile('cover');
        }
        return $this->_tpl->result['cover'];
    }

    /**
     *
     * @param array $result
     * @return string
     */
    public function renderAlbom(array $result) {
        $alb = model_gallery::getClass('model_albom');
        foreach ($result as $row) {
            if (is_array($row)) {
                foreach ($row as $value) {
                    if (0 == $value ['access_cat'] or 0 == $value ['access_albom']) {
                        $this->_tpl->set('[access_denied]', '');
                        $this->_tpl->set('[/access_denied]', '');
                        $this->_tpl->set_block("#\\[access_granted\\](.*?)\\[/access_granted\\]#si", '');
                    } else {
                        $this->_tpl->set('[access_granted]', '');
                        $this->_tpl->set('[/access_granted]', '');
                        $this->_tpl->set_block("#\\[access_denied\\](.*?)\\[/access_denied\\]#si", '');
                    }
                    if ($this->_isAdmin()) {
                        $this->_tpl->set('[delete-link]', '<a href="javascript:void(0)" onclick="deleteAlbom(\'' . $value['id'] . '\')">');
                        $this->_tpl->set('[/delete-link]', '</a>');
                    } else {
                        $this->_tpl->set_block("#\\[delete-link\\](.*?)\\[/delete-link\\]#si", '');
                    }
                    $this->_tpl->set('[albom]', '');
                    $this->_tpl->set('[/albom]', '');
                    $this->_tpl->set_block("#\\[category\\](.*?)\\[/category\\]#si", '');
                    $this->_tpl->set_block("#\\[file\\](.*?)\\[/file\\]#si", '');
                    $this->_tpl->set('{file_num}', $value ['images']);
                    $meta_data = (is_string($value ['meta_data'])) ? unserialize($value ['meta_data']) : $value ['meta_data'];
                    $this->_tpl->set('{id}', $value ['id']);
                    $cover = '';
                    if ($meta_data ['cover'] != '') {
                        $cover = str_replace('%replace%/', 'thumbs/', $meta_data ['cover']);
                        $this->_tpl->set('{src}', ($cover != '' && file_exists(ROOT_DIR . $cover)) ? $cover : $alb->getRandFile($value ['id']) );
                    } else {
                        $this->_tpl->set('{src}', $alb->getRandFile($value ['id']));
                    }
                    $this->_tpl->set('{author}', $value['author']);
                    $this->_tpl->set('{meta_name}', (isset($meta_data ['meta_title'])) ? $meta_data ['meta_title'] : '');
                    $this->_tpl->set('{meta_title}', (isset($meta_data ['meta_title'])) ? $meta_data ['meta_title'] : '');
                    $this->_tpl->set('{meta_keyword}', (isset($meta_data ['meta_keywords'])) ? stripslashes($meta_data ['meta_keywords']) : '');

                    $this->_tpl->set('{name}', stripslashes($value ['title']));
                    $this->_tpl->set('[link]', '<a href="' . HOME_URL . 'gallery/albom/' . $value ['id'] . '-' . $meta_data ['meta_title'] . '">');
                    $this->_tpl->set('[/link]', '</a>');
                    $this->_tpl->compile('cover');
                }
            }
        }
        return $this->_tpl->result['cover'];
    }

    /**
     *
     * @param array $result
     * @return string
     */
    public function renderFile(array $result) {
        $access_data = null;
        $meta_data = null;
        $other_dat = null;
        $lang = null;
        $cat = null;
        $lang = model_gallery::getRegistry('lang');
        foreach ($result ['file'] as $value) {
            $this->_tpl->set_block("#\\[delete-link\\](.*?)\\[/delete-link\\]#si", '');
            $access_data = unserialize($value ['access_data']);
            $meta_data = unserialize($value ['meta_data']);
            $other_dat = (is_string($value ['other_dat'])) ? unserialize($value ['other_dat']) : $value ['other_dat'];
            $access_data ['access'] = explode(',', $access_data ['accessView']);
            if (!in_array(model_gallery::$user ['user_group'], $access_data ['access']) && (GALLERY_MODE === 1)) { //check permission album
                $this->_tpl->set('[access_denied]', '');
                $this->_tpl->set('[/access_denied]', '');
                $this->_tpl->set_block("#\\[access_granted\\](.*?)\\[/access_granted\\]#si", '');
            } else {
                $this->_tpl->set('[access_granted]', '');
                $this->_tpl->set('[/access_granted]', '');
                $this->_tpl->set_block("#\\[access_denied\\](.*?)\\[/access_denied\\]#si", '');
            }
            $this->_tpl->set('{src}', model_file::getThumb($value ['path']));
            $value ['title'] = ($value ['title'] != '') ? stripslashes($value ['title']) : $lang ['title'] ['title_1'];
            $this->_tpl->set('{name}', $value ['title']);
            $this->_tpl->set('[file]', '');
            $this->_tpl->set('[/file]', '');
            $this->_tpl->set('{width}', $other_dat ['info'] ["originalWidth"]);
            $this->_tpl->set('{height}', $other_dat ['info'] ["originalHeight"]);
            $this->_tpl->set('{meta_name}', (isset($meta_data ['meta_title'])) ? $meta_data ['meta_title'] : '');
            $this->_tpl->set('{meta_title}', (isset($meta_data ['meta_title'])) ? $meta_data ['meta_title'] : '');
            $this->_tpl->set('{meta_keyword}', (isset($meta_data ['meta_keywords'])) ? stripslashes($meta_data ['meta_keywords']) : '');

            $this->_tpl->set('{date}', model_gallery::dateFormat($value ['date']));
            $this->_tpl->set('{id}', $value ['id']);

            if (GALLERY_MODE === 1) {
                $cat = model_gallery::getRegistry('model_category')->getCategory($other_dat ['other_dat'] ['category']);
                $cat_meta_data = (is_string($cat ['meta_data'])) ? unserialize($cat ['meta_data']) : $cat ['meta_data'];
                //http://gallery.ru/gallery/albom/2-albom-1.101
                $this->_tpl->set('[link]', '<a href="' . HOME_URL . 'gallery/albom/' . $value ['parent_id'] . '-' . $meta_data ['meta_title'] . '.' . $value ['id'] . '">');
                //http://gallery.ru/gallery/show/10-demo-1
                $this->_tpl->set('[catlink]', '<a href="' . HOME_URL . 'gallery/show/' . $cat ['id'] . '-' . $cat_meta_data ['meta_title'] . '.' . $value ['id'] . '">');
            } elseif (GALLERY_MODE === 2) {
                $cat = model_gallery::getRegistry('model_category')->getCategory($value ['parent_id']);
                $cat_meta_data = (is_string($cat ['meta_data'])) ? unserialize($cat ['meta_data']) : $cat ['meta_data'];
                //http://gallery.ru/gallery/albom/2-albom-1.101
                $this->_tpl->set('[link]', '<a href="' . HOME_URL . 'gallery/full/' . $value ['parent_id'] . '-' . $cat_meta_data ['meta_title'] . '.' . $value ['id'] . '">');
                //http://gallery.ru/gallery/show/10-demo-1
                $this->_tpl->set('[catlink]', '<a href="' . HOME_URL . 'gallery/show/' . $value ['parent_id'] . '-' . $cat_meta_data ['meta_title'] . '.' . $value ['id'] . '">');
            }
            $this->_tpl->set('{category}', $cat ['title']);
            $this->_tpl->set('[/catlink]', '</a>');
            $this->_tpl->set('[/link]', '</a>');
            $this->_tpl->set_block("#\\[albom\\](.*?)\\[/albom\\]#si", '');
            $this->_tpl->set_block("#\\[category\\](.*?)\\[/category\\]#si", '');
            $this->_tpl->compile('cover');
        }
        return $this->_tpl->result['cover'];
    }

}

