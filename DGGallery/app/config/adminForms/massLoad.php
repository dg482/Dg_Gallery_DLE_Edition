<?php

return array(
    'form' => array(
        'action' => '',
        'method' => 'post',
        'name' => 'newcat',
        'upload' => array(
            'legend' => '�������� �������� ���������� � ���������.',
            'row' => array(
                array(
                    'label' => '��������� ��������:<br /><small></small>',
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