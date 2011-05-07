<?php
function listguilds() {
    if(CheckAccess('other', 'read'))
    {
        $guild = @$_GET['guild'];
        if(!is_numeric($guild)) // If the user hasn't selected a guild we'll just present a list.
        {
            echo '<p class="header">Guild List</p>';
            
            $sql = 'SELECT t1.*, t2.name as char_name_founder FROM guilds as t1, characters as t2 WHERE t2.id = t1.char_id_founder ORDER BY t1.name';
            $query = mysql_query2($sql);
            echo '<table>';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th width="250">Guild name</th>';
            echo '<th width="30">KP</th>';
            echo '<th width="170">Created</th>';
            echo '<th>Founder</th>';
            echo '<th width="70">Actions</th>';
            echo '</tr>';
            
            $mode = 'b';
            while($row = mysql_fetch_array($query, MYSQL_ASSOC))
            {
                $mode = ($mode == 'a' ? 'b': 'a');
                
                echo '<tr class="color_'.$mode.'">';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.htmlentities($row['name']).'</td>';
                echo '<td>'.$row['karma_points'].'</td>';
                echo '<td>'.htmlentities($row['date_created']).'</td>';
                echo '<td>'.htmlentities($row['char_name_founder']).'</td>';
                echo '<td><a href="./index.php?do=listguilds&amp;guild='.$row['id'].'">Details</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        else // Now we can get into the details.
        {
            $sql = 'SELECT g.*, c.name AS founder_name, a.name AS alliance_name, g2.name AS leading_guild from guilds AS g LEFT JOIN alliances AS a ON a.id = g.alliance LEFT JOIN guilds AS g2 ON g2.id = a.leading_guild LEFT JOIN characters AS c ON c.id=g.char_id_founder WHERE g.id='.$guild.' LIMIT 1';
            $row = mysql_fetch_array(mysql_query2($sql), MYSQL_ASSOC);
            
            echo '<p class="header">Guild Infos for "'.htmlentities($row['name']).'"</p>';
            echo '<a href="./index.php?do=listguilds">Back to listing</a><br />';
            
            echo '<table border="1" cellspacing="0"  cellpadding="0">';
            echo '<tr><td width="150">Name</td><td>'.htmlentities($row['name']).'</td></tr>';
            echo '<tr><td>Karma points (KP)</td><td>'.htmlentities($row['karma_points']).'</td></tr>';
            echo '<tr><td>Date created</td><td>'.htmlentities($row['date_created']).'</td></tr>';
            echo '<tr><td>Founder name</td><td>'.htmlentities($row['founder_name']).'</td></tr>';
            echo '<tr><td>Web page</td><td><a href="http://'.$row['web_page'].'">'.htmlentities($row['web_page']).'</a></td></tr>';
            echo '<tr><td>MOTD</td><td>'.htmlentities($row['motd']).'</td></tr>';
            echo '</table>';
            
            if($row['alliance'] != 0)
            {
                if($row['alliance_name'])
                {
                    echo '<br />Alliance:';
                    echo '<br />Leading guild: '.htmlentities($row['leading_guild']);
                    echo '<br />Alliance name: '.htmlentities($row['alliance_name']);
                    echo '<br /><b>Guilds:</b><br />';
                    
                    echo '<table><tr>';
                    echo '<th>ID</th>';
                    echo '<th width="250">Guild name</th>';
                    echo '<th width="30">KP</th>';
                    echo '<th width="170">Created</th>';
                    echo '<th>Founder</th>';
                    echo '<th width="70">Actions</th>';
                    echo '</tr>';
                    
                    $sql = 'SELECT g.*, c.name as founder_name FROM guilds AS g, characters as c WHERE g.alliance = '.$row['alliance'].' AND c.id = g.char_id_founder ORDER BY name';
                    $query = mysql_query2($sql);
                    $mode = 'b';
                    while($row2 = mysql_fetch_array($query, MYSQL_ASSOC))
                    {
                        $mode = ($mode == 'a' ? 'b': 'a');
                        
                        echo '<tr class="color_'.$mode.'">';
                        echo '<td>'.$row2['id'].'</td>';
                        echo '<td>'.htmlentities($row2['name']).'</td>';
                        echo '<td>'.$row2['karma_points'].'</td>';
                        echo '<td>'.htmlentities($row2['date_created']).'</td>';
                        echo '<td>'.htmlentities($row2['founder_name']).'</td>';
                        echo '<td><a href="./index.php?do=listguilds&amp;guild='.$row2['id'].'">Details</a></td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            }
            
            echo '<br /><b>Members:</b><br />';
            
            echo '<table border="0">';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th align="left">Member Name</th>';
            echo '<th>Level</th>';
            echo '<th width=100>Guild points</th>';
            echo '<th>Actions</th>';
            echo '</tr>';
            
            $sql = 'SELECT * FROM characters WHERE guild_member_of = '.$guild.' ORDER BY name';
            $query = mysql_query2($sql);
            $mode = 'b';
            while($row = mysql_fetch_array($query, MYSQL_ASSOC))
            {
                $mode = ($mode == 'a' ? 'b': 'a');
                
                echo '<tr class="color_'.$mode.'">';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.htmlentities($row['name']).'</td>';
                echo '<td>'.htmlentities($row['guild_level']).'</td>';
                echo '<td>'.htmlentities($row['guild_points']).'</td>';
                echo '<td><a href="./index.php?do=editguildmember&amp;id='.$row['id'].'">Edit</a>';
                if(CheckAccess('other', 'delete'))
                {
                    echo ' - <a href="./index.php?do=deleteguildmember&amp;id='.$row['id'].'&amp;guild='.$guild.'">Delete</a>';
                }
                echo '</td></tr>';
            }
            echo '</table>';
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function editguildmember()
{
    if(!CheckAccess('other', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    $edit = CheckAccess('other', 'edit');
    $member = @$_GET['id'];
    if(!is_numeric($member))
    {
        echo '<p class="error">You have to specify a valid member ID!</p>';
        return;
    }
    
    if($edit && isset($_POST['guild_public_notes']) && isset($_POST['guild_private_notes']))
    {
        $public_notes = mysql_real_escape_string(str_replace("\r", '', $_POST['guild_public_notes']));
        $private_notes = mysql_real_escape_string(str_replace("\r", '', $_POST['guild_private_notes']));
        
        $sql = 'UPDATE characters SET guild_public_notes = \''.$public_notes.'\', guild_private_notes = \''.$private_notes.'\' WHERE id = '.$member.' LIMIT 1';
        $query = mysql_query2($sql);
        if(1 == ($rows = mysql_affected_rows()))
        {
            echo '<p class="error">Update successful.</p>';
        }
        else
        {
            echo '<p class="error">Something went wrong while updating! (updated rows '.$rows.')</p>';
        }
    }
    
    echo '<p class="header">'.($edit ? 'Edit' : 'View').' a character\'s guild notes</p>';
    
    $sql = 'SELECT c.*, g.name as guild FROM characters AS c, guilds AS g WHERE c.id = '.$member.' AND g.id = c.guild_member_of LIMIT 1';
    $row = mysql_fetch_array(mysql_query2($sql), MYSQL_ASSOC);
    
    echo '<a href="./index.php?do=listguilds&amp;guild='.$row['guild_member_of'].'">Back</a><br/>';
    if($edit)
    {
        echo '<form action="./index.php?do=editguildmember&amp;id='.$member.'" method="post">';
    }
    echo '<table>';
    echo '<tr class="color_a"><td>ID</td><td>'.$member.'</td></tr>';
    echo '<tr class="color_b"><td>Name</td><td>'.htmlentities($row['name']).'</td></tr>';
    echo '<tr class="color_a"><td>Guild</td><td>'.htmlentities($row['guild']).'</td></tr>';
    echo '<tr class="color_b"><td>Guild Public Notes</td><td>';
    echo ($edit ? '<textarea name="guild_public_notes" style="width: 400px; height: 100px;">' : '<div style="width: 400px; height: 100px;overflow: auto;"><pre style="margin: 0px;white-space: normal;">');
    $notes = htmlentities($row['guild_public_notes']);
    echo ($edit ? $notes : nl2br($notes));
    echo ($edit ? '</textarea>' : '</pre></div>').'</td></tr>';
    
    echo '<tr class="color_a"><td>Guild Private Notes</td><td>';
    echo ($edit ? '<textarea name="guild_private_notes" style="width: 400px; height: 100px;">' : '<div style="width: 400px; height: 100px;overflow: auto;"><pre style="margin: 0px;white-space: normal;">');
    $notes = htmlentities($row['guild_private_notes']);
    echo ($edit ? $notes : nl2br($notes));
    echo ($edit ? '</textarea>' : '</pre></div>').'</td></tr>';
    echo '</table>';
    
    if($edit)
    {
        echo '<input type="submit" value="update" /></form>';
    }
}

function deleteguildmember()
{
    if(CheckAccess('other', 'delete'))
    {
        $id = @$_GET['id'];
        $guild = @$_GET['guild'];
        if(!is_numeric($id))
        {
            echo '<p class="error">You have to specify a valid member ID!</p>';
            return;
        }
        if(!is_numeric($guild))
        {
            echo '<p class="error">You have to specify a valid guild ID!</p>';
            return;
        }
        
        $sql = 'SELECT * FROM characters WHERE id = '.$id.' LIMIT 1';
        $row = mysql_fetch_array(mysql_query2($sql), MYSQL_ASSOC);
        
        $passed = false;
        $password = @$_POST['password'];
        if(!empty($password))
        {
            $sql = 'SELECT username FROM accounts WHERE password = \''.md5($password).'\' LIMIT 1';
            $row2 = mysql_fetch_array(mysql_query2($sql), MYSQL_ASSOC);
            if($_SESSION['username'] == $row2['username'])
            {
                $passed = true;
            } 
        }
        if(!$passed)
        {
            echo '<strong>Are sure you want to remove "'.$row['name'].'" from the guild?</strong><br/>';
            echo '<form action="./index.php?do=deleteguildmember&amp;id='.$id.'&amp;guild='.$guild.'" method="post">';
            echo 'Password: <input type="password" name="password" /><br/>';
            echo '<input type="submit" value="Yes"/>';
            echo ' - <a href="./index.php?do=listguilds&amp;guild='.$guild.'">No</a></form>';
        }
        else
        {
            $sql = 'UPDATE characters SET guild_member_of = 0 WHERE id = '.$id.' LIMIT 1';
            $query = mysql_query2($sql);
            if(1 == ($rows = mysql_affected_rows()))
            {
                echo '<p class="error">"'.htmlentities($row['name']).'" now belongs to no guild.</p>';
            }
            else
            {
                echo '<p class="error">Something went wrong! '.$rows.' characters are excluded from their guild.</p>';
            }
            echo '<a href="./index.php?do=listguilds&amp;guild='.$guild.'">Back</a>';
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>
