{* **************************************

	Yamwat - List of...

************************************** *}
{include file='menu.tpl'}
<link rel="stylesheet" type="text/css" href="css/table.style.css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
$.tablesorter.defaults.widgets = ['zebra'];
$.tablesorter.defaults.sortList = [[1,1]];
$("#list").tablesorter();
});
</script>
<div class="content"><b>Wiki {$list_name}</b>: {$list|@sizeof}
<table id="list" class="tablesorter" style="width:auto;">
 <thead>
 <tr>
  <th class="listof">{$list_name} &nbsp;&nbsp;</th>
  <th class="listof">count &nbsp;&nbsp;</th>
 </tr>
 </thead>
 <tbody>
{foreach from=$list item=x}
 <tr>
  <td class="listof"><a href="./?a=wikis&amp;{$url_name}={$x.name|urlencode}">{if $x.name}{$x.name}{else}(unknown){/if}</a></td>
  <td class="listof">{$x.count}</td>
 </tr>
{foreachelse}
{/foreach}
 </tbody>
</table>
</div>