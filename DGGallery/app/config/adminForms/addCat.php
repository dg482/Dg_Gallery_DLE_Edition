<?php
return array(
    'form' => array(
        'action' => '',
        'method' => 'post',
        'name' => 'newcat',
        'addcat' => array(
            'legend' => '���������� ����� ���������',
            'row' => array(
                array(
                    'label' => '���:<br /><small></small>',
                    'type' => 'text',
                    'key' => 'title'
                ),
                array(
                    'label' => '������������ ���������:<br /><small></small>',
                    'type' => 'select',
                    'key' => 'parent_id',
                    'values' => array(
                        'data' => 'gallery_cat',
                        'key' => 'id',
                        'label' => 'title',
                        'check'=>'parent_id'
                    )
                ),
                array(
                    'label' => '�������������� ���:<br /><small>������� title</small>',
                    'type' => 'text',
                    'key' => 'meta_title'
                ),
                array(
                    'label' => '������� ���������:<br /><small></small>',
                    'type' => 'uploadify'
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
                array(
                    'label' => '�������� ���������:<br /><small>��������� �������.</small>',
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
                    'label' => '�������� ��������:<br /><small>��������� �������.</small>',
                    'type' => 'multiple',
                    'key' => 'access',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
//                array(
//                    'label' => '�������� �����:<br /><small>��������� �������.</small>',
//                    'type' => 'checkbox',
//                    'key' => 'guestMode'
//                ),
            )
        ),
    )
);