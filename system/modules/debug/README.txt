TYPOlight debugger
==================

0. Preamble
1. Installation
2. Usage & examples
3. Reporting bugs
4. Known issues

0. Preamble
==================
Currently it was only possible to debug TYPOlight via print_r(), var_dump() and echo.
This bears several problems, as the html code is not valid anymore and the debugging 
code had to be stripped from the release version.
Aside from that we have the global array TL_DEBUG in TYPOlight which is a step into the 
right direction but only a step.
I was pretty unsatisfied by using these techniques and stumbled across FirePHP.
So we have an idea, the tools and a bunch of hours to make it work and the result is the 
TYPOlight debugger.
It hooks directly into the TYPOlight workflow and allows you to debug items and trace the 
execution workflow.

Feature requests are always welcome on the tracker (See reporting bugs).

Thanks go out to Stefan "lindesbs" Lindecke for fruitful discussion
and Leo Unglaub for allowing me to test on his XAMPP and long debugging sessions.

I wish you fun with this extension.

1. Installation
==================
Simply copy the debugger to your TYPOlight installation like any other extension.
Then go to the backend and configure it according to your needs.

You must tick the checkbox for "enable debugger" AND all users for who the debugger 
shall be activated as otheriwse the debugger will not start up for security reasons.

On client side (Browser) it needs FireBug (at least 1.5.0) which can be fetched from [1]
It also needs the firePHP plugin for FireBug, which you can get from [2].

[1] http://getfirebug.com/releases/firebug/1.5X/
[2] https://addons.mozilla.org/en-US/firefox/addon/6149

2. Usage & examples
==================

You can use the debugger in many ways aside from the predefined data.
So you can log directly from your extensions via the already known way of using the global logging array.

if ($GLOBALS['TL_CONFIG']['debugMode'])
	$GLOBALS['TL_DEBUG']['CHANNEL']['MESSAGE']=$SOMETHING;
	
Where:
	CHANNEL is one out of 'log', 'info', 'warn' and 'error'
	MESSAGE is your custom label you want to use.
	$SOMETHING is any value you want to have logged. May it be an object, an array or simply a string value.

You can also append "[]" at the end, to have the data attac hed as a new element in the virtual array,
for greater compatibility with the original debug array.
	
Examples:

logging of an object with an info icon.
-snip-
global $objPage;
if ($GLOBALS['TL_CONFIG']['debugMode'])
	$GLOBALS['TL_DEBUG']['info']['current page object']=$objPage;
-snap-

logging of some text, without icon.
-snip-
if ($GLOBALS['TL_CONFIG']['debugMode'])
	$GLOBALS['TL_DEBUG']['something you should know']='42 is the answer.';
-snap-

Trigger a warning in PHP which will get routed to the FirePHP console.
-snip-
	trigger_error('warning via normal PHP way', E_USER_WARNING);
-snap-

3. Reporting bugs
==================

Please report bugs and feature requests on the tracker
http://tracker.cyberspectrum.de/ (project tl_debug).
You will have to register there in order to submit issues 
to protect me from spammers.

4. Known issues
==================

Sometimes there is an exception being thrown under XAMPP in the backend.
The problem did not occur under my Linux Environment until now.
If you run into this problem, please provide your phpinfo() and php.ini along with a list of the active modules 
in TYPOlight so we can hunt it down.
One definate origin of this Exception is in the database drivers which try to close resources in their __destruct().
The problem herein is, that those resources got already freed by PHP on it's own and therefore cause a warning and
finally an Exception from within an destructor, which is illegal from within destructors.
Update: I hope this Exception is finally tracked down and the bug squished. After long debugging hours with tracing 
into php itself in CGI mode, we (leo-unglaub and I) found out that the creation order of objects in TYPOlight DOES 
matter. Make sure you never request the database before instanciating the User (Backend or Frontend, whichever is 
needed according to the mode). If you do so, the Database object will get freed before the User class can save back 
it's Session data (why the session data is stored in PHP and the database is beyond my understanding though).
To achieve this, the configuration now automatically writes itself into system/config/initconfig.php at the beginning.
