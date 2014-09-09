{* **************************************

	Yamwat - Home page

************************************** *}
{include file='menu.tpl'}
<div class="content"><br />
Welcome to <b>Daily Wikis</b>: tracking MediaWiki wikis on the web.<br /><br />
<ul>
<li><a href="./?a=wikis">Wikis</a> ({if $wikis_count}{$wikis_count}{else}0{/if})</li>
<li><a href="./?a=topics">topics</a> ({if $topics_count}{$topics_count}{else}0{/if})</li>
<li><a href="./?a=networks">networks</a> ({if $networks_count}{$networks_count}{else}0{/if})</li>
<li><a href="./?a=languages">languages</a> ({if $languages_count}{$languages_count}{else}0{/if})</li>
<li><a href="./?a=versions">versions</a> ({if $versions_count}{$versions_count}{else}0{/if})</li>
</ul>
<br />
status:<br />
<ul>
<li>History: {if $history_count}{$history_count}{else}0{/if} entries</li>
<li>Last update: {if $system_last_update}{$system_last_update}{else}(none){/if}</li>
</ul>
<br />
</div>
