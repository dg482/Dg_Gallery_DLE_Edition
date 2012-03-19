<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 * Форма выбора категории при загрузке файлов в режиме Архив (GALLERY_MODE = 2).
 *
 */

return array(
    'form' => array(
        'action' => '',
        'method' => 'post',
        'name' => 'newcat',
        'upload' => array(
            'legend' => 'Массовая загрузка материалом в категорию.',
            'row' => array(
                array(
                    'label' => 'Категория загрузки:<br /><small></small>',
                    'type' => 'select',
                    'key' => 'parent_id',
                    'values' => array(
                        'data' => 'gallery_cat',
                        'key' => 'id',
                        'label' => 'title',
                        'check' => 'parent_id'
                    )
                ),
            )
        ),
    )
);