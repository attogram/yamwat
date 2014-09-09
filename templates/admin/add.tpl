{* **************************************

	Yamwat - Admin - ADD SITE

************************************** *}
{include file="menu.tpl"}
<form>
<div class="content">
{if isset($result)}
{$result}
<br /><br />

wiki: <a href="./?a=wiki&amp;wiki={$smarty.get.wiki|urlencode}"><b>{$smarty.get.wiki|escape}</b></a>
 &nbsp; <a target="edit" href="./?a=edit&amp;wiki={$smarty.get.wiki|urlencode}">(edit)</a> 
 <a href="./?a=tools&amp;wiki={$smarty.get.wiki|urlencode}">(tools)</a>
<br /><br />
{/if}
<input type="hidden" name="a" value="add" />
Add MediaWiki wiki:
<pre>
wiki    : <input name="wiki" type="text" size="30" />  example.com
API     : <input name="api" type="text" size="30" />  /w/api.php
Protocol: <select name="protocol"><option value="http">http</option><option value="https">https</option></select>://
Network : <input name="network" type="text" size="30" />
Topic   : <input name="topic" type="text" size="30" />

<input type="submit" value="add" />
<?pre>
</div>
</form>