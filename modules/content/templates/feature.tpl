{capture "content"}

	<h1>{$content.title}</h1>

	{$content.content}

	<a href="#" class="btn btn-primary trigger-filter">SHOP</a>

{/capture}
{include file="inc/box.tpl" content=$.capture.content title=0}

{capture assign="footer_grid"}

	<div class="zek-project-grid grid">
		{foreach $projects project}
			<div class="grid-item grid-item--w{$project.class_w} grid-item--h{$project.class_h} shop{$project.is_shop}">
				<div class='wrap'>
				{if strpos($project.picture, 'gif') == true }
					<img src="/media/uploads/project/{$project.picture}" class="img-responsive" data-slick-index="{$.foreach.default.index}" data-url="{$project.url|escape}" />
				{else}
					<img src="{$imageCache->img($project.picture, 'project/', $project.thumb_w, $project.thumb_h, true)}" class="img-responsive" data-slick-index="{$.foreach.default.index}" data-url="{$project.url|escape}" />
				{/if}
				<span class='title'>{$project.title}</span>
				</div>
			</div>
		{/}
		<div class="grid-sizer hidden"></div>
	</div>

{/capture}
{$dwoo.env.footer_grid=$footer_grid}

{if $active_project}{$dwoo.env.single_project=true}{/}

{capture assign="footer_obj"}

	<!-- Modal -->
	<div id="zekProjects">

		<div class="modal-logo">
			<svg width="35px" height="27px" viewBox="0 0 35 27" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				<g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
					<g id="Landing" transform="translate(-495.000000, -35.000000)" fill="#000000">
						<path d="M522.677563,35 C520.82475,35 519.084812,35.7067143 517.774937,36.989 C516.465063,38.2721429 515.743187,39.9774286 515.743187,41.792 C515.743187,43.076 516.123812,44.297 516.812437,45.3735714 C516.384563,45.7914286 513.5325,48.914 512.450125,50.0998571 C511.4255,49.0378571 508.264125,45.7652857 507.823562,45.3337143 C508.495125,44.2661429 508.866562,43.0575714 508.866562,41.792 C508.866562,38.0471429 505.756813,35 501.9335,35 C500.082,35 498.339875,35.7067143 497.031313,36.989 C495.721875,38.2721429 495,39.9774286 495,41.792 C495,43.6057143 495.721875,45.3114286 497.031313,46.5941429 C498.339875,47.8772857 500.082,48.5831429 501.9335,48.5831429 C503.252563,48.5831429 504.507313,48.2064286 505.610687,47.525 C506.012313,47.9184286 508.751062,50.6732857 510.057875,51.9894286 L502.994438,58.9095714 C502.696937,59.2005714 502.53375,59.5871429 502.532438,59.9985714 C502.532438,60.4112857 502.696937,60.7978571 502.994438,61.0897143 C503.2915,61.3798571 503.687,61.541 504.107,61.541 C504.527,61.541 504.9225,61.3798571 505.219125,61.0897143 L512.346875,53.8708571 L519.45625,61.0897143 C519.75375,61.3798571 520.148812,61.541 520.56925,61.541 C520.989687,61.541 521.385625,61.3798571 521.68225,61.0897143 C521.97975,60.7978571 522.142938,60.4112857 522.142938,59.9985714 C522.142938,59.5871429 521.978438,59.2005714 521.681375,58.9095714 L514.689687,51.914 C516.087063,50.5082857 518.66,47.9227143 519.040625,47.5494286 C520.13525,48.215 521.376,48.5831429 522.677563,48.5831429 C526.499563,48.5831429 529.610625,45.5364286 529.610625,41.792 C529.610625,38.0471429 526.499563,35 522.677563,35" id="Imported-Layers"></path>
					</g>
				</g>
			</svg>
		</div>

		<div class="zek-pages" data-open="{tif $active_project.id}">
			{foreach $projects project}
			<article data-url="{$project.url|escape}" data-pid="{$project.id}" tabindex="-1">
				<h1>{$project.title}</h1>
				{*<div class="project-image">*}
					{*<img src="{$imageCache->img($project.picture, 'project/', 1200, 600, true)}" class="img-responsive" />*}
				{*</div>*}
				{$project.content}
			</article>
			{/}
		</div>

	</div>

{/capture}
{$dwoo.env.footer_obj=$footer_obj}
