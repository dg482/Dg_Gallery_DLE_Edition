<!--������ �� ������ �������-->
<script type="text/javascript" src="{js_min}"></script>
<!--editors-->
<!--���� ������������ ������  -->
<script type="text/javascript" src="/engine/classes/js/bbcodes.js"></script>

<!--���� ������������ wysiwyg ��������.  �������� http://gallery.ru �� ��� ����� �����.-->
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/gallery/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/gallery/tiny_mce/tiny_mce_gzip.js"></script>
<!--/editors-->
<!--�������� ������� �������, ������������ � ������ ���� �� ������������ ������
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/assets/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/assets/swfobject.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/assets/swfobject_.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/gallery/gallery.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/gallery/gallery.ass.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/assets/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/gallery/gallery.upload.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/gallery/gallery.effect.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/gallery/gallery.label.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/gallery/gallery.ajax.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/gallery/gallery.player.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/gallery/gallery.comments.js"></script>
<script type="text/javascript" src="http://gallery.ru/DGGallery/admin/theme/default/js/gallery/gallery.ready.js"></script>
-->
<!--������ �������������� ���������-->
<script type="text/javascript" src="{THEME}/gallery/js/jquery.hoverscroll.js"></script>
<script type="text/javascript" src="{THEME}/gallery/js/gallery.ready.js"></script>
<link rel="stylesheet" type="text/css" href="{THEME}/gallery/js/highslide/highslide.css"/> 
<div class="wtop wsh">
  <div class="wsh"> 
    <div class="wsh">&nbsp;</div>
  </div>
</div>
<div class="shadlr">
  <div class="shadlr">
    <div class="container">
      <div class="vsep">
        <div class="vsep">
          <div id="midside" class="rcol">
          [aviable-gallery=profile|edit_albom]
            <div class="speedbar">{speedbar}</div>
            [/aviable-gallery]
            [not-aviable-gallery=profile|edit_albom]
            <form action="http://gallery.ru/gallery/search/keyword/" name="searchform" method="post">
              <ul class="searchbar reset" style="margin:0 0 10px 0;">
                <li class="lfield">
                  <input id="story" name="keyword" value="{search_keyword}" type="text" />
                </li>
                <li class="lbtn">
                  <input title="�����" alt="�����" type="image" src="{THEME}/images/spacer.gif" />
                </li>
              </ul>
            </form>
            [/not-aviable-gallery]
            <div style="width:660px"> [not-aviable-gallery=profile|edit_albom]
              <div style="display:inline-block; width:100%; text-align:center;">
                <div id="search-letter">
                  <ul>
                    <li> {alfa}</li>
                    [aviable-gallery=file|show_file]
                    <li class="sort"><a href="{order_url}ORDER_DATE" class="sort date {ORDER_DATE}">�� ����</a> <a href="{order_url}ORDER_COMMENTS" class="sort comment {ORDER_COMMENTS}">�� �������</a> <a href="{order_url}ORDER_DOWNLOAD" class="sort dl {ORDER_DOWNLOAD}">�� ������������</a> <a href="{order_url}ORDER_RATING" class="sort rating {ORDER_RATING}">�� ������������</a> </li>
                    [/aviable-gallery]
                  </ul>
                </div>
              </div>
              <div style="display:inline-block; width:100%; text-align:center; margin-bottom:20px;">
                <div id="speedbar">{speedbar}</div>
              </div>
              [/not-aviable-gallery] </div>
            {content} 
            {pagination} </div>
          <div id="sidebar" class="lcol"> {include file="sidebar.tpl"} </div>
          <div class="clr"></div>
        </div>
      </div>
      <div class="footbox">
        <div class="rcol"> </div>
        <div class="lcol">
          <p>��������� ����������, ��<br />
            �������������� �������� ��������<br />
            <b>D. G. Gallery</b>.<br />
            ������� ������ 1.5.</p>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="wbtm wsh">
  <div class="wsh">
    <div class="wsh">&nbsp;</div>
  </div>
</div>
