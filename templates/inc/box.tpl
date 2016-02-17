<div class="module{if !empty($class)} {$class}{/if}">

	{if $titlewide}
	<h2>{$titlewide}</h2>
	{/}

	{*<div class="module_container">*}
	{if $title}
		<h1>{$title}</h1>
	{/}
	{$content}
	{*</div>*}
</div>
