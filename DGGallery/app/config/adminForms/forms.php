<?php

//$options = array();
//if (extension_loaded('gd')) {
//    $gd = gd_info();
//    $options[0] = 'GD ' . $gd["GD Version"];
//}
//
//if (extension_loaded('imagick')) {
//    //$i = imagick::getVersion();
//    $options[1] = 'Imagick'; //substr($i["versionString"], 0, 31);
//}
return array(
    'form' => array(
        'action' => '?&action=save_setting',
        'method' => 'post',
        'name' => 'config',
        'setting' => array(
            'legend' => '�������� ��������� �������',
            'row' => array(
                array(
                    'label' => '��������� �������:',
                    'type' => 'checkbox',
                    'key' => 'status'
                ),
                array(
                    'label' => '��������� ��������:',
                    'type' => 'text',
                    'key' => 'title'
                ),
                array(
                    'label' => '����� � ����������� ���������:',
                    'type' => 'text',
                    'key' => 'title_speedbar'
                )
            )
        ),
        'meta' => array(
            'legend' => '��������� meta ��������',
            'row' => array(
                array(
                    'label' => ' Title:',
                    'type' => 'text',
                    'key' => 'metatitle'
                ),
                array(
                    'label' => 'Description:',
                    'type' => 'textarea',
                    'key' => 'metadescr'
                ),
                array(
                    'label' => 'Keywords: ',
                    'type' => 'textarea',
                    'key' => 'metakeywords'
                )
            )
        ),
        'upload' => array(
            'legend' => '��������� �������� ������ �� ������',
            'row' => array(
                array(
                    'label' => '�������� ������ ��� ������� You Tube:<br /><small>���������� ��������.</small>',
                    'type' => 'checkbox',
                    'key' => 'youTubeThumbManualLoad'
                ),
                array(
                    'label' => '�������� ������ ��� ������� Vimeo:<br /><small>���������� ��������.</small>',
                    'type' => 'checkbox',
                    'key' => 'vimeoThumbManualLoad'
                ),
                array(
                    'label' => '������ ������� � ��������:<br /><small>���������� ��������.</small>',
                    'type' => 'multiple',
                    'key' => 'accessupload',
                    'values' => array(
                        'data' => 'user_group', //global => $user_group
                        'key' => 'id', // key => id => $user_group[id]
                        'label' => 'group_name' //key => group_name => $user_group[group_name]
                    )
                ),
            )
        ),
        'fileWork' => array(
            'legend' => '��������� ����������� ������',
            'row' => array(
//                array(
//                    'type' => 'select',
//                    'key' => 'image_handler',
//                    'label' => '���������� �� ���������',
//                    'values' => $options
//                ),
                array(
                    'label' => '������������ ������ � kb:<br /><small>1024kb = 1mb</small>',
                    'type' => 'text',
                    'key' => 'maxsize'
                ),
                array(
                    'label' => '������������ ������ � px:<br />������ ���������:<small> 800x600, 800</small>',
                    'type' => 'text',
                    'key' => 'maxprop_original',
                    'subelement' => array(
                        'type' => 'select',
                        'key' => 'resize_mode_original',
                        'width' => '200',
                        'values' => array(
                            '0' => '�� ���������� �������',
                            '1' => '�� ������',
                            '2' => '�� ������'
                        )
                    )
                ),
                array(
                    'label' => '��������� watermark:<br /><small>������ ���������.</small>',
                    'type' => 'checkbox',
                    'key' => 'watermark'
                ),
                array(
                    'label' => '������������ ������ � px:<br />������ ��������:<small> 250x150, 250</small>',
                    'type' => 'text',
                    'key' => 'maxprop_slider',
                    'subelement' => array(
                        'type' => 'select',
                        'key' => 'resize_mode_thumbs',
                        'values' => array(
                            '0' => '�� ���������� �������',
                            '1' => '�� ������',
                            '2' => '�� ������'
                        )
                    )
                ),
                array(
                    'label' => '��������� watermark:<br /><small>������ ��������.</small>',
                    'type' => 'checkbox',
                    'key' => 'watermarkSlider'
                ),
                array(
                    'label' => '������������ ������ � px:<br />������� ���������:<small> 250x150, 250</small>',
                    'type' => 'text',
                    'key' => 'maxprop_cover',
                    'subelement' => array(
                        'type' => 'select',
                        'key' => 'resize_mode_cover',
                        'values' => array(
                            '0' => '�� ���������� �������',
                            '1' => '�� ������',
                            '2' => '�� ������'
                        )
                    )
                ),
                array(
                    'label' => '��������� watermark:<br /><small>������� ���������.</small>',
                    'type' => 'checkbox',
                    'key' => 'watermarkCover'
                ),
                array(
                    'label' => '�������� watermark:<br /><small>dleimages/.</small>',
                    'type' => 'text',
                    'key' => 'watermarkSource'
                ),
//                array(
//                    'label' => '����������� ������ ��� ������������ watermark:<br /><small>100</small>',
//                    'type' => 'text',
//                    'key' => 'watermarkMinSize'
//                ),
                array(
                    'label' => '��������� ������:',
                    'type' => 'checkbox',
                    'key' => 'rainbow'
                ),
                array(
                    'label' => '���-�� ������:<br /><small> max 10</small>',
                    'type' => 'text',
                    'key' => 'rainbowColorNum'
                ),
                array(
                    'label' => '��������� ������ �� �����:<br /><small>������� .flv, mp4.</small>',
                    'type' => 'checkbox',
                    'key' => 'fileFrame'
                ),
                array(
                    'label' => '��������� � ��������� ��� �����:<br /> ',
                    'type' => 'checkbox',
                    'key' => 'FileHash'
                ),
            )
        ),
        'uploadify' => array(
            'legend' => '��������� ���������� "Uploadify"',
            'row' => array(
                array(
                    'label' => '������������ ���-�� ������:<br /><small> 0 = no limit</small>',
                    'type' => 'text',
                    'key' => 'uploadifyMaxFile'
                ),
//                array(
//                    'label' => '������ ���������� � px:<br /><small> 100x70</small>',
//                    'type' => 'text',
//                    'key' => 'uploadifysize'
//                ),
//                array(
//                    'label' => '�����������:<br /><small> uploadify-background.png</small>',
//                    'type' => 'text',
//                    'key' => 'uploadifyimg'
//                ),
//                array(
//                    'label' => '����� (eng):<br /><small>"Select File", "Browse", "-" </small>',
//                    'type' => 'text',
//                    'key' => 'uploadifytext'
//                ),
            )
        ),
        'contentAccess' => array(
            'legend' => '��������� ������� � �������� �������.',
            'row' => array(
                array(
                    'label' => '�������� ��������:<br /><small>���������� ��������.</small>',
                    'type' => 'multiple',
                    'key' => 'accessCreate',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
//                array(
//                    'label' => '��������� ��������:<br /><small>���������� ��������.</small>',
//                    'type' => 'multiple',
//                    'key' => 'albomApprove',
//                    'values' => array(
//                        'data' => 'user_group',
//                        'key' => 'id',
//                        'label' => 'group_name'
//                    )
//                ),
//                array(
//                    'label' => '��������������� ��������:<br /><small>���������� ��������.</small>',
//                    'type' => 'multiple',
//                    'key' => 'accessComm',
//                    'values' => array(
//                        'data' => 'user_group',
//                        'key' => 'id',
//                        'label' => 'group_name'
//                    )
//                ),
                array(
                    'label' => '��������������� ������:<br /><small>���������� ��������.</small>',
                    'type' => 'multiple',
                    'key' => 'accessCommFile',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
                array(
                    'label' => '��������� ������������:<br /><small>���������� ��������.</small>',
                    'type' => 'multiple',
                    'key' => 'commentsApprove',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
            )
        ),
        'videoSetting' => array(
            'legend' => '��������� ���������� ����� ����������.',
            'row' => array(
                array(
                    'label' => '��������� ������ c <a href="http://www.youtube.com" class="web-link" target="_blank">You Tube</a> ',
                    'type' => 'checkbox',
                    'key' => 'allowYouTube'
                ),
                array(
                    'label' => '��������� ������ c <a href="http://smotri.com" class="web-link" target="_blank">smotri.com</a>',
                    'type' => 'checkbox',
                    'key' => 'allow_smotri_com'
                ),
                array(
                    'label' => '��������� ������ �  <a href="http://vimeo.com" class="web-link"  target="_blank">vimeo.com</a>',
                    'type' => 'checkbox',
                    'key' => 'allow_vimeo_com'
                ),
                array(
                    'label' => '��������� ������ c <a href="http://rutube.ru"  class="web-link"  target="_blank">rutube.ru</a>',
                    'type' => 'checkbox',
                    'key' => 'allow_rutube_ru'
                ),
                array(
                    'label' => '��������� ������ <a href="http://gametrailers.com"  class="web-link"  target="_blank">gametrailers.com</a>',
                    'type' => 'checkbox',
                    'key' => 'allow_gametrailers_com'
                ),
//                array(
//                    'label' => '��������� ������ <a href="http://video.mail.ru"  class="web-link"  target="_blank">video.mail.ru</a>',
//                    'type' => 'checkbox',
//                    'key' => 'allow_video_mail_ru'
//                ),
            )
        ),
        'videoSettingPlayerGlobal' => array(
            'legend' => '����� ���������',
            'row' => array(
                array(
                    'label' => '������ ������:',
                    'type' => 'text',
                    'key' => 'widthPlayer'
                ),
                array(
                    'label' => '������ ������:',
                    'type' => 'text',
                    'key' => 'heightPlayer'
                ),
                array(
                    'label' => '�������� �������������� ���������������:',
                    'type' => 'checkbox',
                    'key' => 'autoPlay'
                ),
                array(
                    'label' => '��������� ��������� ������� ������ �� ���������� ������� .flv:',
                    'type' => 'checkbox',
                    'key' => 'watermarkVideo'
                ),
                array(
                    'label' => '�������������� �������� �����:',
                    'subelement' => array(
                        'type' => 'select',
                        'key' => 'flv_watermark_pos',
                        'values' => array(
                            'left' => '�� ������ ����',
                            'center' => '�� ������',
                            'right' => '�� ������� ����'
                        )
                    )
                ),
                array(
                    'label' => '������� ������������ �������� �����',
                    'type' => 'text',
                    'key' => 'flv_watermark_al'
                ),
                array(
                    'label' => '�������� ����� ������� ����� ��� ������� Youtube',
                    'type' => 'checkbox',
                    'key' => 'tube_related'
                ),
                array(
                    'label' => '������������ ����� DLE ��� ��������������� ������� � ������� Youtube',
                    'type' => 'checkbox',
                    'key' => 'tube_dle'
                ),
            )
        ),
        'videoSettingPlayer' => array(
            'legend' => '��������� ���� ������ ��� ��������������� FLV � MP3 �������',
            'row' => array(
                array(
                    'label' => '������������ ��������� DLE',
                    'type' => 'checkbox',
                    'key' => 'video_setting_dle'
                ),
                array(
                    'label' => '�������� ������������ Youtube',
                    'subelement' => array(
                        'type' => 'select',
                        'key' => 'youtube_quality',
                        'values' => array(
                            'small' => '������',
                            'medium' => '�������',
                            'large' => '�������',
                            'hd720' => 'HD 720'
                        )
                    )
                ),
                array(
                    'label' => '�������� ����� ������� ����� � �������� ������ �����',
                    'type' => 'checkbox',
                    'key' => 'first_frame_preview'
                ),
                array(
                    'label' => '������������ ����������� ������� ��� ����������� �����������',
                    'type' => 'checkbox',
                    'key' => 'standart_cover'
                ),
                array(
                    'label' => '�������� ������������� ������ ���������� �������',
                    'type' => 'checkbox',
                    'key' => 'autohide'
                ),
                array(
                    'label' => '������ ����������� � ��������',
                    'type' => 'text',
                    'key' => 'buffer'
                ),
                array(
                    'label' => '���� ���������� ������������ ������:',
                    'type' => 'text',
                    'key' => 'progressBarColor'
                ),
            )
        ),
        'viewTools' => array(
            'legend' => '��������� �������������� ����������.',
            'row' => array(
                array(
                    'label' => '�������� �������:<br /><small>���������� ��������.</small>',
                    'type' => 'checkbox',
                    'key' => 'statusAlfavit'
                ),
                array(
                    'label' => '��� ���������:<br /><small>������ ���������.</small>',
                    'type' => 'checkbox',
                    'key' => 'allCategory'
                ),
                array(
                    'label' => '����������� �����������:<br /><small>������<a href="http://en.wikipedia.org/wiki/Nested_set_model" class="web-link" target="_blank"> Nested Sets</a></small>',
                    'type' => 'checkbox',
                    'key' => 'coments_tree'
                ),
            )
        ),
        'pagination' => array(
            'legend' => '��������� ������������ ���������',
            'row' => array(
                array(
                    'label' => '������� ��������:<br />',
                    'type' => 'text',
                    'key' => 'indexPage'
                ),
                array(
                    'label' => '� ����������:<br />',
                    'type' => 'text',
                    'key' => 'catPage'
                ),
                array(
                    'label' => '� ��������:<br />',
                    'type' => 'text',
                    'key' => 'albomPage'
                ),
                array(
                    'label' => '������������:<br />',
                    'type' => 'text',
                    'key' => 'commPage'
                ),
                array(
                    'label' => '��������� �����������:<br />',
                    'type' => 'text',
                    'key' => 'lastCommPage'
                ),
                array(
                    'label' => '�������� ������:<br />',
                    'type' => 'text',
                    'key' => 'searchPage'
                ),
            )
        ),
        'performance' => array(
            'legend' => '��������� ������������������ �������.',
            'row' => array(
                array(
                    'label' => '������ JavaScript:<br /><small><a href=" http://code.google.com/p/minify/" class="web-link" target="_blank">Minify</a></small>',
                    'type' => 'checkbox',
                    'key' => 'minJs'
                ),
//                array(
//                    'label' => '������� ��������:<br /><small>mysql: +1 (4)</small>',
//                    'type' => 'checkbox',
//                    'key' => 'ratingAlbom'
//                ),
//                array(
//                    'label' => '������� �������� (+ -):<br /><small>mysql: +3</small>',
//                    'type' => 'checkbox',
//                    'key' => 'ratingAlbomType'
//                ),
                array(
                    'label' => '������� ������:<br /><small>mysql: +1 (4)</small>',
                    'type' => 'checkbox',
                    'key' => 'ratingFile'
                ),
                array(
                    'label' => '������� ������ (+ -):<br /><small>mysql: +3</small>',
                    'type' => 'checkbox',
                    'key' => 'ratingFileType'
                ),
                array(
                    'label' => '��������� �����:<br /><small>mysql: +1</small>',
                    'type' => 'checkbox',
                    'key' => 'logViewFile'
                ),
                array(
                    'label' => '���������� �����:<br /><small>mysql: +1 </small>',
                    'type' => 'checkbox',
                    'key' => 'coutnDownloadFile'
                ),
            )
        ),
        'performanceExtend' => array(
            'legend' => '��������� ������������������ ������� (����������).',
            'row' => array(
                array(
                    'label' => '���������� ���� ��� ���������� ������������:<br /><small>mysql: +3 - 5 </small> ',
                    'type' => 'checkbox',
                    'key' => 'update_cache_addcomm'
                ),
                array(
                    'label' => '������� ������������:<br /><small>mysql: +3</small> ',
                    'type' => 'checkbox',
                    'key' => 'countComm'
                ),
                array(
                    'label' => '������� ������:<br /><small>mysql: +1</small> ',
                    'type' => 'checkbox',
                    'key' => 'countFile'
                ),
                array(
                    'label' => '������ �� �����:<br /><small>mysql: +2</small>',
                    'type' => 'text',
                    'key' => 'def_flood'
                ),
                array(
                    'label' => '����������� � ����� ������������:',
                    'type' => 'checkbox',
                    'key' => 'sendEmailcomm'
                ),
//                array(
//                    'label' => '����������� � ����� ��������:',
//                    'type' => 'checkbox',
//                    'key' => 'sendEmailalbom'
//                ),
            )
        ),
        'debug' => array(
            'legend' => '������� �������.',
            'row' => array(
                array(
                    'label' => '�������� ����� �������:<br /><small>������ ��� �������</small>',
                    'type' => 'checkbox',
                    'key' => 'debug'
                ),
                array(
                    'label' => '�������� ����� ������� Ajax:<br /><small>������ ��� �������</small>',
                    'type' => 'checkbox',
                    'key' => 'debugAjax'
                ),
                array(
                    'label' => '�������� �������:<br /><small>���������� ��������.</small>',
                    'type' => 'multiple',
                    'key' => 'debugAccessGroup',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                )
            )
        ),
    )
);
