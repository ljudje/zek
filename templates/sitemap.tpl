<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>{$config.url}</loc>
		<changefreq>hourly</changefreq>
		<priority>1</priority>
	</url>
{$dwoo.env.menus=$menu.main}
{template menus_loop parent_id=0 level}
	{foreach $dwoo.env.menus.$parent_id mid item name="$parent_id"}
	<url>
		<loc>{Config::get('url')}{$item.url}</loc>
		<changefreq>weekly</changefreq>
		<priority>0.5</priority>
	</url>
	{if $dwoo.env.menus[$item.nav_id]}
		{menus_loop $item.nav_id $level+1}
	{/if}
	{/foreach}
{/template}
{menus_loop 0 0}

</urlset>
