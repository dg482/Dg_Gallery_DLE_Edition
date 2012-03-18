<div class="g-block"> [access_granted]
  [link]<img src="{src}" alt="{meta_name} || {meta_keyword}"/>[/link]
  [/access_granted]
  [access_denied]
  <div class="error b-4">
    <p>Доступ запрещен.</p>
  </div>
  [/access_denied]
  <div style="padding:5px;"> [category] <span class="grey">Категория:</span> [link]{name}[/link]<br />
    <span class="grey">Альбомов:</span> <b>{albom_num}</b> [/category]
    [albom] <span class="grey">Название:</span> [link]{name}[/link]<br />
    <span class="grey">Кол-во файлов:</span> <b>{file_num}</b> [group=1]<span class="grey">[delete-link]удалить[/delete-link]</b></span>[/group]
    [/albom]
    [file]
    <div style="width:55%; float:left"> [link]{name}[/link]<br />
      <span class="grey">Категория:</span>[catlink] {category}[/catlink]<br />
    </div>
    <div style="width:40%; float: right; text-align:right"> <span class="grey">{width}x{height}</span> <br />
      <span class="grey">{date}</span> <br />
    </div>
    [/file] </div>
</div>
