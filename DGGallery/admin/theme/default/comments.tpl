<div class="bcomment">
	<div class="dtop">
		<div class="lcol"><span><img src="{foto}" alt=""/></span></div>
		<div class="rcol">
			<ul class="reset">
				<li><h4>{author}</h4></li>
				<li>{date}</li>
			</ul>
			<ul class="cmsep reset">
				<li>������: {group-name}</li>
				<li>{ip}</li>
			</ul>
		</div>
		<div class="clr"></div>
	</div>
	<div class="cominfo"><div class="dpad">
		[not-group=5]
		<div class="comedit">
			<div class="selectmass"></div>
			<ul class="reset">
				<li><a href="javascript:void(0);" onclick="gallery.comments.confirm('{id}')">�����������</a></li>
				<li>[com-edit]��������[/com-edit]</li>
				<li>[com-del]�������[/com-del]</li>
			</ul>
		</div>
		[/not-group]
		<ul class="cominfo reset">
			<li>�����������: {registration}</li>
			<li>������������: {comm-num}</li>
			<li>����������: {news-num}</li>
		</ul>
	</div>
	<span class="thide">^</span>
	</div>
	<div class="dcont">
		[aviable=lastcomments]<h3 style="margin-bottom: 0.4em;">{news_title}</h3>[/aviable]
		{comment}
		[signature]<br clear="all" /><div class="signature">--------------------</div><div class="slink">{signature}</div>[/signature]
		<div class="clr"></div>
	</div>
</div>