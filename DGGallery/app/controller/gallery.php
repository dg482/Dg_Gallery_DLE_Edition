<?php

/**
 * Основной контроллер скрипта, вывод категорий, альбомов, файлов, полный просмотр отдельного файла.
 * Параметры вывода определяются до начала маршрутизации запроса  в index.php.
 * <pre>
 * $INDEXMODE - параметры главной страницы может принимать три значения.
 * * 1. category [default] - выводятся категории, альбомы, файлы.
 * * 2. albuom - выводится список альбомов. (проверка доступа альбом, категория)
 * * 3. files - выводятся файлы. (без проверок доступа)
 * $SHOWMODE - параметры просмотра альбомов, може принимать два значения
 * * 1.albom [default] - выводятся альбомы.
 * * 2.files - выводятся файлы.
 * </pre>
 *
 * @package gallery
 * @author Dark Ghost
 * @copyright 2011
 * @access public
 * @since 1.5.3 (07.2011)
 *
 */
class controller_gallery {

    /**
     *
     * @var array
     */
    protected $_config_cms;

    /**
     *
     * @var array
     */
    protected $_config;

    /**
     *
     * @var object
     */
    protected $_request;

    /**
     *
     * @var object
     */
    protected $_view;

    /**
     *
     * @var object
     */
    protected $_tpl;

    /**
     *
     * @var object
     */
    protected $_db;

    /**
     *
     * @var string
     */
    public static $INDEXMODE;

    /**
     *
     * @var string
     */
    public static $SHOWMODE;

    /**
     *
     * @var array
     */
    protected $_lang;

    /**
     *
     * @var array
     */
    public static $CATEGORY;

    public function __construct($site = true) {
        $config = null;
        include ROOT_DIR . '/engine/data/config.php';
        $this->_config_cms = $config;
        model_gallery::setRegistry('config_cms', $this->_config_cms);
        $this->_config = model_gallery::getRegistry('config');
        $this->_view = new view_template ();
        $this->_tpl = $this->_view->getView();
        $this->_view->setView('main.tpl');
        $this->_db = model_gallery::getRegistry('module_db');
        $this->_lang = (include ROOT_DIR . '/DGGallery/app/lang/gallery.php');
        model_gallery::setRegistry('lang', $this->_lang);
        if ($site && (false === model_request::isAjax())) {
            if ($this->_config['allCategory']) {
                if (stripos($this->_tpl->copy_template, '{cat_tree}') !== false) {
                    $this->_tpl->set('{cat_tree}', model_gallery::getClass('model_category')->catTree());
                }
            } else {
                $this->_tpl->set('{cat_tree}', '');
            }

            if ($this->_config['statusAlfavit']) {
                if (stripos($this->_tpl->copy_template, '{alfa}') !== false) {
                    $this->_tpl->set('{alfa}', model_search::getAlfa());
                }
            } else {
                $this->_tpl->set('{alfa}', '');
            }
            if (null !== model_gallery::getRegistry('route'))
                if ('sort' != model_gallery::getRegistry('route')->getAction()) {
                    $this->_tpl->set('{' . model_search::$DEF_ORDER . '}', ' active ');
                }
            if (model_request::getPost('keyword')) {
                $query = model_search::strip_data(trim(strip_tags(model_request::getPost('keyword'))));
                $this->_tpl->set('{search_keyword}', $query);
            } else {
                $this->_tpl->set('{search_keyword}', '');
            }
            $this->_config;
            $this->_setMetaTag(array(
                'meta_title' => $this->_config['title'],
                'meta_descr' => $this->_config['metadescr'],
                'meta_keywords' => $this->_config['metakeywords']
            ));
        }
    }

    /**
     *
     * @return  event
     */
    public function sortAction() {
        $param = model_gallery::getRegistry('route')->getParam();
        $order = current($param);
        $allow = array('ORDER_COMMENTS', 'ORDER_DATE', 'ORDER_RATING', 'ORDER_DOWNLOAD');
        $order = array(
            'ORDER_COMMENTS' => ' comments',
            'ORDER_DATE' => 'date',
            'ORDER_RATING' => 'rating',
            'ORDER_DOWNLOAD' => 'download'
        );
        if (!in_array($order, $allow)) {
            $order = 'ORDER_DATE';
        }
        model_search::$DEF_ORDER = $order;
        return $this->indexAction();
    }

    /**
     * http://site.ru/gallery/
     * В зависимости от значения переменной self::$INDEXMODE будут выведены категоии, альбомы или файлы.
     *
     * Перехват исключений:
     * 1 - массив категорий пуст
     * 2 - неопределено или неподдерживаемое значение self::$INDEXMODE
     *
     * @return string
     */
    public function indexAction() {
        $content = '';
        $lang = null;
        $model = model_gallery::getClass('model_gallery');
        self::$CATEGORY = 0;
        $result = model_gallery::getClass('model_category')->getCatPage();
        $navPrefix = 'gallery/';
        $lang = model_gallery::getRegistry('lang');
        if (GALLERY_MODE === 2) {
            $result ['cat'] = array();
        }
        try {
            if (is_array($result ['cat'])) {
                switch (self::$INDEXMODE) {
                    case 'category' :
                        model_gallery::setRegistry('view_action', 'category');
                        $content .= model_gallery::getClass('view_cover')->renderIndexCategory($result);
                        $navPrefix = 'gallery/category';
                        $this->_setSpeedbar(array());
                        break;
                    case 'albom' :
                        model_gallery::setRegistry('view_action', 'albom');
                        $result = model_gallery::getClass('model_albom')->getPage($this->_config ['indexPage']);
                        try {
                            if (is_array($result ['alb'])) {
                                $content .= model_gallery::getClass('view_cover')->renderAlbom($result);
                            } else {
                                throw new controller_exception($this->_lang ['exception'] ['empty_cat']);
                            }
                        } catch (controller_exception $exc) {
                            $this->_tpl->set('{pagination}', '');
                            $content = $exc->set404();
                        }
                        $navPrefix = 'gallery/';
                        $this->_setSpeedbar(array());
                        break;
                    case 'files' :
                        model_gallery::setRegistry('view_action', 'file');
                        $result = model_gallery::getClass('model_file')->getPage($this->_config ['indexPage']);
                        if (null === $result ['file']) {
                            throw new controller_exception('404');
                        }

                        $content = model_gallery::getClass('view_cover')->renderFile($result);

                        $this->_setSpeedbar(array());
                        break;
                    default :
                        break;
                }
                $counter = (int) $result ['count'];
                if ($counter > $this->_config ['indexPage']) {
                    $this->_tpl->set('{pagination}', $model->_nav((int) $counter, array(
                                'global_query_end' => $this->_config ['indexPage'],
                                'nav_prefix' => $navPrefix)
                                    //'nav_suffix' => '.html'
                                    , clone $this->_tpl));
                } else {
                    $this->_tpl->set('{pagination}', '');
                }
                $this->_tpl->set('{json}', '');
            } else {

                throw new controller_exception($this->_lang ['exception'] ['empty']);
                $this->_setSpeedbar(array());
            }
        } catch (controller_exception $exc) {
            $this->_setSpeedbar(array());
            $this->_tpl->set('{pagination}', '');
            $content = $exc->set404();
        }
        $this->_setJsMin();
        if (null !== model_gallery::getRegistry('route'))
            $this->_setOrder(HOME_URL . model_gallery::getRegistry('route')->getServerURI() . '/');
        $this->_tpl->set('{content}', $content);
        return $this->_view->compile('main');
    }

    /**
     * http://site.ru/gallery/show/[ID_CATEGORY]-[META_TITLE_ALBOM]
     * В зависимости от значения переменной self::self::$SHOWMODE бедут выведенв альбомы или файлы.
     *
     * Перехват исключений по следующим событиям:
     * 1 - пустой массив результатов
     * 2 - у группы пользователя нет доступа к категории
     * 3 - неопределено или неподдерживаемое значение self::$SHOWMODE
     *
     * @global type $metatags
     * @return type
     */
    public function showAction() {
        $this->_setOrder(HOME_URL . model_gallery::getRegistry('route')->getServerURI() . '/');
        $id = model_request::getRequest('id');
        $content = '';
        $count = 0;
        $alb = model_gallery::getClass('model_albom');

        $model = model_gallery::getClass('model_gallery');
        self::$CATEGORY = model_gallery::getClass('model_category')->getCatInfo($id);
        if (is_array(self::$CATEGORY)) {
            global $metatags;

            $meta_data = (is_string(self::$CATEGORY ['meta_data'])) ? unserialize(self::$CATEGORY ['meta_data']) : self::$CATEGORY ['meta_data'];
            $meta_data ['meta_title'] = self::$CATEGORY ['title'];
            $this->_setMetaTag($meta_data);
        }
        if (GALLERY_MODE === 1) {
            $count = $alb->count($id);
            $count = (int) $count ['count'];
        } elseif (GALLERY_MODE === 2)
            $count = 1;

        try {
            if ($count) {
                switch (self::$SHOWMODE) {
                    case 'albom' :
                        model_gallery::setRegistry('view_action', 'show_albom');
                        $result = $alb->getPageCategory($id, $this->_config ['catPage']);
                        $result ['count'] = $count;
                        try {
                            //check permission category
                            if (false === model_gallery::getClass('model_category')->getAccessCat($id)) {
                                throw new controller_exception($this->_lang ['exception'] ['access_denied']);
                            }
                            if (is_array($result ['alb'])) {
                                $content .= model_gallery::getClass('view_cover')->renderAlbom($result);
                            } else {
                                throw new controller_exception($this->_lang ['exception'] ['empty_cat']);
                            }

                            if ($count > $this->_config ['catPage']) {
                                $this->_tpl->set('{pagination}', $model->_nav((int) $count, array(
                                            'global_query_end' => $this->_config ['catPage'],
                                            'nav_prefix' => 'gallery/show/' . self::$CATEGORY ['id'] . '-' . self::$CATEGORY ['meta_title']), //'nav_suffix' => '.html'
                                                clone $this->_tpl));
                            } else {
                                $this->_tpl->set('{pagination}', '');
                            }
                        } catch (controller_exception $exc) {
                            $this->_tpl->set('{pagination}', '');
                            $content = $exc->set404();
                        }


                        $this->_setSpeedbar(array('cat' => stripslashes(self::$CATEGORY ['title'])));
                        break;
                    case 'files':
                        model_gallery::setRegistry('view_action', 'show_file');
                        //check permission category
                        if (false === model_gallery::getClass('model_category')->getAccessCat($id)) {
                            throw new controller_exception($this->_lang ['exception'] ['access_denied']);
                        }
                        if (GALLERY_MODE == 1) {//albom
                            $result = model_gallery::getClass('model_file')->getPage($this->_config ['catPage']);
                        } elseif (GALLERY_MODE == 2) {//only file
                            $result = model_gallery::getClass('model_file')->getPage($this->_config ['catPage'], array(
                                'parent_id' => "parent_id='" . self::$CATEGORY ['id'] . "'"
                                    ));
                        }

                        if (null === $result ['file']) {
                            throw new controller_exception('404');
                        }

                        $content = model_gallery::getClass('view_cover')->renderFile($result);

                        $this->_setSpeedbar(array('cat' => stripslashes(self::$CATEGORY ['title'])));
                        $counter = (int) $result ['count'];
                        if ($counter > $this->_config ['indexPage']) {
                            $this->_tpl->set('{pagination}', $model->_nav((int) $counter, array(
                                        'global_query_end' => $this->_config ['indexPage'],
                                        'nav_prefix' => 'gallery/show/' . self::$CATEGORY ['id'] . '-' . self::$CATEGORY ['meta_title']
                                            ), clone $this->_tpl));
                        } else {
                            $this->_tpl->set('{pagination}', '');
                        }
                        break;
                    default :
                        $this->_setSpeedbar(array('cat' => 'error'));
                        throw new controller_exception('system error, not a view');
                        break;
                }
            } else {
                throw new controller_exception($this->_lang ['exception'] ['empty_cat']);
            }
        } catch (controller_exception $exc) {
            $this->_setSpeedbar(array('cat' => stripslashes(self::$CATEGORY ['title'])));
            $this->_tpl->set('{pagination}', '');
            $content = $exc->set404();
        }

        $this->_tpl->set('{content}', $content);
        $this->_setJsMin();
        $this->_tpl->set('{json}', '');
        return $this->_view->compile('main');
    }

    /**
     * Просмотр альбома, результат зависит от статичной переменной model_albom::$MODE определяющей текущий режим вывода по умолчанию.
     * Дополнительные методы будут вызываться в зависимости от наличия в шаблонах тегов {json},[file-list] и т.д.
     * @return mixed|string
     */
    public function albomAction() {
        $this->_setOrder(HOME_URL . model_gallery::getRegistry('route')->getServerURI() . '/');
        model_gallery::setRegistry('view_action', 'albom');
        $content = '';
        $result = model_gallery::getClass('model_albom')->openAlbomSite();
        if (false == model_request::isAjax() && 'addcomment' == model_request::getPost('action')) {
            $content = model_gallery::getClass('model_comments')->add();
        }
        #if ($_POST['action'] === 'mass_delete')
        model_gallery::getClass('model_comments')->massAction(); //mass action comments

        try {
            $_albomInfo = model_gallery::getRegistry('model_albom')->getInfo();

            if (false === model_gallery::getRegistry('model_category')->getAccessCat($_albomInfo ['parent_id'])) { //check access category
                $this->_setSpeedbar(array('err' => $this->_lang ['exception'] ['access_denied']));
                throw new controller_exception($this->_lang ['exception'] ['access_denied']);
            }

            if (false === model_gallery::getRegistry('model_albom')->getAccessAlbon()) { //check access  albom
                $this->_setSpeedbar(array('err' => $this->_lang ['exception'] ['access_denied_alb']));
                throw new controller_exception($this->_lang ['exception'] ['access_denied_alb']);
            }
            if (null === $result) {
                $this->_tpl->set('{pagination}', '');
                throw new controller_exception('ошибка, скрипт вернул пустой набор параметров.');
                $this->_setSpeedbar(array('err' => 'error'));
            } else {
                switch (model_albom::$MODE) {
                    case 'preview' :
                        $content = model_gallery::getClass('view_show')->renderPreview($result);
                        $this->_tpl->set('{pagination}', '');
                        break;
                    case 'tile' :
                        $content = $this->_showTile($result);
                        break;
                    case 'image' :
                        $content = model_gallery::getClass('view_show')->renderFull($result);
                        break;
                    default :
                        break;
                }
                $this->_setSpeedbar(array(
                    'cat' => '<a href="' . HOME_URL . 'gallery/show/' . self::$CATEGORY ['id'] . '-' . stripslashes(self::$CATEGORY ['meta_title']) . '">' . stripslashes(self::$CATEGORY ['title']) . '</a>',
                    'alb' => $result ['info'] ['title']
                ));
//                if (null === model_request::getRequest('id_file')) {
//                    $this->_setMetaTag(array('meta_title' => $_albomInfo ['meta_data'] ['meta_title'], 'meta_descr' => $_albomInfo ['meta_data'] ['meta_descr'], 'meta_keywords' => $_albomInfo ['meta_data'] ['meta_keywords']));
//                }
            }
        } catch (controller_exception $exc) {
            $this->_tpl->set('{pagination}', '');
            $content = $exc->set404();
        }
        $this->_tpl->set('{content}', $content);
        $this->_setJsMin();
        return $this->_view->compile('main');
    }

    /**
     * Просмотр превью, слаидера, информации о авторе, комментариеи.
     * @return string
     */
    public function fullAction() {
        $content = '';
        model_gallery::setRegistry('view_action', 'full');
        $result = model_gallery::getClass('model_albom')->openAlbomSite();
        $this->_tpl->set('{pagination}', '');
        try {
            if (null === $result) {
                throw new controller_exception('empty result');
            }
            $content .= model_gallery::getClass('view_show')->renderFull($result);

            $this->_setSpeedbar(array('cat' => self::$CATEGORY['title']));
        } catch (controller_exception $exc) {
            $this->_tpl->set('{pagination}', '');
            $content = $exc->set404();
        }
        $this->_tpl->set('{content}', $content);
        $this->_setJsMin();
        return $this->_view->compile('main');
    }

    /**
     *
     * @param string $uri
     * @return void
     */
    protected function _setOrder($uri) {
        $param = model_gallery::getRegistry('route')->getServerURI();
        $order = end(explode('/', $param));
        $allow = array(
            'ORDER_COMMENTS' => 'ORDER_COMMENTS',
            'ORDER_DATE' => 'ORDER_DATE',
            'ORDER_RATING' => 'ORDER_RATING',
            'ORDER_DOWNLOAD' => 'ORDER_DOWNLOAD',
            'ORDER_VIEW' => 'ORDER_VIEW'
        );
        $_search = array('ORDER_COMMENTS/', 'ORDER_DATE/', 'ORDER_RATING/', 'ORDER_DOWNLOAD/');
        $uri = str_replace($_search, '', $uri);
        if (!in_array($order, $allow)) {
            $order = 'ORDER_DATE';
        }
        unset($allow[$order]);
        model_search::$DEF_ORDER = $order;
        $this->_tpl->set('{order_url}', $uri);
        $this->_tpl->set('{' . model_search::$DEF_ORDER . '}', ' active ');
        foreach ($allow as $_order) {
            $this->_tpl->set('{' . $_order . '}', '');
        }
    }

    /**
     *
     * @return type
     */
    public function searchAction() {
        model_gallery::setRegistry('view_action', 'search');
        $param = model_gallery::getRegistry('route')->getParam();
        $this->_setOrder(HOME_URL . model_gallery::getRegistry('route')->getServerURI() . '/');
        $content = '';
        $mId = null;
        $speedbar_title = '';
        $content = '';
        $result = null;
        if (count($param)) {
            $param ['where'] = model_request::getPost('_where');
            switch ($param [0]) {
                case 'keyword' :
                case 'color' :
                case 'letter' :
                    $query = model_search::strip_data(trim(strip_tags(urldecode($param [1]))));
                    if (model_request::getPost('keyword')) {
                        $query = model_search::strip_data(trim(strip_tags(model_request::getPost('keyword'))));
                    }
                    $count = null;
                    if ($param [0] === 'letter') {
                        $where = "symbol='{$query}'";
                        $_query = $query;
                        if ($query == '\#') {
                            $_query = '0-9';
                            $where = 'symbol  REGEXP  \'[0-9]\' ';
                        }
                        $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE $where");
                        $speedbar_title = $this->_lang ['search_letter'] . '<b>' . $_query . '</b>';
                    } elseif ($param [0] === 'color') {
                        $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE other_dat REGEXP  '[[:<:]]{$query}[[:>:]]' AND status !='cat_cover'");
                        $speedbar_title = $this->_lang ['search_color'] . '<span style="background-color:#' . $query . '"class="search-color"></span>';
                    } elseif ($param [0] == 'keyword') {
                        $speedbar_title = $this->_lang ['search_tag'] . '<b>' . $query . '</b>';
                        $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_tags WHERE tag='{$query}'");
                    }
                    try {
                        if ($count ['count']) {
                            $mId = model_gallery::getClass('model_search')->get(array(
                                'search' => $query,
                                'where' => $param [0]
                                    ), $this->_config ['searchPage']);
                            if ($mId) {
                                $this->_db = model_gallery::getRegistry('module_db');
                                while ($row = $this->_db->get_row($mId)) {
                                    if (is_string($row['other_dat']))
                                        $row['other_dat'] = unserialize($row['other_dat']);
                                    $result ['file'] [$row ['id']] = $row;
                                }

                                if (is_array($result ['file'])) {
                                    if ($param [0] == 'letter') {
                                        $access_data = null;
                                        $key = null;
                                        foreach ($result ['file'] as $key => $val) {
                                            $result ['file'] [$key] ['access_albom'] = 0;
                                            $result ['file'] [$key] ['access_cat'] = 1;
                                            $access_data = unserialize($val ['access_data']);
                                            $access_data ['access'] = explode(',', $access_data ['accessView']);
                                            if (in_array(model_gallery::$user ['user_group'], $access_data ['access'])) { //check permission album
                                                $result ['file'] [$key] ['access_albom'] = 1;
                                            }
                                        }
                                        $content = model_gallery::getClass('view_cover')->renderAlbom($result);
                                    } else {
                                        $content = model_gallery::getClass('view_cover')->renderFile($result);
                                    }
                                } else {
                                    throw new controller_exception('empty result');
                                }
                            }
                        } else {
                            throw new controller_exception('empty result');
                        }
                        if ($count ['count'] > $this->_config ['searchPage']) {
                            $query = str_replace("\\", '', $query);
                            $this->_tpl->set('{pagination}', model_gallery::getClass('model_gallery')->_nav((int)
                                            $count ['count'], array(
                                        'global_query_end' => $this->_config ['searchPage'],
                                        'nav_prefix' => 'gallery/search/' . $param [0] . '/' . urlencode($query)), ////'nav_suffix' => '.html'
                                            clone $this->_tpl));
                        } else {
                            $this->_tpl->set('{pagination}', '');
                        }
                    } catch (controller_exception $exc) {
                        $this->_tpl->set('{pagination}', '');
                        $content = $exc->set404();
                    }
                    break;
                default :
                    break;
            }
        }

        $this->_setSpeedbar(array('cat' => $speedbar_title));
        $this->_tpl->set('{content}', $content);

        $this->_setJsMin();

        return $this->_view->compile('main');
    }

    /**
     * Вспомогательный метод.
     * Вывод списка файлов.
     *
     * @param array $result
     * @return string
     * @deprecated
     */
    private function _showFile($result) {
        return 'depricated';
    }

    /**
     *
     * @param array $result
     * @param dle_template $tpl
     * @param view_template $view
     * @return string
     * @deprecated
     */
    private function _setAlbom($result, dle_template $tpl, view_template $view) {
        return 'depricated';
    }

    /**
     * Просмотр альбома, слайдер, инфо, комм. и т.д.
     * @param string $tplName
     * @param array $result
     * @return string
     * @deprecated
     */
    private function _showPreview(array $result, $tplName = 'show_preview.tpl') {
        return 'depricated';
    }

    /**
     * Просмотр альбома, пердпроасмотр, пагинация, запрошенное изображение первое.
     *
     * Ссылка ведет на fullAction()
     * @param array $result
     * @return string
     */
    private function _showTile(array $result) {
        $content = '';
        $view = model_gallery::getClass('view_template');
        $view->setView('tile.tpl');
        $tpl = $view->getView();
        $file = null;
        #$_current_file_id = model_request::getRequest('id_file');
        try {
            #$_albomInfo = model_gallery::getRegistry('model_albom')->getInfo();
            $_albomInfo = $result ['info'];
            if (false === is_array($result ['file'])) {
                throw new controller_exception('error. 404.');
            }
            foreach ($result ['file'] as $file) {
                $tpl->set('{meta_name}', $file ["title"]);
                $tpl->set('{meta_keyword}', ($file ["other_dat"] ["tag"]) ? stripcslashes($file ["other_dat"] ["tag"]) : '' );
                $tpl->set('{cover}', model_file::getThumb($file ['path']));
                #$tpl->set('{css_active}', ($file['id'] == $_current_file_id) ? ' class="active"' : '');
                $tpl->set('[link]', '<a href="' . HOME_URL . 'gallery/full/' . $_albomInfo ['id'] . '-' . $_albomInfo ['meta_data'] ['meta_title'] . '.' . $file ['id'] . '">');
                $tpl->set('[/link]', '</a>');
                $content = $view->compile('tile');
            }
            $this->_tpl->set('{pagination}', model_gallery::getClass('model_gallery')->_nav((int) $result ['count'], array('global_query_end' => $this->_config ['albomPage'], 'nav_prefix' => 'gallery/albom/' . $_albomInfo ['id'] . '-' . $_albomInfo ['meta_data'] ['meta_title']), //'nav_suffix' => '.html'
                            clone $this->_tpl));
        } catch (controller_exception $exc) {
            $content = $exc->set404();
        }
        return $content;
    }

    /**
     * Метод возвращает адрес при использование сжатия скриптов
     * @return sting
     */
    protected function _setJsMin($name = 'jssite') {
        if (!$this->_config ['minJs'] || $this->_config ['minJs'] == '') {
            return false;
        }
        if (stripos($this->_tpl->copy_template, '{js_min}') !== false) {
            require ROOT_DIR . '/DGGallery/min/utils.php';
            $this->_tpl->set('{js_min}', HOME_URL . 'DGGallery' . Minify_groupUri($name));
        }
    }

    /**
     * Получение классов DLE, метод не востребован но несколько раз применяется.
     * @param string $name
     * @param string $className
     * @return className obj
     * @deprecated
     */
    protected function _getDleClass($name, $className) {
        $class = null;
        if ($name == 'parse') {
            $className = 'ParseFilter';
            require_once ROOT_DIR . '/engine/classes/parse.class.php';
            $class = new $className(array(), array(), 1, 1);
        }
        model_gallery::setRegistry($name, $class);
        return $class;
    }

    /**
     * Установка значений глобальной переменной используемой для вывода заголовка, описания, ключевых слов.
     * @global array $metatags
     * @param array $meta
     * @return void
     */
    protected function _setMetaTag($meta) {
        global $metatags;
        $metatags ['title'] = $meta ['meta_title'];
        $metatags ['description'] = $meta ['meta_descr'];
        $metatags ['keywords'] = $meta ['meta_keywords'];
    }

    /**
     * Вывод контекстной навигации, упрощенная реализация.
     *
     * @param array $data
     * @return string
     */
    protected function _setSpeedbar(array $data) {
        global $_s_navigation;
        if (stripos($this->_tpl->copy_template, '{speedbar}') === false) {
            return;
        }
        $home = ($this->_config ['title_speedbar']) ? stripslashes($this->_config ['title_speedbar']) : 'Home';
        if (count($data)) {
            $home = '<a href="' . HOME_URL . 'gallery/">' . $home . '</a> &raquo; ';
        }
        $_s_navigation = $home . implode(' &raquo; ', $data);
        $this->_tpl->set('{speedbar}', $_s_navigation);
    }

}