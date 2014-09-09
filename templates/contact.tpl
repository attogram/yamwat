{* **************************************

	Yamwat - Web Contact Form

************************************** *}
{include file='menu.tpl'}
<form action="./?a=contact" method="POST">
<div class="content">
{if $result}<h1>{$result}</h1>{/if}
<p>Contact {$system_name}:</p>
<p>Your Email: <input name="email" value="{$email}" type="text" size="40" /></p>
<p>Your Message:<br />
<textarea name="msg" rows="10" cols="50">{$msg}</textarea>
</p>
<p><input type="submit" value="Send message" /></p>
</div>
</form>