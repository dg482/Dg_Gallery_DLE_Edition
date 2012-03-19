<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

if (!defined('HOME_URL')) {
    //    if (null === $config) {
    //        require_once ROOT_DIR . '/engine/data/config.php';
    //    }
    define('HOME_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/');
}
// 1 - категории => альбомы => файлы
// 2 - категории => файлы
define('GALLERY_MODE', 2);
define('GALLERY', TRUE);

/**
 * Отладочная информация.
 * 1. true [default]
 * 2. false
 * @var bool
 */
model_gallery::$debug = true;

/**
 * Кол-вл памяти затраченное до запуска скрипта
 * @var int
 */
model_debug::$m = (model_gallery::$debug) ? ((memory_get_peak_usage() / 1024) / 1024) : 0;

/**
 * Режим вывода.
 * 1. category [default] - выводятся категории, альбомы, файлы.
 * 2. albuom - выводится список альбомов. (проверка доступа альбом, категория)
 * 3. files - выводятся файлы. (без проверок доступа)
 * @var string
 */
controller_gallery::$INDEXMODE = 'category';

/**
 * Режим просмотра альбома.
 * 1. preview [default] - просмотр полного изображения с слайдером превью
 * 2. tile - постраничный вывод превью, режим предпросмотра
 * 3. image - только изображение.
 * @var string
 */
model_albom::$MODE = 'preview';
/**
 * Режим вывода альбома.
 * 1.albom [default] - выводятся альбомы.
 * 2.files - выводятся файлы.
 * @var string
 */
controller_gallery::$SHOWMODE = 'albom';

/**
 * включает проставление ссылок в дереве категорий
 * 1. true [default]
 * 2. false
 * @var bool
 */
model_category::$SITE = true;


$_config = model_gallery::getRegistry('config');

switch (GALLERY_MODE) {
    case 1:
        controller_gallery::$INDEXMODE = 'category';
        controller_gallery::$SHOWMODE = 'albom';
        model_albom::$MODE = 'preview';
        break;
    case 2:
        controller_gallery::$INDEXMODE = 'files';
        controller_gallery::$SHOWMODE = 'files';
        model_albom::$MODE = 'image';
        break;
    default:
        break;
}

/**
 * Обработка AJAX запросов
 */
if (model_request::isAjax()) {
    $tpl->result['content'] = model_gallery::run();
    model_request::setHeaderJson();
    if (is_array($tpl->result['content']))
        echo module_json::getJson($tpl->result['content']);
    else
        echo $tpl->result['content'];
    die();
}

$tpl->result['content'] = model_gallery::run(); // post || get query

/**
 * Автозагрузка классов
 * @param string $className
 */

function __autoload($className) {
    $className = ROOT_DIR . '/DGGallery/app/' . str_replace('_', '/', $className);
    if (file_exists($className . '.php'))
        include_once $className . '.php';
}


