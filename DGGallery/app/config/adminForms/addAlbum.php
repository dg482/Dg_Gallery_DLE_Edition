<?php

return array(
    'form' => array(
        'action' => '',
        'method' => 'post',
        'name' => 'addalbum',
        'addalbum' => array(
            'legend' => '���������� ������ �������',
            'row' => array(
                array(
                    'label' => '��������:<br /><small></small>',
                    'type' => 'text',
                    'key' => 'title'
                ),
                array(
                    'label' => '���������:<br /><small></small>',
                    'type' => 'select',
                    'key' => 'parent_id',
                    'values' => array(
                        'data' => 'gallery_cat',
                        'key' => 'id',
                        'label' => 'title'
                    )
                ),
                array(
                    'label' => '�������������� ���:<br /><small>������� title</small>',
                    'type' => 'text',
                    'key' => 'meta_title'
                ),
                array(
                    'label' => '��������:<br /><small>������ ��������</small>',
                    'type' => 'textareawysiwyg',
                    'key' => 'descr'
                ),
                array(
                    'label' => '��������:<br /><small>������� description</small>',
                    'type' => 'textarea',
                    'key' => 'meta_descr'
                ),
                array(
                    'label' => '�������� �����:<br /><small>������� keywords</small>',
                    'type' => 'textarea',
                    'key' => 'meta_keywords'
                ),
            )
        ),
         'contentAccess' => array(
            'legend' => '��������� ������� � �������� �������.',
            'row' => array(
//                array(
//                    'label' => '�������� �����:',
//                    'type' => 'checkbox',
//                    'key' => 'guestMode'
//                ),
                 array(
                    'label' => '�������� ��������:<br /><small>��������� �������.</small>',
                    'type' => 'multiple',
                    'key' => 'accessView',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
                array(
                    'label' => '��������������� ��������:<br /><small>���������� ��������.</small>',
                    'type' => 'multiple',
                    'key' => 'accessComments',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
                array(
                    'label' => '��������������� ������:<br /><small>���������� ��������.</small>',
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