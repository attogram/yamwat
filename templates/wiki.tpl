{* **************************************

	Yamwat - Wiki

************************************** *}
{include file='menu.tpl'}
<div class="content"><p>
Wiki Info: <strong>{$siteinfo.wiki|escape}</strong> 
&nbsp; <a target="wiki" href="{$siteinfo.protocol}://{$siteinfo.wiki|urlencode}/"><img src="img/v.png" width="12" height="12" alt="view wiki"></a>
  <a href="./?a=wiki.history&amp;wiki={$siteinfo.wiki|urlencode}"><img src="img/h.png" width="12" height="12" alt="history"></a> 
{if $enable_web_admin && $is_admin} 
 <a href="./?a=edit&amp;wiki={$siteinfo.wiki|urlencode}"><img src="img/edit.png" width="12" height="12" alt="edit"></a> 
 <a href="./?a=tools&amp;wiki={$siteinfo.wiki|urlencode}"><img src="img/tools.png" width="12" height="12" alt="tools"></a>
{/if}</p>
<p>
topic   : <a href="./?a=wikis&amp;topic={$siteinfo.topic|urlencode}">{if $siteinfo.topic}{$siteinfo.topic}{else}?{/if}</a><br />
network : <a href="./?a=wikis&amp;network={$siteinfo.network|urlencode}">{if $siteinfo.network}{$siteinfo.network}{else}?{/if}</a><br />
language: <a href="./?a=wikis&amp;language={$siteinfo.language|urlencode}">{if $siteinfo.language}{$siteinfo.language}{else}?{/if}</a><br />
version : <a href="./?a=wikis&amp;version={$siteinfo.generator|urlencode}">{if $siteinfo.generator}{$siteinfo.generator}{else}?{/if}</a><br />
</p>
<p>
Activity Index: <strong>{if $siteinfo.aindex}{$siteinfo.aindex}{else}0{/if}</strong> 
(from <a href="./?a=wiki.history&amp;wiki={$siteinfo.wiki|urlencode}">{$history_count} history entries</a>)
</p>
<br />
<table>
 <tr><td>base:</td><td><a target="wiki" href="{$siteinfo.base}">{$siteinfo.base|urldecode}</a></td></tr>
 <tr><td>sitename:</td><td>{$siteinfo.sitename}</td></tr>
 <tr><td>wikiid:</td><td>{$siteinfo.wikiid}</td></tr>
 <tr><td>language:</td><td><a href="./?a=wikis&amp;language={$siteinfo.language|urlencode}">{$siteinfo.language}</a></td></tr>
 <tr><td>rights:</td><td>{$siteinfo.rights}</td></tr> 
 <tr><td>version:</td><td><a href="./?a=wikis&amp;version={$siteinfo.generator|urlencode}">{$siteinfo.generator}</a></td></tr>
 <tr><td>network:</td><td><a href="./?a=wikis&amp;network={$siteinfo.network|urlencode}">{$siteinfo.network}</a></td></tr>
 <tr><td>topic:</td><td><a href="./?a=wikis&amp;topic={$siteinfo.topic|urlencode}">{$siteinfo.topic}</a></td></tr>
 <tr><td>wiki time:</td><td>{$siteinfo.time}</td></tr>
 <tr><td>sys time:</td><td>{$siteinfo.datetime_utc} UTC</td></tr>
</table>

<p>Wiki Stats:</p>
<table>
 <tr><td>pages:</td><td>{$siteinfo.pages}</td></tr>
 <tr><td>articles:</td><td>{$siteinfo.articles}</td></tr>
 <tr><td>edits:</td><td>{$siteinfo.edits}</td></tr>
 <tr><td>images:</td><td>{$siteinfo.images}</td></tr>
 <tr><td>users:</td><td>{$siteinfo.users}</td></tr>
 <tr><td>activeusers:</td><td>{$siteinfo.activeusers}</td></tr>
 <tr><td>admins:</td><td>{$siteinfo.admins}</td></tr>
 <tr><td>jobs:</td><td>{$siteinfo.jobs}</td></tr>
</table>

<p>Wiki info:</p>
<table>
 <tr><td>mainpage:</td><td>{$siteinfo.mainpage}</td></tr>
 <tr><td>server:</td><td><a target="wiki" href="{$siteinfo.server}">{$siteinfo.server}</a></td></tr>
 <tr><td>api:</td><td>{$siteinfo.api}</td></tr>
 <tr><td>protocol:</td><td>{$siteinfo.protocol}</td></tr>
 <tr><td>case:</td><td>{$siteinfo.case}</td></tr>
 <tr><td>phpversion:</td><td>{$siteinfo.phpversion}</td></tr>
 <tr><td>phpsapi:</td><td>{$siteinfo.phpsapi}</td></tr>
 <tr><td>dbtype:</td><td>{$siteinfo.dbtype}</td></tr>
 <tr><td>dbversion:</td><td>{$siteinfo.dbversion}</td></tr>
 <tr><td>rev:</td><td>{$siteinfo.rev}</td></tr>
 <tr><td>fallback8bit<br />Encoding:</td><td>{$siteinfo.fallback8bitEncoding}</td></tr>
 <tr><td>writeapi:</td><td>{$siteinfo.writeapi}</td></tr>
 <tr><td>timezone:</td><td>{$siteinfo.timezone}</td></tr>
 <tr><td>timeoffset:</td><td>{$siteinfo.timeoffset}</td></tr>
 <tr><td>articlepath:</td><td>{$siteinfo.articlepath}</td></tr>
 <tr><td>scriptpath:</td><td>{$siteinfo.scriptpath}</td></tr>
 <tr><td>script:</td><td>{$siteinfo.script}</td></tr>
 <tr><td>variant<br />articlepath:</td><td>{$siteinfo.variantarticlepath}</td></tr>
</table>

</div>
