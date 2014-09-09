{* **************************************

	Yamwat - Footer

************************************** *}

<footer>
<div class="footer">
<div style="text-align:right;float:right;"><a target="yamwat" href="{$core_url}"><img 
src="img/yamwat.88.31.png" width="88" height="31"
alt="powered by Yamwat {$core_version}" style="float:right; padding: 20px 20px 20px 20px;"></a>
<br class="clear" /><span style="font-size:80%;">page generated in {$generation_time} seconds</span> &nbsp; &nbsp;
</div>
<a href="./" style="font-weight:bold;">{$system_name}</a> @ {$time_utc} UTC<br /><br />
Powered by <a target="yamwat" href="{$core_url}">Yamwat</a> {$core_version} (Yet Another MediaWiki API Tool)<br />
</div>
</footer>
{if $debug}<pre>DEBUG: {$debug|@print_r:true}</pre>{/if}
{if !$is_admin}
<script type="text/javascript"><!--
google_ad_client = "ca-pub-6538490817784091";
/* Daily Wikis - Footer */
google_ad_slot = "6142574272";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-8640608-4']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
{/if}
</body></html>


