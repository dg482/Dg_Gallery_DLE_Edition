<link rel="stylesheet" type="text/css" href="{THEME}/gallery/css/gallery-ui-1.5.css"/>
<link rel="stylesheet" type="text/css" href="{THEME}/gallery/css/jquery-ui-1.8.9.custom.css"/>
        <div id="part-info" style="display:block;"> 
          <div class="success b-6">
            <p>����������� ������������ �������� �������� ����� ��� ���������� ��������� ��������.</p>
          </div>
        </div>
        <div id="part-index" style="display:none;">
          <ul class="user-albom">
            [list-albom]
            <li class="b-4">
              <div class="cover"> [link] <img src="{cover}" alt="" title="�������" /> [/link]</div>
              <div class="info-albom"> {title}<br />
                <span class="grey">���-�� ������:</span>{files}<br />
                [edit-link]�������������[/edit-link]
              </div>
            </li>
            [/list-albom]
          </ul>
        </div>
        <div id="part-add" style="display:none;"> {include file="form-addalbom.tpl"} </div>
                <div id="part-search" style="display:none;">
          <fieldset class="b-4 clear">
            <table width="100%" border="0" class="file-table-profile ">
              <thead>
                <tr>
                  <td colspan="2"><fieldset  class="b-4" style="background:#F1F5F7">
                  <legend>���������  ������</legend>
                        <label for="category">���������</label><!-- id="category"-->
                        <select name="category" id="category" style="width:200px;">
                          <option value="0" selected="selected">-----------</option>
                        {albom}
                        </select>
                        <label for="search1"> �� ������ �: </label>
                        <!-- class="date"  name="date-1"-->
                        <input type="text" name="date-1" class="date f_input" style="width:80px" id="search1"/>
                        <label for="search2">��: </label>
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
                  <a href="javascript:void(0)" onclick="deleteFile('{id-file}')" class="delete" >delete</a>
                  <a href="{preview-path}" target="_blank" onclick="return hs.expand(this)" class="preview" rel="{preview-path}">preview</a>
                  </td>
              </tr>
              [/file-list]
              </tbody>
              
            </table>
          </fieldset>
        </div>

<script type="text/javascript">
$(document).ready(function(){
var sidebar = ["info","index","add",'search'];
gallery.ass.initSideBar(sidebar,'info');
gallery.ass.setDatePicker($('.file-item'));
gallery.ass.wysiwyg = true;
tinyMCE_GZ.init({
            plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
            themes : 'advanced',
            skin : "cirkuit",
            disk_cache : true,
            languages:"ru",
            debug : false
});
gallery.ass.InitWYSIWYG();
	})
	</script> 