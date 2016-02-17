	<h4>{$locale.files_title}</h4>
	<ul class="files_list list-unstyled">
		{foreach $files item}
		<li>
			<a href="/media/uploads/file/{$item.file}" onclick="window.open(this.href); return false;">{tif $item.title ? $item.title|escape : $item.file|escape}</a> ({$item.meta.type|upper}, {tif $item.meta.size_h})
		</li>
		{/foreach}
	</ul>
