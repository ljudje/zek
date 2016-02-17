{capture "content"}

	<h1>{$content.title}</h1>
	<p class="lead">{$content.lead}</p>

	{if $categories_list}
		{$dwoo.env.catshown=true}
		<section id="category-list" class="thumbnails">
		{foreach current ($categories_list) category}
			<article class="span3">
				<h3 class="clearfix">
					<a href="{$category.url}" title="{$category.title|escape}" class="pull-left">{if !empty ($category.picture)}<img src="{$imageCache->img($category.picture, 'product_category/', 24, 24, true, 'center', null, false, $picname)}" class="pull-left" alt="{$category.title|escape}" /> {/}{$category.title}</a>
				</h3>
				<ul class="unstyled">
				{foreach $categories_list[$category.nav_id] subcat}
					{*{if $.foreach.default.index <= 50}*}
					<li>
						<a href="{$subcat.url}">{$subcat.title}</a>
					</li>
					{*{/}*}
				{/}
				{*{if sizeof ($categories_list[$category.nav_id]) > 51}*}
					{*<li><a href="{$category.url}" class="more_link">več</a></li>*}
				{*{/}*}
				</ul>
			</article>
		{/}
		</section>
	{/}

{/capture}
{include file="inc/box.tpl" content=$.capture.content title=0}
