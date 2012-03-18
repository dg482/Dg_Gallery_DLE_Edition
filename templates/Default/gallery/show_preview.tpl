<link rel="stylesheet" type="text/css" href="{THEME}/gallery/css/jquery.hoverscroll.css"/>
<div class="static" id="user-work-area">
  <div id="work-area">
    <div id="part-open">
      <div style="height:25px; width:100%;"> <span style="float:right"> [next-file]��������� ����[/next-file]</span> <span style="float:left"> [prev-file]���������� ����[/prev-file] </span></div>
      [file-image] <img src="{file-path}" alt="{file-title}, {file-keyword}" title="{file-title}, {file-keyword}" id="gallery-image"/> [/file-image]
      [file-video]{media=640,360}[/file-video]
      [field-label]
      <div id="label-box">
        <ul>
          {label}
        </ul>
      </div>
      [/field-label]
      <ul class="slide-bottom" id="img-list-{id}">
        <li id="viewport" style="width:100%;">
          <ul>
            [file-list]
            <li> <a href="{link-file}" rel="{item-id}"> <img src="{preview}" alt="{preview-alt}" title="{preview-title}"></a></li>
            [/file-list]
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
<center><a href="javascript:void(0)" onclick="$('#slide-show').children('a').eq(0).click();">�������� ����� ���</a></center>
<div style=" display:none" id="slide-show">
[file-list]
 <a href="{path}"   onclick="return hs.expand(this)">
  <img src="{preview}" alt="{preview-alt}" title="{preview-title}" /></a>
 [/file-list]
 </div>
[is_author]
[edit-link]�������������[/edit-link]
[/is_author]
[albom_description]
<div class="pheading" style="padding-top:20px;">
  <h2 class="lcol">�������� ������� {albom_name}</h2>
</div>
<div class="pheading" >
  <div class="userinfo" style="padding-left:0; ">
    <div class="rcol"> {albom_description} </div>
  </div>
</div>
[/albom_description] 

<!--�������� �����-->
<div class="pheading" style="padding-top:20px;">
  <h2 class="lcol">{title} </h2>
</div>
<div class="pheading" >
  <div class="userinfo" style="padding-left:0; ">
    <div class="rcol">
      <ul style="width:30%; float:left;">
        [field-colors]
        <li><span class="grey">������ ���������:</span> <b>{width}x{height}</b></li>
        [/field-colors]
        <li><span class="grey">������ �����:</span> <b>{size}</b> <a href="/engine/download_images.php?id={id-file}">�������</a></li>
        <li><span class="grey">��������</span> <b>{date}</b></li>
        [rating]
        <li><span class="grey">�������</span> <b id="rating-{id-file}">{rating}</b> <a href="javascript:void(0)" onclick="setRating('{id-file}',2); return false;" class="rating up">+1</a> <a href="javascript:void(0)" onclick="setRating('{id-file}',1); return false;"  class="rating down">-1</a> </li>
        [/rating]
      </ul>
      <ul style="width:65%; float: right; text-align:left;">
        <li><span class="grey" style="width: 80px; display: block; float: left;">HTML:</span>
          <input type="text" value="{blog_html}" class="f_input" />
        </li>
        <li><span class="grey" style="width: 80px; display: block; float: left;">BB Code:</span>
          <input type="text" value="{bb_code}" class="f_input"/>
        </li>
        <li><span class="grey">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> </li>
      </ul>
      <ul class="ussep">
        <li><span class="grey"></span>
          <div class="colors">{colors}</div>
          <div class="tags">{tag}</div>
        </li>
        <li style="width:100%; float:left; padding-top:10px;"><span class="grey">��������:</span> [edit-description]�������������[/edit-description][field-description]{description}[/field-description]</li>
      </ul>
    </div>
    <div class="clr"></div>
  </div>
</div>
<!--/�������� �����--> 
<!--����� �������, �����-->
<div class="pheading" style="padding-top:20px;">
  <h2 class="lcol">�����: {author}</h2>
  <a class="addcombtn" href="#" onclick="$('#authorInfo').toggle();return false;"  style="float:right;"><b>��������� ����������</b></a>
  <div class="clr"></div>
</div>
<div class="pheading" id="authorInfo" style="display:none;">
  <div class="userinfo">
    <div class="lcol" style="padding-right:0;">
      <div class="avatar"><img src="{foto}" alt=""/></div>
      <ul class="reset">
        <li>{email}</li>
        [not-group=5]
        <li>{pm}</li>
        [/not-group]
      </ul>
    </div>
    <div class="rcol">
      <ul>
        <li><span class="grey">������ ���:</span> <b>{fullname}</b></li>
        <li><span class="grey">������:</span> {status} [time_limit]&nbsp;� ������ ��: {time_limit}[/time_limit]</li>
        <li><span class="grey">ICQ:</span> <b>{icq}</b></li>
      </ul>
      <ul class="ussep">
        <li><span class="grey">���������� ������������:</span> <b>{comm_num}</b> [{last_comments}]</li>
        <li><span class="grey">���� �����������:</span> <b>{registration}</b></li>
        <li><span class="grey">��������� ���������:</span> <b>{lastdate}</b></li>
      </ul>
      <ul class="ussep">
        <li><span class="grey">����� ����������:</span> {land}</li>
        <li><span class="grey">������� � ����:</span> {info}</li>
      </ul>
    </div>
    <div class="clr"></div>
  </div>
</div>
<!--/����� �������, �����--> 
<!--���������� �����������--> 
[addcommentfile]
<div class="pheading" style="padding-top:20px;">
  <h2 class="lcol">�����������:</h2>
  <a class="addcombtn" href="#" onclick="$('#addcform').toggle();return false;"  style="float:right;"><b>�������� �����������</b></a>
  <div class="clr"></div>
</div>
[/addcommentfile] 
<!--/���������� �����������--> 
<!--����� ���������� �����������--> 
{addcomments} 
<!--���������� � ��������� �������--> 
{info} 
<!--����������� � �����--> 
{comments} 
<!--������������ ���������--> 
{pagination} 

<script type="text/javascript">
$(document).ready(function(){
gallery.ass.wysiwyg = false;

$.fn.hoverscroll.params = $.extend($.fn.hoverscroll.params, {
	vertical : false,
	width: $('#viewport').outerWidth(true),
	height: 300,
	arrows: true,
	id: '{id-file}',
	debug : false
});
//[access_edit]
gallery.ass.wysiwyg = true;
tinyMCE_GZ.init({
            plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
            themes : 'advanced',
            skin : "cirkuit",
            disk_cache : true,
            languages:"ru",
            debug : false 
			
});
//[/access_edit]
 var obj = $('#viewport').children('ul').hoverscroll();
	hs.graphicsDir = '/engine/classes/highslide/graphics/';
	hs.align = 'center';
	hs.transitions = ['expand', 'crossfade'];
	hs.fadeInOut = true;
	hs.dimmingOpacity = 0.8;
	hs.outlineType = 'rounded-white';
	hs.captionEval = 'this.thumb.alt';
	hs.marginBottom = 105; // make room for the thumbstrip and the controls
	hs.numberPosition = 'caption';
	// Add the slideshow providing the controlbar and the thumbstrip
	hs.addSlideshow({
		//slideshowGroup: 'group1',
		interval: 5000,
		repeat: false,
		useControls: true,
		overlayOptions: {
			className: 'text-controls',
			position: 'bottom center',
			relativeTo: 'viewport',
			offsetY: -60
		},
		thumbstrip: {
			position: 'bottom center',
			mode: 'horizontal',
			relativeTo: 'viewport'
		}
	});
 })
</script> 
