{* **************************************

	Yamwat - Admin - Tools

************************************** *}
{include file="menu.tpl"}
<div class="content">
<form action="" method="GET">
<select name="wiki">
<option value="">Choose wiki:</option>
{foreach from=$wikis item=x key=k}
<option value="{$k}" {if $k == $wiki}selected {/if}>{$k}</option>
{/foreach}
</select> <select name="ns">
<option value="" selected>All Namespaces</option>
<option value="0">0 - [Main]</option>
<option value="1">1 - Talk</option>
<option value="2">2 - User</option>
<option value="3">3 - User talk</option>
<option value="4">4 - [SITE]</option>
<option value="5">5 - [SITE] talk</option>
<option value="6">6 - File</option>
<option value="7">7 - File talk</option>
<option value="8">8 - MediaWiki</option>
<option value="9">9 - MediaWiki talk</option>
<option value="10">10 - Template</option>
<option value="11">11 - Template talk</option>
<option value="12">12 - Help</option>
<option value="13">13 - Help talk</option>
<option value="14">14 - Category</option>
<option value="15">15 - Category talk</option>
<option value="100">100 - Portal</option>
<option value="101">101 - Portal talk</option>
<option value="108">108 - [Book]</option>
<option value="109">109 - [Book] talk</option>
</select> <select name="limit">
<option value="">limit</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="25">25</option>
</select>
<pre>{$menu}</pre>
<input type="submit" value="GET"></form>
</div>