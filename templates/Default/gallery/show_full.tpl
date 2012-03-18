<link rel="stylesheet" type="text/css" href="{THEME}/gallery/css/jquery.hoverscroll.css"/>
<div class="static" id="user-work-area">
  <div id="work-area">
    <div id="part-open">
      <div style="height:25px; width:100%;"> <span style="float:left"> [next-file]следующий файл[/next-file]</span> <span style="float:right"> [prev-file]предыдущий файл[/prev-file] </span></div>
      [file-image] <img src="{file-path}" alt="{file-title}" title="{file-title}" id="galleryImage"/> [/file-image]
      [file-video]{media=640,360}[/file-video]
      [field-label]
      <div id="label-box">
        <ul>
          {label}
        </ul>
      </div>
      [/field-label] </div>
  </div>
</div> 
<div class="pheading" style="padding-top:20px;">
  <h2 class="lcol">{title} </h2>
</div>
<div class="pheading" >
  <div class="userinfo" style="padding-left:0; ">
    <div class="rcol">
      <ul style="width:30%; float:left;">
        <li><span class="grey">Размер оригинала:</span> <b>{width}x{height}</b></li>
        <li><span class="grey">Размер файла:</span> <b>{size}</b></li>
        <li><span class="grey">Добавлен</span> <b>{date}</b></li>
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
             [field-colors]<div class="colors">{colors}</div>[/field-colors]
          <div class="tags">{tag}</div>
        </li>
        [field-description] [/field-description]
        <li style="width:100%; float:left; padding-top:10px;"><span class="grey">Описание:</span> [edit-description]редактировать[/edit-description]  {description}</li>
      </ul>
    </div>
    <div class="clr"></div>
  </div>
</div>
<div class="pheading" style="padding-top:20px;">
  <h2 class="lcol">Автор: {author}</h2>
  <a class="addcombtn" href="#" onclick="$('#authorInfo').toggle();return false;"  style="float:right;"><b>подробная информация</b></a>
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
        <li><span class="grey">Полное имя:</span> <b>{fullname}</b></li>
        <li><span class="grey">Группа:</span> {status} [time_limit]&nbsp;В группе до: {time_limit}[/time_limit]</li>
        <li><span class="grey">ICQ:</span> <b>{icq}</b></li>
      </ul>
      <ul class="ussep">
        <li><span class="grey">Количество комментариев:</span> <b>{comm_num}</b> [{last_comments}]</li>
        <li><span class="grey">Дата регистрации:</span> <b>{registration}</b></li>
        <li><span class="grey">Последнее посещение:</span> <b>{lastdate}</b></li>
      </ul>
      <ul class="ussep">
        <li><span class="grey">Место жительства:</span> {land}</li>
        <li><span class="grey">Немного о себе:</span> {info}</li>
      </ul>
    </div>
    <div class="clr"></div>
  </div>
</div>
[addcommentfile]
<div class="pheading" style="padding-top:20px;">
  <h2 class="lcol">Комментарии:</h2>
  <a class="addcombtn" href="#" onclick="$('#addcform').toggle();return false;"  style="float:right;"><b>Оставить комментарий</b></a>
  <div class="clr"></div>
</div>
[/addcommentfile] 
{addcomments}
 {info}
{comments}
{pagination} 
<script type="text/javascript">
$(document).ready(function(){
	// Override default parameters onload
$.fn.hoverscroll.params = $.extend($.fn.hoverscroll.params, {
	vertical : false,
	width: $('#viewport').outerWidth(true),
	height: 300,
	arrows: true
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
// Generate hoverscroll with overridden default parameters


 })
	</script> 
