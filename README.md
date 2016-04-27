Yamwat - Yet Another MediaWiki API Tool
======
![Yamwat logo](https://raw.github.com/attogram/yamwat/master/web/img/yamwat.88.31.png)

Version 2.7

* Yamwat is an open source project building <b>Y</b>et <b>A</b>nother <b>M</b>edia<b>W</b>iki <b>A</b>PI <b>T</b>ool. 
* Yamwat is written in PHP, uses a SQLite database, and has a plugin system for API calls.
* Use it from a command line interface, or a web interface built with SMARTY templates.

Project Info:
------
* Download latest version: https://github.com/attogram/yamwat/archive/master.zip
* Code Repository: https://github.com/attogram/yamwat


Requirements:
------
* PHP version 5.1.0 or higher
* PHP extensions: curl, json, PDO, pdo_sqlite


Install:
------
* install the code into a non web accessible directory
* copy ./config.sample.php to ./config.php
* copy ./config.cli.sample.php to ./config.cli.php
* copy ./web/config.web.sample.php to ./web/config.web.php
* edit ./config.php for your system setup
 * set `$config['system_name']` to your web site name (used in html titles, and USER_AGENT for API calls)
 * set `$config['system_version']` to your web site version (used in USER_AGENT for API calls)
 * set `$config['system_email']` to your contact email (used in USER_AGENT for API calls)
 * set `$config['system_url']` to your main web site URL (used in USER_AGENT for API calls)
 * set `$config['referer']` to your main web site URL (used in API calls)
 * enable/disable web admin interface: set `$config['enable_web_admin']` to TRUE or FALSE
 * enable web admin password: set `$config['web_admin_password']`  (default password is 'admin')
 * enable/disable debug mode: set `$config['debug']` to TRUE or FALSE
 * enable/disable SMARTY Template debug mode: set `$config['SMARTY_debug']` to TRUE or FALSE
* edit ./config.cli.php and set `$config['yamwat_home']` to the code directory
* edit ./web/config.web.php and set `$config['yamwat_home']` to the code directory
* If on unix:
 * chmod 777 ./templates_c
 * chmod 777 ./db
* move the ./web directory to a web accessible location


Command Line Interface:
------
<pre>
Usage:  php -f yamwat.php a=[action] [options]

Internal actions:
Create table  : a=create table=  ( wiki | wiki_history )
Add wiki      : a=add wiki=example.com api=/example/api.php [protocol=http] [network=] [topic=]
Delete wiki   : a=delete wiki=example.com
Edit wiki     : a=edit wiki=example.com [wiki_new=] [api=] [protocol=] [network=] [topic=]
List wikis    : a=wikis [network=] [topic=] [language=] [version=] [limit= (network|topic|language|version) ] [dir= (gte,gt,lt,lte)] [c=]
List a wiki   : a=wiki wiki=example.com
List topics   : a=topics
List networks : a=networks
List languages: a=languages
List versions : a=versions
Wiki history  : a=history wiki=example.com

Plugin actions:
Get siteinfo      : a=siteinfo wiki=example.com [save=1]
Get Search results: a=search wiki=example.com search=STRING [ns=] [limit=1]
Get Recent changes: a=recentchanges wiki=example.com [user=] [ns=] [limit=1]
Get Random Page   : a=random wiki=example.com [ns=] [limit=1]
Get API Help      : a=help wiki=example.com
Debug             : a=debug

Global options:
Debug messages     :  debug=1
Only error messages:  silent=1
</pre>


Changelog:
------
* Version 2.7
* Version 2.6 - moved to github
* Version 2.5 - 2013-01-13
 * auto load all plugins in the ./lib/plugin/ directory
* Version 2.4 - 2013-01-13
 * web contact form
 * admin edit action defaults to list of wikis
 * contrib: config setup, history counts
* Version 2.3 - 2013-01-13
 * allow sorting on activity index
 * various contrib scripts added
 * better error reporting on failed CURL get()
 * design updates on templates
* Version 2.2 - 2013.01.11
 * web icons for view, history, edit, tools
 * display fixes for activity index
 * db update to v2 contrib script
* Version 2.1 - 2013.01.10
 * activity index display corrections
 * ./contrib/ directory for misc. scripts
* Version 2.0 - 2013.01.10
 * design updates on wiki, wiki.history templates
 * refactoring, minor updates and fixes
* Version 1.9 - 2013.01.10
 * siteinfo plugin now saves activity index to database
* Version 1.8 - 2013.01.10
 * core refactoring and updates
 * new db field for activity index: wiki.aindex, wiki_history.aindex
* Version 1.7 - 2013.01.09
 * bugfix: show correct all-time activity index on wiki.history
* Version 1.6 - 2013.01.09
 * bugfix: allow deleting of topic or network - yamwat.core.php
* Version 1.5 - 2013.01.09
 * Yamwat Activity Index Version 0.2
 * bugfix: wikis list default where clause now properly set
 * web footer redesign, with 88x31 'powered by Yamwat' icon
 * minor updates
* Version 1.4 - 2013.01.08
 * move setup vars (yamwat_home, etc) into global $config
 * move smarty config vars into ./web/config.web.php
 * bugfix: wiki.history page now loads ok, even if no history in database
 * set curl connection timeout to 15 seconds, response timeout to 45 seconds
 * layout updates on template: web.history, footer
 * minor updates to message returns
* Version 1.3 - 2013.01.08
 * add get_activity_index() to yamwatCORE class (called in get_history())
 * add activity index to wiki.history template
 * move plugin_exists() to yamwatPLUGIN class
 * minor updates
* Version 1.2 - 2013.01.07
 * bugfix: cli and web config array initialization
* Version 1.1 - 2013.01.07
 * improved error checking when including config files
* Version 1.0 - 2013.01.07
 * moved cli/web specific $config settings to ./config.cli.php and ./web/config.web.php
 * created config distributions: ./config.dist.php, ./config.cli.dist.php, ./web/config.web.dist.php
 * added .gitignore for config files
 * remove List of... from home template
 * bugfix: show correct header after bad login attempt
* Version 0.9 - 2013.01.07
 * html title variable added to header template
 * header template now called individually for each action
 * 404 header sent for non-existing urls
 * bugfix: CLI siteinfo now saves to database, defaults to save=1
 * bugfix: wiki.history web template now shows most up-to-date network, topic, language, version
* Version 0.8 - 2013.01.07
 * add password protection to web admin actions (admin, tools, edit, add, all API Plugin actions)
 * add where limits on wikis list - where [pages,edits,images,users,activeusers,admins] [>=,>,<,<=] #
 * add save option to siteinfo plugin web interface
* Version 0.7 - 2013.01.06
 * change interface naming scheme: site = wiki, sites = wikis
 * Add counts of wikis, topics, networks, languages, versions, history entries
 * minor fixes and updates
* Version 0.6 - 2013.01.05 
 * new database schema: all TEXT columns: NOT NULL default ''
 * new database table: 'site_history'
 * siteinfo plugin now updates table 'site' and adds to table 'site_history'
 * new CLI and WEB actions for wiki history and diff stats
 * tablesorter on web lists of [topics, networks, languages, versions, history]
 * result messages for web admin tools: add site, edit[/delete] site
 * minor fixes and updates
* Version 0.5 - 2013.01.04 - add list of [topics, networks, languages, versions] to CLI and WEB interfaces, minor fixes and updates
* Version 0.4 - 2013.01.03 - add 'topic' column to site table, wiki info, wikis list, add site, edit site
* Version 0.3 - 2013.01.02 - config files for CLI and WEB, auto create sites table if db file does not exist, refactoring, cleaning
* Version 0.2 - 2013.01.01 - templating menus, clean out unused functions, minor fixes and updates
* Version 0.1 - 2013.01.01 - Initial release


Files:
------
<pre>
./license.txt - Open Source licenses
./readme.md - This file
./config.dist.php - System configuration variables (distribution version)
./config.cli.dist.php - Command Line Interface configuration variables (distribution version)
./yamwat.php - Command Line Interface

./lib/yamwat.core.php - Yamwat CORE classes
./lib/yamwat.cli.php - Yamwat CLI classes
./lib/yamwat.web.php - Yamwat WEB classes

./lib/plugin/debug.php - Yamwat Plugin: System Debug
./lib/plugin/help.php - Yamwat Plugin: Get from API: help
./lib/plugin/random.php - Yamwat Plugin: Get from API: random
./lib/plugin/recentchanges.php - Yamwat Plugin: Get from API: random
./lib/plugin/search.php - Yamwat Plugin: Get from API: opensearch
./lib/plugin/siteinfo.php - Yamwat Plugin: Get from API: siteinfo

./lib/curl/curl.php - Curl library main class
./lib/curl/curl_response.php - Curl library response class

./lib/Smarty/Smarty.class.php - SMARTY template system
./lib/Smarty/debug.tpl - Debug for SMARTY templating (enable in ./config.php: $config['SMARTY_debug'] = TRUE; )
./lib/Smarty/plugins/* - SMARTY standard plugins
./lib/Smarty/sysplugins/* - SMARTY standard sysplugins

./db/ - Database directory (on unix: chmod 777)  (set in ./config.php: $config['db_file'] = 'db/db.sqlite'; )

./templates/header.tpl - Web template: Header
./templates/footer.tpl - Web template: Footer
./templates/home.tpl - Web template: Home page
./templates/menu.tpl - Web template: Main menu
./templates/wiki.tpl - Web template: Wiki - all info
./templates/wiki.history.tpl - Web template: Wiki history log
./templates/wikis.tpl - Web template: List of Wikis - statistics and groupings
./templates/list.tpl - Web template: List of (topics, networks, languages, versions)

./templates/admin/login.tpl - Web Admin template:  Admin login
./templates/admin/home.tpl - Web Admin template:  Admin Home page
./templates/admin/add.tpl - Web Admin template:  Add a site
./templates/admin/edit.tpl - Web Admin template:  Edit a site (also delete a site)
./templates/admin/tools.tpl - Web Admin template:  Tools (plugins)
./templates/admin/result.tpl - Web Admin template: Result page for plugins

./templates_c/ - SMARTY compiled templates directory (on unix: chmod 777)

./web/config.web.dist.php - Web Interface configuration variables (distribution version)
./web/index.php - Web Interface

./web/css/style.css - main CSS styles
./web/css/table.style.css - CSS for tablesorter

./web/img/asc.gif - Ascending icon for tablesorter
./web/img/bg.gif - up/down icon for tablesorter
./web/img/desc.gif - Descending icon for tablesorter
./web/img/v.png - View Wiki 12x12 icon
./web/img/h.png - Wiki History 12x12 icon
./web/img/edit.png - Edit Wiki 12x12 icon
./web/img/tools.png - Tools 12x12 licon
./web/img/yamwat.88.31.ping - Powered by Yamwat 88x31 icon
./web/img/yamwat.logo.png - Yamwat 100x100 icon

./web/js/jquery.js - jQuery library
./web/js/jquery.tablesorter.min.js  - jQuery tablesorter plugin
</pre>


License:
------
Yamwat, Yet Another MediaWiki API Tool: https://github.com/attogram/yamwat

Copyright (c) 2013 Yamwat contributors
 
Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


Additional Licenses:
------
Yamwat includes other open source licensed software.
See license.txt for more information.
