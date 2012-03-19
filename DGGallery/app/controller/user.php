<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */


/**
 * Профиль пользователя в разделе галереи.
 *
 * При вызове производится проверка существования записи для пользователя в таблице учета,
 * если запись отсутствует она будет созданна, дальнейшая работа будет производится по идентификатору определенному при проверке.
 */
class controller_user extends controller_gallery {

    /**
     * Данные пользователя.
     * @var array
     */
    protected $_user;

    /**
     * Является ли пользователь автором просматриваемых альбомов.
     * @var bool
     */
    private $_isAuthor;

    /**
     * Является ли пользователь Администратором.
     * @var bool
     */
    private $_isAdmin;

    /**
     * Идентификатор пользователя в таблице статистики
     * @var int
     */
    protected $_userId;

    public function __construct() {
        parent::__construct();
        $this->_user = model_gallery::$user;
        $this->_isAdmin = ($this->_user['user_group'] == 1) ? true : false;
        if ($this->_user['user_id'])
            $this->check(); //add user db
    }

    /**
     *
     * @return type
     */
    public function exampleAction() {
        try {
            if (false === GALLERY) {
                throw new controller_exception('access denied');
            }
            $this->_tpl->set('{content}', model_gallery::getClass('view_examlpe')->render(array('parem1' => 'value1')));
            $this->_tpl->set('{js_min}', '');
            $this->_setSpeedbar(array(
                'home' => 'example action'
            ));
        } catch (controller_exception $exc) {
            $this->_tpl->set('{js_min}', '');
            $this->_setSpeedbar(array(
                'home' => 'access denied'
            ));
            $this->_tpl->set('{pagination}', '');
            $this->_tpl->set('{content}', $exc->set404());
        }
        return $this->_view->compile('main');
    }

    /**
     * @return string
     * @throws controller_exception
     */
    public function addalbomAction() {
        $content = '';
        $allowCat = model_gallery::getClass('model_category')->getAccessGranted();
        $_id = 0;
        try {
            $info = null;
            if (null === $this->_user['user_id']) {
                throw new controller_exception('access denied');
            }
            if (model_request::getPost('config') && (is_array($allowCat))) {// add albom
                $data = $_POST['config'];
                $data['parent_id'] = (int) $_POST['config']['category'];
                $_id = model_gallery::getClass('model_albom')->add($data, true);
                if (is_string($_id) && (0 === intval($_id))) {
                    $content = $_id;
                } elseif (intval($_id)) {
                    $alb = model_gallery::getRegistry('model_albom');
                    $alb->setId($_id);
                    $info = $alb->getInfo($_id);
                }
                if ($info['id']) {
                    $this->_setSpeedbar(array(
                        'home' => '<a href="' . HOME_URL . 'gallery/user/profile/">' . $this->_lang['title']['cp'] . '</a>',
                        'alb' => '<a href="' . HOME_URL . 'gallery/user/editalbom/' . $info['id'] . '">' . $info['title'] . '</a>'
                    ));

                    $content .= model_gallery::getRegistry('view_template')->
                        msgbox($this->_lang['info']['save_ok'], '<br /><br /><a href="' . HOME_URL . 'gallery/user/editalbom/' . $info['id'] . '">' . $this->_lang['info']['add_ok'] . "</a>");
                }
            } else {
                $this->_setSpeedbar(array(
                    'home' => '<a href="' . HOME_URL . 'gallery/user/profile/">' . $this->_lang['title']['cp'] . '</a>',
                    'alb' => 'error'
                ));
            }
            $this->_tpl->set('{pagination}', '');
            $this->_tpl->set('{content}', $content);
            $this->_tpl->set('{js_min}', '');
        } catch (controller_exception $exc) {
            $this->_tpl->set('{js_min}', '');
            $this->_setSpeedbar(array(
                'home' => 'access denied'
            ));
            $this->_tpl->set('{pagination}', '');
            $this->_tpl->set('{content}', $exc->set404());
        }
        return $this->_view->compile('main');
    }

    /**
     * Просмотр профиля.
     *
     * Вернуть user  .tpl для передачи в тег {content} шаблона gallery/main.tpl
     * @return string
     */
    public function profileAction() {
        try {
            if (null === $this->_user['user_id']) {
                throw new controller_exception('access denied');
            }
            model_gallery::setRegistry('view_action', 'profile');
            if (GALLERY_MODE === 1)
                $content = model_gallery::getClass('view_user')->render(1);
            elseif (GALLERY_MODE === 2)
                $content = model_gallery::getClass('view_user')->render(2);
            $this->_setJsMin('user');
            $this->_setSpeedbar(array(
                'home' => $this->_lang['title']['cp']
            ));
            if (stripos($content, '[list-albom]') !== false) {
                $content = preg_replace("#\\[list-albom\\](.*?)\\[/list-albom\\]#ies", "\$this->createAlbomLits('\$1')", $content);
            }
            $this->_tpl->set('{pagination}', '');
            $this->_tpl->set('{content}', $content);
        } catch (controller_exception $exc) {
            $this->_tpl->set('{js_min}', '');
            $this->_setSpeedbar(array(
                'home' => 'access denied'
            ));
            $this->_tpl->set('{pagination}', '');
            $this->_tpl->set('{content}', $exc->set404());
        }
        return $this->_view->compile('main');
    }

    /**
     * Редактирование альбома автором.
     *
     * @global null $gallery_cat
     * @return type
     */
    public function editalbomAction() {
        $param = model_gallery::getRegistry('route')->getParam();
        $content = '';
        model_gallery::setRegistry('view_action', 'edit_albom');

        $info = $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE id='{$param[0]}' LIMIT 1");
        $this->_isAuthor = ($this->_user['name'] == $info['author']) ? true : false;
        try {
            if (null === $info) {
                throw new controller_exception('albom no exists');
            }
            //TODO: странное условие, тем более что альбомы выводятся только текущему пользователю
            // и визит в чужой пофиль не возможен
            if ($this->_isAdmin || $this->_isAuthor) {
                $content = model_gallery::getClass('view_editAlbom')->render($info);
                $this->_setJsMin('user');
                $this->_setSpeedbar(array(
                    'home' => '<a href="' . HOME_URL . 'gallery/user/profile/">' . $this->_lang['title']['cp'] . '</a>',
                    'alb' => $this->_lang['title']['title_24'] . ' ' . $info['title']
                ));
            } else {
                throw new controller_exception('access denied');
            }
        } catch (controller_exception $exc) {
            $this->_setSpeedbar(array(
                'home' => '<a href="' . HOME_URL . 'gallery/user/profile/">' . $this->_lang['title']['cp'] . '</a>'
            ));
            $this->_tpl->set('{pagination}', '');
            $content = $exc->set404();
        }
        $this->_tpl->set('{content}', $content);
        $this->_tpl->set('{pagination}', '');
        return $this->_view->compile('main');
    }

    /**
     * Проверка наличия записи пользователя в таблице статистики, определение ID.
     */
    public function check() {
        $check = $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX .
            "_dg_gallery_user WHERE user_id='{$this->_user['user_id']}'");
        if (null === $check['id']) {
            $this->_db->query('INSERT INTO ' . DBNAME . '.' . PREFIX . '_dg_gallery_user (user_id) VALUES ' . "('{$this->_user['user_id']}')");
            $check['id'] = $this->_db->insert_id();
        }
        $this->_userId = $check['id'];
    }

    /**
     * Вспомогательный метод обеспечивающий вывод списка альбомов пользователя при просмотре профиля.
     * Входной параметр строка шаблона user.tpl заключенная в теги [list-albom][/list-albom]
     *
     * Вернуть скомпилированную строку с заплнеными данными поо альбому.
     * @param string $str
     * @return string
     */
    public function createAlbomLits($str) {
        $tpl = clone $this->_tpl;
        $tpl->template = stripslashes($str);
        $tpl->copy_template = stripslashes($str);
        //TODO: просмотр альбомов?
        $userAlb = model_gallery::getClass('model_user')->getAlbom($this->_user['name']);
        try {
            if (!is_array($userAlb["alb"])) {
                throw new controller_exception($this->_lang['error']['err_17']);
            }
            foreach ($userAlb["alb"] as $val) {
                $this->_isAuthor = ($this->_user['name'] == $val['author']) ? true : false;
                $tpl->set('{title}', stripslashes($val ['title']));
                $tpl->set('{rating}', (int) $val['rating']);
                $tpl->set('{votes}', (int) $val['votes']);
                $tpl->set('{files}', (int) $val['images']);
                $tpl->set('[link]', '<a href="' . HOME_URL . 'gallery/albom/' . $val['id'] . '-' . $val['meta_data']['meta_title'] . '">');
                $tpl->set('[/link]', '</a>');
                if ($this->_isAdmin || $this->_isAuthor) {
                    $tpl->set('[edit-link]', '<a href="' . HOME_URL . 'gallery/user/editalbom/' . $val['id'] . '">');
                    $tpl->set('[/edit-link]', '</a>');
                } else {
                    $tpl->set_block("'\\[edit-link\\](.*?)\\[/edit-link\\]'si", '');
                }

                if ($val['meta_data']['cover'] != '') {
                    $cover = str_replace('%replace%/', 'thumbs/', $val['meta_data']['cover']);
                    $tpl->set('{cover}', ($cover != '' && file_exists(ROOT_DIR . $cover)) ? $cover :
                        model_gallery::getClass('model_albom')->getRandFile($val['id']) );
                } else {
                    $tpl->set('{cover}', model_gallery::getClass('model_albom')->getRandFile($val ['id']));
                }
                $tpl->compile('list');
            }
            return $tpl->result['list'];
        } catch (controller_exception $exc) {
            return $exc->setInfo();
        }
    }

    /**
     * @return string
     * @throws controller_exception
     */
    public function savealbomAction() {
        $param = model_gallery::getRegistry('route')->getParam();
        model_gallery::setRegistry('view_action', 'edit_albom');
        $info = $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE id='{$param[0]}' LIMIT 1");
        $content = '';
        try {
            $this->_isAuthor = ($this->_user['name'] == $info ['author']) ? true : false;
            if ($info && $this->_isAdmin || $this->_isAuthor) {
                if (model_request::getPost('config')) {// add albom
                    $data = $_POST['config'];
                    $data['id'] = $info['id'];
                    //$data['parent_id'] = (int) $_POST['config']['category'];
                    model_gallery::getClass('model_albom')->add($data, true);
                    global $lang;
                    $content .= model_gallery::getRegistry('view_template')->
                        msgbox($this->_lang['info']['save_ok'], "<br /><br /><a href=\"javascript:history.go(-1)\">" . $lang['all_prev'] . "</a>");

                    $this->_setJsMin('user');
                    $this->_setSpeedbar(array(
                        'home' => '<a href="' . HOME_URL . 'gallery/user/profile/">' . $this->_lang['title']['cp'] . '</a>',
                        'alb' => '<a href="' . HOME_URL . 'gallery/user/editalbom/' . $info['id'] . '">' . $info['title'] . '</a>',
                        'save' => $this->_lang['title']['save']
                    ));
                }
            } else {
                $this->_setSpeedbar(array(
                    'err' => ' access denied'
                ));
                throw new controller_exception('access denied');
            }
        } catch (controller_exception $exc) {
            $this->_tpl->set('{pagination}', '');
            $content .= $exc->set404();
        }

        $this->_tpl->set('{content}', $content);
        $this->_tpl->set('{pagination}', '');
        return $this->_view->compile('main');
    }

}

