<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 * Форма добавления редактирования альбома.
 * Form for edit the album.
 */

return array(
    'form' => array(
        'action' => '',
        'method' => 'post',
        'name' => 'addalbum',
        'editalbum' => array(
            'legend' => 'Редактирование альбома',
            'row' => array(
                array(
                    'label' => 'Название:<br /><small></small>',
                    'type' => 'text',
                    'key' => 'title'
                ),
                array(
                    'label' => 'Категория:<br /><small></small>',
                    'type' => 'select',
                    'key' => 'parent_id',
                    'values' => array(
                        'data' => 'gallery_cat',
                        'key' => 'id',
                        'label' => 'title',
                        'check' => 'gallery_cat'
                    )
                ),
                array(
                    'label' => 'Альтернативное имя:<br /><small>метатег title</small>',
                    'type' => 'text',
                    'key' => 'meta_title'
                ),
                array(
                    'label' => 'Описание:<br /><small>полное описание</small>',
                    'type' => 'textareawysiwyg',
                    'key' => 'descr'
                ),
                array(
                    'label' => 'Описание:<br /><small>метатег description</small>',
                    'type' => 'textarea',
                    'key' => 'meta_descr'
                ),
                array(
                    'label' => 'Ключевые слова:<br /><small>метатег keywords</small>',
                    'type' => 'textarea',
                    'key' => 'meta_keywords'
                ),
                array(
                    'type' => 'submit'
                )
            )
        ),
        'contentAccessAlbom' => array(
            'legend' => 'Настройки доступа.',
            'row' => array(
//                array(
//                    'label' => 'Гостевой режим:',
//                    'type' => 'checkbox',
//                    'key' => 'guestMode'
//                ),
                array(
                    'label' => 'Просмотр:',
                    'type' => 'multiple',
                    'key' => 'accessView',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
//                array(
//                    'label' => 'Комментирование:',
//                    'type' => 'multiple',
//                    'key' => 'accessComments',
//                    'values' => array(
//                        'data' => 'user_group',
//                        'key' => 'id',
//                        'label' => 'group_name'
//                    )
//                ),
                array(
                    'label' => 'Комментирование файлов:',
                    'type' => 'multiple',
                    'key' => 'accessCommentsFile',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
            )
        ),
    )
);