<?php

return array(
    'form' => array(
        'action' => '',
        'method' => 'post',
        'name' => 'addalbum',
        'addalbum' => array(
            'legend' => 'Добавление нового альбома',
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
                        'label' => 'title'
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
            )
        ),
         'contentAccess' => array(
            'legend' => 'Настройки доступа к функциям скрипта.',
            'row' => array(
//                array(
//                    'label' => 'Гостевой режим:',
//                    'type' => 'checkbox',
//                    'key' => 'guestMode'
//                ),
                 array(
                    'label' => 'Просмотр разрешен:<br /><small>локальное правило.</small>',
                    'type' => 'multiple',
                    'key' => 'accessView',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
                array(
                    'label' => 'Комментирование альбомов:<br /><small>глобальный параметр.</small>',
                    'type' => 'multiple',
                    'key' => 'accessComments',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
                array(
                    'label' => 'Комментирование файлов:<br /><small>глобальный параметр.</small>',
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