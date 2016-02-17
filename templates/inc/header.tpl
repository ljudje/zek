<body class="{if $home}home{else}index{/}{if $page.class} {$page.class}{/}{if $dwoo.env.top_bar_class} {$dwoo.env.top_bar_class}{/}{if $dwoo.env.single_project} modal-open{/}">
<div id="main-body-wrap" class="{if $dwoo.env.single_project}visible{/}">
	<header>
		<div class="container">
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">

					<div class="text-center">
						<a class="internal main-logo" href="{$config.url}" rel="home" title="{$locale.page_title|escape}">
							<img src="/media/dsg/logo.svg" alt="{$locale.page_title|escape}" class="fn org logo organization-name name img-responsive" />
						</a>
					</div>

				</div>
			</div>
		</div>

	</header>

	{*<div class="menu-overlay">*}
		{*<a href="#" class="menu-overlay-close">*}
			{*<svg enable-background="new 0 0 100 100" viewBox="0 0 100 100">*}
				{*<polygon fill="#010101" points="77.6,21.1 49.6,49.2 21.5,21.1 19.6,23 47.6,51.1 19.6,79.2 21.5,81.1 49.6,53 77.6,81.1 79.6,79.2   51.5,51.1 79.6,23 "/>*}
			{*</svg>*}
		{*</a>*}
		{*<div class="container">*}
			{*<div class="row">*}
				{*<div class="col-sm-12">*}
					{*<ul class="list-unstyled">*}
						{*{foreach $menu.main.0 item}*}
						{*<li>*}
							{*<a href="{$item.url}" title="{$item.title|escape}">{$item.title}</a>*}
						{*</li>*}
						{*{/}*}
					{*</ul>*}
				{*</div>*}
			{*</div>*}
		{*</div>*}
	{*</div>*}

