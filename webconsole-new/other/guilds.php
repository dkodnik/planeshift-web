<?php
function listguilds() {
    if(CheckAccess('other', 'read'))
    {
        $guild = intval(@$_GET['guild']);
        if(empty($guild)) // If the user hasn't selected a guild we'll just present a list.
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
                echo '<td><a href="./index.php?do=listguilds&guild='.$row['id'].'">Details</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        else // Now we can get into the details.
        {
            $sql = 'SELECT g.*, c.name AS founder_name, a.name AS alliance_name, g2.name AS leading_guild from guilds AS g LEFT JOIN alliances AS a ON a.id = g.alliance LEFT JOIN guilds AS g2 ON g2.id = a.leading_guild LEFT JOIN characters AS c ON c.id=g.char_id_founder WHERE g.id='.$guild;
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
                        echo '<td><a href="./index.php?do=listguilds&guild='.$row2['id'].'">Details</a></td>';
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
                echo '<td>';
                /* Just waiting for the account page to come.
                 * 
                echo '<a href="./index.php?do=editaccounts&id='.$row['id'].'">Edit</a>';
                if(CheckAccess('other', 'delete'))
                {
                    echo ' - <a href="./index.php?do=deleteaccounts&id='.$row['id'].'">Delete</a>';
                }*/
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
?>