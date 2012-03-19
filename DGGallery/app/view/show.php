<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

/**
 * Представление show_preview.tpl, show_full.tpl
 * вывод файла.
 */
class view_show extends view_template {

    /**
     * @var string
     */
    protected $_dir;

    /**
     *
     */
    public function __construct() {
        parent::__construct();
        $this->_tpl = new dle_template();
        $this->_tpl->dir = $this->_dir;
    }

    /**
     * @param array $result
     * @return mixed|string
     */
    public function renderPreview(array $result) {
        $this->setView('show_preview.tpl');
        return $this->_render($result);
    }

    /**
     * @param array $result
     * @return mixed|string
     */
    public function renderFull(array $result) {
        $this->setView('show_full.tpl');
        return $this->_render($result);
    }

    /**
     * @param array $result
     * @return mixed|string
     * @throws controller_exception
     */
    protected function _render(array $result) {

        self::$CATEGORY = controller_gallery::$CATEGORY;
        $content = '';
        $_current_file_id = null;
        $file = null;
        $count = null;
        $_albomInfo = null;
        $_current_file_id = model_request::getRequest('id_file');
        //prev. next file
        $param = model_gallery::getRegistry('model_route')->getParam();
        $keyNext = array_search('next', $param);
        $keyPrev = array_search('prev', $param);
        if (GALLERY_MODE === 1) {
            $index = null;
            if ($keyNext || $keyPrev) {//pager < || > CURRENT-ID-FILE
                $index = model_gallery::getClass('model_file')->getFileCache($_current_file_id, $result ['file'], TRUE);
                if ($keyPrev) {
                    $index -= 1;
                }
                if ($keyNext) {
                    $index += 1;
                }
            }
            if (null == $_current_file_id) {
                $file = current($result ['file']);
            } else {
                if ($index) {
                    $file = $result ['file'][$index];
                    $_current_file_id = $file['id'];
                } else {
                    $file = model_gallery::getClass('model_file')->getFileCache($_current_file_id, $result ['file'], FALSE);
                }
            }
        } elseif (GALLERY_MODE === 2) {
            if ($keyNext || $keyPrev) {//pager
                $_id = (int) model_request::getRequest('id'); //category id
                if ($keyNext)//next file
                    $file = model_gallery::getClass('model_file')->getNextFile($_current_file_id, $_id);
                if ($keyPrev)//prev file
                    $file = model_gallery::getClass('model_file')->getPrevFile($_current_file_id, $_id);
            } else {
                // get file
                $file = model_gallery::getClass('model_file')->getFile($_current_file_id);
            }
            self::$CATEGORY = model_gallery::getRegistry('model_category')->getCatInfo($file['parent_id']);
            $this->_setSpeedbar(array(
                'cat' => '<a href="' . HOME_URL . 'gallery/show/' . self::$CATEGORY ['id'] . '-' . self::$CATEGORY ['meta_title'] . '">' . self::$CATEGORY ['title'] . '</a>'
            ));
            $this->_config['accessCommFile'] = explode(',', $this->_config['accessCommFile']);
        }
        $this->_tpl->set('{id-file}', $file['id']);
        $_albomInfo = $result ['info'];
        try {
            # $_albomInfo = model_gallery::getRegistry('model_albom')->getInfo();
            if (stripos($this->_tpl->copy_template, '[prev-file]') !== false) {
                $ids = null;
                if (GALLERY_MODE === 1) {
                    $ids = model_gallery::getClass('model_file')->getMinMaxId($file['parent_id']);
                    if ($file['id'] != $ids['min']) {
                        $this->_tpl->set('[prev-file]', '<a href="' . HOME_URL . 'gallery/albom/' . $result ['info'] ['id'] . '-' .
                            $result ['info'] ["meta_data"] ['meta_title'] . '.' . $file['id'] . '/prev/">');
                        $this->_tpl->set('[/prev-file]', '</a>');
                    } else {
                        $this->_tpl->set_block("#\\[prev-file\\](.*?)\\[/prev-file\\]#si", '');
                    }
                    if ($file['id'] != $ids['max']) {
                        $this->_tpl->set('[next-file]', '<a href="' . HOME_URL . 'gallery/albom/' . $result ['info'] ['id'] . '-' .
                            $result ['info'] ["meta_data"] ['meta_title'] . '.' . $file['id'] . '/next/">');
                        $this->_tpl->set('[/next-file]', '</a>');
                    } else {
                        $this->_tpl->set_block("#\\[next-file\\](.*?)\\[/next-file\\]#si", '');
                    }
                } elseif (GALLERY_MODE === 2) {
                    $ids = model_gallery::getClass('model_file')->getMinMaxId(self::$CATEGORY ['id']);
                    if ($file['id'] != $ids['min']) {
                        $this->_tpl->set('[prev-file]', '<a href="' . HOME_URL . 'gallery/full/' . self::$CATEGORY ['id'] . '-' .
                            self::$CATEGORY ['meta_title'] . '.' . $file['id'] . '/prev/">');
                        $this->_tpl->set('[/prev-file]', '</a>');
                    } else {
                        $this->_tpl->set_block("#\\[prev-file\\](.*?)\\[/prev-file\\]#si", '');
                    }

                    if ($file['id'] != $ids['max']) {
                        $this->_tpl->set('[next-file]', '<a href="' . HOME_URL . 'gallery/full/' . self::$CATEGORY ['id'] . '-'
                            . self::$CATEGORY ['meta_title'] . '.' . $file['id'] . '/next/">');
                        $this->_tpl->set('[/next-file]', '</a>');
                    } else {
                        $this->_tpl->set_block("#\\[next-file\\](.*?)\\[/next-file\\]#si", '');
                    }
                } else {
                    $this->_tpl->set_block("#\\[prev-file\\](.*?)\\[/prev-file\\]#si", '');
                    $this->_tpl->set_block("#\\[next-file\\](.*?)\\[/next-file\\]#si", '');
                }
            }

            if (GALLERY_MODE === 2) {
                controller_gallery::$CATEGORY = self::$CATEGORY;
                $_albomInfo['id'] = true;
            }

            //TODO: в режиме 2 массивы не пусты но и не содержат информации,
            //dump array(1) { ["other_dat"]=> bool(false) } array(2) { ["access_data"]=> bool(false) ["data"]=> bool(false) }
            // ? вернуть из модели null в режиме 2 или ..........
            if ($file['id'] && $_albomInfo['id']) {
                if ($this->_config['logViewFile']) {//count views file
                    model_gallery::getClass('model_file')->updateViewFile($file['id']);
                }
                if ($file ['title'] || $file ["descr"] || $file ["other_dat"] ['tag']) { //file metadata
                    $this->_setMetaTag(
                        array(
                            'meta_title' => $file ['title'],
                            'meta_descr' => substr(strip_tags($file ["descr"]), 0, 200),
                            'meta_keywords' => $file ["other_dat"] ['tag']
                        ));
                } else { // albom metadata
                    $this->_setMetaTag(
                        array(
                            'meta_title' => $_albomInfo ['title'], //htmlentities($_albomInfo ['title'], ENT_QUOTES, 'cp1251'),
                            'meta_descr' => $_albomInfo ['meta_data'] ['meta_descr'],
                            'meta_keywords' => $_albomInfo ['meta_data'] ['meta_keywords']
                        ));
                }

                $this->_tpl->set('{meta_keyword}', stripcslashes($file ["other_dat"] ['tag']));
                $_current_file_id = $file ['id'];
                $this->_tpl->set('{albom_name}', $_albomInfo ['title']);
                if (!empty($_albomInfo ['meta_data'] ['description'])) { //set albom description
                    $this->_tpl->set('{albom_description}', $_albomInfo ['meta_data'] ['description']);
                    $this->_tpl->set('[albom_description]', '');
                    $this->_tpl->set('[/albom_description]', '');
                } else {
                    $this->_tpl->set('{albom_description}', '');
                    $this->_tpl->set_block("#\\[albom_description\\](.*?)\\[/albom_description\\]#si", '');
                }
                //--------
                model_gallery::getClass('model_file')->setInfo($file, $this->_tpl); //set info file
                //-------
                if ($result ['info'] ['info_author'])
                    $this->_setInfoUser($result ['info'] ['info_author'], $this->_tpl); // set info author
                if (GALLERY_MODE === 2 && $file['user_id'])//KLUDGE: исходный массив с данными в режиме 2 пуст берем данные пользователя из $file, надо востановить его в модели, чтобы избежать подзапросов и доп проверок в контроллере, хотя .....
                    $this->_setInfoUser($file, $this->_tpl); // set info author
                $this->_tpl->set('{description_albom}', stripslashes($result ['info'] ['meta_data'] ['description']));
                $file ['comm_access'] = intval($file ['comm_access']);
                //FIX:29.08.11
                //комментарии выводились тольок для групп которым разрешено добавление
                $comm = model_gallery::getClass('model_comments'); //get comments model
                $count = $comm->count($_current_file_id, 'file');
                if ((($file ['comm_access'] || model_gallery::getRegistry('model_albom')->getAccessComments()) ||
                    ((false == model_gallery::getRegistry('model_albom')->getAccessComments()) && (1 === $file ['comm_access']))) ||
                    ((GALLERY_MODE === 2) && (in_array(model_gallery::$user['user_id'], $this->_config['accessCommFile'])))
                ) { //set add comments form
                    $this->_tpl->set('[addcommentfile]', '');
                    $this->_tpl->set('[/addcommentfile]', '');
                    $this->_tpl->set('{addcomments}', $comm->getAddForm($_current_file_id));
                } else {
                    $this->_tpl->set_block("#\\[addcommentfile\\](.*?)\\[/addcommentfile\\]#si", '');
                    $this->_tpl->set('{addcomments}', '');
                    //$this->_tpl->set('{comments}', '');
                    //$this->_tpl->set('{pagination}', '');
                }
                if ($count ['count'] > $this->_config ['commPage'] && !$this->_config['coments_tree']) { // set comm pager
                    $this->_tpl->set('{pagination}', model_gallery::getClass('model_gallery')->_nav((int) $count ['count'], array(
                            'global_query_end' => $this->_config ['commPage'],
                            'nav_prefix' => 'gallery/albom/' . $result ['info'] ['id'] . '-' . $result ['info'] ["meta_data"] ['meta_title'] . '.' . $_current_file_id),
                        //'nav_suffix' => '.html'
                        clone $this->_tpl));
                } else {
                    $this->_tpl->set('{pagination}', '');
                }
                if ($count['count']) {//load comments
                    $this->_tpl->set('{comments}', $comm->load(
                        array(
                            'id' => $_current_file_id,
                            'status' => 'file',
                            'where' => 'parent_id',
                            'start' => 0,
                            'end' => $this->_config ['commPage'],
                            'count' => $count
                        ), clone $this->_tpl));
                } else {
                    $this->_tpl->set('{comments}', '<div id="gallery-ajax-comments"></div>');
                }

                if ($file ['rating_access'] && $this->_config['ratingFile']) {
                    $this->_tpl->set('[rating]', '');
                    $this->_tpl->set('[/rating]', '');
                    //$tpl->set('{rating}', ceil($file['rating'] / $file['vote_num']));
                    $this->_tpl->set('{rating}', $file ['rating']);
                } else {
                    $this->_tpl->set('{rating}', '----');
                    $this->_tpl->set_block("#\\[rating\\](.*?)\\[/rating\\]#si", '');
                }
                if (model_gallery::getRegistry('model_albom')->isAuthor()) {
                    $this->_tpl->set('[edit-link]', '<a href="' . HOME_URL . 'gallery/user/editalbom/' . $_albomInfo ['id'] . '" >');
                    $this->_tpl->set('[/edit-link]', '</a>');
                    $this->_tpl->set('[is_author]', '');
                    $this->_tpl->set('[/is_author]', '');
                } else {
                    $this->_tpl->set_block("#\\[is_author\\](.*?)\\[/is_author\\]#si", '');
                    $this->_tpl->set_block("#\\[edit-link\\](.*?)\\[/edit-link\\]#si", '');
                }
                switch ($file ['status']) {
                    case 'albom' :
                    case 'catfile':
                        $this->_tpl->set_block("#\\[file-video\\](.*?)\\[/file-video\\]#si", '');
                        $this->_tpl->set('[file-image]', '');
                        $this->_tpl->set('[/file-image]', '');
                        break;
                    case 'video' :
                    case 'youtube' :
                    case 'vimeo'://ADD: добавлены vimeo, smotri.com, rutube, gametrailers
                    case 'smotri.com':
                    case 'rutube':
                    case 'gametrailers':
                        $this->_tpl->set_block("#\\[file-image\\](.*?)\\[/file-image\\]#si", '');
                        $this->_tpl->set('[file-video]', '');
                        $this->_tpl->set('[/file-video]', '');
                        break;
                    default :
                        break;
                }
                if (stripos($this->_tpl->copy_template, '{json}') !== false) { // javasctipt variable
                    $js = module_json::getJson(model_gallery::getClass('model_albom')->getFileList());
                    //  $js .= 'gallery.lang =' . module_json::getJson($this->_lang ['javaScript']) . ";\r";
                    $this->_tpl->set('{json}', $js);
                }
                if (stripos($this->_tpl->copy_template, '{related-files}') !== false) {//похожие файлы
                    $rnd = model_gallery::getClass('view_related');
                    $_result = model_gallery::getClass('model_file')->getRelatedFiles();
                    $this->_tpl->set('{related-files}', $rnd->render($_result));
                }
                $content = $this->compile('show');
            } else {
                throw new controller_exception($this->_lang ['exception'] ['no_file']);
            }
        } catch (controller_exception $exc) {
            $content = $exc->set404();
        }
        if (stripos($content, '[file-list]') !== false) { //slider static
            $content = preg_replace("#\\[file-list\\](.*?)\\[/file-list\\]#ies", "\$this->createFileLits('\$1')", $content);
        }

        return $content;
    }

    /**
     * Формирование списка файлов.
     * @param string $tpl
     * @return string
     */
    public function createFileLits($tpl) {
        return model_gallery::getClass('model_albom')->getFileListAlbum($tpl, clone $this->_tpl);
    }

}