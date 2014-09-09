{* **************************************

	Yamwat - Wikis

************************************** *}
{include file='menu.tpl'}
<link rel="stylesheet" type="text/css" href="css/table.style.css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(document).ready(function() { 
$.tablesorter.defaults.widgets = ['zebra'];
{if 
$where == 'aindex'}$.tablesorter.defaults.sortList = [[1,1]];{elseif 
$where == 'pages'}$.tablesorter.defaults.sortList = [[5,1]];{elseif 
$where == 'articles'}$.tablesorter.defaults.sortList = [[6,1]];{elseif 
$where == 'edits'}$.tablesorter.defaults.sortList = [[7,1]];{elseif 
$where == 'images'}$.tablesorter.defaults.sortList = [[8,1]];{elseif 
$where == 'users'}$.tablesorter.defaults.sortList = [[9,1]];{elseif 
$where == 'activeusers'}$.tablesorter.defaults.sortList = [[10,1]];{elseif 
$where == 'admins'}$.tablesorter.defaults.sortList = [[11,1]];
{else}$.tablesorter.defaults.sortList = [[5,1]];{/if}
$("#wikis").tablesorter();
});
</script>
<div class="content">
<b>Wikis List</b>: where={$where}
<br />wikis: {$wikis|sizeof} (of {$wikis_count} total)
<br /><form action="./" style="display:inline;">
<input type="hidden" name="a" value="wikis" />
{if isset($network)}<input type="hidden" name="network" value="{$network}" />{/if}
{if isset($topic)}<input type="hidden" name="topic" value="{$topic}" />{/if}
{if isset($language)}<input type="hidden" name="language" value="{$language}" />{/if}
{if isset($version)}<input type="hidden" name="versions" value="{$version}" />{/if}
where <select name="where">
	<option value="aindex" {if $where == 'aindex'}selected="selected"{/if}>activity index</option>
	<option value="pages" {if $where == 'pages'}selected="selected"{/if}>pages</option>
	<option value="articles" {if $where == 'articles'}selected="selected"{/if}>articles</option>
	<option value="edits" {if $where == 'edits'}selected="selected"{/if}>edits</option>
	<option value="images" {if $where == 'images'}selected="selected"{/if}>images</option>
	<option value="users" {if $where == 'users'}selected="selected"{/if}>users</option>
	<option value="ausers" {if $where == 'activeusers'}selected="selected"{/if}>activeusers</option>
	<option value="admins" {if $where == 'admins'}selected="selected"{/if}>admins</option>
</select> 
<select name="dir">
	<option value="gte" {if $dir == 'gte'}selected="selected"{/if}>&gt;=</option>
	<option value="gt" {if $dir == 'gt'}selected="selected"{/if}>&gt;</option>
	<option value="lt" {if $dir == 'lt'}selected="selected"{/if}>&lt;</option>
	<option value="lte" {if $dir == 'lte'}selected="selected"{/if}>&lt;=</option>
</select> <input type="text" name="c" size="9" value="{if $c}{$c}{else}0{/if}"> <input type="submit" value="update" />
</form>

{if isset($network)}<br />where <a href="./?a=networks">network</a> = {$network}{/if}
{if isset($topic)}<br />where <a href="./?a=topics">topic</a> = {$topic}{/if}
{if isset($language)}<br />where <a href="./?a=languages">language</a> = {$language}{/if}
{if isset($version)}<br />where <a href="./?a=versions">version</a> = {$version}{/if}
<table id="wikis" class="tablesorter">
 <thead>
 <tr>
  <th>wiki &nbsp; v<span style="color:#888">iew </span>h<span style="color:#888">istory</span></th>
  <th>activity<br />index</th>
  <th>lang</th>  
  <th>topic</th>
  <th>network</th>
  <th>pages</th>
  <th>articles</th>
  <th>edits</th>
  <th>images</th>
  <th>users</th>
  <th>active<br />users</th>
  <th>admins</th>
  <th>MediaWiki<br />version</th>
  <th>last updated (UTC)</th>
 </tr>
 </thead>
 <tbody>
{foreach from=$wikis item=x}
 <tr>
  <td><a href="./?a=wiki&amp;wiki={$x.wiki|urlencode}"><b>{$x.wiki|truncate:24:''}</b></a> <a 
   target="wiki" href="{$x.protocol}://{$x.wiki}/"><img src="img/v.png" width="12" height="12" alt="view wiki"></a> <a 
   href="./?a=wiki.history&amp;wiki={$x.wiki|urlencode}"><img src="img/h.png" width="12" height="12" alt="history"></a>{if $enable_web_admin && $is_admin} <a 
   target="edit" href="./?a=edit&amp;wiki={$x.wiki|urlencode}"><img src="img/edit.png" width="12" height="12" alt="edit"></a> <a 
   href="./?a=tools&amp;wiki={$x.wiki|urlencode}"><img src="img/tools.png" width="12" height="12" alt="tools"></a>{/if}</td>
  <td class="right"><strong>{$x.aindex}</strong></td>
  <td><a href="./?a=wikis&amp;language={$x.language|urlencode}">{$x.language|truncate:6:''}</a></td>  
  <td><a href="./?a=wikis&amp;topic={$x.topic|urlencode}">{$x.topic}</a></td>
  <td><a href="./?a=wikis&amp;network={$x.network|urlencode}">{$x.network}</a></td>
  <td class="right">{$x.pages}</td>
  <td class="right">{$x.articles}</td>
  <td class="right">{$x.edits}</td>
  <td class="right">{$x.images}</td>
  <td class="right">{$x.users}</td>
  <td class="right">{$x.activeusers}</td>
  <td class="right">{$x.admins}</td>
  <td><a href="./?a=wikis&amp;version={$x.generator|urlencode}">{$x.generator|replace:'MediaWiki ':''|truncate:10:''}</a></td>
  <td>{$x.datetime_utc}</td>
 </tr>
{foreachelse}
 <tr>
  <td colspan="14">{if isset($error)}{$error}{else}No wikis found{/if}</td>
 </tr>
{/foreach}
 </tbody>
</table>
</div>