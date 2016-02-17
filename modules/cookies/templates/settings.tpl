{capture assign="top_bar"}

	<div class="row">
		<div class="col-md-8">
			<h1>{$content.title}</h1>
			<p class="lead">{$content.lead}</p>
		</div>
	</div>

{/capture}
{$dwoo.env.top_bar=$top_bar}

{capture "content"}

	<div class="row">
		<div class="col-md-6">
			<h2>Nastavitve</h2>
			<div id="cc-modal-wrapper">
				<div class="cc-content"></div>
			</div>
		</div>
		<div class="col-md-6">
			{$content.content}
		</div>
	</div>

{/capture}
{include file="inc/box.tpl" content=$.capture.content title=0}
