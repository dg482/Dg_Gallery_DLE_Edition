<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 * Форма добавления новой категории.
 * Form for adding new category.
 */

return array(
    'form' => array(
        'action' => '',
        'method' => 'post',
        'name' => 'newcat',
        'addcat' => array(
            'legend' => 'Добавление новой категории',
            'row' => array(
                array(
                    'label' => 'Имя:<br /><small></small>',
                    'type' => 'text',
                    'key' => 'title'
                ),
                array(
                    'label' => 'Родительская категория:<br /><small></small>',
                    'type' => 'select',
                    'key' => 'parent_id',
                    'values' => array(
                        'data' => 'gallery_cat',
                        'key' => 'id',
                        'label' => 'title',
                        'check' => 'parent_id'
                    )
                ),
                array(
                    'label' => 'Альтернативное имя:<br /><small>метатег title</small>',
                    'type' => 'text',
                    'key' => 'meta_title'
                ),
                array(
                    'label' => 'Обложка категории:<br /><small></small>',
                    'type' => 'uploadify'
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
                    'label' => 'Загрузка разрешена:<br /><small>локальное правило.</small>',
                    'type' => 'multiple',
                    'key' => 'accessupload',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name',
                        'setValue' => 'access_load'
                    )
                ),
                array(
                    'label' => 'Просмотр разрешен:<br /><small>локальное правило.</small>',
                    'type' => 'multiple',
                    'key' => 'access',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
//                array(
//                    'label' => 'Гостевой режим:<br /><small>локальное правило.</small>',
//                    'type' => 'checkbox',
//                    'key' => 'guestMode'
//                ),
            )
        ),
    )
);