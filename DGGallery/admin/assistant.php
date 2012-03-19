<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

class assistant
{

    /**
     * @var array
     * */
    protected $_config;
    /**
     * @var array
     * */
    public static $_registry;
    /**
     * @var array
     * */
    protected $_lang;
    /**
     * @var int
     * */
    protected $_config_cms;
    /**
     * @var array
     * */
    public static $_user;

    /**
     *
     */
    public function __construct()
    {
        define('DEMO', false);
        global $member_id;
        if (1 < $member_id ['user_group']) {
            die('access denied ');
        }
        self::$_user = $member_id;
        if (!self::$_user) {
            die('ACCESS DENIED!');
        }
        define('HOME_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/');
        $cache = model_gallery::getClass('model_cache_file');
        model_gallery::setRegistry('model_cache_file', $cache);
        //set global variable
        $this->setGlobalVar();
        self::$_registry = model_gallery::getAllRegistry();
        $config = new model_config ();
        self::setRegistry('model_config', $config);
        if (!file_exists(ROOT_DIR . '/DGGallery/app/config/config_gallery.php')) {
            die("error, file configuration don't exists");
        }
        $this->_config = include ROOT_DIR . '/DGGallery/app/config/config_gallery.php';
        self::setRegistry('config', $this->_config);
        // 1 - категории => альбомы => файлы
        // 2 - категории => файлы
        define('GALLERY_MODE', ($this->_config['mode']) ? (int)$this->_config['mode'] : 1);
        $admin = '/admin/index.php?action=cat';
        if ($this->_config ['dle']) {
            if (file_exists(ROOT_DIR . '/engine/data/config.php')) {
                #unset($config);
                $config = array();
                include ROOT_DIR . '/engine/data/config.php';
                $this->_config_cms = $config;
            }
            $admin = $this->_config_cms ['http_home_url'] . $this->_config_cms ['admin_path'] . '?mod=dg_gallery';
        }

        $this->_lang = (include ROOT_DIR . '/DGGallery/app/lang/gallery.php');
        self::setRegistry('lang', $this->_lang);
        model_gallery::setRegistry('admin_path', $admin);
        #model_gallery::setRegistry('route', model_gallery::getClass('model_route'));
    }

    /**
     *
     */
    public function setGlobalVar()
    {
        global $sort_list, $gallery_cat;
        $sort_list = array(
            array('id' => '1', 'label' => 'по дате'),
            array('id' => '2', 'label' => 'по рейтингу'),
            array('id' => '3', 'label' => 'по скачиваниям'),
            array('id' => '4', 'label' => 'по комментариям'),
            array('id' => '5', 'label' => 'только изображения'),
            array('id' => '6', 'label' => 'только видео'),
            array('id' => '7', 'label' => 'только you tube')
        );

        $gallery_cat = model_cache_file::get('tree_category');
        $gallery_cat [0] ['title'] = 'без категории';
        $gallery_cat [0] ['id'] = '0';
    }

    /**
     * @static
     * @param $n
     * @param $obj
     */
    public static function setRegistry($n, $obj)
    {
        model_gallery::setRegistry($n, $obj);
    }

    /**
     * @static
     * @param $n
     * @return mixed
     */
    public static function getRegistry($n)
    {
        return model_gallery::getRegistry($n);
    }

    /**
     * @static
     * @return mixed
     */
    public static function getAllRegistry()
    {
        return model_gallery::getAllRegistry();
    }

    /**
     * @return string
     */
    public function run()
    {
        $result = $this->setHeader();
        $result .= $this->setFooter();
        return $result;
    }

    /**
     * @return string
     */
    private function setHeaderCss()
    {
        self::$_registry ['config'] ['adminskin'] = 'default';
        $css = array('style', 'block', 'codemirror/codemirror', 'smoothness/jquery-ui-1.8.9.custom');
        $link = '';
        foreach ($css as $f) {
            $link .= '<link href="' . self::$_registry ['config'] ['http'] . 'DGGallery/admin/theme/default/css/' . $f . '.css" rel="stylesheet" type="text/css" />';
        }
        return $link;
    }

    /**
     * @return string
     */
    private function getHeaderJs()
    {
        self::$_registry ['config'] ['adminskin'] = 'default';
        $js = array( /* 'gallery', 'gallery.ass', 'gallery.effect', 'gallery.label', 'gallery.editor',
              'gallery.ajax', 'gallery.upload','gallery.player', */
            'tiny_mce/tiny_mce_gzip', /* 'gallery.ready', */);
        //'tiny_mce/jquery.tinymce',
        $link = '';
        foreach ($js as $f) {
            #  '$href{$jsUri}'
            $link .= '<script type="text/javascript" src="' . self::$_registry ['config'] ['http'] . 'DGGallery/admin/theme/default/js/gallery/' . $f . ".js\" ></script>\r";
        }
        return $link;
    }

    /**
     * @return string
     */
    protected function setHeader()
    {

        $action = strtolower(model_request::getRequest('action'));
        $action = ('' === $action) ? 'index' : $action;
        $inlineJs = '';
        $styleBlock = '';
        $content = '';
        $catJson = '';
        $css = $this->setHeaderCss();
        if (file_exists(ROOT_DIR . '/DGGallery/min/utils.php'))
            require ROOT_DIR . '/DGGallery/min/utils.php';
        else
            die('file no existt ' . ROOT_DIR . '/DGGallery/min/utils.php');
        $jsUri = Minify_groupUri('js');
        # $cssUri = Minify_groupUri('css');
        $href = self::$_registry ['config'] ['http'] . 'DGGallery';
        $config = self::$_registry ['config_cms'];
        $js = $this->getHeaderJs();
        $nav = $this->setNav();

        $cat = model_gallery::getClass('model_category');
        model_gallery::setRegistry('model_category', $cat);
        $catJson = $cat->getCategoryNavJson();

        $admin = model_gallery::getRegistry('admin_path');
        $sideBar = $this->_sideBar();
        $admCtrl = new controller_admin;

        $actionCtrl = str_replace('-', '_', $action) . 'Action';
        $mvcOut = array(
            'content' => null,
            'inlineJs' => null,
            'cssBlock' => null
        );
        if (method_exists($admCtrl, $actionCtrl)) {
            $mvcOut = $admCtrl->$actionCtrl();
        }
        $content .= $mvcOut['content'];
        $inlineJs .= $mvcOut['inlineJs'];
        $styleBlock = $mvcOut['cssBlock'];

        global $config;
        $contextMenuCat = module_json::getJson($this->_lang ['contextMenuCat']);
        $lang = module_json::getJson($this->_lang ['javaScript']);
        if (null === $this->_db) {
            $this->_db = model_gallery::getRegistry('module_db');
        }
        $comm = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments ");
        $file = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file ");
        $title = $this->_lang['title']['title_32'];
        if (false === DEMO) {
            switch (GALLERY_MODE) {
                case 1:
                    $title = $this->_lang['title']['title_30'];
                    break;
                case 2:
                    $title = $this->_lang['title']['title_31'];
                    break;
                default:
                    break;
            }
        } else {
            $file['count'] = $file['count'] . ' / 50';
        }

        return <<< HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>D.G. Gallery 1.5</title>
{$css}
<!-- firebug lite
<script type="text/javascript" src="https://getfirebug.com/firebug-lite.js"></script>
-->
<script type="text/javascript" src="{$config['http_home_url']}engine/classes/js/bbcodes.js"></script>
<script type="text/javascript" src="{$href}{$jsUri}"></script>
{$js}
<script type="text/javascript">
//<![CDATA[
var dle_root = '{$config['http_home_url']}';
var dle_admin = '{$config['admin_path']}';
var dle_copy_quote = '';
var nav = {$nav};\r
var catMenu = {$contextMenuCat};\r
var admin = '{$admin}';\r
gallery.lang = $lang;
$('document').ready(function(){
gallery.ass.setContextMenu(catMenu,$('span.item').children('a'));
gallery.ass.catShow($('span.item').children('a').not('.albom-open'));
tinyMCE_GZ.init({
            plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
            themes : 'advanced',
            skin : "cirkuit",
            disk_cache : true,
            languages:"ru",
            debug : false
});
{$inlineJs}
      $('.albom-open').live('click', function(){
            document.location.href = admin +'&action=open&id='+$(this).attr('rel');
        });
$('a.log').tipsy({
gravity: 'e'
})
$('a.dle').tipsy({
gravity: 'w'
})
});
//]]>
</script>
</head>
<body>
<div style="overflow:hidden; width:100%">
<div id="top">

<a class="dle" href="{$config['http_home_url']}{$config['admin_path']}?mod=options&action=options" title="{$this->_lang['title']['title_25']}"></a>
<a class="log" href="{$admin}" title="{$this->_lang['title']['title_29']}"></a><div class="statistic">
<p>{$this->_lang['title']['title_26']}<strong style="display:inline-block; float:right; padding:0 10px;">{$title}</strong></p>
<p>{$this->_lang['title']['title_27']}<strong style="display:inline-block; float:right; padding:0 10px;">{$file['count']}</strong></p>
<p>{$this->_lang['title']['title_28']}<strong style="display:inline-block; float:right; padding:0 10px;">{$comm['count']}</strong></p>
</div>
</div>
</div>
{$sideBar}
<div id="content">
<div class="block b-10 edit-images" id="user-work-area">
<div id="work-area-top-bar">
<ul class="nav"><li class=""></li></ul>
</div>
<div id="work-area-side-bar">
<ul class="nav"></ul>
</div>
<div id="work-area">
{$content}
</div>
</div>

HTML;
    }

    /**
     * @return string
     */
    private function _sideBar()
    {

        $action = strtolower(model_request::getRequest('action'));
        $action = ('' === $action) ? 'index' : $action;
        $index = array('index', 'info', 'setting', 'mass', 'save_setting', 'add_cat', 'add_category', 'moder');
        $editcat = array('editcat', 'edit-template', 'add_alb', 'open');
        $catTree = '';
        $css = array('index' => (in_array($action, $index)) ? 'active' : '', 'editcat' => (in_array($action, $editcat)) ? 'active' : '');
        $class = array('index' => (in_array($action, $index)) ? '' : 'class="hidden"', 'editcat' => (in_array($action, $editcat)) ? '' : 'class="hidden"');
        if ($action != 'edit-template' && $action != 'open') {
            $cat = self::getRegistry('model_category');
            //$catTree .= '<span class="search"><input type="text" name="search" value="" class="b-4"/><button onclick="">search</button></span>';
            $catTree .= $cat->catTree();
        }
        if ('open' == $action) {
            $alb = self::getRegistry('model_albom');
            if (null === $alb) {
                $alb = self::getClass('model_albom');
            }
            $catTree .= $alb->getTree();
        }

        return <<< HTML
         <div id="side-bar">
<ul class="sysnav">
<li class="{$css['index']} system"><a href="javascript:" rel="system">{$this->_lang['nav_block']['system']}</a></li>
<li class="{$css['editcat']} category"><a href="javascript:" rel="category">{$this->_lang['nav_block']['cat']}</a></li>
<!--<li class="tender"><a href="javascript:" rel="tender">{$this->_lang['nav_block']['tender']}</a></li>-->
</ul>
<div id="sysnav" {$class['index']}></div>
<div id="tree" {$class['editcat']}>
$catTree
</div>
</div>
HTML;
    }

    /**
     * @return mixed
     */
    public function runAjax()
    {
        $action = model_request::getRequest('action');
        $action = (null === $action) ? 'index' : $action;
        $admin = '/admin/index.php?action=cat';
        if ($this->_config_cms) {
            $admin = $this->_config_cms ['http_home_url'] . $this->_config_cms ['admin_path'] . '?mod=dg_gallery';
        }
        $admCtrl = new controller_ajax_admin;
        $actionCtrl = str_replace('-', '_', $action) . 'Action';
        if (method_exists($admCtrl, $actionCtrl)) {
            return $admCtrl->$actionCtrl();
        }
    }

    /**
     * @return string
     */
    protected function setFooter()
    {
        model_gallery::$user = self::$_user;
        $debug = ' '; #model_debug::show();
        return <<< HTML
$debug</div></body>
</html>
HTML;
    }

    /**
     * @return null|string
     */
    protected function setNav()
    {
        $http = model_gallery::getRegistry('admin_path');
        $nav = array('group' =>
        array('title' => $this->_lang ['menu'] ['group1'] ['title'],
            array('title' => $this->_lang ['menu'] ['group1'] ['info'], 'href' => $http . '&action=index'),
            array('title' => $this->_lang ['menu'] ['group1'] ['addCat'], 'href' => $http . '&action=add_cat'),
            array('title' => $this->_lang ['menu'] ['group1'] ['addAlbom'], 'href' => $http . '&action=add_alb'),
            //array('title' => $this->_lang ['menu'] ['group1'] ['addTender'], 'href' => $http . '&action=add_tender'),
            array('title' => $this->_lang ['menu'] ['group1'] ['setting'], 'href' => $http . '&action=setting')
        )
        );
        // //            'info' => array(
        //                'title' => $this->_lang['menu']['info']['title'],
        //                'imageSetting' => $this->_lang['menu']['info']['imageSetting'],
        //                'generalSetting' => $this->_lang['menu']['info']['generalSetting'],
        //                'newCat' => $this->_lang['menu']['info']['newCat'],
        //                'newAlbom' => $this->_lang['menu']['info']['newAlbom'],
        //                'newTender' => $this->_lang['menu']['info']['newTender'],
        //                'loadFile' => $this->_lang['menu']['info']['loadFile'],
        //                'loadArch' => $this->_lang['menu']['info']['loadArch']
        //            )


        $cache = model_gallery::getRegistry('model_cache_file');
        $navi = model_cache_file::getJson('admin_nav');
        if (!$navi) {
            $json = model_gallery::getClass('module_json');
            $navi = $json->getJson($nav);
            $cache->setCacheJson('admin_nav', $nav);
        }
        return $navi;
    }

}
