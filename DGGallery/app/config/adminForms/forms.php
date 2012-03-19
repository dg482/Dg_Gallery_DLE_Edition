<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 * Форма основных настроек скрипта.
 * Form general setting.
 */

return array(
    'form' => array(
        'action' => '?&action=save_setting',
        'method' => 'post',
        'name' => 'config',
        'setting' => array(
            'legend' => 'Основные настройки скрипта',
            'row' => array(
                array(
                    'label' => 'Выключить галерею:',
                    'type' => 'checkbox',
                    'key' => 'status'
                ),
                array(
                    'label' => 'Заголовок страницы:',
                    'type' => 'text',
                    'key' => 'title'
                ),
                array(
                    'label' => 'Текст в контекстной навигации:',
                    'type' => 'text',
                    'key' => 'title_speedbar'
                )
            )
        ),
        'meta' => array(
            'legend' => 'Настройки meta описания',
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
            'legend' => 'Настройки загрузки файлов на сервер',
            'row' => array(
                array(
                    'label' => 'Загрузка превью для роликов You Tube:<br /><small>глобальный параметр.</small>',
                    'type' => 'checkbox',
                    'key' => 'youTubeThumbManualLoad'
                ),
                array(
                    'label' => 'Загрузка превью для роликов Vimeo:<br /><small>глобальный параметр.</small>',
                    'type' => 'checkbox',
                    'key' => 'vimeoThumbManualLoad'
                ),
                array(
                    'label' => 'Группы допуска к загрузке:<br /><small>глобальный параметр.</small>',
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
            'legend' => 'Обработка загруженных файлов',
            'row' => array(
//                array(
//                    'type' => 'select',
//                    'key' => 'image_handler',
//                    'label' => 'Обработчик по умолчанию',
//                    'values' => $options
//                ),
                array(
                    'label' => 'Максимальный размер в kb:<br /><small>1024kb = 1mb</small>',
                    'type' => 'text',
                    'key' => 'maxsize'
                ),
                array(
                    'label' => 'Максимальный размер в px:<br />Превью оригинала:<small> 800x600, 800</small>',
                    'type' => 'text',
                    'key' => 'maxprop_original',
                    'subelement' => array(
                        'type' => 'select',
                        'key' => 'resize_mode_original',
                        'width' => '200',
                        'values' => array(
                            '0' => 'По наибольшей стороне',
                            '1' => 'По ширине',
                            '2' => 'По высоте'
                        )
                    )
                ),
                array(
                    'label' => 'Наложение watermark:<br /><small>Превью оригинала.</small>',
                    'type' => 'checkbox',
                    'key' => 'watermark'
                ),
                array(
                    'label' => 'Максимальный размер в px:<br />Превью слайдера:<small> 250x150, 250</small>',
                    'type' => 'text',
                    'key' => 'maxprop_slider',
                    'subelement' => array(
                        'type' => 'select',
                        'key' => 'resize_mode_thumbs',
                        'values' => array(
                            '0' => 'По наибольшей стороне',
                            '1' => 'По ширине',
                            '2' => 'По высоте'
                        )
                    )
                ),
                array(
                    'label' => 'Наложение watermark:<br /><small>Превью слайдера.</small>',
                    'type' => 'checkbox',
                    'key' => 'watermarkSlider'
                ),
                array(
                    'label' => 'Максимальный размер в px:<br />Обложка категории:<small> 250x150, 250</small>',
                    'type' => 'text',
                    'key' => 'maxprop_cover',
                    'subelement' => array(
                        'type' => 'select',
                        'key' => 'resize_mode_cover',
                        'values' => array(
                            '0' => 'По наибольшей стороне',
                            '1' => 'По ширине',
                            '2' => 'По высоте'
                        )
                    )
                ),
                array(
                    'label' => 'Наложение watermark:<br /><small>Обложка категории.</small>',
                    'type' => 'checkbox',
                    'key' => 'watermarkCover'
                ),
                array(
                    'label' => 'Источник watermark:<br /><small>dleimages/.</small>',
                    'type' => 'text',
                    'key' => 'watermarkSource'
                ),
//                array(
//                    'label' => 'Минимальный размер для накладывания watermark:<br /><small>100</small>',
//                    'type' => 'text',
//                    'key' => 'watermarkMinSize'
//                ),
                array(
                    'label' => 'Получение цветов:',
                    'type' => 'checkbox',
                    'key' => 'rainbow'
                ),
                array(
                    'label' => 'Кол-во цветов:<br /><small> max 10</small>',
                    'type' => 'text',
                    'key' => 'rainbowColorNum'
                ),
                array(
                    'label' => 'Получение превью из кадра:<br /><small>форматы .flv, mp4.</small>',
                    'type' => 'checkbox',
                    'key' => 'fileFrame'
                ),
                array(
                    'label' => 'Создавать и проверять хэш файла:<br /> ',
                    'type' => 'checkbox',
                    'key' => 'FileHash'
                ),
            )
        ),
        'uploadify' => array(
            'legend' => 'Настройка загрузчика "Uploadify"',
            'row' => array(
                array(
                    'label' => 'Максимольное кол-во файлов:<br /><small> 0 = no limit</small>',
                    'type' => 'text',
                    'key' => 'uploadifyMaxFile'
                ),
//                array(
//                    'label' => 'Размер загрузчика в px:<br /><small> 100x70</small>',
//                    'type' => 'text',
//                    'key' => 'uploadifysize'
//                ),
//                array(
//                    'label' => 'Изображение:<br /><small> uploadify-background.png</small>',
//                    'type' => 'text',
//                    'key' => 'uploadifyimg'
//                ),
//                array(
//                    'label' => 'Текст (eng):<br /><small>"Select File", "Browse", "-" </small>',
//                    'type' => 'text',
//                    'key' => 'uploadifytext'
//                ),
            )
        ),
        'contentAccess' => array(
            'legend' => 'Настройки доступа к функциям скрипта.',
            'row' => array(
                array(
                    'label' => 'Создание альбомов:<br /><small>глобальный параметр.</small>',
                    'type' => 'multiple',
                    'key' => 'accessCreate',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
//                array(
//                    'label' => 'Модерация альбомов:<br /><small>глобальный параметр.</small>',
//                    'type' => 'multiple',
//                    'key' => 'albomApprove',
//                    'values' => array(
//                        'data' => 'user_group',
//                        'key' => 'id',
//                        'label' => 'group_name'
//                    )
//                ),
//                array(
//                    'label' => 'Комментирование альбомов:<br /><small>глобальный параметр.</small>',
//                    'type' => 'multiple',
//                    'key' => 'accessComm',
//                    'values' => array(
//                        'data' => 'user_group',
//                        'key' => 'id',
//                        'label' => 'group_name'
//                    )
//                ),
                array(
                    'label' => 'Комментирование файлов:<br /><small>глобальный параметр.</small>',
                    'type' => 'multiple',
                    'key' => 'accessCommFile',
                    'values' => array(
                        'data' => 'user_group',
                        'key' => 'id',
                        'label' => 'group_name'
                    )
                ),
                array(
                    'label' => 'Модерация комментариев:<br /><small>глобальный параметр.</small>',
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
            'legend' => 'Настройки добавления видео материалов.',
            'row' => array(
                array(
                    'label' => 'Разрешить ролики c <a href="http://www.youtube.com" class="web-link" target="_blank">You Tube</a> ',
                    'type' => 'checkbox',
                    'key' => 'allowYouTube'
                ),
                array(
                    'label' => 'Разрешить ролики c <a href="http://smotri.com" class="web-link" target="_blank">smotri.com</a>',
                    'type' => 'checkbox',
                    'key' => 'allow_smotri_com'
                ),
                array(
                    'label' => 'Разрешить ролики с  <a href="http://vimeo.com" class="web-link"  target="_blank">vimeo.com</a>',
                    'type' => 'checkbox',
                    'key' => 'allow_vimeo_com'
                ),
                array(
                    'label' => 'Разрешить ролики c <a href="http://rutube.ru"  class="web-link"  target="_blank">rutube.ru</a>',
                    'type' => 'checkbox',
                    'key' => 'allow_rutube_ru'
                ),
                array(
                    'label' => 'Разрешить ролики <a href="http://gametrailers.com"  class="web-link"  target="_blank">gametrailers.com</a>',
                    'type' => 'checkbox',
                    'key' => 'allow_gametrailers_com'
                ),
//                array(
//                    'label' => 'Разрешить ролики <a href="http://video.mail.ru"  class="web-link"  target="_blank">video.mail.ru</a>',
//                    'type' => 'checkbox',
//                    'key' => 'allow_video_mail_ru'
//                ),
            )
        ),
        'videoSettingPlayerGlobal' => array(
            'legend' => 'Общие настройки',
            'row' => array(
                array(
                    'label' => 'Ширина плеера:',
                    'type' => 'text',
                    'key' => 'widthPlayer'
                ),
                array(
                    'label' => 'Высота плеера:',
                    'type' => 'text',
                    'key' => 'heightPlayer'
                ),
                array(
                    'label' => 'Включить автоматическое воспроизведение:',
                    'type' => 'checkbox',
                    'key' => 'autoPlay'
                ),
                array(
                    'label' => 'Разрешить наложение водяных знаков на видеофайлы формата .flv:',
                    'type' => 'checkbox',
                    'key' => 'watermarkVideo'
                ),
                array(
                    'label' => 'Местоположение водяного знака:',
                    'subelement' => array(
                        'type' => 'select',
                        'key' => 'flv_watermark_pos',
                        'values' => array(
                            'left' => 'По левому краю',
                            'center' => 'По центру',
                            'right' => 'По правому краю'
                        )
                    )
                ),
                array(
                    'label' => 'Степень прозрачности водяного знака',
                    'type' => 'text',
                    'key' => 'flv_watermark_al'
                ),
                array(
                    'label' => 'Включить показ похожих видео для сервиса Youtube',
                    'type' => 'checkbox',
                    'key' => 'tube_related'
                ),
                array(
                    'label' => 'Использовать плеер DLE при воспроизведении роликов с сервиса Youtube',
                    'type' => 'checkbox',
                    'key' => 'tube_dle'
                ),
            )
        ),
        'videoSettingPlayer' => array(
            'legend' => 'Настройки флэш плеера для воспроизведения FLV и MP3 формата',
            'row' => array(
                array(
                    'label' => 'Использовать настройки DLE',
                    'type' => 'checkbox',
                    'key' => 'video_setting_dle'
                ),
                array(
                    'label' => 'Качество видеороликов Youtube',
                    'subelement' => array(
                        'type' => 'select',
                        'key' => 'youtube_quality',
                        'values' => array(
                            'small' => 'Низкое',
                            'medium' => 'Среднее',
                            'large' => 'Высокое',
                            'hd720' => 'HD 720'
                        )
                    )
                ),
                array(
                    'label' => 'Включить показ первого кадра в качестве превью видео',
                    'type' => 'checkbox',
                    'key' => 'first_frame_preview'
                ),
                array(
                    'label' => 'Использовать стандартную обложку при отображении видеоплеера',
                    'type' => 'checkbox',
                    'key' => 'standart_cover'
                ),
                array(
                    'label' => 'Скрывать автоматически панель управления плеером',
                    'type' => 'checkbox',
                    'key' => 'autohide'
                ),
                array(
                    'label' => 'Размер видеобуфера в секундах',
                    'type' => 'text',
                    'key' => 'buffer'
                ),
                array(
                    'label' => 'Цвет индикатора проигрывания ролика:',
                    'type' => 'text',
                    'key' => 'progressBarColor'
                ),
            )
        ),
        'viewTools' => array(
            'legend' => 'Настройки дополнительных параметров.',
            'row' => array(
                array(
                    'label' => 'Включить алфавит:<br /><small>глобальный параметр.</small>',
                    'type' => 'checkbox',
                    'key' => 'statusAlfavit'
                ),
                array(
                    'label' => 'Все Категории:<br /><small>список категорий.</small>',
                    'type' => 'checkbox',
                    'key' => 'allCategory'
                ),
                array(
                    'label' => 'Древовидные комментарии:<br /><small>модель<a href="http://en.wikipedia.org/wiki/Nested_set_model" class="web-link" target="_blank"> Nested Sets</a></small>',
                    'type' => 'checkbox',
                    'key' => 'coments_tree'
                ),
            )
        ),
        'pagination' => array(
            'legend' => 'Настройки постраничной навигации',
            'row' => array(
                array(
                    'label' => 'Главная страница:<br />',
                    'type' => 'text',
                    'key' => 'indexPage'
                ),
                array(
                    'label' => 'В категориях:<br />',
                    'type' => 'text',
                    'key' => 'catPage'
                ),
                array(
                    'label' => 'В альбомах:<br />',
                    'type' => 'text',
                    'key' => 'albomPage'
                ),
                array(
                    'label' => 'Комментариев:<br />',
                    'type' => 'text',
                    'key' => 'commPage'
                ),
                array(
                    'label' => 'Последние комментарии:<br />',
                    'type' => 'text',
                    'key' => 'lastCommPage'
                ),
                array(
                    'label' => 'Страницы поиска:<br />',
                    'type' => 'text',
                    'key' => 'searchPage'
                ),
            )
        ),
        'performance' => array(
            'legend' => 'Настройки производительности скрипта.',
            'row' => array(
                array(
                    'label' => 'Сжатие JavaScript:<br /><small><a href=" http://code.google.com/p/minify/" class="web-link" target="_blank">Minify</a></small>',
                    'type' => 'checkbox',
                    'key' => 'minJs'
                ),
//                array(
//                    'label' => 'Рейтинг альбомов:<br /><small>mysql: +1 (4)</small>',
//                    'type' => 'checkbox',
//                    'key' => 'ratingAlbom'
//                ),
//                array(
//                    'label' => 'Рейтинг альбомов (+ -):<br /><small>mysql: +3</small>',
//                    'type' => 'checkbox',
//                    'key' => 'ratingAlbomType'
//                ),
                array(
                    'label' => 'Рейтинг файлов:<br /><small>mysql: +1 (4)</small>',
                    'type' => 'checkbox',
                    'key' => 'ratingFile'
                ),
                array(
                    'label' => 'Рейтинг файлов (+ -):<br /><small>mysql: +3</small>',
                    'type' => 'checkbox',
                    'key' => 'ratingFileType'
                ),
                array(
                    'label' => 'Просмотры файла:<br /><small>mysql: +1</small>',
                    'type' => 'checkbox',
                    'key' => 'logViewFile'
                ),
                array(
                    'label' => 'Скачивание файла:<br /><small>mysql: +1 </small>',
                    'type' => 'checkbox',
                    'key' => 'coutnDownloadFile'
                ),
            )
        ),
        'performanceExtend' => array(
            'legend' => 'Настройки производительности скрипта (расширенно).',
            'row' => array(
                array(
                    'label' => 'Обновление кеша при добавление комментариев:<br /><small>mysql: +3 - 5 </small> ',
                    'type' => 'checkbox',
                    'key' => 'update_cache_addcomm'
                ),
                array(
                    'label' => 'Счетчик комментариев:<br /><small>mysql: +3</small> ',
                    'type' => 'checkbox',
                    'key' => 'countComm'
                ),
                array(
                    'label' => 'Счетчик файлов:<br /><small>mysql: +1</small> ',
                    'type' => 'checkbox',
                    'key' => 'countFile'
                ),
                array(
                    'label' => 'Защита от флуда:<br /><small>mysql: +2</small>',
                    'type' => 'text',
                    'key' => 'def_flood'
                ),
                array(
                    'label' => 'Уведомление о новых комментариях:',
                    'type' => 'checkbox',
                    'key' => 'sendEmailcomm'
                ),
//                array(
//                    'label' => 'Уведомление о новых альбомах:',
//                    'type' => 'checkbox',
//                    'key' => 'sendEmailalbom'
//                ),
            )
        ),
        'debug' => array(
            'legend' => 'Отладка скрипта.',
            'row' => array(
                array(
                    'label' => 'Включить режим отладки:<br /><small>только для опытных</small>',
                    'type' => 'checkbox',
                    'key' => 'debug'
                ),
                array(
                    'label' => 'Включить режим отладки Ajax:<br /><small>только для опытных</small>',
                    'type' => 'checkbox',
                    'key' => 'debugAjax'
                ),
                array(
                    'label' => 'Выводить группам:<br /><small>глобальный параметр.</small>',
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
