<?php

/**
 * Класс: mail
 *
 * @author Dark Ghost
 * @copyright 2011
 * @package
 */
class model_mail {

    public function __construct() {

    }

    public static function send($title, $msg) {
        global $config;
        if (null === $config) {
            include ROOT_DIR . '/engine/data/config.php';
        }
        include_once ENGINE_DIR . '/classes/mail.class.php';
        $mail = new dle_mail($config, true);
        $body = file_get_contents(ROOT_DIR . '/DGGallery/cache/system/page/email.tpl');
        $body = str_replace(array('{title}', '{message}'), array($title, $msg), $body);
        $mail->send($config['admin_mail'], $title, $body);
    }

}

