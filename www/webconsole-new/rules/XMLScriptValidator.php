<?php

function XMLScriptValidator()
{
    // block unauthorized access
    if (isset($_POST['commit']) && !checkaccess('natres', 'read')) 
    {
        echo '<p class="error">You are not authorized to validate scripts</p>';
        return;
    }
    // name check
    $name = '';
    if (isset($_GET['name']))
    {
        $name = escapeSqlString($_GET['name']);
    }
    else
    {
        echo '<p class="error"> no name supplied for script.</p>';
        return;
    }
    
    // we are parsing progression events.
    if ($_GET['type'] == 'progression_events')
    {
        $sql = "SELECT event_script FROM progression_events WHERE name='$name'";
        $result = mysql_query2($sql);
        $row = fetchSqlAssoc($result);
        
        echo '<p>Loading event script "'.$name.'".</p>';
        set_error_handler('HandleXmlError');
        $dom = new DOMDocument;
        $dom->loadXML($row['event_script']);
        echo '<p>Done loading event script, now validating.</p>';
        if ($dom->relaxNGValidate('./rules/ProgressionScript.rng')) 
        {
            echo "<p>This document is valid!</p>\n";
        }
        else 
        {
            echo '<p class="error">This document is not valid</p>';
        }
        restore_error_handler();
    }
}

// custom error handler, because loadXML returns PHP warnings instead of other things, we need to catch and print these, because no production 
// server actually has report warnings on.
function HandleXmlError($errno, $errstr, $errfile, $errline)
{
    if ($errno==E_WARNING && ((substr_count($errstr, "DOMDocument::loadXML()") > 0) || (substr_count($errstr, "DOMDocument::relaxNGValidate()") > 0)))
    {
        echo '<p class="error">'.$errstr.'</p>';
    }
    else
    { //Returning false in function HandleXmlError() causes a fallback to the default error handler.
        return false;
    }
}

?>