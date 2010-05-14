<?php
/*
 * This script is designed to operate outside the regular context of the WC, the path mentioned below can be used to point it to its files.
 */
$pathToWC = './../'; // this *must* end in a /, use ./ if the script is in the WC root, otherwise use an additional ../ for every directory it is below that.
include($pathToWC.'commonfunctions.php');
include($pathToWC.'../secure/db_config.php');
include($pathToWC.'quests/validatequest.php');

SetUpDB($db_hostname, $db_username, $db_password, $db_name);
date_default_timezone_set('UTC');
session_save_path($pathToWC.'sessions');
session_start();
if (!isset($_SESSION['totalq']))
{
    $_SESSION['totalq'] = "SQL Queries Performed:";
}
$script = (isset($_POST['script']) ? $_POST['script'] : '');

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
    <head>
        <meta name="author" content="PlaneShift MMORPG" />
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="'.$pathToWC.'./global.css" />
        <title>PlaneShift - Administrator Console</title>
    </head>
    <body>
        <div class="container">';
echo '          <p> Please note that this validator will give errors like "no such quest in database" when you try to complete/require any step of the quest itself, since, it is indeed not in the database yet when you use this script.<br />
                <form name="prospectscript" method="post" action="'.$_SERVER['PHP_SELF'].'">
                Quest Script:<br/><textarea name="script" rows="25" cols="80">'.$script.'</textarea><br />
                <input type="checkbox" name="show_lines">Show script lines?<br />
                <input type="submit" name="submit" value="submit"></form></p>';

if ($script != '') 
{
    append_log('<p class="error">');
    append_log("parsing script");
    parseScript(0, $script, isset($_POST['show_lines'])); // using 0 as ID is a bit "hackish" to signify the script is not in the database (script 0 does not exist).
    append_log("parsing script completed");
    append_log('</p>');
    echo $parse_log;
}

echo '</div><hr/>This is Debugging Information Only: '.($_SESSION['totalq']);
unset($_SESSION['totalq']);
echo '<div class="footer">
All material found here is (c) Atomic Blue.<br/>
</div>
</div> <!-- end container -->
</body>
</html>';
?>
