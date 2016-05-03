<?php 

/**
*   Planeshift quest script validator
*   Author: G. Hofstee
*/

$parse_log = '';
$line_number = 0;
$hideWarnings = false;
$currentScript = '';

function validatequest()
{
    if(checkaccess('quests', 'read'))
    {
        global $parse_log;
        $id = (isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : 0)); // If an ID is posted, use that, otherwise, use GET, if neither is available, use 0.
        $scriptId = (isset($_POST['script_id']) ? $_POST['script_id'] : (isset($_GET['script_id']) ? $_GET['script_id'] : ''));
        $warnCheck = (isset($_POST['noWarnings']) ? 'checked="checked"' : '');
        $warnQNCheck = (isset($_POST['noQNWarnings']) ? 'checked="checked"' : '');
        $showLinesCheck = (isset($_POST['showLines']) ? 'checked="checked"' : '');

        echo '
<p>show script lines means it will show all lines it found in the script and number them (so you can look at what errors belong
to what line in your browser).</p>
<form method="post" action="./index.php?do=validatequest&amp;id='.$id.'">
    <table>
        <tr><td>Quest ID: (use -1 for KA scripts)</td><td><input type="text" name="id" id="questId" value="'.$id.'" /></td></tr>
        <tr id="scriptRow"><td>Script ID: (KA scripts only)</td><td><input type="text" id="scriptId" name="script_id" value="'.$scriptId.'" /></td></tr>
        <tr><td><input type="checkbox" name="showLines" ' . $showLinesCheck . ' />Show script lines?</td><td></td></tr>
        <tr><td><input type="checkbox" name="noWarnings" ' . $warnCheck . ' />Hide Warnings?</td><td></td></tr>
        <tr><td><input type="checkbox" name="noQNWarnings" ' . $warnQNCheck . ' />Hide "No QuestNote" Warnings?</td><td></td></tr>
        <tr><td><input type="submit" name="submit" value="submit" /></td><td></td></tr>
    </table>
</form>
<script type="text/javascript">//<![CDATA[
    // this function hides the script ID field if quest ID is not -1 (script ID is only relevant to KA scripts, which have a quest ID of -1.
    function changeQuestId()
    {
        if (document.getElementById("questId").value == "-1")
        {
            document.getElementById("scriptRow").style.display = "table-row";
        }
        else
        {
            document.getElementById("scriptRow").style.display = "none";
        }
    }
    document.getElementById("questId").addEventListener("input", changeQuestId);
    changeQuestId(); // call it once to set innitial hiding/display of the field in accordance to the actual value.
//]]></script>
';

        if(isset($_POST['submit']))
        {
            if (is_numeric($id))
            {
                parseScripts($id, $scriptId, isset($_POST['showLines']), isset($_POST['noWarnings']), isset($_POST['noQNWarnings']));
            }
        }
        echo $parse_log;
    }
    else
    {
        echo '<p class="error">You are not authorized to view this page.</p>';
    }
}



// quest_id is not unique, (KA scripts for example, but others too are not enforced to be unique.
// So we need to collect them all, and then handle the actual script the next method
function parseScripts($questId, $scriptId, $showLines, $hideWarnings, $hideQNWarnings) 
{
    $condition = ($questId == -1 && is_numeric($scriptId) ? "id = '$scriptId'" : "quest_id = '$questId'");
    $result = mysql_query2("SELECT id, script FROM quest_scripts WHERE $condition"); 
    if (sqlNumRows($result) < 1)
    {
        echo '<p class="error">Error: no quest found with ID '.$questId.($questId != -1 ? '' : ' and script ID '.$scriptId).'</p>';
        return;
    }
    while ($row = fetchSqlAssoc($result))
    {
        $id = $row['id'];
        append_log('<p class="error">');
        append_log("parsing script # $id with Quest ID $questId"); 
        parseScript($questId, $row['script'], $showLines, $hideWarnings, $hideQNWarnings, $id);
        append_log("parsing script # $id with Quest ID $questId completed");
        append_log('');
        // KA scripts (quest id -1) have a different edit page.
        if ($questId == -1)
        {   // notice that $id is obtained for this sql query, and contains the script_id, while scriptId was submitted by the user and may be blank to parse all KA scripts.
            append_log('<a href="./index.php?do=ka_scripts&amp;sub=Edit&amp;areaid='.$id.'">Edit this script</a>');
        }
        else
        {
            append_log('<a href="./index.php?do=editquest&amp;id='.$questId.'">Edit this script</a>');
        }
        append_log('</p>');
    }
}

function parseScript($quest_id, $script, $show_lines, $hideWarnings, $hideQNWarnings, $scriptId, $quest_name='') 
{
    $line = '';
    $p_count = 0;
    $m_count = 0;
    $npc_name = '';
    $step = 1;
    $total_steps = count(explode("\n...", $script)); // notice we count the number of chuncks the script gets split into, not the number of ... lines.
    $assigned = false; // to check if the quest is already assigned.
    global $line_number;
    $line_number = 0;
    global $currentScript;
    $currentScript = $script;
    $GLOBALS['hideWarnings'] = $hideWarnings;
    $variablesTracker = array('set' => array(), 'unset' => array()); // creates a 2d array to store all set and unset variable commands in.
    $ready_for_complete = false; // this var is used to see if there has been an "NPC: trigger before we use the "complete quest" command. without it, the server crashes on loadquest.
    $seen_npc_triggers = false; // this variable is used to see if there has been any "NPC:" trigger since the last P:
    $seen_menu_triggers = false; // this variable is used to determine if this script uses at least 1 menu: tag (if it does, they must match P: tags 1:1)
    $quest_note_found = false; // this variable checks if there is a quest note for each step and not more than one
    $pStar = false;
    $menuInputBox = false;
    $seenTripleDot = false;
    
    if($show_lines)
    {
        echo "<br />\n";
        echo "<br />\n";
        echo "Quest ID: $quest_id ".($quest_id == -1 ? "and Script ID: $scriptId " : '')."<br />\n";
    }
    
    while(getNextLine($line, $script)) 
    {
        if($show_lines)
        {
            echo "$line_number: ".htmlentities($line)." <br />\n"; // debug line, shows you all the lines of the script.
        }
        if(iconv('utf-8', 'ISO-8859-1//IGNORE', $line) != $line || iconv('utf-8', 'ISO-8859-1//IGNORE', $line) === false)
        {
            append_log("Warning: illegal character (like smart quotes and the like) found on line $line_number");
        }
        if(strncasecmp($line, '#', 1) === 0) //comment line
        {
            continue; //ignore comment lines
        }
        elseif (strncasecmp($line, 'P:', 2) === 0) // P: trigger
        {
            $seenTripleDot = false;
            // If there was a previous set, the NPC: part should have reduced p/m_count to 0.
            if ($p_count > 0)  
            {
                append_log("Parse Error: there have been more P: or player than npc: tags before $line_number");
            }
            if ($m_count > 0) // there should always be a P: tag before every new set of menu: tags, so we can check this here.
            {
                append_log("Parse Error: there have been more Menu: than npc: tags before $line_number");
            }
            if ($seen_menu_triggers && $pStar && !$menuInputBox) // if there was a P: * tag without Menu: ?= tag, it is likely a mistake.
            {
                append_log("Warning: found a 'P: *' entry without 'Menu ?=' before $line_number");
            }
            
            $pStar = $menuInputBox = $seen_npc_triggers = $seen_menu_triggers = false;
            $count = $m_count = $p_count = 0;

            if (strpos($line, 'P: *') !== false)
            {
                $pStar = true;
            }
            if (getTriggerCount($line, 'P:', $count) === false) //get the amount of P: tags
            {
                append_log("Parse Error: P: with no text on line $line_number");
            }
            else // $count is filled by a side-effect of the getTriggerCount.
            {
                $p_count = $count;
            }
            // store P: for comparing with NPC: triggers
            checkVariables($line, 'p');
        }
        elseif (strncasecmp($line, 'Menu:', 5) === 0) // Menu: trigger
        {
            $seenTripleDot = false;
            $count = 0;
            if ($seen_npc_triggers) 
            {
                append_log("Parse Error: found a Menu: trigger following an NPC: trigger on line $line_number");
            }
            if ($seen_menu_triggers) 
            {
                append_log("Parse Error: found two sets of Menu: triggers before line $line_number");
            }
            if (getTriggerCount($line, 'Menu:', $count) === false)
            {
                append_log("Parse Error: Menu: with no text on line $line_number");
            }
            else // $count is filled by a side-effect of the getTriggerCount.
            {
                $m_count = $count;
            }
            if (strpos($line, '?=') !== false)
            {
                $menuInputBox = true;
            }
            
            $seen_menu_triggers = true;
            checkVariables($line, 'menu');
        }
        elseif (strpos($line, ":") !== false) // NPC_NAME: trigger, check for content, and match with the amount of P: triggers
        {
            // if a line starts with questnote, the : was likely added as a mistake since no NPCs are named "questnote". Send error.
            if(strncasecmp($line, 'QuestNote', 9) === 0)
            {
                append_log("Parse Error: found QuestNote and a colon ':' on the same line, the QuestNote command can not contain colons at line $line_number");
                continue;
            }
            $seenTripleDot = false;
            // Every P: and NPC: combo should be unique, we don't check this atm. (This means a trigger may not already exist, requires parsing of all scripts.)
            $count = 0;
            $seen_npc_triggers = true;
            $temp_name = substr($line, 0, strpos($line, ':'));
            if (strpos($npc_name, $temp_name) === false) // new npc name
            {
                if (stripos($npc_name, $temp_name) !== false) // single letter, or shorthand in the wrong case.
                {
                    // no error, we don't care, just need to avoid it going into the else code. :)
                }
                else
                {
                    $npc_name = $temp_name;
                    if ($quest_id == -1 && $npc_name == 'general')
                    {
                        // valid situation, general means all npcs, does not exist in database.
                    }
                    elseif (validate_npc($npc_name) === false) 
                    {
                        append_log("Parse Error: invalid NPC name: $npc_name at line $line_number");
                    }
                }
            }
            
            if(getTriggerCount($line, $temp_name.':', $count) === false) //get the amount of NPC: tags .':' adds the ':' which is not included in the name.
            {
                append_log("Parse Error: $temp_name:  with no text on line $line_number");
            }
            $p_count -= $count; // substract the amount of npc: tags from the P: count, they should match 1 on 1 .
            $m_count -= $count; // substract the amount of npc: tags from the P: count, they should match 1 on 1 .
            if ($p_count < 0) 
            {
                append_log("Parse Error: there are more $npc_name: triggers than there are P: or player triggers before line $line_number");
            }
            // Menu: if "officially" optional, so if there are none, it is valid too (should be old quests only). If ?= was seen in a menu, the count doesn't matter.
            if ($seen_menu_triggers && $m_count < 0 && !$menuInputBox) 
            {
                append_log("Parse Error: there are more $npc_name: triggers than there are Menu: triggers before line $line_number");
            }
            checkVariables($line, 'npc');
            $ready_for_complete = true;
        }
        elseif(strncasecmp($line, 'Player ', 7) === 0) // player does something
        {
            $seenTripleDot = false;
            // If there was a previous set, the NPC: part should have reduced p/m_count to 0.
            if ($p_count > 0)  
            {
                append_log("Parse Error: there have been more P: or player than npc: tags before $line_number");
            }
            if ($m_count > 0) // there should always be a P: tag before every new set of menu: tags, so we can check this here.
            {
                append_log("Parse Error: there have been more Menu: than npc: tags before $line_number");
            }
            if ($seen_menu_triggers && $pStar && !$menuInputBox) // if there was a P: * tag without Menu: ?= tag, it is likely a mistake.
            {
                append_log("Warning: found a 'P: *' entry without 'Menu ?=' before $line_number");
            }
            
            $pStar = $menuInputBox = $seen_npc_triggers = $seen_menu_triggers = false;
            $count = $m_count = $p_count = 0;
            
            handle_player_action($line);
            $p_count++; // this is a valid trigger too for npc:
            checkVariables($line, 'player');
        }
        elseif(strncasecmp($line, 'QuestNote ', 10) === 0) // Quest Note
        {
            if ($quest_note_found) 
            {
                append_log("Parse Error: there already is a QuestNote defined in the same step $step before line $line_number.");
            }
            if (trim(substr($line, 10)) == '')
            {
                append_log("Parse Error: empty Questnote at line $line_number.");
            }
            elseif (strlen(trim($line)) < 15) 
            {
                append_log("Warning: QuestNote is too short at line $line_number.");
            }
            $quest_note_found = true;
        }
        elseif(strncasecmp($line, '...', 3) === 0) // New Step
        {
            $seenTripleDot = true;
            if ($p_count > 0) 
            {
                append_log("Parse Error: there have been more P: or player than npc: tags before $line_number");
            }
            if ($m_count > 0 && !$menuInputBox) // if there was a Menu: ?= tag, it may match larger amounts of P/NPC tags.
            {
                append_log("Parse Error: there have been more Menu: than npc:/P: tags before $line_number");
            }
            if ($seen_menu_triggers && $pStar && !$menuInputBox) // if there was a P: * tag without Menu: ?= tag, it is likely a mistake.
            {
                append_log("Warning: found a 'P: *' entry without 'Menu ?=' before $line_number");
            }
            if (strlen($line) > 3)
            {
                if (strpos($line, ' ') != 3)
                {
                    append_log("Parse Error: no whitespace after ... at line $line_number");
                }
                elseif (strcasecmp(substr($line, 4, 8), 'norepeat') === 0) 
                {
                    if (strcasecmp(trim($line), '... NoRepeat') !== 0)
                    {
                        append_log("Parse Error: illegal entry following '... NoRepeat' (no characters, not even a dot are allowed after NoRepeat) at line $line_number");
                    }
                    // else valid, do nothing
                }
                elseif (trim(substr($line, 4)) == '') 
                {
                    // valid, do nothing
                }
                else
                {
                    append_log("Parse Error: unknown entry following ... at line $line_number");
                }
            }
            // check for quest notes
            if (!$quest_note_found && $step > 1 && !$hideQNWarnings && $quest_id != -1) 
            {
                append_log("Warning: step $step has no QuestNote before line $line_number");
            }
            $step++;
            $pStar = $menuInputBox = $seen_npc_triggers = $seen_menu_triggers = $quest_note_found = $ready_for_complete = false;
            $count = $m_count = $p_count = 0;

        }
        else // we found a command
        {
            $commands = explode('.', $line);  // Notice that this also drops the trailing '.' of every command.
            for($i = 0; $i < count($commands); $i++) // the last one is after the last dot, which is to be ignored.
            {
                // the last one is after the last dot, and has no content, we can safely ignore this.
                if ($i == count($commands) - 1 && trim($commands[$i]) == '')
                {
                    continue;
                }
                if (strncasecmp($commands[$i], 'norepeat', 8) === 0)
                {
                    if (!$seenTripleDot) 
                    {
                        append_log("Parse Error: NoRepeat found without \"...\" before it on line $line_number");
                    }
                    $seenTripleDot = false;
                    continue;
                }
                $seenTripleDot = false;
                
                if(strncasecmp(trim($commands[$i]), 'complete', 8) === 0)
                {
                    if (!$ready_for_complete)
                    {
                        append_log("Parse Error: found a complete quest statement without any preceding P: and Menu: triggers on line $line_number");
                        continue;
                    }
                }
                if(trim($commands[$i]) != '') 
                {
                    parse_command(trim($commands[$i]), $assigned, $quest_id, $total_steps, $quest_name, $variablesTracker);  // using totalsteps now, since we can both require and close future steps now.
                }
                else 
                {
                    append_log("Warning: empty command found at line $line_number");
                }
            }
        }
    }
    // post parse validations
    if(!$assigned && $quest_id > 0) //ignore KA scripts which are -1
    {
        append_log('Parse Error: script never assigned any quest.');
    }
    // warnings only, since interquest communication could use this legitimately. Check got added to bust typos.
    foreach ($variablesTracker['set'] as $myVar)
    {
        if (!in_array($myVar, $variablesTracker['unset'], true))
        {
            append_log("Warning: $myVar gets set in this quest, but never unset. Please verify this variable is supposed to be used in interquest communication.");
        }
    }
    foreach ($variablesTracker['unset'] as $myVar)
    {
        if (!in_array($myVar, $variablesTracker['set'], true))
        {
            append_log("Warning: $myVar gets unset in this quest, but never set. Please verify this variable is supposed to be used in interquest communication.");
        }
    }
}

/**
* This function puts the next line from $script into $line and removes that line from $script. (any following line that starts
* with a space is appended too.
*/
function getNextLine(&$line, &$script) 
{
    global $line_number; 
    $line_number++;
    if(trim($script) == '') 
    {
        return false;
    }
    $posn = strpos($script, "\n"); // On some OS's, when you hit enter it inserts \r\n instead of just \n, in those cases we want the line to stop at \r instead of \n.
    $posr = strpos($script, "\r"); //  This script assumes that \r does not come alone.
    if($posn === false) { $posn = strlen($script); }
    $pos = ($posr !== false && $posr < $posn ? $posr : $posn);
    // === means identical rather than equal, php often has functions that give 0 as valid result, and false as error. when used in an
    // if statement, these are both false unless checked like this.
    $line = substr($script, 0, $pos);
    $script = substr($script, $posn+1); //+1 to lose the \n
    while (trim($script) != '' && isspace(substr($script,0,1))) //if the next line starts with a space char, append it to the previous line
    { 
        $line_number++;
        $posn = strpos($script, "\n");
        $posr = strpos($script, "\r");
        if($posn === false) { $posn = strlen($script); }
        $pos = ($posr !== false && $posr < $posn ? $posr : $posn);
        $line.=substr($script, 0, $pos); // . is a "glue" char, not a method call (for all the c/java ppl out there)
        //+1 to lose the \n, if there is none because this is the last line, $pos is the last char, and substr replace any lenght that takes 
        //it out of bounds by the actual lenght
        $script = substr($script, $posn+1); 
    }
    return true;
}

// feed this function 1 char, and it'll tell you if it is a space char (\s) feed it more than 1 char, and it returns false
function isspace($char) 
{
    if (strlen($char) > 1) 
    {
        return false;
    }
    if (ctype_space($char))
    {
        return true;
    }
    return false;
}

function append_log($msg) 
{
    global $parse_log;
    global $hideWarnings;
    if(strtolower(substr($msg, 0, 7)) == "warning")
    {
        if(!$hideWarnings)
        {
            $parse_log.= '<span class="warning">' . $msg . "</span><br />\n";
        }
    }
    else
    {
        $parse_log.=$msg."<br />\n";
    }
}

/**
* get the count for $trigger in $line and put it in $count. If any trigger has no text following it, return false otherwise true.
*/ 
function getTriggerCount($line, $trigger, &$count)
{
    global $line_number;
    $count = 0;
    $pos = 0;
    while (($pos = stripos($line, $trigger, $pos)) !== false) 
    {
        if (strcasecmp($trigger, 'Menu:') === 0) //if we check a menu trigger, check next ones for exact grammar.
        {
            $trigger_without_colon = substr($trigger, 0, strlen($trigger)-1); //check if the second "menu:" has a colon following it.
            if (($temp_pos = stripos($line, $trigger_without_colon, $pos + strlen($trigger))) !== false)
            {
                $temp_pos2 = stripos($line, $trigger, $pos + strlen($trigger));
                if ($temp_pos != $temp_pos2) // it's only a mismatch if "trigger" and "trigger_without_colon" are not starting at the same place.
                {
                    append_log("Warning: no ':' after '$trigger_without_colon' found on line $line_number, please make sure this is intended.");
                }
            }
        } 
        $next_pos = stripos($line, $trigger, $pos + strlen($trigger)); //check if there is another one
        if ($next_pos === false)  //if no more triggers are found, this line goes till the end
        { 
            $next_pos = strlen($line);
        }
        $temp_line = substr($line, $pos+strlen($trigger)+1, $next_pos-$pos); 
        if (trim($temp_line) == "") // if any trigger is found, but nothing but whitespace follows it, return false
        {
            return false;
        }
        else 
        {
            $count++;
        }
        check_parenthesis($temp_line); // check for parenthesis and and if they're found, check if they're valid.
        $pos += strlen($trigger); // move the position pointer past our first find so it doesn't get seen again.
    }
    return true;
}

function checkVariables($line, $type)
{
    global $line_number;
    $words = preg_split("/[^a-zA-Z0-9$]+/", $line); // splits a line by any characters that are not alphanumerical or the dollar sign itself. + means greedy meaning 3 spaces in a row are considered only 1 split.).
    foreach($words as $word)
    {
        if (strpos(trim($word), '$') !== false)  // A $variable was found in this word
        {
            if ($type == 'npc' || $type == 'menu')  // currently there are no other types, there could be in the future though, so this can be easily extended.
            {
                if ($word != '$playerrace' && $word != '$sir' && $word != '$playername' && $word != '$his' && $word != '$time' && $word != '$npc')
                {
                    append_log("Parse Error: misplaced variable ($word) on line $line_number");
                }
            }
            else 
            {
                append_log("Parse Error: misplaced variable ($word) on line $line_number. Was not expecting any variable in this line.");
            }
        }
    }
}

function validate_npc($name)
{
    global $line_number;
    $split = explode(" ", trim($name));  // explode is faster than split if you don't use regex, returns input if pattern is not found.
    if (count($split) == 1) // single name npc
    {
        $query = sprintf("SELECT id, name FROM characters WHERE name = '%s' AND lastname = '' AND npc_master_id != 0", escapeSqlString($split[0]));
        $result = mysql_query2($query);
        if(sqlNumRows($result) > 0) // we found a valid npc
        {
            $row = fetchSqlAssoc($result);
            if (strcmp($split[0], $row['name']) !== 0) // case check
            {
                append_log("Parse Error: NPC ({$split[0]}) has wrong case, should be '{$row['name']}' on line $line_number");
                return false;
            }
            return true;
        }
        $query = sprintf("SELECT id FROM characters WHERE name = '%s' AND lastname = '' AND character_type != 0 AND npc_master_id = 0", escapeSqlString($split[0]));
        $result = mysql_query2($query);
        if(sqlNumRows($result) > 0) // we found a valid npc, but the master_ID is 0, that will crash the server.
        {
            append_log("Parse Error: NPC ({$split[0]}) has npc_master_id set to 0, and thus can not be used in a quest on line $line_number");
            return false;
        }
    }
    elseif (count($split) == 2) // dual name npc
    {
        $query = sprintf("SELECT id, name, lastname FROM characters WHERE name = '%s' AND lastname = '%s' AND npc_master_id != 0", escapeSqlString($split[0]), escapeSqlString($split[1]));
        $result = mysql_query2($query);
        if(sqlNumRows($result) > 0) // we found a valid npc
        {
            $row = fetchSqlAssoc($result);
            if (strcmp($split[0].' '.$split[1], $row['name'].' '.$row['lastname']) !== 0) // case check
            {
                append_log("Parse Error: NPC ({$split[0]}  {$split[1]}) has wrong case, should be '{$row['name']} {$row['lastname']}' on line $line_number");
                return false;
            }
            return true;
        }
        $query = sprintf("SELECT id FROM characters WHERE name = '%s' AND lastname = '%s' AND character_type != 0 AND npc_master_id = 0", escapeSqlString($split[0]), escapeSqlString($split[1]));
        $result = mysql_query2($query);
        if(sqlNumRows($result) > 0) // we found a valid npc, but the master_ID is 0, that will crash the server.
        {
            append_log("Parse Error: NPC ({$split[0]} {$split[1]}) has npc_master_id set to 0, and thus can not be used in a quest on line $line_number");
            return false;
        }
    }
    return false; // in case there is a name with 2 spaces (george walker bush) or more. or if no results were found in either of the queries.
}

/*
* This method checks for {}, [] and () on a line, confirms they're a matching pair, and do some simple content checks on pronouns.
*/
function check_parenthesis($line)
{
    global $line_number;
    
    // First we check for file markers ( )
    $inside = '';
    cut_block($line, $inside, '(', ')');
    if($inside != '')
    {
        $sounds = explode('|', $inside);
        for($i = 0; $i < count($sounds); $i++)
        {
            if (trim($sounds[$i]) == '') 
            {
                append_log("Parse Error: empty file declaration in $inside at line $line_number");
            }
        }
    }
    cut_block($line, $inside, '[', ']'); // check for matchin [] blocks too, these are actions
    
    // then we check for pronoun markers { }
    $inside = '';
    cut_block($line, $inside, '{', '}');
    if ($inside != '')
    {
        $pronouns = explode(',', $inside);
        for ($i = 0; $i < count($pronouns); $i++) 
        {
            $pronoun = explode(':', $pronouns[$i]);
            if (count($pronoun) != 2) 
            {
                append_log("pronoun {$pronouns[$i]} does not have the format pronoun:name at line $line_number");
            }
            if ($pronoun[0] == 'him' || $pronoun[0] == 'he')
            {
                continue;
            }
            elseif($pronoun[0] == 'her' || $pronoun[0] == 'she')
            {
                continue;
            }
            elseif($pronoun[0] == 'it')
            {
                continue;
            }
            elseif($pronoun[0] == 'them' || $pronoun[0] == 'they')
            {
                continue;
            }
            else
            {
                append_log("Parse Error: invalid pronoun {$pronoun[0]} on line $line_number");
            }
        }
    }
}

/**
* This method will take all content between $left and $right from $line and place it in $inside.
* This method is recursive, but atm you will only find [] multiple times in the real databse.
* Only the content of the last complete set of $left/$right is returned.
*/
function cut_block ($line, &$inside, $leftchar, $rightchar) 
{
    global $line_number;
    if (strlen($leftchar) == 0 || strlen($rightchar) == 0) 
    {
        return false;
    }
    
    $pos_start = strpos($line, $leftchar);
    $pos_end = strpos($line, $rightchar);
    while ($pos_start !== false && $pos_end !== false) 
    {
        if ($pos_start > $pos_end) 
        {
            append_log("Parse Error: $rightchar before $leftchar at line $line_number");
            return false;
        }
        $inside = substr($line, $pos_start+strlen($leftchar), $pos_end - $pos_start - strlen($leftchar));  // start+strlen coz we don't want the starting { or ( or whatever
        if(trim($inside) == '')
        {
            append_log("Parse Error: no text between $leftchar and $rightchar on line $line_number");
        }
        $pos_start = strpos($line, $leftchar, $pos_start+strlen($leftchar));
        $pos_end = strpos($line, $rightchar, $pos_end+strlen($rightchar));
    }
    if ($pos_start !== false) 
    {
        append_log("Parse Error: $leftchar without $rightchar at line $line_number");
        return false;
    }
    elseif ($pos_end !== false) 
    {
        append_log("Parse Error: $rightchar without $leftchar at line $line_number");
        return false;
    }
    return true;
}

function handle_player_action($line)
{
    global $line_number;
    $words = explode(' ', $line);
    if(count($words) < 2)
    {
        append_log("Parse Error: no command following 'player' at line $line_number");
        return;
    }
    if (strcasecmp($words[1], 'gives') === 0) // "player gives"
    {
        $name_count = 0;
        if (count($words) < 3)
        {
            append_log("Parse Error: no npc name following 'player gives' at line $line_number");
            return;
        }
        $query = sprintf("SELECT id FROM characters WHERE name = '%s' AND lastname = '' AND npc_master_id != 0", escapeSqlString($words[2]));
        $result = mysql_query2($query);
        if (sqlNumRows($result) > 0) // we found a valid npc
        {
            $name_count = 1;
        }
        $query = sprintf("SELECT id FROM characters WHERE name = '%s' AND lastname = '' AND character_type != 0 AND npc_master_id = 0", escapeSqlString($words[2]));
        $result = mysql_query2($query);
        if(sqlNumRows($result) > 0) // we found a valid npc, but the master_ID is 0, that will crash the server.
        {
            append_log("Parse Error: NPC ({$words[2]}) has npc_master_id set to 0, and thus can not be used in a quest on line $line_number");
            return;
        }
        if ($name_count == 0 && count($words) > 3)
        {
            $query = sprintf("SELECT id FROM characters WHERE name = '%s' AND lastname = '%s' AND npc_master_id != 0", escapeSqlString($words[2]), escapeSqlString($words[3]));
            $result = mysql_query2($query);
            if (sqlNumRows($result) > 0) // we found a valid npc
            {
                $name_count = 2;
            }
            $query = sprintf("SELECT id FROM characters WHERE name = '%s' AND lastname = '%s' AND npc_master_id = 0 AND character_type != 0", escapeSqlString($words[2]), escapeSqlString($words[3]));
            $result = mysql_query2($query);
            if(sqlNumRows($result) > 0) // we found a valid npc, but the master_ID is 0, that will crash the server.
            {
                append_log("Parse Error: NPC ({$words[2]} {$words[3]}) has npc_master_id set to 0, and thus can not be used in a quest on line $line_number");
                return;
            }
        }
        if ($name_count == 0)
        {
            append_log("Parse Error: could not find NPC name in \"player gives\" on line $line_number, Please check NPC Name");
            return;
        }
        $items = trim(implode(' ', array_slice($words, 2 + $name_count)));
        if (strlen($items) > 1 && substr($items, strlen($items) - 1) == ".") // eat the trailing "."
        {
            $items = substr($items, 0, strlen($items) - 1);
        }
        $item = explode(',', $items);
        for ($i = 0; $i < count($item); $i++)
        {
            $parts = explode(' ', trim($item[$i]), 2);
            if (is_numeric($parts[0]))
            {
                if ($parts[0] < 0)
                {
                    append_log("Parse Error: cannot give a negative amount of items in 'player gives' on line $line_number");
                }
                elseif (count($parts) < 2)
                {
                    append_log("Parse Error: could not read item name in 'player gives' on line $line_number");
                }
                else
                {
                    $itemName = trim(implode(' ', array_slice($parts, 1)));
                    validate_item($itemName);
                    // check for invalid flags on the item.
                    validateItemFlags($itemName);
                    // check all cases of "give money", they are not technically stackable, but the server will still allow giving multiple.
                    $trias = strtolower($itemName); // check for tria case insensitive.
                    if ($trias == 'tria' || $trias == 'hexa' || $trias == 'octa' || $trias == 'circle')
                    {
                        continue; // valid
                    }
                    // check if an item is stackable, and how many items were given. (will not yield any result if itemname is not valid).
                    $query = sprintf("SELECT flags FROM item_stats WHERE name = '%s'", escapeSqlString($itemName));
                    $result = mysql_query2($query);
                    if (sqlNumRows($result) > 0)
                    {
                        $row = fetchSqlAssoc($result);
                        if (stripos($row['flags'], 'STACKABLE') !== false)
                        {
                            if ($parts[0] > 65)
                            {
                                append_log("Parse Error: cannot give more than 65 (max stack size) of $itemName in 'player gives' on line $line_number");
                            }
                        }
                        else
                        {
                            if ($parts[0] != 1)
                            {
                                append_log("Parse Error: cannot give more than 1 of $itemName (not flagged STACKABLE) in 'player gives' on line $line_number");
                            }
                        }
                    }
                }
            }
            else
            {
                $itemName = trim($item[$i]);
                validate_item($itemName);
                validateItemFlags($itemName);
            }
        }
    }
    else
    {
        append_log("Parse Error: unknown player command: player {$words[1]} at line $line_number");
    }
}

/*
 * Quest name got added for the prospect validator, in which case the quest is not in the database. ($quest_id = 0) Then in the case of "complete quest" and
 * "require completion of quest", we should check first if id==0 and name==name, before looking on the database.
 */
function parse_command($command, &$assigned, $quest_id, $step, $quest_name, &$variablesTracker)  
{
    global $line_number;
    if (strncasecmp($command, 'assign quest', 12) === 0)
    {
        $assigned = true;
    }
    elseif (strncasecmp($command, 'fireevent', 9) === 0)
    { // can't check this yet
    }
    elseif (strncasecmp($command, 'complete', 8) === 0 || strncasecmp($command, 'uncomplete', 10) === 0)
    {
        // threat 'uncomplete' the same as 'complete' from this point on
        if (strncasecmp($command, 'uncomplete', 10) === 0) {
            $command = substr(trim($command), 2); // removes 'un'
        }
        
        if ($quest_id == 0) 
        {
            if (strcasecmp(trim($command), "complete $quest_name") === 0)
            {
                // valid, do nothing
            }
            elseif (strncasecmp($command, "complete $quest_name step ", 15 + strlen($quest_name)) === 0)
            {
                $split_complete = explode(' ', substr(trim($command), 15 + strlen($quest_name)));
                if (count($split_complete) > 1) 
                {
                    append_log("Parse Error: illegal text following 'complete $quest_name step {$split_complete[0]}' on line $line_number");
                }
                elseif ($split_complete[0] != '' && is_numeric($split_complete[0]) && $split_complete[0] <= $step && $split_complete[0] > 0) 
                {
                    // valid, do nothing
                }
                elseif ($split_complete[0] == '' || !is_numeric($split_complete[0]))
                {
                    append_log("Parse Error: you did not provide a valid step number for 'complete quest' on line $line_number");
                }
                else
                {
                    append_log("Parse Error: completing a step that is higher than the total number of steps in this quest on line $line_number");
                }
            }
            else
            {
                append_log("Parse Error: invalid questname ($command) at line $line_number");
            }
            return; // in all cases, when $quest_id is 0, do not go further than here. (the other checks are on the database, and thus will fail.
        }
        $query = sprintf("SELECT name FROM quests WHERE id = '%s'", escapeSqlString($quest_id));
        $result = mysql_query2($query); // this may bug up if more quests have the same id (which they shouldn't have(?)) (KA scripts are exluded since they can't complete.
        if(sqlNumRows($result) > 0)
        {
            $row = fetchSqlRow($result);
            $name = $row[0];
            // can complete previous steps too
            if (strcasecmp(trim($command), "complete $name") === 0) 
            {
                // valid, do nothing
            }
            elseif (strncasecmp($command, "complete $name step", 14+strlen($name)) === 0)
            {
                $split_complete = explode(' ', substr(trim($command), 15+strlen($name)));
                if (count($split_complete) > 1) 
                {
                    append_log("Parse Error: illegal text following 'complete $name step {$split_complete[0]}' on line $line_number");
                }
                elseif ($split_complete[0] != '' && is_numeric($split_complete[0]) && $split_complete[0] <= $step && $split_complete[0] > 0) 
                {
                    // valid, do nothing
                }
                elseif ($split_complete[0] == '' || !is_numeric($split_complete[0]))
                {
                    append_log("Parse Error: you did not provide a valid step number for 'complete quest' on line $line_number");
                }
                else
                {
                    append_log("Parse Error: completing a step that is higher than the total number of steps in this quest on line $line_number");
                }
            }
            else
            {
                append_log("Parse Error: invallid questname ($command) at line $line_number");
            }
        }
        else 
        {
            append_log("Parse Error: could not determine questname for this script at line $line_number");
        }
    }
    elseif (strncasecmp($command, 'give', 4) === 0)
    {
        $words = explode(' ', trim(substr($command, 4)));
        // we convert to lower because this check is not case sensitive. In addition, array_slice returns an empty string if there are no elements.
        $trias = strtolower(implode(' ', array_slice($words, 1))); 
        // check all cases of "give money".
        if (is_numeric($words[0]) && ($trias == 'tria' || $trias == 'hexa' || $trias == 'octa' || $trias == 'circle'))
        { 
            if ($words[0] < 0)
            { 
                append_log("Parse Error: while giving money (tria/hexa/octa/circle), the parameter before the currency should be a positive number at line $line_number");
            }
            // else: valid
        } // check for "give faction"
        elseif (is_numeric($words[0]) && count($words) > 1 && strtolower($words[1]) == 'faction')
        {
            if (count($words) < 3)  // "give" is stripped from $words, so we check < 3 rather than < 4.
            {
                append_log("Parse Error: 'give 1 faction factionname' (example) requires at least 4 paramers at line $line_number");
            }
            elseif ($words[0] < 0)
            {
                append_log("Parse Error: 'give 1 faction factionname' (example) requires the second parameter to be a positive number at line $line_number");
            }
            else
            { // the remaining words should be the faction name.
                validate_faction(implode(' ', array_slice($words, 2)));
            }
        } // check for give exp.
        elseif (is_numeric($words[0]) && count($words) > 1 && strtolower($words[1]) == 'exp')
        {
            if(count($words) != 2)
            {
                append_log("Parse Error: too much/few parameters for exp command 'give 1 exp' (example) at line $line_number");
            }
            elseif ($words[0] < 0)
            {
                append_log("Parse Error: 'give 1 exp' (example) requires the second parameter to be a positive number at line $line_number");
            }
            // else: valid
        }
        else // it's an item, or an offering of items.
        {
            $given = explode(' or ', substr($command, 5));
            if (count(explode(' or ', strtolower($command))) > count($given))
            {
                append_log("Parse Error: encountered uppercase \"or\" in give command on line $line_number");
                return;
            }
            for ($i = 0; $i < count($given); $i++)  // a choice may be presented seperated by "or", so we need to validate them all.
            {
                if (trim($given[$i] == ''))
                {
                    append_log("Parse Error: nothing to give on line $line_number");
                    return;
                }
                $words = explode(' ', trim($given[$i]));
                
                if (is_numeric($words[0]))
                {
                    if ($words[0] < 0)
                    {
                        append_log("Parse Error: item quantity must be positive at line $line_number");
                    }
                    elseif  (count($words) == 2 || !is_numeric($words[1]))
                    {
                        $itemName = implode(' ', array_slice($words, 1));
                        validate_item($itemName);
                        validateItemFlags($itemName);
                    }
                    elseif ($words[1] < 0 || $words[1] > 300)
                    {
                        append_log("Parse Error: item quality must be positive and 300 or less at line $line_number");
                    }
                    else
                    {
                        $itemName = implode(' ', array_slice($words, 2));
                        validate_item($itemName);
                        validateItemFlags($itemName);
                    }
                }
                else // all words are part of the item name.
                {
                    $itemName = implode(' ', $words);
                    validate_item($itemName);
                    validateItemFlags($itemName);
                }
            }
        }
    }
    elseif (strncasecmp($command, 'setvariable', 11) === 0)
    {
        $words = explode(' ', trim(substr($command, 11)));
        if (count($words) != 2)
        {
            append_log("Parse Error: setvariable needs 2 arguments at line $line_number");
            return;
        }
        if (strncmp($words[0], 'Quest_', 6) !== 0) // case sensitive check
        {
            append_log("Warning: Variables for quests should start with 'Quest_' at line $line_number");
        }
        $variablesTracker['set'][] = $words[0];
    }
    elseif (strncasecmp($command, 'unsetvariable', 13) === 0)
    {
        $words = explode(' ', trim(substr($command, 13)));
        if (trim($words[0]) == '' || count($words) > 1)
        {
            append_log("Parse Error: unsetvariable needs 1 argument at line $line_number");
            return;
        }
        if (strncmp($words[0], 'Quest_', 6) !== 0) // case sensitive check
        {
            append_log("Warning: Variables for quests should start with 'Quest_' at line $line_number");
        }
        $variablesTracker['unset'][] = $words[0];
    }
    elseif (strncasecmp($command, 'run script', 10) === 0)
    {
        $script = trim(substr($command, 10));
        $paramstart = strpos($script, '<<');
        if ($paramstart === false)
        {   //no params, the whole thing is the scriptname.
            validate_scriptname($script);
        }
        else
        {
            $scriptname = trim(substr($script, 0, $paramstart));
            validate_scriptname($scriptname);
            $param = '';
            cut_block($script, $param, '<<', '>>');
            if (trim($param) == '')
            {
                append_log("Parse Error: could not load parameters at line $line_number");
                return;
            }
            $params = explode(',', $param);
            for($i = 0; $i < count($params); $i++)
            {
                if (trim($params[$i]) == '')
                {
                    append_log("Parse Error: found empty parameter in run script on line $line_number");
                }
                elseif (strpos($params[$i], '"') !== false)
                {
                    append_log("Parse Error: you are not allowed to use double quotes in parameters for run script on line $line_number");
                }
                elseif (strpos($params[$i], ';') !== false)
                {
                    append_log("Parse Error: you are not allowed to use a semi-colon ';' in parameters for run script on line $line_number");
                }
                else
                {
                    $quotecount = substr_count($params[$i], "'");
                    if ($quotecount == 0)
                    {
                        // valid
                    }
                    elseif ($quotecount == 2)
                    {
                        $temp = trim($params[$i]);
                        // check if the quotes are the first and last char of the param
                        if (strpos($temp, "'") === 0 && strpos($temp, "'", 1) === strlen($temp) - 1)
                        {
                            if (trim(substr($temp, 1, strlen($temp)-2)) == '')
                            {
                                append_log("Parse Error: no variable inside quotes in parameter for script on line $line_number");
                            }
                            elseif (is_numeric(substr($temp, 1, strlen($temp)-2)))
                            {
                                append_log("Parse Error: numeric parameters should not be inside quotes for script on line $line_number");
                            }// else valid
                        }
                        else
                        {
                            append_log("Parse Error: quotes should only be at the begining and the end of a parameter of a script on line $line_number");
                        }
                    }
                    else
                    {
                        append_log("Parse Error: invalid amount of single quotes in parameters for run script on line $line_number");
                    }
                }
            }
        }
    }
    elseif (strncasecmp($command, 'doadmincmd', 10) === 0)
    {
        $cmd = trim(substr($command, 10));
        if ($cmd == '') 
        {
            append_log("Parse Error: no admin command found on line $line_number");
            return;
        }
        // This assumes all commands are of the /command type, there are more admin commands, but they are not available in quests.
        $words = explode(' ', $cmd);
        $query = sprintf("SELECT group_member FROM command_group_assignment WHERE command_name = '%s'", escapeSqlString($words[0]));
        $result = mysql_query2($query);
        if (sqlNumRows($result) < 1)
        {
            append_log("Parse Error: could not find admin command ({$words[0]}) in the database on line $line_number");
            return;
        } 
        // do parameter validating of commands.
        if ($words[0] == '/teleport')
        {
            // check param count
            if (count($words) != 8)
            {
                append_log("Parse Error: /teleport needs exactly 7 parameters (ex: /teleport targetchar map npcroom1 0 -2 7 1). Check for spaces in the edit screen if you count 7 params on line $line_number");
            }
            // check for second and third fixed keyword
            elseif ($words[1] != 'targetchar' || $words[2] != 'map')
            {
                append_log("Parse Error: /teleport always has 'targetchar' and 'map' as first and second parameter ('/teleport targetchar map') on line $line_number");
            }
            // check if X is valid.
            elseif (!is_numeric($words[4]))
            {
                append_log("Parse Error: X coordinate in teleport is not a number on line $line_number");
            }
            // check if Y is valid.
            elseif (!is_numeric($words[5]))
            {
                append_log("Parse Error: Y coordinate in teleport is not a number on line $line_number");
            }
            // check if Z is valid.
            elseif (!is_numeric($words[6]))
            {
                append_log("Parse Error: Z coordinate in teleport is not a number on line $line_number");
            }
            // check if I is valid.
            elseif (!is_numeric($words[7]))
            {
                append_log("Parse Error: sector istance in teleport is not a number on line $line_number");
            }
            // check if sector is valid
            else
            {
                $query = sprintf("SELECT id FROM sectors WHERE name = '%s'", escapeSqlString($words[3]));
                $result = mysql_query2($query);
                if (sqlNumRows($result) < 1)
                {
                    append_log("Parse Error: invalid sector name in teleport, sector does not exist in database on line $line_number");
                }
            }
            
            
        }
    }
    elseif (strncasecmp($command, 'require', 7) === 0) 
    {
        // Found a "require command"
        $requirements = substr($command, 8); // remove the require part and it's trailing space.
        $requirements = explode('|', $requirements); // split on all cases of the OR operator.
   
        foreach($requirements AS $requirement) 
        {
            $require = trim($requirement); // we use $requirement for the error message later if need be.
            // Determine if the next word is "no" or "not" and remove that too. (It's not relevant for the parser to know which is the case, as in 
            // both cases it is whatever that follows that needs to be valid.)
            if (strncasecmp($require, 'not', 3) === 0) 
            {
                $require = trim(substr($require, 3));
            }
            elseif (strncasecmp($require, 'no', 2) === 0)
            {
                $require = trim(substr($require, 2));
            }
            // Now find out which command was used.
            if (strncasecmp($require, 'completion of', 13) === 0) 
            {
                check_completion($quest_id, $step, substr($require, 13), $quest_name);
            }
            elseif (strncasecmp($require, 'assignment of', 13) === 0) 
            {
                check_completion($quest_id, $step, substr($require, 13), $quest_name);
            }
            elseif (strncasecmp($require, 'time of day', 11) === 0)
            {
                validate_time_of_day(substr($require, 11));
            }
            elseif (strncasecmp($require, 'guild', 5) === 0)
            {
                // dunno if/how this should be checked.
            }
            elseif (strncasecmp($require, 'active magic', 12) === 0)
            {
                validate_magic(substr($require, 12), true);
            }
            elseif (strncasecmp($require, 'known spell', 11) === 0)
            {
                validate_magic(substr($require, 11));
            }
            elseif (strncasecmp($require, 'race', 4) === 0)
            {
                validate_race(substr($require, 4));
            }
            elseif (strncasecmp($require, 'gender', 6) === 0)
            {
                validate_gender(substr($require, 6));
            }
            elseif (strncasecmp($require, 'trait', 5) === 0)
            {
                validate_trait(substr($require, 5));
            }
            elseif (strncasecmp($require, 'married', 7) === 0)
            {
                // valid, nothing to check  
            }
            elseif (strncasecmp($require, 'possessed', 9) === 0 || strncasecmp($require, 'equipped', 8) === 0) // case for possessed and equipped are identical
            {
                // remove the "possessed" or "equipped" from the start
                $item = trim(substr($require, (strncasecmp($require, 'possessed', 9) === 0 ? 9 : 8)));

                // if the next word is "amount", we're dealing with an item or category that is required in a certain amount.
                if (strncasecmp($item, 'amount', 6) === 0)
                {
                    // remove the "amount"
                    $item = trim(substr($item, 6));
                    $items = explode(' ', $item);
                    $amount = explode('-', $items[0]);
                    if (count($amount) == 1)
                    {
                        if (!is_numeric($amount[0]))
                        {
                            append_log("Parse Error: either one number '1', or two in the form '1-3' should follow after 'amount' in possessed/equipped command at line $line_number");
                            return; // avoid meaningless follow up errors.
                        }
                        // else valid.
                    }
                    elseif(count($amount) == 2) 
                    {
                        if (trim($amount[0]) == '' && trim($amount[1]) == '')
                        {
                            append_log("Parse Error: no number before or after the quantity seperator after 'amount' in possessed/equipped command at line $line_number");
                        } // cross check: if one is empty, the other must be a number.
                        elseif (trim($amount[1]) == '')
                        {
                            append_log("Warning: max quantity missing while quantity seperator is present in possessed/equipped command at line $line_number");
                            if (!is_numeric($amount[0]))
                            {
                                append_log("Parse Error: the character before the quantity seperator after 'amount' is not a number in possessed/equipped command at line $line_number");
                            }
                        } // cross check: if one is empty, the other must be a number.
                        elseif (trim($amount[0]) == '')
                        {
                            append_log("Warning: min quantity missing while quantity seperator is present in possessed/equipped command at line $line_number");
                            if (!is_numeric($amount[1]))
                            {
                                append_log("Parse Error: the character following the quantity seperator after 'amount' is not a number in possessed/equipped command at line $line_number");
                            }
                        }
                        elseif (is_numeric($amount[0]) && is_numeric($amount[1]) && $amount[0] > $amount[1])
                        {
                            append_log("Parse Error: the min amount is more than the max amount in possessed/equipped command at line $line_number");
                        }
                        // else valid.

                    }
                    else
                    {
                        append_log("Parse Error: too many minus signs after 'amount' in possessed/equipped command at line $line_number");
                    }
                    // remove the "1-1" number that followed amount from the item string.
                    $item = trim(substr($item, strlen($items[0])));
                }
                
                $cat_pos = strpos($item, 'category');
                $item_pos = strpos($item, 'item');
                if ($cat_pos === false && $item_pos === false)
                {
                    append_log("Parse Error: no 'item' or 'category' identifier in possessed/equipped command at line $line_number");
                    return;
                }
                
                // if anything is before low_pos (that is, it is not 0), then that must be the quality .
                $low_pos = ($cat_pos === false ? $item_pos : ($item_pos === false ? $cat_pos : min($item_pos, $cat_pos)));
                if ($low_pos > 0)
                {
                    $quality = explode('-', trim(substr($item, 0, $low_pos)));
                    $item = substr($item, $low_pos);
                    if (count($quality) == 2 && trim($quality[0]) == '')
                    {
                        append_log("Warning: min quality missing while quality seperator is present in possessed/equipped command at line $line_number");
                    }
                    if (count($quality) == 2 && trim($quality[1]) == '')
                    {
                        append_log("Warning: max quality missing while quality seperator is present in possessed/equipped command at line $line_number");
                    }
                    if (count($quality) > 2)
                    {
                        append_log("Parse Error: you can only use 1 minus sign to seperate min/max quality in possessed/equipped command at line $line_number");
                    }
                    elseif (trim($quality[0]) != '' && (!is_numeric(trim($quality[0])) || trim($quality[0]) > 300 || trim($quality[0]) < 0))
                    {
                        echo('QMin ' . $quality[0] . ' $item ' . $item . '<br>');
                        append_log("Parse Error: min quality should be between 0 and 300 in possessed/equipped command at line $line_number");
                    }
                    elseif (count($quality) == 2 && trim($quality[1]) != '' && (!is_numeric(trim($quality[1])) || trim($quality[1]) > 300 || trim($quality[1]) < 0))
                    {
                        append_log("Parse Error: max quality should be between 0 and 300 in possessed/equipped command at line $line_number");
                    }
                    elseif (count($quality) == 2 && trim($quality[1]) != '' && trim($quality[0]) != '' && trim($quality[0]) > trim($quality[1]))
                    {
                        append_log("Parse Error: min quality cannot exceed max quality in possessed/equipped command at line $line_number");
                    }                
                }
                if ($cat_pos !== false)
                { // $item hold a category name.
                    validate_category(trim(substr($item, 8)));
                }
                else
                { // $item hold an item name.
                    validate_item(trim(substr($item, 4)), true);
                }
            }
            elseif (strncasecmp($require, 'skill', 5) === 0)
            {
                $require = trim(substr($require, 5)); // remove "skill"
                if (strncasecmp($require, 'buffed', 6) === 0) 
                { // "buffed" is an optional flag to determine if we want pure skill, or buffed skill. We remove it from the string.
                    $require = trim(substr($require, 6));
                }
                // find the last - sign to find the skill range. 
                $lasthyphen = strrpos($require, '-');
                if ($lasthyphen === false)
                { // - sign is manditory to provide a skill range.
                    append_log("Parse Error: no '-' sign follows the skill name at line $line_number");
                    return;
                }
                // the last space before the last '-' sign will be what splits the skill range from the skill name.
                $lastspace = strrpos($require, ' ', -(strlen($require)-$lasthyphen));
                $skillname = substr($require, 0, $lastspace);
                $skillrange = substr($require, $lastspace+1);
                if ($lastspace === false || trim($skillname) == '')
                {
                    append_log("Parse Error: could not determine skill name at line $line_number");
                    return;
                }
                $skillranges = explode('-', $skillrange);
                if (count($skillranges) != 2 || !is_numeric(trim($skillranges[0])) || $skillranges[0] < 0 ||
                    !is_numeric(trim($skillranges[1])) || $skillranges[1] < 0 || $skillranges[0] > $skillranges[1])
                {
                    append_log("Parse Error: invalid skill range at line $line_number");
                    return;
                }
                // might be valid, but is unusual, warn to pay attention.
                if ($skillranges[0] > 200 || $skillranges[1] > 200)
                {
                    append_log("Warning: skill exceeds 200 at line $line_number - be sure this is intended");
                }
                
                validate_skill($skillname);
            }
            elseif (strncasecmp($require, 'variable', 8) === 0)
            {
                $parameters = explode(' ', trim(substr($require, 8)));
                if (count($parameters) > 3)
                {
                    append_log("Parse Error: Require variable has too many arguments at line $line_number");
                    return;
                }
                if (trim($parameters[0]) == "")
                {
                    append_log("Parse Error: No require variable name (check for double spaces) at line $line_number");
                    return;
                }
                if (strncmp($parameters[0], 'Quest_', 6) !== 0) // case sensitive check
                {
                    append_log("Warning: Variables for quests should start with 'Quest_' at line $line_number");
                }
                if (count($parameters) == 1)
                {
                    return; // valid, we already checked empty/other cases above.
                }
                if (count($parameters) == 2) // 2 params means a key/value pair.
                {
                    if ($parameters[1] == '')
                    {
                        append_log("Parse Error: No require variable value (check for double spaces) at line $line_number");
                    }
                    return; // valid case, we're done.
                }
                // that leaves the 3 param case, it takes the form of "require variable <name> <min> <max>"
                if (!is_numeric($parameters[1]) && $parameters[1] != 'none' && $parameters[1] != '')
                {
                    append_log("Parse Error: Require variable parameter 'min' must be a number or 'none' at line $line_number");
                }
                if (!is_numeric($parameters[2]) && $parameters[2] != 'none' && $parameters[2] != '')
                {
                    append_log("Parse Error: Require variable parameter 'max' must be a number or 'none' at line $line_number");
                }
                if ($parameters[1] != 'none' && $parameters[2] != 'none' && $parameters[2] < $parameters[1])
                {
                    append_log("Parse Error: Require variable parameter 'max' must be higher than 'min' at line $line_number");
                }
            }
            else 
            {
                append_log("Parse Error: unknown requirement (require $requirement) at line $line_number");
            }
        }
    }
    elseif (strncasecmp($command, 'Introduce', 9) === 0)
    {
    }
    elseif (strncasecmp($command, 'Menu', 4) === 0) // This is basically an error catcher, it should be below all other cases.
    {
        append_log("Parse Error: no ':' following 'Menu' at line $line_number");
    }
    elseif (strncasecmp($command, "P", 1) === 0) // This is basically an error catcher, it should be below all other cases.
    {
        append_log("Parse Error: no ':' following 'P' at line $line_number");
    }
    else 
    {
        append_log("Parse Error: unknown command ($command) at line $line_number");
    }
}

function check_completion($quest_id, $step, $quest, $quest_name)
{
    global $line_number;
    if (trim($quest) == '')
    {
        append_log("Parse Error: no quest mentioned at line $line_number");
        return;
    }
    if ($quest_id == 0) // special number used for prospect console. Means the quest itself is not in the database, so lets check if it is valid with the name that was given.
    {
        if (strcasecmp(trim($quest), $quest_name) === 0)
        {
            return; //valid, nothing else to do.
        }
        else if (strncasecmp(trim($quest), "$quest_name step", 5 + strlen($quest_name)) === 0)
        {
            $step_nr = trim(substr(trim($quest), 5 + strlen($quest_name)));
            if ($step_nr == '' || !is_numeric($step_nr))
            {
                append_log("Parse Error: you did not provide a valid step number for 'require completion' on line $line_number");
                return;
            }
            elseif ($step_nr <= $step)
            {
                // valid
                return;
            }
            else
            {
                append_log("Parse Error: you can't refer to quest steps that exceed the total number of steps at line $line_number");
                return;
            }
        }
        // else we need to run past the rest of the checks, though the next one is guaranteed to fail with quest_id 0, the one after that may pass.
    }
    $result = mysql_query2("SELECT name FROM quests WHERE id = '$quest_id'");
    if (sqlNumRows($result) > 0) // First we check if it's a reference to this script (most of them are)
    {
        $row = fetchSqlRow($result);
        $name = $row[0];
        if (strcasecmp(trim($quest), $name) === 0)
        {
            // valid, nothing else to do
            return;
        }
        else if (strncasecmp(trim($quest), "$name step", 5 + strlen($name)) === 0)
        {
            $step_nr = trim(substr(trim($quest), 5 + strlen($name)));
            if ($step_nr == '' || !is_numeric($step_nr))
            {
                append_log("Parse Error: you did not provide a valid step number for 'require completion' on line $line_number");
                return;
            }
            elseif ($step_nr <= $step)
            {
                // valid
                return;
            }
            else
            {
                append_log("Parse Error: you can't refer to quest steps that exceed the total number of steps at line $line_number");
                return;
            }
        }
    }// if it's not, we need to check all data.
    $name = trim($quest);
    $complete_step = '';
    if (($pos = stripos($quest, 'step')) !== false)
    {
        $name = trim(substr($quest, 0, $pos));
        $complete_step = trim(substr($quest, $pos + 4));
        if ($name == '')
        {
            append_log("Parse Error: no quest mentioned at line $line_number");
            return;
        }
        elseif ($complete_step == '' || $complete_step < 1)
        {
            append_log("Parse Error: invalid quest step at line $line_number");
            return;
        }
    }
    $query = sprintf("SELECT id FROM quests WHERE name='%s'", escapeSqlString($name));
    $result = mysql_query2($query); 
    if (sqlNumRows($result) > 0)  // found a quest with that name
    {
        append_log("Warning: references to another quest are not recommended, only use if you really must: line $line_number");
        $row = fetchSqlRow($result);
        $id = $row[0];
        if($complete_step == '')
        {
            // found a matching quest with no steps, we're done checking.
            return;
        }
        else
        {
            $result = mysql_query2("SELECT script FROM quest_scripts WHERE quest_id = '$id'"); 
            if (sqlNumRows($result) > 0)  // found a quest with that name
            {
                $row = fetchSqlRow($result);
                $target_steps = explode('...', $row[0]);
                if ($complete_step > count($target_steps)) // target quest does not have this many steps
                {
                    append_log("Parse Error: target quest does not have $complete_step steps at line $line_number");
                }
            }
            else
            {
                append_log("Parse Error: there is no script for id $id (which belongs to $quest) in the database at line $line_number");
            }
        }
    }
    else
    {
        append_log("Parse Error: could not find any quest named $name in the database at line $line_number");
    }
}

function validate_time_of_day($time)
{
    global $line_number;
    if (trim($time) == "")
    {
        append_log("Parse Error: could not determine time on line $line_number");
        return;
    }
    $minmax = explode("-", $time);
    if (count($minmax) != 2 || trim($minmax[0]) == "" || trim($minmax[1]) == "")
    {
        append_log("Parse Error: invalid time format ($time) on line $line_number");
        return;
    }
    if (trim($minmax[0]) > trim($minmax[1]))
    {
        append_log("Warning: time min: {$minmax[0]} is before max: {$minmax[1]} at line $line_number");
        return;
    }
    if (trim($minmax[0]) < 0 || trim($minmax[0]) > 24)
    {
        append_log("Warning: time min: {$minmax[0]} is not between 0 and 24 at line $line_number");
        return;
    }
    if (trim($minmax[1]) < 0 || trim($minmax[1]) > 24)
    {
        append_log("Warning: time max: {$minmax[1]} is not between 0 and 24 at line $line_number");
        return;
    }
    // all other cases are valid, do nothing.
}

function validate_skill($skillname)
{
    global $line_number;
    $query = sprintf("SELECT skill_id FROM skills WHERE name = '%s'", $skillname);
    $result = mysql_query2($query);
    if (sqlNumRows($result) == 1)
    {
        // valid skill, do nothing
    } else
        append_log ("Parse Error: skill $skillname not valid at line $line_number");
}

function validate_item($itemName, $checkCase = false)
{
    global $line_number;
    if (trim($itemName) == '')
    {
        append_log("Parse Error: could not read item name on line $line_number");
        return;
    }
    $query = sprintf("SELECT name FROM item_stats WHERE name = '%s' AND stat_type='B'", escapeSqlString($itemName));
    $result = mysql_query2($query);
    if (sqlNumRows($result) > 0)
    {
        $row = fetchSqlRow($result);
        // notice that the "where" in sql is not case sensitive, but the result of the query is, and might differ from what we used to search.
        if ($checkCase && $row[0] != $itemName) 
        {
            append_log("Parse Error: item name is case sensitive, use '{$row[0]}' instead of '$itemName' in database on line $line_number");
        }
        // valid item, do nothing
    }
    else
    {
        append_log("Parse Error: no item with name: $itemName in database on line $line_number");
    } 
}

// items that have certain flags cannot be given in quests, this function checks those. This function does not check item names itself, if an 
// invalid item name is given, this function will not yield any result. Always use validate_Item first.
function validateItemFlags($itemName)
{
    global $line_number;
    $query = sprintf("SELECT flags FROM item_stats WHERE name = '%s'", escapeSqlString($itemName));
    $result = mysql_query2($query);
    if (sqlNumRows($result) > 0)
    {
        $row = fetchSqlAssoc($result);
        if (stripos($row['flags'], 'BUY_PERSONALISE') !== false)
        {
            append_log("Parse Error: $itemName is flagged BUY_PERSONALISE and can't be given on line $line_number");
        }
    }
}

function validate_category($categoryname)
{
    global $line_number;
    if (trim($categoryname) == '')
    {
        append_log("Parse Error: could not read category name on line $line_number");
    }
    $query = sprintf("SELECT category_id FROM item_categories WHERE name = '%s'", escapeSqlString($categoryname));
    $result = mysql_query2($query);
    if (sqlNumRows($result) < 1)
    {
        append_log("Parse Error: no category with name: $categoryname in database on line $line_number");
    }
}
    

function validate_faction($factionName)
{
    global $line_number;
    $query = sprintf("SELECT id FROM factions WHERE faction_name = '%s'", escapeSqlString($factionName));
    $result = mysql_query2($query);
    if(sqlNumRows($result) < 1)
    {
        append_log("Parse Error: no faction ($factionName) found in database on line $line_number");
    }
}

// otherBuffs determines if we look for an actual magic, or just for any "buff".
function validate_magic($magicName, $otherBuffs = false)
{
    global $line_number;
    $magic = trim($magicName);
    $query = sprintf("SELECT id FROM spells WHERE name = '%s'", escapeSqlString($magic));
    $result = mysql_query2($query);
    if (sqlNumRows($result) > 0)
    {
        return; // valid case
    }
    if (sqlNumRows($result) < 1 && !$otherBuffs)
    {
        append_log("Parse Error: could not find magic '$magic' in the database at line $line_number");
        return;
    }
    
    // Other places we can find magic buffs are in <apply> tags of progression events, they could be given as variable from a quest too. 
    // First we try if this quest script calls any progression events using the spell name as parameter (a lot of them are, "quest_timer" and whatever).
    global $currentScript;
    if(validateMagicNameInProgressionScriptParam($currentScript, $magic))
    {
        return; // found something, we're good.
    }
    
    // Try to find the spell name in a progression event.
    $spellName = str_replace('&', '&amp;', $magic);
    // technically single quotes are not allowed unescaped in values in XML, but it does work if you use double quotes for the values, so some old scripts still have these. The regex finds both.
    $spellName = str_replace("'", "('|&apos;)", $spellName);
    $spellName = escapeSqlString($spellName);
    
    // this regex matches "<apply " followed by any non ">" character, followed by the text: name="$spellname" the escaping of double quotes is for PHP, not the regex engine.
    $query = "SELECT name, event_script FROM progression_events WHERE event_script REGEXP '<apply [^>]*name=\"$spellName\"'";
    $result = mysql_query2($query);
    // multiple scripts could match our buff name.
    while ($row = fetchSqlAssoc($result))
    {
        $matches;
        // difference with the SQL above is this matching the whole <apply > tag down to the last >
        $pattern = '/<apply [^>]*name="'.$spellName.'"[^>]*>/';
        // preg_match_all stores all results in an *array* in element 0 of the $matches array. So the first result is actually $matches[0][0].
        preg_match_all($pattern, $row['event_script'], $matches);
        // there might be multiple <apply> tags in <if> branches
        foreach ($matches[0] as $match)
        {
            // we want to make sure that the <apply> we found is for a type="buff" or type="debuff", in addition to just containing the right name.
            if (stripos($match, 'type="buff"') !== false || stripos($match, 'type="debuff"') !== false)
            {
                return; // valid, we found something.
            }
        }
    }
    // Last resort, search for a progression event parameter in *all* quest scripts to see if anyone can make the magic we want.
    $escapedMagic = escapeSqlString($magic);
    // search for the magic name enclosed by single quotes, only progression event parameters should look like that.
    $query = "SELECT script FROM quest_scripts WHERE script LIKE '%\'$escapedMagic\'%'";
    $result = mysql_query2($query);
    while ($row = fetchSqlAssoc($result))
    {
        if (validateMagicNameInProgressionScriptParam($row['script'], $magic))
        {
            return;
        }
    }
    
    // if we reach this point, complain.
    append_log("Parse Error: could not find magic '$magic' in the magic database, or in any progression event at line $line_number");
}

// #winning in descriptive function names.
// returns true if found, false if not.
function validateMagicNameInProgressionScriptParam($questScript, $magic)
{
    $matches;
    // We assume a correct script here. 
    // Search for somethign that looks like: Run script give_quest_timeout <<'Red Way staff repair timer',10>>
    $pattern = '/Run script [^\n]*<<[^>]*\''.$magic.'\'[^>]*>>/';
    // preg_match_all stores all results in an *array* in element 0 of the $matches array. So the first result is actually $matches[0][0].
    preg_match_all($pattern, $questScript, $matches);
    foreach ($matches[0] as $match)
    {
        // remove "run script" at the start and everything after << as well.
        $eventName = trim(substr($match, 10, strpos($match, '<<') - 10));
        // make an array with each param.
        $params = explode(',', trim(substr($match, strpos($match, '<<') + 2, strpos($match, '>>') - (strpos($match, '<<') +2))));
        $paramIndex = 0; // determine the index, notice that is is always found at some position or we would not be here.
        foreach ($params as $index => $param)
        {
            if (strcmp(trim($param, "'"), $magic) === 0) // check param without '' against magic name. 
            {
                $paramIndex = $index;
                break;
            }
        }
        $query = sprintf("SELECT event_script FROM progression_events WHERE name = '%s'", escapeSqlString($eventName));
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        $eventScript = $row['event_script'];
        
        $doc = new DOMDocument();
        @$doc->loadXML($eventScript); // load script as XML, suppressing any errors.
        $lets = $doc->getElementsByTagName('let'); // find all <let> tags
        foreach ($lets as $let)  // Notice that <let> tags can be nested in theory, we assume they are not.
        {
            $alias = array('Param'.$paramIndex);
            $vars = explode(';', $let->getAttribute('vars'));
            foreach ($vars as $var)
            {
                if (trim($var) == '') // last variable can have an additional ; at the end, ignore that.
                {
                    continue;
                }
                $explodedVar = explode('=', $var);
                if (strcmp($explodedVar[1], $alias[0]) === 0) // if our ParamX got assigned to any variable, we want to track that variable too.
                {
                    $alias[] = $explodedVar[0]; // save it as an alias
                }
            }
            $applies = $let->getElementsByTagName('apply'); // find all apply tags inside the <let> tag
            foreach ($applies as $apply)
            {
                // if name attribute is a known alias for our parameter, this script can apply the magic we need.
                if (in_array($apply->getAttribute('name'), $alias) && ($apply->getAttribute('type') == 'buff' || $apply->getAttribute('type') ==  'debuff')) 
                {
                    return true;
                }
            }
        }
        // if we're still here, we need to try an match any <apply> tags outside <let> tags. 
        $applies = $doc->getElementsByTagName('apply'); // find all apply tags inside the document
        foreach ($applies as $apply)
        {
            // if name attribute is a known alias for our parameter, this script can apply the magic we need.
            if (in_array($apply->getAttribute('name'), array('Param'.$paramIndex)) && ($apply->getAttribute('type') == 'buff' || $apply->getAttribute('type') ==  'debuff')) 
            {
                return true;
            }
        }
    }
    return false;
}

function validate_race($race_name)
{
    global $line_number;
    $race = trim($race_name);
    $query = sprintf("SELECT id FROM race_info WHERE name = '%s'", escapeSqlString($race));
    $result = mysql_query2($query);
    if (sqlNumRows($result) < 1)
    {
        append_log("Parse Error: could not find race ($race) in the database at line $line_number");
    }
}

function validate_scriptname($scriptname)
{
    global $line_number;
    $script = trim($scriptname);
    $query = sprintf("SELECT name FROM progression_events WHERE name = '%s'", escapeSqlString($script));
    $result = mysql_query2($query);
    if (sqlNumRows($result) < 1)
    {
        append_log("Parse Error: could not find script name ($script) in the database at line $line_number");
    }
}

function validate_trait($tra)
{
    global $line_number;
    $trait = explode(' in', trim($tra), 2);
    if (count($trait) != 2)
    {
        append_log("Parse Error: no ' in' found in trait requirement ($tra) at line $line_number");
        return;
    }
    $name = trim($trait[0]);
    $location = trim($trait[1]);
    
    $query = sprintf("SELECT id FROM traits WHERE name = '%s' AND location = '%s'", escapeSqlString($name), escapeSqlString($location));
    $result = mysql_query2($query);
    if (sqlNumRows($result) < 1)
    {
        append_log("Parse Error: could not find trait name ($name) for location ($location) in the database at line $line_number");
        return;
    }
}

function validate_gender($gen)
{
    global $line_number;
    $gender = trim($gen);
    if (strcmp($gender, "male") === 0)
    {
        // valid, do nothing
    }
    elseif (strcmp($gender, "female") === 0)
    {
        // valid, do nothing
    }
    elseif (strcmp($gender, "neutral") === 0)
    {
        // valid, do nothing
    }
    elseif (strcasecmp($gender, "male") === 0 || strcasecmp($gender, "female") === 0 || strcasecmp($gender, "neutral") === 0)
    {
        append_log("Parse Error: encountered gender with uppercase char ($gender) use lower case only at line $line_number");
    }
    else
    {
        append_log("Parse Error: encountered unknown gender ($gender) at line $line_number");
    }
}
