<?php

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