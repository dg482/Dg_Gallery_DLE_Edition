<fieldset class="b-4">
  <legend class="b-4">���������� ������ �������</legend>
  <table width="100%" border="0" class="table-form">
    <tbody>
      <tr>
        <td class="td-label"><label>��������:<br>
          </label></td>
        <td class="td-content"><input type="text" value="" name="config[title]" class="input-text b-4"></td>
      </tr>
      <tr>
        <td class="td-label"><label>���������:<br>
          </label></td>
        <td class="td-content"><select name="config[category]" style="width:200px;">
            {categoryes}
          </select></td>
      </tr>
      <tr>
        <td class="td-label"><label>�������������� ���:<br>
            <small>������� title</small></label></td>
        <td class="td-content"><input type="text" value="" name="config[meta_title]" class="input-text b-4"></td>
      </tr>
      <tr>
        <td class="td-label"><label>��������:<br>
            <small>������ ��������</small></label></td>
        <td class="td-content"><!--{editor__}-->
        <textarea id="descr" class="jwysiwyg" name="config[descr]" cols="" rows=""></textarea>
        </td>
      </tr>
      <tr>
        <td class="td-label"><label>��������:<br>
            <small>������� description</small></label></td>
        <td class="td-content"><textarea class="b-4 " id="meta_descr" name="config[meta_descr]" cols="" rows=""></textarea></td>
      </tr>
      <tr>
        <td class="td-label"><label>�������� �����:<br>
            <small>������� keywords</small></label></td>
        <td class="td-content"><textarea class="b-4 " id="meta_keywords" name="config[meta_keywords]" cols="" rows=""></textarea></td>
      </tr>
      <tr>
        <td class="td-label">&nbsp;</td>
        <td class="td-content"><input type="submit" value="���������"  class="fbutton" style="font-size:12px;" /></td>
      </tr>
    </tbody>
  </table>
</fieldset>
