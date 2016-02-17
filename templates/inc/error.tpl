{capture "content"}

	<h1>Napaka</h1>
	<p>{$error_message}</p>

{/capture}
{include file="inc/box.tpl" content=$.capture.content title=0}