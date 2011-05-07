<?php

function listgms()
{
    if(checkaccess('admin', 'read'))
    {
        echo '<p class="header">List Game Masters</p>';
        
        $sql = 'SELECT a.id AS account_id, a.username AS account_name, a.security_level, s.title AS security_title, c.id AS character_id, c.name AS firstname, c.lastname, c.time_connected_sec, g.id AS guild_id, g.name AS guild ';
        $sql.= 'FROM characters AS c LEFT JOIN accounts AS a ON c.account_id = a.id LEFT JOIN guilds AS g ON g.id = c.guild_member_of LEFT JOIN security_levels AS s ON a.security_level = s.level WHERE a.security_level >= 20 AND a.security_level <= 50';
        $query = mysql_query2($sql);
        
        if(checkaccess('admin', 'create'))
        {
            echo '<form action="./index.php?do=addgm" method="post">';
            echo '<input type="text" name="username" size="20" /><input type="submit" value="Find Username" />';
            echo '</form>';
        }
        echo '<table>';
        echo '<tr><th>Account/Character ID</th><th>Security Level</th><th>Account</th><th>Firstname</th><th>Lastname</th><th>Guild</th><th>Total Time Connected</th><th>Actions</th></tr>';        
        
        $color = 'b';
        while($row = mysql_fetch_array($query, MYSQL_ASSOC))
        {
            $color = ($color == 'a' ? 'b' : 'a');
            $sec_level = (isset($row['security_title']) && !empty($row['security_title']) ? $row['security_title'] : 'Unknown');
            
            echo '<tr class="color_'.$color.'">';
            echo '<td>'.(checkaccess('other', 'read') ? '<a href="./index.php?do=listaccounts&amp;id='.$row['account_id'].'">' : '');
            echo $row['account_id'].(checkaccess('other', 'read') ? '</a>' : '').' / ';
            echo (checkaccess('npcs', 'edit') ? '<a href="./index.php?do=npc_details&amp;npc_id='.$row['character_id'].'&amp;sub=main">' : '');
            echo $row['character_id'].(checkaccess('npcs', 'edit') ? '</a>' : '').'</td>';
            echo '<td>'.$sec_level.'('.$row['security_level'].')</td>';
            echo '<td>'.(checkaccess('other', 'read') ? '<a href="./index.php?do=listaccounts&amp;id='.$row['account_id'].'">' : '');
            echo htmlentities($row['account_name']).(checkaccess('other', 'read') ? '</a>': '').'</td>';
            echo '<td>'.(checkaccess('npcs', 'edit') ? '<a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$row['character_id'].'">' : '');
            echo htmlentities($row['firstname']).(checkaccess('npcs', 'edit') ? '</a>' : '').'</td>';
            echo '<td>'.htmlentities($row['lastname']).'</td>';
            echo '<td>'.(checkaccess('other', 'read') ? '<a href="./index.php?do=listguilds&amp;guild='.$row['guild_id'].'">' : '');
            echo htmlentities($row['guild']).(checkaccess('other', 'read') ? '</a>' : '').'</td>';
            
            $t = $row['time_connected_sec'];
            $days = floor($t / (60*60*24));
            $t -= $days * 60*60*24;
            
            $hours = floor($t / (60*60));
            $t -= $hours * 60*60;
            
            $min = floor($t / 60);
            $t -= $min * 60;
            
            $secs = $t;
            echo '<td>'.$days.' days, '.$hours.' hours, '.$min.' minutes, '.$secs.' seconds</td>';
            echo '<td>'.(checkaccess('admin', 'edit') ? '<a href="./index.php?do=editgm&amp;id='.$row['character_id'].'">Edit</a> ' : '');
            echo '<a href="./index.php?do=viewgmlog&amp;id='.$row['character_id'].'">View Commandlog</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function viewgmlog()
{
    if(checkaccess('admin', 'read'))
    {
        $id = (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 'nan');
        $lines_per_page = (isset($_GET['lines_per_page']) && is_numeric($_GET['lines_per_page']) ? $_GET['lines_per_page'] : 200);
        $page = (isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 0);
        
        echo '<p class="header">View Game Master Commandlog</p>';
        echo '<a href="./index.php?do=listgms">Back</a><br/>';
        if($id == 'nan')
        {
            echo '<p class="error">You have to specify a valid ID!</p>';
        }
        else
        {
            $sql = "SELECT name, lastname FROM characters WHERE id = '$id' LIMIT 1";
            $row = mysql_fetch_array(mysql_query2($sql), MYSQL_ASSOC);
            $name = $row['name'].' '.$row['lastname'];
            
            $sql = "SELECT COUNT(*) FROM gm_command_log WHERE gm = '$id'";
            $page_count = mysql_fetch_array(mysql_query2($sql), MYSQL_NUM);
            $page_count = ceil($page_count[0] / $lines_per_page);
            
            if($page >= $page_count)
            {
                $page = $page_count - 1;
            }
            if($page < 0)
            {
                $page = 0;
            }
            
            $sql = "SELECT ex_time, command FROM gm_command_log WHERE gm = '$id' LIMIT ".($page * $lines_per_page).', '.$lines_per_page;
            $query = mysql_query2($sql);
            
            echo '<form action="./index.php" method="get">';
            echo '<input type="hidden" name="do" value="viewgmlog" />';
            echo '<input type="hidden" name="id" value="'.$id.'" />';
            echo '<input type="hidden" name="page" value="'.$page.'" />';
            echo 'Lines per page: <input type="text" name="lines_per_page" size="5" value="'.$lines_per_page.'" />';
            echo '</form><br/>';
            
            echo 'Page: ';
            for($i = 0; $i< $page_count; $i++)
            {
                if($i == $page)
                {
                    echo ($i+1);
                }
                else
                {
                    echo '<a href="./index.php?do=viewgmlog&amp;id='.$id.'&amp;lines_per_page='.$lines_per_page.'&amp;page='.$i.'">'.($i+1).'</a>';
                }
                echo ($i == ($page_count - 1) ? '' : ' | ');
            }
            echo '<br/>';
            
            echo 'Viewing Commandlog of GM "'.htmlentities($name).'"<br/><pre>';
            while($row = mysql_fetch_array($query, MYSQL_ASSOC))
            {
                echo '('.$row['ex_time'].') '.htmlentities($row['command'])."\n";
            }
            echo '</pre>';
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function addgm()
{
    if(checkaccess('admin', 'create'))
    {
        $username = (isset($_POST['username']) ? $_POST['username'] : '');
        $username = str_replace('*', '%', $username);
        
        echo '<p class="header">Adding a new Game Master</p>';
        
        if(empty($username))
        {
            echo '<a href="./index.php?do=listgms">Back</a>';
            echo '<p class="error">You have to enter a username to search for!</p>';
        }
        else
        {
            $sql = 'SELECT a.id AS account_id, a.username AS account_name, c.id AS character_id, c.name AS firstname, c.lastname, c.time_connected_sec, g.id AS guild_id, g.name AS guild ';
            $sql.= 'FROM characters AS c LEFT JOIN accounts AS a ON c.account_id = a.id LEFT JOIN guilds AS g ON g.id = c.guild_member_of WHERE a.security_level = 0 AND c.name'." LIKE '".mysql_real_escape_string($username)."'";
            $query = mysql_query2($sql);
            
            echo '<table>';
            echo '<tr><th>Account/Character ID</th><th>Account</th><th>Firstname</th><th>Lastname</th><th>Guild</th><th>Total Time Connected</th><th>Actions</th></tr>';        
            
            $color = 'b';
            while($row = mysql_fetch_array($query, MYSQL_ASSOC))
            {
                $color = ($color == 'a' ? 'b' : 'a');
                
                echo '<tr class="color_'.$color.'">';
                echo '<td>'.(checkaccess('other', 'read') ? '<a href="./index.php?do=listaccounts&amp;id='.$row['account_id'].'">' : '');
                echo $row['account_id'].(checkaccess('other', 'read') ? '</a>' : '').' / ';
                echo (checkaccess('npcs', 'edit') ? '<a href="./index.php?do=npc_details&amp;npc_id='.$row['character_id'].'&amp;sub=main">' : '');
                echo $row['character_id'].(checkaccess('npcs', 'edit') ? '</a>' : '').'</td>';
                echo '<td>'.(checkaccess('other', 'read') ? '<a href="./index.php?do=listaccounts&amp;id='.$row['account_id'].'">' : '');
                echo htmlentities($row['account_name']).(checkaccess('other', 'read') ? '</a>': '').'</td>';
                echo '<td>'.(checkaccess('npcs', 'edit') ? '<a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$row['character_id'].'">' : '');
                echo htmlentities($row['firstname']).(checkaccess('npcs', 'edit') ? '</a>' : '').'</td>';
                echo '<td>'.htmlentities($row['lastname']).'</td>';
                echo '<td>'.(checkaccess('other', 'read') ? '<a href="./index.php?do=listguilds&amp;guild='.$row['guild_id'].'">' : '');
                echo htmlentities($row['guild']).(checkaccess('other', 'read') ? '</a>' : '').'</td>';
                
                $t = $row['time_connected_sec'];
                $days = floor($t / (60*60*24));
                $t -= $days * 60*60*24;
                
                $hours = floor($t / (60*60));
                $t -= $hours * 60*60;
                
                $min = floor($t / 60);
                $t -= $min * 60;
                
                $secs = $t;
                echo '<td>'.$days.' days, '.$hours.' hours, '.$min.' minutes, '.$secs.' seconds</td>';
                echo '<td><a href="./index.php?do=editgm&amp;id='.$row['character_id'].'">Make a GM</a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }    
        
    }
    else
    { 
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function editgm()
{
    if(checkaccess('admin', 'edit') || checkaccess('admin', 'create'))
    {
        $id = (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 'nan');
        $security_level = (isset($_POST['security_level']) && is_numeric($_POST['security_level']) ? $_POST['security_level'] : 'nan');
        
        echo '<p class="header">Editing/Creating a Game Master</p>';
        
        if($id == 'nan')
        {
            echo '<p class="error">You have to specify a valid ID!</p>';
        }
        else
        {
            $sql = 'SELECT c.name, c.lastname, c.account_id, a.security_level FROM characters AS c, accounts AS a WHERE c.id = \''.$id.'\' AND a.id = c.account_id LIMIT 1';
            $query = mysql_query2($sql);
            
            if(!checkaccess('admin', 'edit') && mysql_num_rows($query) > 0)
            { // You don't have the right to edit!
                echo '<p class="error">You are not authorized to use these functions</p>';
                return false;
            }
            elseif(!checkaccess('admin', 'create'))
            { // You don't have the right to create!
                echo '<p class="error">You are not authorized to use these functions</p>';
                return false;
            }
            $row = mysql_fetch_array($query);
            
            if($security_level != 'nan')
            {
                $sql = "UPDATE accounts SET security_level = '".$security_level."' WHERE id = '".$row['account_id']."' LIMIT 1";
                $query = mysql_query2($sql);
                echo '<p style="color: yellow;">Successfully updated the security level!</p>';
                $row['security_level'] = $security_level;
            }
            
            $levels = array(0, 21, 22, 23, 24);
            $arr = array();
            $sql = 'SELECT level, title FROM security_levels';
            $query2 = mysql_query2($sql);
            while($row2 = mysql_fetch_array($query2))
            {
                if(in_array($row2['level'], $levels))
                {
                    $arr[$row2['level']] = $row2['title'];
                }
            } 
            $levels = $arr;
            unset($arr);
            
            echo '<a href="./index.php?do=listgms">Back</a>';
            echo '<form action="./index.php?do=editgm&amp;id='.$id.'" method="post">';
            echo '<table>';
            echo '<tr class="color_a"><td>ID: </td><td>'.$id.'</td></tr>';
            echo '<tr class="color_b"><td>Firstname: </td><td>'.htmlentities($row['name']).'</td></tr>';
            echo '<tr class="color_a"><td>Lastname: </td><td>'.htmlentities($row['lastname']).'</td></tr>';
            echo '<tr class="color_a"><td>Security Level: </td><td><select name="security_level">';
            foreach($levels as $level => $name)
            {
                echo '<option value="'.$level.'"'.($row['security_level'] == $level ? ' selected="selected"' : '').'>';
                echo htmlentities($name).' ('.$level.')</option>';
            }
            echo '</select></td></tr>';
            echo '</table>';
            echo '<input type="submit" value="Save" /></form>';
        }
    }
    else
    { 
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>
