<?php
function ka_trigger()
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    if (isset($_POST['commit']) && !checkaccess('npcs', 'edit'))
    {
        echo '<p class="error">You are not authorized to edit KAs</p>';
        return;
    }
    
    // handle create/delete buttons found on this page.
    if (isset($_POST['commit']) && $_POST['commit'] == "Create New KA Area")
    {  
        $area = escapeSqlString($_POST['area']);
        $trigger_text = escapeSqlString($_POST['trigger_text']);
        $query = "INSERT INTO npc_triggers (trigger_text, area) VALUES ('$trigger_text', '$area')";
        mysql_query2($query);
        $triggerId = sqlInsertId();
        $query = "INSERT INTO npc_responses (trigger_id) VALUES ('$triggerId')";
        mysql_query2($query);
        echo '<p class="error">Area Addition Successful</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == "Delete KA")
    {
        $area = escapeSqlString($_POST['area']);
        $query = "SELECT id FROM npc_triggers WHERE area='$area'";
        $result = mysql_query2($query);
        while ($row = fetchSqlAssoc($result))
        {
            $id = $row['id'];
            $q2 = "DELETE FROM npc_responses WHERE trigger_id='$id'";
            mysql_query2($q2);
        }
        $query = "DELETE FROM npc_knowledge_areas WHERE area='$area'";
        mysql_query2($query);
        $query = "DELETE FROM npc_triggers WHERE area='$area'";
        mysql_query2($query);
        echo '<p class="error">Area '.$area.' was successfully deleted</p>';
    }
    
    // notice that union only merges distinct entities, so if both selects have "general" as a result, only 1 is returned.
    $query = "(SELECT DISTINCT area FROM npc_triggers) UNION (SELECT DISTINCT area FROM npc_knowledge_areas) ORDER BY area";
    $result = mysql_query2($query);
    echo '<table border="1">';
    echo '<tr><th>KA</th>';
    if (checkaccess('npcs', 'edit'))
    {
        echo '<th>Action</th>';
    }
    echo '</tr>'."\n";
    while ($row = fetchSqlAssoc($result))
    {
        if ($row['area'] != '')
        {
            echo '<tr><td>';
            echo '<a href="./index.php?do=ka_detail&amp;area='.htmlentities($row['area']).'">'.$row['area'].'</a>';
            echo '</td>';
            if (checkaccess('npcs', 'edit'))
            {
                echo '<td><form action="./index.php?do=ka_trigg" method="post"><div>';
                echo '<input type="hidden" name="area" value="'.htmlentities($row['area']).'" />';
                echo '<input type="submit" name="commit" value="Delete KA" /></div></form></td>';
            }
            echo '</tr>'."\n";
        }
    }
    echo '</table>'."\n";
    if (checkaccess('npcs', 'edit'))
    {
        echo '<p>Create New Trigger:</p>'."\n";
        echo '<form action="./index.php?do=ka_trigg" method="post">'."\n";
        echo '<table border="1">'."\n";
        echo '<tr><td>KA area name: </td><td><input type="text" name="area" /></td></tr>'."\n";
        echo '<tr><td>KA trigger text: </td><td><input type="text" name="trigger_text" /></td></tr>'."\n";
        echo '<tr><td><input type="submit" name="commit" value="Create New KA Area" /></td><td></td></tr>'."\n";
        echo '</table>'."\n";
        echo '</form>'."\n";
    }
}

function ka_detail()
{
    // access checks
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    if (isset($_POST['commit']) && !checkaccess('npcs', 'edit'))
    {
        echo '<p class="error">You are not authorized to edit KAs</p>';
        return;
    }
    
    // Handle any updates, then proceed by re-listing the details of this KA.
    if (isset($_POST['commit']) && $_POST['commit'] == "Save Changes")
    {
        $tid = escapeSqlString($_POST['trigger_id']);
        $trigger_text = escapeSqlString($_POST['trigger_text']);
        $query = "UPDATE npc_triggers SET trigger_text='$trigger_text' WHERE id='$tid'";
        mysql_query2($query);
        $response1 = escapeSqlString($_POST['response1']);
        $response2 = escapeSqlString($_POST['response2']);
        $response3 = escapeSqlString($_POST['response3']);
        $response4 = escapeSqlString($_POST['response4']);
        $response5 = escapeSqlString($_POST['response5']);
        $script = escapeSqlString($_POST['script']);
        $prerequisite = escapeSqlString($_POST['prerequisite']);
        $audio_path1 = escapeSqlString($_POST['audio_path1']);
        $audio_path2 = escapeSqlString($_POST['audio_path2']);
        $audio_path3 = escapeSqlString($_POST['audio_path3']);
        $audio_path4 = escapeSqlString($_POST['audio_path4']);
        $audio_path5 = escapeSqlString($_POST['audio_path5']);
        $query = "UPDATE npc_responses SET response1='$response1', response2='$response2', response3='$response3', response4='$response4', ";
        $query .= "response5='$response5', script='$script', prerequisite='$prerequisite', audio_path1='$audio_path1', audio_path2='$audio_path2', ";
        $query .= "audio_path3='$audio_path3', audio_path4='$audio_path4', audio_path5='$audio_path5' WHERE trigger_id='$tid'";
        mysql_query2($query);
        echo '<p class="error">Trigger/response set updated.</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == "Remove")
    {
        $tid = escapeSqlString($_POST['trigger_id']);
        $query = "DELETE FROM npc_triggers WHERE id='$tid'";
        $result = mysql_query2($query);
        $query = "DELETE FROM npc_responses WHERE trigger_id='$tid'";
        $result = mysql_query2($query);
        echo '<p class="error">Trigger deleted.</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == "Create New Trigger")
    {
        $area = escapeSqlString($_GET['area']);
        $trigger_text = escapeSqlString($_POST['trigger_text']);
        $query = "INSERT INTO npc_triggers (trigger_text, prior_response_required, area) VALUES ('$trigger_text', '0', '$area')";
        $result = mysql_query2($query);
        $triggerId = sqlInsertId();
        $query = "INSERT INTO npc_responses (trigger_id) VALUES ('$triggerId')";
        mysql_query2($query);
        echo '<p class="error">Trigger created.</p>';
    }
    
    // make a url for all the forms and self-links to use, remove "trigger" if it is in there, the only links that want that set it themselves.
    // This also facilitates the use of this page by NPC->List personal KAs
    $urlParts = array();
    parse_str($_SERVER['QUERY_STRING'], $urlParts);
    if (array_key_exists('trigger', $urlParts))
    {
        unset($urlParts['trigger']);
    }
    $url = './index.php?'.htmlentities(http_build_query($urlParts));
    
    // we need an area to list our details.
    if (!isset($_GET['area']))
    {
        echo '<p class="error">No Area selected, redirecting you to "List KA triggers".</p>';
        ka_trigger();
        return;
    }
    $area = escapeSqlString($_GET['area']);
    $triggerId = (isset($_GET['trigger']) ? escapeSqlString($_GET['trigger']) : '');
    
    $query = "SELECT t.id, t.trigger_text, t.prior_response_required, r.id AS r_id, r.response1, r.response2, r.response3, r.response4, ";
    $query .= "r.response5, r.script, r.prerequisite, r.audio_path1, r.audio_path2, r.audio_path3, r.audio_path4, r.audio_path5 ";
    $query .= " FROM npc_triggers AS t LEFT JOIN npc_responses AS r ON t.id = r.trigger_id WHERE t.area = '$area'";
    $query .= " ORDER BY t.id = '$triggerId' DESC"; // this actually works as a normal order by if the variable is empty.
    
    $result = mysql_query2($query);
    if (sqlNumRows($result) == 0)
    {
        echo '<p class="error">KA has no entries.</p>';
    }
    else
    {
        echo '<table border="1"><tr><th>Trigger</th><th>Response</th>';
        if (checkaccess('npcs', 'edit'))
        {
            echo '<th>Action</th>';
        }
        
        // This is the NPC list (last column, it spans over all rows, +1 for the header row.
        echo '<td rowspan="'.(sqlNumRows($result)+1).'">The following NPC use this KA:<br />';
        // we try to split the area back into an NPC name, so we can find the owners of "personal" KAs 
        $nameParts = explode(' ', $area);
        // in case of only one name, this will be the same as firstName.
        $lastName = escapeSqlString($nameParts[count($nameParts) - 1]);
        // if there are both first and last names, assign all except the last to "firstName", otherwise (one name only), assign the same to both.
        $firstName = escapeSqlString(count($nameParts) > 1 ? substr($area, 0, -(strlen($lastName) + 1)) : $area);
        $query2 = "(SELECT c.id, c.name, c.lastname FROM npc_knowledge_areas AS nka LEFT JOIN characters AS c ON c.id=nka.player_id WHERE area='$area') ";
        $query2 .= "UNION ";
        $query2 .= "(SELECT id, name, lastname FROM characters WHERE name = '$area' OR lastname = '$area' OR (name = '$firstName' AND lastname = '$lastName')) ";
        $query2 .= "ORDER BY name";
        $result2 = mysql_query2($query2);
        while ($row2 = fetchSqlAssoc($result2)) 
        {
            echo '<a href="./index.php?do=npc_details&amp;sub=kas&amp;npc_id='.$row2['id'].'">'.htmlentities($row2['name']).' '.htmlentities($row2['lastname']).'</a><br />';
        }
        echo '</td>';
        echo '</tr>';
        
        // Loop through the results, the one with a matching trigger ID
        while ($row = fetchSqlAssoc($result))
        {
            $rowTriggerId = $row['id'];
            echo '<tr>';
            echo '<td>';
            // List the trigger details if the user selected a trigger
            if ($triggerId == $rowTriggerId)
            {
                if (checkaccess('npcs', 'edit'))
                {
                    // solitary input field, at the end of the table is a javascript that takes the content of this field and adds it to the main form.
                    echo '<input type="text" name="trigger_text" id="triggerText" value="'.htmlentities($row['trigger_text']).'"/>';
                }
                else
                {
                    echo htmlentities($row['trigger_text']);
                }
            }
            else
            {
                echo '<a href="'.$url.'&amp;trigger='.$rowTriggerId.'">'.htmlentities($row['trigger_text']).'</a>';
            }
            echo '</td>';
            echo '<td>';
            // List the content of response if the user selected a trigger
            if ($triggerId == $rowTriggerId)
            {
                if (checkaccess('npcs', 'edit'))
                {
                    echo '<form action="'.$url.'&amp;trigger='.$rowTriggerId.'" id="mainForm" method="post">';
                    echo '<table>';
                    echo '<tr><td class="align_right"><input type="hidden" name="trigger_id" value="'.$rowTriggerId.'" />';
                    // the following gets changed by javascript (script at the end of the table).
                    echo '<input type="hidden" name="trigger_text" id="triggerTextForm" value="'.htmlentities($row['trigger_text']).'" />';
                    echo '<input type="hidden" name="commit" value="Save Changes" />';
                    echo 'Response 1: </td>'; // td started 3 lines up
                    echo '<td><textarea name="response1" rows="4" cols="55">'.htmlentities($row['response1']).'</textarea></td></tr>';
                    echo '<tr><td class="align_right">Audio Path 1: </td>';
                    echo '<td><input type="text" name="audio_path1" size="55" value="'.$row['audio_path1'].'" /></td></tr>';
                    echo '<tr><td class="align_right">Response 2: </td>';
                    echo '<td><textarea name="response2" rows="4" cols="55">'.htmlentities($row['response2']).'</textarea></td></tr>';
                    echo '<tr><td class="align_right">Audio Path 2: </td>';
                    echo '<td><input type="text" name="audio_path2" size="55" value="'.$row['audio_path2'].'" /></td></tr>';
                    echo '<tr><td class="align_right">Response 3: </td>';
                    echo '<td><textarea name="response3" rows="4" cols="55">'.htmlentities($row['response3']).'</textarea></td></tr>';
                    echo '<tr><td class="align_right">Audio Path 3: </td>';
                    echo '<td><input type="text" name="audio_path3" size="55" value="'.$row['audio_path3'].'" /></td></tr>';
                    echo '<tr><td class="align_right">Response 4: </td>';
                    echo '<td><textarea name="response4" rows="4" cols="55">'.htmlentities($row['response4']).'</textarea></td></tr>';
                    echo '<tr><td class="align_right">Audio Path 4: </td>';
                    echo '<td><input type="text" name="audio_path4" size="55" value="'.$row['audio_path4'].'" /></td></tr>';
                    echo '<tr><td class="align_right">Response 5: </td>';
                    echo '<td><textarea name="response5" rows="4" cols="55">'.htmlentities($row['response5']).'</textarea></td></tr>';
                    echo '<tr><td class="align_right">Audio Path 5: </td>';
                    echo '<td><input type="text" name="audio_path5" size="55" value="'.$row['audio_path5'].'" /></td></tr>';
                    echo '<tr><td colspan="2"><hr/></td></tr>';
                    echo '<tr><td class="align_right">Script: </td>';
                    echo '<td><textarea name="script" id="script" rows="5" cols="55">'.htmlentities($row['script']).'</textarea></td></tr>';
                    echo '<tr><td class="align_right">Prerequisite: </td>';
                    echo '<td><textarea name="prerequisite" rows="5" cols="55">'.htmlentities($row['prerequisite']).'</textarea></td></tr>';
                    echo '</table>';
                    echo '</form>';
                    echo 'Add an action to the script: <span class="warning" id="scriptMsgBox"></span><br />';
                    echo '<select name="addToScript" id="addToScriptType" onchange="typeChanged()">';
                    echo '<option value="respond">Say one response</option>';
                    echo '<option value="respondpublic">Say one public response</option>';
                    echo '<option value="respondanim">Say one response and do an animation</option>';
                    echo '<option value="publicaction">Add a public action</option>';
                    echo '<option value="addanim">Add animation</option>';
                    echo '<option value="train">Train player</option>';
                    echo '<option value="assignquest">Assign quest</option>';
                    echo '<option value="completequest">Complete quest</option>';
                    echo '<option value="offeritem">Give Item</option>';
                    echo '</select>';
                    // this is the only way we can make the item select box appear atm, so we hide it, and let the javascript copy it when needed.
                    echo ' <span id="actionSubMenu"></span><span id="itemBox" style="display:none;">'.DrawItemSelectBox('action_sub').'</span>';
                    $skillNames = PrepSelect('skillnames');
                    echo '<span id="skillBox" style="display:none;">'.DrawSelectBox('skillnames', $skillNames, 'action_sub', '').'</span>';
                    echo '<input type="button" onclick="addToScript()" value="add action to script" />';
                    
                }
                else
                {
                    echo 'Response 1: '.htmlentities($row['response1']).'<br/>';
                    echo 'Audio Path 1: '.$row['audio_path1'].'<br/>';
                    echo 'Response 2: '.htmlentities($row['response2']).'<br/>';
                    echo 'Audio Path 2: '.$row['audio_path2'].'<br/>';
                    echo 'Response 3: '.htmlentities($row['response3']).'<br/>';
                    echo 'Audio Path 3: '.$row['audio_path3'].'<br/>';
                    echo 'Response 4: '.htmlentities($row['response4']).'<br/>';
                    echo 'Audio Path 4: '.$row['audio_path4'].'<br/>';
                    echo 'Response 5: '.htmlentities($row['response5']).'<br/>';
                    echo 'Audio Path 5: '.$row['audio_path5'].'<br/>';
                    echo '<hr/>';
                    echo 'Script: '.htmlentities($row['script']).'<br/>';
                    echo 'Prerequisite: '.htmlentities($row['prerequisite']).'<br/>';
                }
            }
            // list the "+" box if this is not the selected trigger
            else
            {
                echo '<a href="'.$url.'&amp;trigger='.$rowTriggerId.'">+</a>';
            }
            echo '</td>';
            // List the remove button.
            if (checkaccess('npcs', 'edit'))
            {
                echo '<td>';
                // show a save button for the whole form if this is the selected trigger. Javascript at the end of the table will make this work.
                if ($triggerId == $rowTriggerId)
                {
                    echo '<p><input type="button" name="update_button" value="Save Changes" onclick="submitForm()" /></p>';
                }
                echo '<form action="'.$url.'" method="post"><div>';
                echo '<input type="hidden" name="trigger_id" value="'.$row['id'].'" />';
                echo '<input type="submit" name="commit" value="Remove" />';
                echo '</div></form></td>';
            }
            echo '</tr>'."\n";
        }
        echo '</table>';
        // the following javascript is outside the PHP file for easier maintenance, but it is still printed only conditionally to PHP being in this part of the code.
?>
<script type="text/javascript">//<![CDATA[
    function changeTriggerText()
    {
        document.getElementById("triggerTextForm").value = document.getElementById("triggerText").value;
    }
    document.getElementById("triggerText").addEventListener("input", changeTriggerText);
    function submitForm()
    {
        document.getElementById("mainForm").submit();
    }
    
    var msgBox = document.getElementById("scriptMsgBox");
    var script = document.getElementById("script");
    
    function addToScript()
    {
        msgBox.innerHTML = "";
        // if script is empty, add response tags.
        if (script.value == '')
        {
            script.value = "<response></response>";
        }
        // all our stuff needs to be in response tags, so add them if not found.
        if (script.value.indexOf("<response>") == -1)
        {
            script.value += "<response>";
        }
        // find </response> tag, and get the index where we can insert (just before it)
        var insertPosition = script.value.indexOf("</response>");
        // if missing, add an end tag.
        if (insertPosition == -1)
        {
            script.value += "</response>";
            insertPosition = script.value.indexOf("</response>");
        }
        // this calls a function with a name equal to the selected value.
        window[document.getElementById("addToScriptType").value](insertPosition);
    }
    
    function typeChanged()
    {
        msgBox.innerHTML = "";
        document.getElementById("actionSubMenu").innerHTML = "";
        window[document.getElementById("addToScriptType").value](-1);
    }
    // below are the functions that handle any add to script actions. There should be 1 for each entry in the select statement "addToScriptType", 
    // with an idential name. Position = -1 means the dropdown changed, and secondary input boxes (if any) should be shown. Any other position 
    // means insert.
    function respond(position)
    {
        if (position == -1)
        {
            // do nothing, function does not have a second menu.
        }
        else
        {
            var output = script.value.substr(0, position) + "<respond/>\n" + script.value.substr(position);
            script.value = output;
            msgBox.innerHTML = "Script updated";
        }
    }
    
    function respondpublic(position)
    {
        if (position == -1)
        {
            // do nothing, function does not have a second menu.
        }
        else
        {
            var output = script.value.substr(0, position) + "<respondpublic/>\n" + script.value.substr(position);
            script.value = output;
            msgBox.innerHTML = "Script updated";
        }
    }
    
    function respondanim(position) 
    {
        if (position == -1)
        {
            document.getElementById("actionSubMenu").innerHTML = 'animation: <input type="text" name="action_sub" value="greet" />';
        }
        else
        {
            var anim = document.getElementsByName("action_sub")[0].value;
            if (anim.indexOf('"') != -1)
            {
                msgBox.innerHTML = "Cannot use quotes in your animation name";
            }
            else if (anim == "")
            {
                msgBox.innerHTML = "Cannot use an empty animation name";
            }
            else
            {
                var output = script.value.substr(0, position) + "<respond/>\n" + '<action anim="' + anim + "\" />\n" + script.value.substr(position);
                script.value = output;
                msgBox.innerHTML = "Script updated";
            }
        }
    }
    
    function publicaction(position) 
    {
        if (position == -1)
        {
            document.getElementById("actionSubMenu").innerHTML = 'action: <input type="text" name="action_sub" value="points to $playername" />';
        }
        else
        {
            var action = document.getElementsByName("action_sub")[0].value;
            if (action.indexOf('"') != -1)
            {
                msgBox.innerHTML = "Cannot use quotes in your action";
            }
            else if (action == "")
            {
                msgBox.innerHTML = "Cannot use an empty action";
            }
            else
            {
                var output = script.value.substr(0, position) + '<actionpublic text="' + action + "\" />\n" + script.value.substr(position);
                script.value = output;
                msgBox.innerHTML = "Script updated";
            }
        }
    }
    
    function addanim(position)
    {
        if (position == -1)
        {
            document.getElementById("actionSubMenu").innerHTML = 'animation: <input type="text" name="action_sub" value="greet" />';
        }
        else
        {
            var anim = document.getElementsByName("action_sub")[0].value;
            if (anim.indexOf('"') != -1)
            {
                msgBox.innerHTML = "Cannot use quotes in your animation name";
            }
            else if (anim == "")
            {
                msgBox.innerHTML = "Cannot use an empty animation name";
            }
            else
            {
                var output = script.value.substr(0, position) + '<action anim="' + anim + "\" />\n" + script.value.substr(position);
                script.value = output;
                msgBox.innerHTML = "Script updated";
            }
        }
    }
    
    function train(position)
    {
        var skillDropDown = document.getElementById("skillBox").innerHTML;
        if (position == -1)
        {
            document.getElementById("actionSubMenu").innerHTML = "select a skill: " + skillDropDown;
        }
        else
        {
            var skill = document.getElementsByName("action_sub")[0].value;
            var output = script.value.substr(0, position) + '<train skill="' + skill + "\" />\n" + script.value.substr(position);
            script.value = output;
            msgBox.innerHTML = "Script updated";
        }
    }
    
    function assignquest(position)
    {
        if (position == -1)
        {
            document.getElementById("actionSubMenu").innerHTML = 'Enter a quest name: <input type="text" name="action_sub" value="quest_name" />';
        }
        else
        {
            var quest = document.getElementsByName("action_sub")[0].value;
            if (quest.indexOf('"') != -1)
            {
                msgBox.innerHTML = "Cannot use quotes in your quest name";
            }
            else if (quest == "")
            {
                msgBox.innerHTML = "Cannot use an empty quest name";
            }
            else
            {
                var output = script.value.substr(0, position) + '<assign q1="' + quest + "\" />\n" + script.value.substr(position);
                script.value = output;
                msgBox.innerHTML = "Script updated";
            }
        }
    }
    
    function completequest(position)
    {
        if (position == -1)
        {
            document.getElementById("actionSubMenu").innerHTML = 'Enter a quest ID: <input type="text" name="action_sub" value="QuestIdHere" />';
        }
        else
        {
            var quest = document.getElementsByName("action_sub")[0].value;
            if (quest.indexOf('"') != -1)
            {
                msgBox.innerHTML = "Cannot use quotes in your quest name";
            }
            else if (quest == "")
            {
                msgBox.innerHTML = "Cannot use an empty quest name";
            }
            else
            {
                var output = script.value.substr(0, position) + '<complete quest_id="' + quest + "\" />\n" + script.value.substr(position);
                script.value = output;
                msgBox.innerHTML = "Script updated";
            }
        }
    }
    
    function offeritem(position)
    { 
        itemDropDown = document.getElementById("itemBox").innerHTML;
        if (position == -1) 
        {
            document.getElementById("actionSubMenu").innerHTML = itemDropDown;
        }
        else
        {
            // notice there is a second element by this name, it is in the next span (id: itemBox)
            var itemId = document.getElementsByName("action_sub")[0].value;
            var insertPosition = script.value.indexOf("</offer>");
            // if missing, add both tags.
            if (insertPosition == -1)
            {
                var output = script.value.substr(0, position) + "<offer>\n</offer>\n" + script.value.substr(position);
                script.value = output;
                insertPosition = script.value.indexOf("</offer>");
            }
            var output = script.value.substr(0, insertPosition) + '<item id="' + itemId + "\" />\n" + script.value.substr(insertPosition);
            script.value = output;
            msgBox.innerHTML = "Script updated";
        }
    }
    // call once to set the initial value.
    typeChanged();
//]]></script>
<?php
    }
    // Show create form.
    if (checkaccess('npcs', 'edit'))
    {
        echo '<form action="'.$url.'" method="post">';
        echo '<table border="1">';
        echo '<tr><td>Create New Trigger:<br/><input type="text" name="trigger_text" /><br/><input type="submit" name="commit" value="Create New Trigger" /></td>';
        echo '</tr>';
        echo '</table>';
        echo '</form>';
    }
}
?>
