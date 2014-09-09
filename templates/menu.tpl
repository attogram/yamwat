{* **************************************

	Yamwat - Main Menu

************************************** *}
<div class="menu"><a href="./">{$system_name}</a>
&nbsp;&nbsp; <a href="./?a=wikis">wikis</a>
&nbsp;&nbsp; <a href="./?a=topics">topics</a>
&nbsp;&nbsp; <a href="./?a=networks">networks</a>
&nbsp;&nbsp; <a href="./?a=languages">languages</a>
&nbsp;&nbsp; <a href="./?a=versions">versions</a>
{if $enable_contact_form} &nbsp;&nbsp;&nbsp; <a href="./?a=contact">contact</a>{/if}
{if $enable_web_admin}{if $is_admin} 
&nbsp;&nbsp;&nbsp; [ <a href="./?a=admin">admin</a> 
&nbsp;&nbsp; <a href="./?a=tools">tools</a>
&nbsp;&nbsp; <a href="./?a=edit">edit</a>
&nbsp;&nbsp; <a href="./?a=add">add</a>
&nbsp;&nbsp; <a href="./?a=logoff">logoff</a> ]
{else}
{/if}{/if}
</div>
