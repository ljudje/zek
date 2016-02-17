{if $modules[$position]}
	{foreach $modules[$position] module}
		<div class="{$module.module}{if $module.module_id != $module.module}{$module.module_id}{/}{if !empty($class)} {$class}{/if}">{$module.output}</div>
	{/}
{/}
