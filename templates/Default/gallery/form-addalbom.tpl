<fieldset class="b-4">
  <legend class="b-4">Добавление нового альбома</legend>
  <table width="100%" border="0" class="table-form">
    <tbody>
      <tr>
        <td class="td-label"><label>Название:<br>
          </label></td>
        <td class="td-content"><input type="text" value="" name="config[title]" class="input-text b-4"></td>
      </tr>
      <tr>
        <td class="td-label"><label>Категория:<br>
          </label></td>
        <td class="td-content"><select name="config[category]" style="width:200px;">
            {categoryes}
          </select></td>
      </tr>
      <tr>
        <td class="td-label"><label>Альтернативное имя:<br>
            <small>метатег title</small></label></td>
        <td class="td-content"><input type="text" value="" name="config[meta_title]" class="input-text b-4"></td>
      </tr>
      <tr>
        <td class="td-label"><label>Описание:<br>
            <small>полное описание</small></label></td>
        <td class="td-content"><!--{editor__}-->
        <textarea id="descr" class="jwysiwyg" name="config[descr]" cols="" rows=""></textarea>
        </td>
      </tr>
      <tr>
        <td class="td-label"><label>Описание:<br>
            <small>метатег description</small></label></td>
        <td class="td-content"><textarea class="b-4 " id="meta_descr" name="config[meta_descr]" cols="" rows=""></textarea></td>
      </tr>
      <tr>
        <td class="td-label"><label>Ключевые слова:<br>
            <small>метатег keywords</small></label></td>
        <td class="td-content"><textarea class="b-4 " id="meta_keywords" name="config[meta_keywords]" cols="" rows=""></textarea></td>
      </tr>
      <tr>
        <td class="td-label">&nbsp;</td>
        <td class="td-content"><input type="submit" value="отправить"  class="fbutton" style="font-size:12px;" /></td>
      </tr>
    </tbody>
  </table>
</fieldset>
