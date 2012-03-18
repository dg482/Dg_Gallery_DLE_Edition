<link rel="stylesheet" type="text/css" href="{THEME}/gallery/css/gallery-ui-1.5.css"/>
<link rel="stylesheet" type="text/css" href="{THEME}/gallery/css/jquery-ui-1.8.9.custom.css"/>
<div id="content">
  <div id="user-work-area" class="_block b-10 view-images" style="height:750px;">
    <div id="work-area-top-bar">
      <ul class="nav">
        <li class="logo"></li>
      </ul>
    </div>
    <div id="work-area-side-bar">
      <ul class="nav">
      </ul>
    </div>
    <div id="work-area"> 
      <!-- --> 
      [profile]       
      <form method="post" action="#" id="search-user">
        <div id="part-info" style="display:none;">{messages}
          <div class="success b-6">
            <p>Используйте переключение активных областей слева для выполнения доступных действий.</p>
          </div>
        </div>
        <div id="part-add" style="display: none;"> {add-form} </div>
        <div id="part-search" style="display:block;">
          <fieldset class="b-4 clear">
            <table width="100%" border="0" class="file-table-profile ">
              <thead>
                <tr>
                  <td colspan="2"><fieldset  class="b-4" style="background:#F1F5F7">
                  <legend>Параметры  показа</legend>
                        <label for="category">Категория</label><!-- id="category"-->
                        <select name="category" id="category" style="width:200px;">
                          <option value="0" selected="selected">-----------</option>
                        {categoryes}
                      
                        </select>
                        <label for="search1"> за период с: </label>
                        <!-- class="date"  name="date-1"-->
                        <input type="text" name="date-1" class="date f_input" style="width:80px" id="search1"/>
                        <label for="search2">по: </label>
                         <!-- class="date"  name="date-2"-->
                        <input type="text" name="date-2"  class="date f_input" style="width:80px" id="search2"/>
                    </fieldset></td>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <td colspan="2" align="center"><div id="pager"></div>&nbsp;</td>
                </tr>
              </tfoot>
              <tbody id="ajax-content-user">[file-list]
              <tr class="file-item">
                <td>#{id-file}&nbsp;{name}</td>
                <td width="100">&nbsp;
                  <a href="javascript:void(0)" onclick="deleteFile('{id-file}'); $(this).parent().parent().hide()" class="delete" >delete</a>
                  <a href="{preview-path}" target="_blank" onclick="return hs.expand(this)"  class="preview" rel="{preview-path}">preview</a>
                  <a href="javascript:void(0)" onclick="editFile('{id-file}'); return false;"  class="setting-tbl">setting</a>
                  
                  </td>
              </tr>
              [/file-list]
              </tbody>
              
            </table>
          </fieldset>
        </div>
      </form>
      <!-- --> 
      [/profile] </div>
  </div>
</div>
[profile] 
<script type="text/javascript">
$(document).ready(function(){
gallery.ass._selectDecorator = false;
gallery.ajax.root = window.location.protocol + '//' + window.location.host+ '/' + 'gallery/ajax/';
var sidebar = ["info","add","search"];
gallery.ass.initSideBar(sidebar,'info');
gallery.ass.selectDecorator($('user'));
gallery.ass.setDatePicker($('.file-item'));
gallery.ass.setPager('{total-file}',1)
gallery.ass.wysiwyg = true;
tinyMCE_GZ.init({
            plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
            themes : 'advanced',
            skin : "cirkuit",
            disk_cache : true,
            languages:"ru",
            debug : false
});
});
	</script> 
[/profile]
