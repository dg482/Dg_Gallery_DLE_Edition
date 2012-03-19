<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */


class controller_exception extends Exception
{

    /**
     * @var string
     */
    private $_msg;

    /**
     * @param $message string
     */
    public function __construct($message)
    {
        $this->_msg = $message;
    }

    /**
     * @return string
     */
    public function set404()
    {
        global $tpl;
        $result = '
<style type="text/css">
h1.error { text-align: center; font-size: 10em; color: #A1A8AB; -moz-text-shadow: -1px -1px 1px rgba(0, 0, 0, 0.35), 1px 1px 1px rgba(0,0,0, 0.75); -webkit-text-shadow: -1px -1px 1px rgba(0, 0, 0, 0.35), 1px 1px 1px rgba(0,0,0, 0.75); text-shadow: -1px -1px 1px rgba(0, 0, 0, 0.35), 1px 1px 1px rgba(0,0,0, 0.75); font-family: Verdana, Arial, Helvetica, sans-serif; }
</style>
<h1 class="error">404</h1>
';
        if ($tpl instanceof dle_template) {
            $tpl->load_template('gallery/exception.tpl');
            $tpl->set('{message}', $this->_msg);
            $tpl->compile('error');
            $result = $tpl->result['error'];
        }
        header("HTTP/1.0 404 Not Found");


        return $result;
    }

    /**
     * @return mixed
     */
    public function setInfo()
    {
        global $tpl, $lang;
        if ($tpl instanceof dle_template) {

        }
        msgbox($lang['all_err_1'], $this->_msg);
        return $tpl->result['info'];
    }

}

