<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

class model_label
{

    /**
     * @var module_db
     */
    protected $_db;
    /**
     * @var mixed
     */
    private $_imgId;

    /**
     * @var
     */
    private $_data;

    /**
     * @var
     */
    protected $_parse;

    const CHECK_IMAGE = true;

    /**
     *
     */
    public function __construct()
    {
        $this->_imgId = model_request::getRequest('id');
        if (null == $this->_db) {
            $this->_db = model_gallery::getRegistry('module_db');
        }
        if (self::CHECK_IMAGE) {
            if (false === $this->_checkImage()) {
                die('error');
            }
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        model_debug::mysql($this->_db->query_num);
        model_debug::setQuery($this->_db->queryText);
    }

    /**
     * @return array
     */
    private function _getRequestParam()
    {
        $c = model_request::getRequest('coord');
        require_once ROOT_DIR . '/engine/classes/parse.class.php';
        $this->_parse = new ParseFilter(array(), array(), 1, 1);
        return array(
            'coords' => array(
                'x' => $c['x'],
                'y' => $c['y'],
                'x2' => $c['x2'],
                'y2' => $c['y2']
            ),
            'size' => array(
                'w' => $c['w'],
                'h' => $c['h']
            ),
            'text' => module_json::convertToCp($this->_parse->process(model_request::getRequest('newLabel')))
        );
    }

    /**
     * @return mixed
     */
    public function add()
    {
        $p = $this->_getRequestParam();
        $file = model_gallery::getClass('model_file');

        $this->_data = $file->getFile($this->_imgId);
        $id = $this->_data['parent_id'];
        $this->_data = unserialize($this->_data['other_dat']);
        //  $this->_data['label'] = array();
        $this->_data['label'][date('Hsi', time())] = $p;
        $this->_db->query('UPDATE ' . PREFIX . "_dg_gallery_file SET other_dat='" . serialize($this->_data) . "' WHERE id='{$this->_imgId}' LIMIT 1");
        $alb = model_gallery::getClass('model_albom');
        return $alb->updateAlbom($id);
    }

    /**
     * @return mixed
     */
    public function delete()
    {
        $lId = model_request::getPost('idL');

        $file = model_gallery::getClass('model_file');
        $info = $file->getFile($this->_imgId);

        $this->_data = $info;
        $id = $this->_data['parent_id'];
        $l = unserialize($info['other_dat']);
        unset($l['label'][$lId]);
        $info['other_dat'] = $l;
        $file->updateFile($this->_imgId, array(
            'other_dat' => serialize($info['other_dat'])
        ));
        $alb = model_gallery::getClass('model_albom');
        return $alb->updateAlbom($id);
    }

    /**
     * @return bool
     */
    private function _checkImage()
    {
        return ($this->_data = $this->_db->super_query('SELECT * FROM ' . PREFIX . "_dg_gallery_file WHERE id='{$this->_imgId}' LIMIT 1")) ? true : false;
    }

    /**
     * @return mixed
     */
    private function _getImage()
    {
        if (!$this->_data) {
            $this->_data = $this->_db->super_query('SELECT * FROM ' . PREFIX . "_dg_gallery_file WHERE id='{$this->_imgId}' LIMIT 1");
        }
        return $this->_data;
    }

}