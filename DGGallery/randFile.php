<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

/**
 * Вывод случайных файлов из галереи.
 *
 */
if (!defined('DATALIFEENGINE')) {
    die("Hacking attempt!");
}
$limit = 3;
if (!class_exists('model_file')) {
    require_once ROOT_DIR . '/DGGallery/app/model/gallery.php';
    require_once ROOT_DIR . '/DGGallery/app/model/category.php';
    require_once ROOT_DIR . '/DGGallery/app/model/cache/file.php';
    require_once ROOT_DIR . '/DGGallery/app/model/file.php';
}
$_tpl = new dle_template;
$_tpl->dir = ROOT_DIR . '/templates/' . $config['skin'] . '/gallery/';
$_tpl->load_template('randFile.tpl');
$db->query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE status!='folder_cover'  ORDER BY RAND() LIMIT " . $limit);
while ($row = $db->get_row()) {
    $_tpl->set('{src}', model_file::getThumb($row['path']));
    $_tpl->set('{title}', $row['title']);
    $cat = model_gallery::getClass('model_category')->getCategory($row['parent_id']);
    $cat_meta_data = (is_string($cat ['meta_data'])) ? unserialize($cat ['meta_data']) : $cat ['meta_data'];
    $_tpl->set('[link]', '<a href="' . $config['http_home_url'] . 'gallery/full/' . $row['parent_id'] . '-' . $cat_meta_data ['meta_title'] . '.' . $row['id'] . '">');
    $_tpl->set('[/link]', '</a>');
    $_tpl->compile('rand');
}
echo $_tpl->result['rand'];