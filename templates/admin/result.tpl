{* **************************************

	Yamwat - Admin - Results from plugin

************************************** *}
{include file="menu.tpl"}
<pre class="content">
Action: {$action} 
wiki  : <a href="./?a=wiki&amp;wiki={$wiki|urlencode}">{$wiki}</a> 
Result: {if $result|is_array}OK

{$result|@print_r}
{else}?

{$result}{/if}
</pre>