{* **************************************

	Yamwat - Wiki History

************************************** *}
{include file='menu.tpl'}
<link rel="stylesheet" type="text/css" href="css/table.style.css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(document).ready(function() { 
$.tablesorter.defaults.widgets = ['zebra'];
$.tablesorter.defaults.sortList = [[4,1]];
$("#history").tablesorter();
});
</script>
<div class="content"><p>
Wiki History: <a href="./?a=wiki&amp;wiki={$wiki|urlencode}"><strong>{$wiki|escape}</strong></a>  
&nbsp; <a target="wiki" href="{$siteinfo.protocol}://{$wiki|urlencode}/"><img src="img/v.png" width="12" height="12" alt="view wiki"></a>
{if $enable_web_admin && $is_admin} 
 <a href="./?a=edit&amp;wiki={$wiki|urlencode}"><img src="img/edit.png" width="12" height="12" alt="edit"></a> 
 <a href="./?a=tools&amp;wiki={$wiki|urlencode}"><img src="img/tools.png" width="12" height="12" alt="tools"></a>
{/if}</p>
<p>
topic   : <a href="./?a=wikis&amp;topic={$siteinfo.topic|urlencode}">{if $siteinfo.topic}{$siteinfo.topic}{else}?{/if}</a><br />
network : <a href="./?a=wikis&amp;network={$siteinfo.network|urlencode}">{if $siteinfo.network}{$siteinfo.network}{else}?{/if}</a><br />
language: <a href="./?a=wikis&amp;language={$siteinfo.language|urlencode}">{if $siteinfo.language}{$siteinfo.language}{else}?{/if}</a><br />
version : <a href="./?a=wikis&amp;version={$siteinfo.generator|urlencode}">{if $siteinfo.generator}{$siteinfo.generator}{else}?{/if}</a><br />
</p>
<p>
Activity Index: <strong>{if $siteinfo.aindex}{$siteinfo.aindex}{else}0{/if}</strong> 
(from {$history|sizeof} entries spanning {if $history.0.days}{$history.0.days}{else}0{/if} days)
</p>
<table id="history" class="tablesorter">
 <thead>
 <tr>
  <th class="right">+/-</th><th>activity<br />index</th>
  <th class="right">diff<br />activity</th><th class="right">diff<br />seconds</th>
  <th class="right">time (UTC)</th>    
  <th class="right">+/-</th><th>pages</th>
  <th class="right">+/-</th><th>articles</th>
  <th class="right">+/-</th><th>edits</th>
  <th class="right">+/-</th><th>images</th>
  <th class="right">+/-</th><th>users</th>
  <th class="right">+/-</th><th>active<br />users</th>
  <th class="right">+/-</th><th>admins</th>
 </tr>
 </thead>
 <tbody>
{foreach from=$history item=x}
 <tr>
  <td class="right"><em>{if $x.aindex_diff > 0}+{/if}{$x.aindex_diff}</em></td><td class="right"><strong>{$x.aindex}</strong></td>
  <td class="right">{$x.activity_index}</td><td class="right"><em>{$x.datetime_diff}</em></td>     
  <td class="right">{$x.datetime_utc}</td>
  <td class="right"><em>{if $x.pages_diff > 0}+{/if}{$x.pages_diff}</em></td><td>{$x.pages}</td>
  <td class="right"><em>{if $x.articles_diff > 0}+{/if}{$x.articles_diff}</em></td><td>{$x.articles}</td>
  <td class="right"><em>{if $x.edits_diff > 0}+{/if}{$x.edits_diff}</em></td><td>{$x.edits}</td>
  <td class="right"><em>{if $x.images_diff > 0}+{/if}{$x.images_diff}</em></td><td>{$x.images}</td>
  <td class="right"><em>{if $x.users_diff > 0}+{/if}{$x.users_diff}</em></td><td>{$x.users}</td>
  <td class="right"><em>{if $x.activeusers_diff > 0}+{/if}{$x.activeusers_diff}</em></td><td>{$x.activeusers}</td>
  <td class="right"><em>{if $x.admins_diff > 0}+{/if}{$x.admins_diff}</em></td><td>{$x.admins}</td>
 </tr>
{foreachelse}
 <tr>
  <td colspan="19">No history found</td>
 </tr>
{/foreach}
 </tbody>
</table>
</div>
