{* **************************************

	Yamwat - Admin - Edit

************************************** *}
{include file="menu.tpl"}
<div class="content">
{if !$wiki}
<p>Choose wiki to edit:</p>
<ul>
<li>Wiki - api - topic - network
{foreach from=$wikis item=x key=k}
<li><a href="./?a=edit&amp;wiki={$k|urlencode}">{$k}</a> - {$x.api} - {$x.topic} - {$x.network}</li>
{foreachelse}
<li>no wikis found in database</li>
{/foreach}
</ul>
{else}
{if isset($result)}<p>{$result}</p>{/if}
<table>
 <tr>
  <td>edit</td>
  <td>wiki</td>
  <td>API</td>
  <td>protocol</td>
  <td>network</td>
  <td>topic</td>
  <td>delete</td>
 </tr>
{foreach from=$wikis item=x key=k}
{if ($wiki && $wiki!=$k) }{/if}
{if ($wiki && $wiki==$k) || !$wiki }
 <tr>
 <form>
 <input type="hidden" name="go" value="1" />
 <input type="hidden" name="a" value="edit" />
 <input type="hidden" name="wiki" value="{$k}" />
   <td><input type="submit" value="edit" /></td>
   <td><input name="wiki_new" value="{$k}" size="25" /></td>
   <td><input name="api" value="{$x.api}" size="15" /></td>
   <td><input name="protocol" value="{$x.protocol}" size="6" /></td>
   <td><input name="network" value="{$x.network}" size="15" /></td>
   <td><input name="topic" value="{$x.topic}" size="15" /></td>
   <td><input type="checkbox" name="delete" />delete</td>
 </form>
 </tr>
{/if}
{foreachelse}
 <tr>
  <td colspan="7">
   No wikis found
  </td>
 </tr>
{/foreach}
</table>
{/if}
</div>