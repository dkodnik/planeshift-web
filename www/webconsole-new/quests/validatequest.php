<?php 

/**
*   Planeshift quest script validator
*   Author: G. Hofstee
*/

$parse_log = '';
$line_number = 0;
function validatequest()
{
    if(checkaccess('quests', 'read'))
    {
        global $parse_log;
        $id = (isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : 0)); // If an ID is posted, use that, otherwise, use GET, if neither is available, use 0.
        echo '
<p>show script lines means it will show all lines it found in the script and number them (so you can look at what errors belong
to what line in your browser).</p>
<form method="post" action="./index.php?do=validatequest&amp;id='.$id.'">
    <div>
        <table>
            <tr><td>Quest ID:</td><td><input type="text" name="id" value="'.$id.'" /></td></tr>
            <tr><td><input type="checkbox" name="show_lines" />Show script lines?</td><td></td></tr>
        </table>
        <input type="submit" name="submit" value="submit" />
    </div>
</form>
';

        if(isset($_POST['submit']))
        {
            $show_lines = isset($_POST['show_lines']);
            if (is_numeric($id))
            {
                parseScripts($id, $show_lines);
            }
            append_log('<a href="./index.php?do=editquest&amp;id='.$id.'">Edit this script</a>');
            append_log('');
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
function parseScripts($quest_id, $show_lines) 
{
    $result = mysql_query2("SELECT script FROM quest_scripts WHERE quest_id = '$quest_id'"); 
    if (mysql_num_rows($result) < 1)
    {
        echo '<p class="error">Error: no quest found with ID '.$quest_id.'</p>';
        return;
    }
    for($i = 1; $row = mysql_fetch_row($result); $i++)
    {
        append_log('<p class="error">');
        append_log("parsing script # $i with ID $quest_id"); 
        parseScript($quest_id, $row[0], $show_lines);
        append_log("parsing script # $i with ID $quest_id completed");
        append_log('</p>');
    }
}

function parseScript($quest_id, $script, $show_lines, $quest_name='') 
{
    $line = '';
    $p_count = 0;
    $m_count = 0;
    $npc_name = '';
    $step = 1;
    $total_steps = count(explode('...', $script));
    $assigned = false; // to check if the quest is already assigned.
    global $line_number;
    $line_number = 0;
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
        echo "Quest ID: $quest_id <br />\n";
    }
    
    while(getNextLine($line, $script)) 
    {
        if($show_lines)
        {
            echo "$line_number: $line <br />\n"; // debug line, shows you all the lines of the script.
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
                append_log("parse error, there have been more P: or player than npc: tags before $line_number");
            }
            if ($m_count > 0) // there should always be a P: tag before every new set of menu: tags, so we can check this here.
            {
                append_log("parse error, there have been more Menu: than npc: tags before $line_number");
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
                append_log("parse error, P: with no text on line $line_number");
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
            	append_log("parse error, found a Menu: trigger following an NPC: trigger on line $line_number");
            }
            if ($seen_menu_triggers) 
            {
            	append_log("parse error, found two sets of Menu: triggers before line $line_number");
            }
            if (getTriggerCount($line, 'Menu:', $count, 80) === false)
            {
                append_log("parse error, Menu: with no text on line $line_number");
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
            // if a line starts with questnote, the : was likely added as a mistake since no NPCs are named "questnote". Send warning.
            if(strncasecmp($line, 'QuestNote', 9) === 0)
            {
                append_log("parse error, found QuestNote and a colon ':' on the same line, the QuestNote command can not contain colons at line $line_number");
                continue;
            }
            $seenTripleDot = false;
            // Every P: and NPC: combo should be unique, we don't check this atm. (This means a trigger may not already exist, requires parsing of all scripts.)
            $count = 0;
            $seen_npc_triggers = true;
            $temp_name = substr($line, 0, strpos($line, ':'));
            if (stripos($npc_name, $temp_name) === false) // new npc name
            {
                $npc_name = $temp_name;
                if ($quest_id == -1 && $npc_name == 'general')
                {
                    // valid situation, general means all npcs, does not exist in database.
                }
                elseif (validate_npc($npc_name) === false) 
                {
                    append_log("parse error, invalid NPC name: $npc_name at line $line_number");
                }
            }
            
            if(getTriggerCount($line, $temp_name.':', $count) === false) //get the amount of NPC: tags .':' adds the ':' which is not included in the name.
            {
                append_log("parse error, $temp_name:  with no text on line $line_number");
            }
            $p_count -= $count; // substract the amount of npc: tags from the P: count, they should match 1 on 1 .
            $m_count -= $count; // substract the amount of npc: tags from the P: count, they should match 1 on 1 .
            if ($p_count < 0) 
            {
                append_log("parse error, there are more $npc_name: triggers than there are P: or player triggers before line $line_number");
            }
            // Menu: if "officially" optional, so if there are none, it is valid too (should be old quests only). If ?= was seen in a menu, the count doesn't matter.
            if ($seen_menu_triggers && $m_count < 0 && !$menuInputBox) 
            {
                append_log("parse error, there are more $npc_name: triggers than there are Menu: triggers before line $line_number");
            }
            checkVariables($line, 'npc');
            
        }
        elseif(strncasecmp($line, 'Player ', 7) === 0) // player does something
        {
            $seenTripleDot = false;
            // If there was a previous set, the NPC: part should have reduced p/m_count to 0.
            if ($p_count > 0)  
            {
                append_log("parse error, there have been more P: or player than npc: tags before $line_number");
            }
            if ($m_count > 0) // there should always be a P: tag before every new set of menu: tags, so we can check this here.
            {
                append_log("parse error, there have been more Menu: than npc: tags before $line_number");
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
                append_log("parse error, there already is a QuestNote defined in the same step $step before line $line_number.");
            }
            if (trim(substr($line, 10)) == '')
            {
                append_log("parse error, empty Questnote at line $line_number.");
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
                append_log("parse error, there have been more P: or player than npc: tags before $line_number");
            }
            if ($m_count > 0 && !$menuInputBox) // if there was a Menu: ?= tag, it may match larger amounts of P/NPC tags.
            {
                append_log("parse error, there have been more Menu: than npc:/P: tags before $line_number");
            }
            if ($seen_menu_triggers && $pStar && !$menuInputBox) // if there was a P: * tag without Menu: ?= tag, it is likely a mistake.
            {
                append_log("Warning: found a 'P: *' entry without 'Menu ?=' before $line_number");
            }
            if (strlen($line) > 3)
            {
                if (strpos($line, ' ') != 3)
                {
                    append_log("parse error, no whitespace after ... at line $line_number");
                }
                elseif (strcasecmp(substr($line, 4, 8), 'norepeat') === 0) 
                {
                    // valid, do nothing
                }
                elseif (trim(substr($line, 4)) == '') 
                {
                    // valid, do nothing
                }
                else
                {
                    append_log("parse error, unknown entry following ... at line $line_number");
                }
            }
            // check for quest notes
            if (!$quest_note_found && $step > 1) 
            {
                append_log("Warning: step $step has no QuestNote before line $line_number");
            }
            $quest_note_found = false;
            $step++;
            $pStar = $menuInputBox = $seen_npc_triggers = $seen_menu_triggers = false;
            $count = $m_count = $p_count = 0;

        }
        else // we found a command
        {
            $commands = explode('.', $line);  // Notice that this also drops the trailing '.' of every command.
            for($i = 0; $i < count($commands); $i++) // the last one is after the last dot, which is to be ignored.
            {
            	// the last one is after the last dot, and has no content, we can safely ignore this.
                if ($i == count($commands) - 1 && trim($commands[$i] == ''))
            	{
            		continue;
            	}
            	if (strncasecmp($commands[$i], 'norepeat', 8) === 0)
            	{
            		if (!$seenTripleDot) 
            		{
                        append_log("parse error, NoRepeat found without \"...\" before it on line $line_number");
            		}
            		$seenTripleDot = false;
            		continue;
            	}
            	$seenTripleDot = false;
            	
            	if(trim($commands[$i]) != '') 
                {
                    parse_command(trim($commands[$i]), $assigned, $quest_id, $total_steps, $quest_name);  // using totalsteps now, since we can both require and close future steps now.
                }
            	else 
            	{
            		append_log("Warning, empty command found at lin $line_number");
            	}
            }
        }
    }
    if(!$assigned && $quest_id > 0) //ignore KA scripts which are -1
    {
        append_log('parse error, script never assined any quest.');
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
    $parse_log.=$msg."<br />\n";
}

/**
* get the count for $trigger in $line and put it in $count. If any trigger has no text following it, return false otherwise true.
*/ 
function getTriggerCount($line, $trigger, &$count, $max_chars_per_line='99999')
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
                    append_log("Warning, no ':' after '$trigger_without_colon' found on line $line_number, please make sure this is intended.");
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
        if (strlen($temp_line) > $max_chars_per_line)
        {
            append_log("Warning, more than $max_chars_per_line char found in trigger on line $line_number");
        }
        check_parenthesis($temp_line); // check for parenthesis and and if they're found, check if they're valid.
        $pos += strlen($trigger); // move the position pointer past our first find so it doesn't get seen again.
    }
    return true;
}

function checkVariables($line, $type)
{
    global $line_number;
    $words = preg_split("/[\s,.?!]+/", $line); // splits a line by any space characters (\n \t \r \f) as well as any comma or dot. + means greedy (tries to make as many matches as possible).
    foreach($words as $word)
    {
        if (strpos(trim($word), '$') !== false)  // A $variable was found in this word
        {
            if ($type == 'npc' || $type == 'menu')  // currently there are no other types, there could be in the future though, so this can be easily extended.
            {
                if ($word != '$playerrace' && $word != '$sir' && $word != '$playername' && $word != '$his' && $word != '$time' && $word != '$npc')
                {
                    append_log("parse error, misplaced variable ($word) on line $line_number");
                }
            }
            else 
            {
                append_log("parse error, misplaced variable ($word) on line $line_number. Was not expecting any variable in this line.");
            }
        }
    }
}

function validate_npc($name)
{
    $split = explode(" ", trim($name));  // explode is faster than split if you don't use regex, returns input if pattern is not found.
    if (count($split) == 1) // single name npc
    {
        $query = sprintf("SELECT id FROM characters WHERE name = '%s' AND lastname = '' AND npc_master_id != 0", mysql_real_escape_string($split[0]));
        $result = mysql_query2($query);
        if(mysql_num_rows($result) > 0) // we found a valid npc
        {
            return true;
        }
    }
    elseif (count($split) == 2) // dual name npc
    {
        $query = sprintf("SELECT id FROM characters WHERE name = '%s' AND lastname = '%s' AND npc_master_id != 0", mysql_real_escape_string($split[0]), mysql_real_escape_string($split[1]));
        $result = mysql_query2($query);
        if(mysql_num_rows($result) > 0) // we found a valid npc
        {
            return true;
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
                append_log("parse error, empty file declaration in $inside at line $line_number");
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
                append_log("parse error, invalid pronoun {$pronoun[0]} on line $line_number");
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
            append_log("parse error, $rightchar before $leftchar at line $line_number");
            return false;
        }
        $inside = substr($line, $pos_start+strlen($leftchar), $pos_end - $pos_start - strlen($leftchar));  // start+strlen coz we don't want the starting { or ( or whatever
        if(trim($inside) == '')
        {
            append_log("parse error, no text between $leftchar and $rightchar on line $line_number");
        }
        $pos_start = strpos($line, $leftchar, $pos_start+strlen($leftchar));
        $pos_end = strpos($line, $rightchar, $pos_end+strlen($rightchar));
    }
    if ($pos_start !== false) 
    {
        append_log("parse error, $leftchar without $rightchar at line $line_number");
        return false;
    }
    elseif ($pos_end !== false) 
    {
        append_log("parse error, $rightchar without $leftchar at line $line_number");
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
        append_log("parse error, no command following 'player' at line $line_number");
        return;
    }
    if (strcasecmp($words[1], 'gives') === 0) // "player gives"
    {
        $name_count = 0;
        if (count($words) < 3)
        {
            append_log("parse error, no npc name following 'player gives' at line $line_number");
            return;
        }
        $query = sprintf("SELECT id FROM characters WHERE name = '%s' AND lastname = '' AND npc_master_id != 0", mysql_real_escape_string($words[2]));
        $result = mysql_query2($query);
        if (mysql_num_rows($result) > 0) // we found a valid npc
        {
            $name_count = 1;
        }
        if ($name_count == 0 && count($words) > 3)
        {
            $query = sprintf("SELECT id FROM characters WHERE name = '%s' AND lastname = '%s' AND npc_master_id != 0", mysql_real_escape_string($words[2]), mysql_real_escape_string($words[3]));
            $result = mysql_query2($query);
            if (mysql_num_rows($result) > 0) // we found a valid npc
            {
                $name_count = 2;
            }
        }
        if ($name_count == 0)
        {
            append_log("parse error, could not find NPC name in \"player gives\" on line $line_number");
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
                    append_log("parse error, cannot give a negative amount of items in 'player gives' on line $line_number");
                }
                elseif (count($parts) < 2)
                {
                    append_log("parse error, could not read item name in 'player gives' on line $line_number");
                }
                else
                {
                    validate_item(trim(implode(' ', array_slice($parts, 1))));
                }
            }
            else
            {
                validate_item(trim($item[$i]));
            }
        }
    }
    else
    {
        append_log("parse error, unknown player command: player {$words[1]} at line $line_number");
    }
}

/*
 * Quest name got added for the prospect validator, in which case the quest is not in the database. ($quest_id = 0) Then in the case of "complete quest" and
 * "require completion of quest", we should check first if id==0 and name==name, before looking on the database.
 */
function parse_command($command, &$assigned, $quest_id, $step, $quest_name)  
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
            elseif (strncasecmp($command, "complete $quest_name step", 14+strlen($quest_name)) === 0)
            {
                $split_complete = explode(' ', substr(trim($command), 15+strlen($quest_name)));
                if (count($split_complete) > 1) 
                {
                    append_log("parse error, illegal text following 'complete $quest_name step {$split_complete[0]}' on line $line_number");
                }
                elseif ($split_complete[0] != '' && is_numeric($split_complete[0]) && $split_complete[0] <= $step && $split_complete[0] > 0) 
                {
                    // valid, do nothing
                }
                else
                {
                    append_log("parse error, completing a step that is higher than the total number of steps in this quest on line $line_number");
                }
            }
            else
            {
                append_log("parse error, invalid questname ($command) at line $line_number");
            }
            return; // in all cases, when $quest_id is 0, do not go further than here. (the other checks are on the database, and thus will fail.
        }
        $query = sprintf("SELECT name FROM quests WHERE id = '%s'", mysql_real_escape_string($quest_id));
        $result = mysql_query2($query); // this may bug up if more quests have the same id (which they shouldn't have(?)) (KA scripts are exluded since they can't complete.
        if(mysql_num_rows($result) > 0)
        {
            $row = mysql_fetch_row($result);
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
                    append_log("parse error, illegal text following 'complete $name step {$split_complete[0]}' on line $line_number");
                }
                elseif ($split_complete[0] != '' && is_numeric($split_complete[0]) && $split_complete[0] <= $step && $split_complete[0] > 0) 
                {
                    // valid, do nothing
                }
                else
                {
                    append_log("parse error, completing a step that is higher than the total number of steps in this quest on line $line_number");
                }
            }
            else
            {
                append_log("parse error, invallid questname ($command) at line $line_number");
            }
        }
        else 
        {
            append_log("parse error, could not determine questname for this script at line $line_number");
        }
    }
    elseif (strncasecmp($command, 'give', 4) === 0)
    {
        $given = explode(' or ', substr($command, 5));
        if (count(explode(' or ', strtolower($command))) > count($given))
        {
            append_log("parse error, encountered uppercase \"or\" in give command on line $line_number");
            return;
        }
        for ($i = 0; $i < count($given); $i++)  // a choice may be presented seperated by "or", so we need to validate them all.
        {
            if (trim($given[$i] == ''))
            {
                append_log("parse error, nothing to give on line $line_number");
                return;
            }
            // to lower case so we can handle this case insensitive.
            $words = explode(' ', trim(strtolower($given[$i])));
            // check all cases of "give money".
            if (in_array('tria', $words, true) || in_array('hexa', $words, true) || in_array('octa', $words, true) || in_array('circle', $words, true))
            { 
                if(count($words) != 2)
                {
                    append_log("parse error, too much/few parameters for 'give 1 money'(example) (money = tria/hexa/octa/circle) at line $line_number");
                }
                elseif (strcasecmp($words[1], 'hexa') === 0 || strcasecmp($words[1], 'tria') === 0 || strcasecmp($words[1], 'octa') === 0 || strcasecmp($words[1], 'circle') === 0)
                {
                    if (is_numeric($words[0]) && $words[0] > 0)
                    { // valid case
                        continue;
                    }
                    else
                    {
                        append_log("parse error, while giving money (tria/hexa/octa/circle), the parameter before the currency should be a positive number at line $line_number");
                    }
                }
                else
                {
                    append_log("parse error, while giving money (tria/hexa/octa/circle), the currency should be the last parameter at line $line_number");
                }
            }
            elseif (in_array('faction', $words, true))
            {
                if (count($words) < 3) 
                {
                    append_log("parse error, 'give 1 faction factionname' (example) requires at least 4 paramers at line $line_number");
                }
                elseif (!is_numeric($words[0]) || $words[0] < 0 || strcasecmp($words[1], 'faction') !== 0)
                {
                    append_log("parse error, 'give 1 faction factionname' (example) requires the second parameter to be a positive number and the third to be 'faction' at line $line_number");
                }
                else
                { // the remaining words should be the faction name.
                    validate_faction(implode(' ', array_slice($words, 2)));
                }
            }
            elseif (in_array('exp', $words, true))
            {
                if(count($words) != 2)
                {
                    append_log("parse error, too much/few parameters for exp command 'give 1 exp'(example) at line $line_number");
                }
                elseif (!is_numeric($words[0]) || $words[0] < 0 || strcasecmp($words[1], 'exp') !== 0)
                {
                    append_log("parse error, 'give 1 exp' (example) requires the second parameter to be a positive number and the third to be 'exp' at line $line_number");
                }
                else
                { // valid
                    continue;
                }
            }
            else // it's an item.
            {
                if(count($words) == 1)
                {
                    validate_item($words[0]);
                }
                else
                {
                    if (is_numeric($words[0]))
                    {
                        if ($words[0] < 0)
                        {
                            append_log("parse error, item quantity must be positive at line $line_number");
                        }
                        else
                        {
                            if  (count($words) == 2 || !is_numeric($words[1]))
                            {
                                validate_item(implode(' ', array_slice($words, 1)));
                            }
                            elseif ($words[1] < 0 || $words[1] > 300)
                            {
                                append_log("parse error, item quality must be positive and 300 or less at line $line_number");
                            }
                            else
                            {
                                validate_item(implode(' ', array_slice($words, 2)));
                            }
                        }
                    }
                    else // all words are part of the item name.
                    {
                        validate_item(implode(' ', $words));
                    }
                }
            }
        }
    }
    elseif (strncasecmp($command, 'setvariable', 11) === 0)
    {
      $words = explode(' ', trim(substr($command, 11)));
      if (count($words)<2)
        append_log("parse error, setvariable needs 2 arguments at line $line_number");
    }
    elseif (strncasecmp($command, 'unsetvariable', 13) === 0)
    {
      $parameters = trim(substr($command, 13));
      $words = explode(' ', $parameters);
      if (trim($parameters)=='' || count($words)<1)
        append_log("parse error, unsetvariable needs 1 argument at line $line_number");
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
                append_log("parse error, could not load parameters at line $line_number");
                return;
            }
            $params = explode(',', $param);
            for($i = 0; $i < count($params); $i++)
            {
                if (trim($params[$i]) == '')
                {
                    append_log("parse error, found empty parameter in run script on line $line_number");
                }
                elseif (strpos($params[$i], '"') !== false)
                {
                    append_log("parse error, you are not allowed to use double quotes in parameters for run script on line $line_number");
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
                                append_log("parse error, no variable inside quotes in parameter for script on line $line_number");
                            }
                            elseif (is_numeric(substr($temp, 1, strlen($temp)-2)))
                            {
                                append_log("parse error, numeric parameters should not be inside quotes for script on line $line_number");
                            }// else valid
                        }
                        else
                        {
                            append_log("parse error, quotes should only be at the begining and the end of a parameter of a script on line $line_number");
                        }
                    }
                    else
                    {
                        append_log("parse error, invalid amount of single quotes in parameters for run script on line $line_number");
                    }
                }
            }
        }
    }
    elseif (strncasecmp($command, 'doadmincmd', 10) === 0)
    {
        $cmd = substr($command, 10);
        if (trim($cmd) == '') 
        {
            append_log("parse error, no admin command found on line $line_number");
            return;
        }
        $query = sprintf("SELECT group_member FROM command_group_assignment WHERE command_name = '%s'", mysql_real_escape_string($cmd));
        $result = mysql_query2($query);
        if (mysql_num_rows($result) < 1)
        {
            append_log("parse error, could not find admin command ($cmd) in the database");
        } // else it's found, do nothing.
    }
    elseif (strncasecmp($command, 'require', 7) === 0) 
    {
        // Found a "require command"
        $requirements = substr($command, 8); // remove the require part and it's trailing space.
        $requirements = explode(' | ', $requirements); // split on all cases of the OR operator.
   
        foreach($requirements AS $requirement) 
        {
            $require = $requirement; // we use this one for the error message later if need be.
            // Determine if the next word is "no" or "not" and remove that too. (It's not relevant for the parser to know which is the case, as in 
            // both cases it is whatever that follows that needs to be valid.)
            if (strncasecmp($require, 'not', 3) === 0) // notice the order of not and no (otherwise no will match the first 2 letters of not).
            {
                $require = substr($require, 4);
            }
            elseif (strncasecmp($require, 'no', 2) === 0)
            {
                $require = substr($require, 3);
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
                validate_magic(substr($require, 12));
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
            elseif (strncasecmp($require, 'married', 7) === 0)
            {
                // valid, nothing to check  
            }
            elseif (strncasecmp($require, 'possessed', 9) === 0 || strncasecmp($require, 'equipped', 8) === 0) // case for possessed and equipped are identical
            {
                $item = trim(substr($require, (strncasecmp($require, 'possessed', 9) === 0 ? 9 : 8)));
                $cat_pos = strpos($item, 'category');
                $item_pos = strpos($item, 'item');
                if ($cat_pos === false && $item_pos === false)
                {
                    append_log("parse error, no 'item' or 'category' identifier in possessed/equipped command at line $line_number");
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
                        append_log("warning, min quality missing while quality seperator is present in possessed/equipped command at line $line_number");
                    }
                    if (count($quality) == 2 && trim($quality[1]) == '')
                    {
                        append_log("warning, max quality missing while quality seperator is present in possessed/equipped command at line $line_number");
                    }
                    if (count($quality) > 2)
                    {
                        append_log("parse error, you can only use 1 minus sign to seperate min/max quality in possessed/equipped command at line $line_number");
                    }
                    elseif (trim($quality[0]) != '' && (!is_numeric(trim($quality[0])) || trim($quality[0]) > 300 || trim($quality[0]) < 0))
                    {
                        append_log("parse error, min quality should be between 0 and 300 in possessed/equipped command at line $line_number");
                    }
                    elseif (count($quality) == 2 && trim($quality[1]) != '' && (!is_numeric(trim($quality[1])) || trim($quality[1]) > 300 || trim($quality[1]) < 0))
                    {
                        append_log("parse error, max quality should be between 0 and 300 in possessed/equipped command at line $line_number");
                    }
                    elseif (count($quality) == 2 && trim($quality[1]) != '' && trim($quality[0]) != '' && trim($quality[0]) > trim($quality[1]))
                    {
                        append_log("parse error, min quality cannot exceed max quality in possessed/equipped command at line $line_number");
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
                    append_log("parse error, no '-' sign follows the skill name at line $line_number");
                    return;
                }
                // the last space before the last '-' sign will be what splits the skill range from the skill name.
                $lastspace = strrpos($require, ' ', -(strlen($require)-$lasthyphen));
                $skillname = substr($require, 0, $lastspace);
                $skillrange = substr($require, $lastspace+1);
                if ($lastspace === false || trim($skillname) == '')
                {
                    append_log("parse error, could not determine skill name at line $line_number");
                    return;
                }
                $skillranges = explode('-', $skillrange);
                if (count($skillranges) != 2 || !is_numeric(trim($skillranges[0])) || $skillranges[0] < 0 ||
                    !is_numeric(trim($skillranges[1])) || $skillranges[1] < 0 || $skillranges[0] > $skillranges[1])
                {
                    append_log("parse error, invalid skill range at line $line_number");
                    return;
                }
                // might be valid, but is unusual, warn to pay attention.
                if ($skillranges[0] > 200 || $skillranges[1] > 200)
                {
                    append_log("Warning, skill exceeds 200 at line $line_number - be sure this is intended");
                }
                
                validate_skill($skillname);
            }
            elseif (strncasecmp($require, 'variable', 8) === 0)
            {
              $parameters = explode(" ", trim($require));
              if (count($parameters) != 2 || trim($parameters[0]) == "" || trim($parameters[1]) == "")
                append_log("parse error, Require variable needs 1 argument at line $line_number");
            }
            else 
            {
                append_log("parse error, unknown requirement (require $requirement) at line $line_number");
            }
        }
    }
    elseif (strncasecmp($command, 'Introduce', 9) === 0)
    {
    }
    elseif (strncasecmp($command, 'Menu', 4) === 0) // This is basically an error catcher, it should be below all other cases.
    {
        append_log("parse error, no ':' following 'Menu' at line $line_number");
    }
    elseif (strncasecmp($command, "P", 1) === 0) // This is basically an error catcher, it should be below all other cases.
    {
        append_log("parse error, no ':' following 'P' at line $line_number");
    }
    else 
    {
        append_log("parse error, unknown command ($command) at line $line_number");
    }
}

function check_completion($quest_id, $step, $quest, $quest_name)
{
    global $line_number;
    if (trim($quest) == '')
    {
        append_log("parse error, no quest mentioned at line $line_number");
        return;
    }
    if ($quest_id == 0) // special number used for prospect console. Means the quest itself is not in the database, so lets check if it is valid with the name that was given.
    {
        if (strcasecmp(trim($quest), $quest_name) === 0)
        {
            return; //valid, nothing else to do.
        }
        else if (strncasecmp(trim($quest), "$quest_name step", 5+strlen($quest_name)) === 0)
        {
            if (trim(substr(trim($quest), 5+strlen($quest_name))) <= $step)
            {
                // valid too
                return;
            }
            else
            {
                append_log("parse error, you can't refer to quest steps that exceed the total number of steps at line $line_number");
                return;
            }
        }
        // else we need to run past the rest of the checks, though the next one is guaranteed to fail with quest_id 0, the one after that may pass.
    }
    $result = mysql_query2("SELECT name FROM quests WHERE id = '$quest_id'");
    if (mysql_num_rows($result) > 0) // First we check if it's a reference to this script (most of them are)
    {
        $row = mysql_fetch_row($result);
        $name = $row[0];
        if (strcasecmp(trim($quest), $name) === 0)
        {
            // valid, nothing else to do
            return;
        }
        else if (strncasecmp(trim($quest), "$name step", 5+strlen($name)) === 0)
        {
            if (trim(substr(trim($quest), 5+strlen($name))) <= $step)
            {
                // valid too
                return;
            }
            else
            {
                append_log("parse error, you can't refer to quest steps that exceed the total number of steps at line $line_number");
                return;
            }
        }
    }// if it's not, we need to check all data.
    $name = trim($quest);
    $complete_step = '';
    if (($pos = stripos($quest, 'step')) !== false)
    {
        $name = trim(substr($quest, 0, $pos));
        $complete_step = trim(substr($quest, $pos+4));
        if ($name == '')
        {
            append_log("parse error, no quest mentioned at line $line_number");
            return;
        }
        elseif ($complete_step == '' || $complete_step < 1)
        {
            append_log("parse error, invalid quest step at line $line_number");
            return;
        }
    }
    $query = sprintf("SELECT id FROM quests WHERE name='%s'", mysql_real_escape_string($name));
    $result = mysql_query2($query); 
    if (mysql_num_rows($result) > 0)  // found a quest with that name
    {
        append_log("Warning, references to another quest are not recommended, only use if you really must: line $line_number");
        $row = mysql_fetch_row($result);
        $id = $row[0];
        if($complete_step == '')
        {
            // found a matching quest with no steps, we're done checking.
            return;
        }
        else
        {
            $result = mysql_query2("SELECT script FROM quest_scripts WHERE quest_id = '$id'"); 
            if (mysql_num_rows($result) > 0)  // found a quest with that name
            {
                $row = mysql_fetch_row($result);
                $target_steps = explode('...', $row[0]);
                if ($complete_step > count($target_steps)) // target quest does not have this many steps
                {
                    append_log("parse error, target quest does not have $complete_step steps at line $line_number");
                }
            }
            else
            {
                append_log("parse error, there is no script for id $id (which belongs to $quest) in the database at line $line_number");
            }
        }
    }
    else
    {
        append_log("parse error, could not find any quest named $name in the database at line $line_number");
    }
}

function validate_time_of_day($time)
{
    global $line_number;
    if (trim($time) == "")
    {
        append_log("parse error, could not determine time on line $line_number");
        return;
    }
    $minmax = explode("-", $time);
    if (count($minmax) != 2 || trim($minmax[0]) == "" || trim($minmax[1]) == "")
    {
        append_log("parse error, invalid time format ($time) on line $line_number");
        return;
    }
    if (trim($minmax[0]) > trim($minmax[1]))
    {
        append_log("warning, time min: {$minmax[0]} is before max: {$minmax[1]} at line $line_number");
        return;
    }
    if (trim($minmax[0]) < 0 || trim($minmax[0]) > 24)
    {
        append_log("warning, time min: {$minmax[0]} is not between 0 and 24 at line $line_number");
        return;
    }
    if (trim($minmax[1]) < 0 || trim($minmax[1]) > 24)
    {
        append_log("warning, time max: {$minmax[1]} is not between 0 and 24 at line $line_number");
        return;
    }
    // all other cases are valid, do nothing.
}

function validate_skill($skillname)
{
    global $line_number;
    $query = sprintf("SELECT skill_id FROM skills WHERE name = '%s'", $skillname);
    $result = mysql_query2($query);
    if (mysql_num_rows($result) == 1)
    {
        // valid skill, do nothing
    } else
        append_log ("parse error, skill $skillname not valid at line $line_number");
}

function validate_item($itemname, $case_sensitive = false)
{
    global $line_number;
    if (trim($itemname) == '')
    {
        append_log("parse error, could not read item name on line $line_number");
        return;
    }
    $query = sprintf("SELECT name FROM item_stats WHERE name = '%s' AND stat_type='B'", mysql_real_escape_string($itemname));
    $result = mysql_query2($query);
    if (mysql_num_rows($result) == 1)
    {
        if (!$case_sensitive)
        {
            return; // valid item, do nothing.
        }
        $row = mysql_fetch_row($result);
        $item_case = $row[0];
        $query = sprintf("SELECT name FROM item_stats WHERE name = BINARY '%s' AND stat_type='B'", mysql_real_escape_string($itemname));
        $result = mysql_query2($query);
        if (mysql_num_rows($result) < 1)
        {
            append_log("parse error, item name at this position is case sensitive, use '$item_case' instead of '$itemname' in database on line $line_number");
        }
        // valid item, do nothing
    }
    elseif (mysql_num_rows($result) > 1)
    {
        append_log("warning: multiple items with name: $itemname in database on line $line_number");
    }
    else
    {
        append_log("parse error, no item with name: $itemname in database on line $line_number");
    } 
}

function validate_category($categoryname)
{
    global $line_number;
    if (trim($categoryname) == '')
    {
        append_log("parse error, could not read category name on line $line_number");
    }
    $query = sprintf("SELECT category_id FROM item_categories WHERE name = '%s'", mysql_real_escape_string($categoryname));
    $result = mysql_query2($query);
    if (mysql_num_rows($result) < 1)
    {
        append_log("parse error, no category with name: $categoryname in database on line $line_number");
    }
}
    

function validate_faction($factionname)
{
    global $line_number;
    $query = sprintf("SELECT id FROM factions WHERE faction_name = '%s'", mysql_real_escape_string($factionname));
    $result = mysql_query2($query);
    if(mysql_num_rows($result) < 1)
    {
        append_log("parse error, no faction ($faction) found in database on line $line_number");
    }
}

function validate_magic($magic_name)
{
    global $line_number;
    $magic = trim($magic_name);
    $query = sprintf("SELECT id FROM spells WHERE name = '%s'", mysql_real_escape_string($magic));
    $result = mysql_query2($query);
    if (mysql_num_rows($result) < 1)
    {
        append_log("parse error, could not find magic ($magic) in the database at line $line_number");
    }
}

function validate_race($race_name)
{
    global $line_number;
    $race = trim($race_name);
    $query = sprintf("SELECT id FROM race_info WHERE name = '%s'", mysql_real_escape_string($race));
    $result = mysql_query2($query);
    if (mysql_num_rows($result) < 1)
    {
        append_log("parse error, could not find race ($race) in the database at line $line_number");
    }
}

function validate_scriptname($scriptname)
{
    global $line_number;
    $script = trim($scriptname);
    $query = sprintf("SELECT name FROM progression_events WHERE name = '%s'", mysql_real_escape_string($script));
    $result = mysql_query2($query);
    if (mysql_num_rows($result) < 1)
    {
        append_log("parse error, could not find script name ($script) in the database at line $line_number");
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
        append_log("parse error, encountered gender with uppercase char ($gender) use lower case only at line $line_number");
    }
    else
    {
        append_log("parse error, encountered unknown gender ($gender) at line $line_number");
    }
}
