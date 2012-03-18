<link rel="stylesheet" type="text/css" href="{THEME}/gallery/css/gallery-ui-1.5.css"/>
<link rel="stylesheet" type="text/css" href="{THEME}/gallery/css/jquery-ui-1.8.9.custom.css"/>
<div id="part-open" class="images b-2 full" style="display:block;"> 
  <!-- id="part-open" not modify --> 
</div>
<div class="images b-2 full" id="part-upload" style="display:none">
  <table width="100%" height="450" border="0" valign="top" style="margin:10px 0 20px 0;">
    <thead>
      <tr height="20">
        <td align="center" colspan="3">загрузка файлов в альбом</td>
      </tr>
    </thead>
      <tbody>
    <tr>
      <td width="80%" valign="top" style="vertical-align: top;" rowspan="2"><div style="height:500px; overflow:auto; overflow-x: hidden;">
          <table width="100%" border="0" class="file-table">
            <!-- class="ui-sortable"> -->
            <tbody class="ui-sortable">
            {files}
            </tbody> 
          </table>
        </div></td>
      <td height="440" class="tbl" colspan="2"><div id="fileQueue" class="uploadifyQueue"></div></td>
    </tr>
    <tr valign="top">
      <td valign="top" class="tbl"><input type="file" id="uploadify" name="uploadify" ></td>
      <td valign="top" class="tbl"> 
      [youtube]<a href="javascript:void(0)" class="youtube-add">youtube-add</a>[/youtube] </td>
    </tr>
</tbody>
</table>
</div>
{form-access}
{form-edit}