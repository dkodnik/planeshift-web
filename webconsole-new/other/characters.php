<?php

function listcharacters()
{
    if(checkaccess('other', 'read'))
    {
        $account_id = (isset($_GET['account_id']) && is_numeric($_GET['account_id']) ? $_GET['account_id'] : 'nan');
        
        echo '<p class="header">List Characters</p>';
        
        $sql = 'SELECT COUNT(*) FROM characters'.($account_id == 'nan' ? '' : ' WHERE account_id = '.$account_id);
        $item_count = mysql_fetch_array(mysql_query2($sql), MYSQL_NUM);
        
        $nav = RenderNav(array('do' => 'listcharacters', 'account_id' => $account_id), $item_count[0]);
        
        $sql = 'SELECT c.id, c.account_id, c.name, c.lastname, c.guild_member_of, c.time_connected_sec, g.name AS guild_name, a.username AS account_name ';
        $sql.= 'FROM characters AS c LEFT JOIN guilds AS g on g.id = c.guild_member_of LEFT JOIN accounts AS a ON a.id = c.account_id ';
        $sql.= ($account_id == 'nan' ? '' : 'WHERE c.account_id = \''.$account_id.'\' ').' ORDER BY name';
        $sql.= $nav['sql'];
        $query = mysql_query2($sql);
        
        $sql = 'SELECT id, username FROM accounts ORDER BY username';
        $query2 = mysql_query2($sql);
        
        echo $nav['html'];
        unset($nav);
        
        // commented this dropdown for now, 500k + records make it a bit hard to use/load. :)
        /*echo 'List all characters of this account: <select name="account_id" onChange="this.form.submit();">'; 
        echo '<option value=""'.($account_id == 'nan' ? ' selected="selected"' : '').'>All</option>';
        while($row = mysql_fetch_array($query2, MYSQL_ASSOC))
        {
            echo '<option value="'.$row['id'].'"'.($row['id'] == $account_id ? ' selected="selected"' : '').'>'.htmlentities($row['username']).'</option>';
        }
        echo '</select>';*/
        
        echo '</form><br/>';
        
        echo '<table>';
        echo '<tr><th>ID</th><th>Firstname</th><th>Lastname</th><th>NPC</th><th>Guild</th><th>Account</th><th>Total time connected</th><th>Actions</th></tr>';
        $color = 'b';
        while($row = mysql_fetch_array($query, MYSQL_ASSOC))
        {
            $color = ($color == 'a' ? 'b' : 'a');
            
            echo '<tr class="color_'.$color.'">';
            echo '<td>'.$row['id'].'</td>';
            echo '<td>'.htmlentities($row['name']).'</td>';
            echo '<td>'.htmlentities($row['lastname']).'</td>';
            echo '<td>'.($row['account_name'] == 'superclient' ? 'Yes' : 'No').'</td>';
            echo '<td><a href="./index.php?do=listguilds&guild='.$row['guild_member_of'].'">'.htmlentities($row['guild_name']).'</a></td>';
            echo '<td><a href="./index.php?do=listaccounts&id='.$row['account_id'].'">'.htmlentities($row['account_name']).'</a></td>';
            
            $t = $row['time_connected_sec'];
            $days = floor($t / (60*60*24));
            $t -= $days * 60*60*24;
            
            $hours = floor($t / (60*60));
            $t -= $hours * 60*60;
            
            $min = floor($t / 60);
            $t -= $min * 60;
            
            $secs = $t;
            echo '<td>'.$days.' days, '.$hours.' hours, '.$min.' minutes, '.$secs.' seconds</td>';
            echo '<td>'; //<a href="./index.php?do=viewcharacter&id='.$row['id'].'">Details</a> ';
            echo (checkaccess('npcs', 'edit') ? '<a href="./index.php?do=npc_details&npc_id='.$row['id'].'">Edit</a>' : '').'</td>';
            echo '</tr>';
            
        }
        echo '</table>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function viewcharacter()
{
    if(checkaccess('other', 'read'))
    {
        $id = (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 'nan');
        
        echo '<p class="header">Character Details</p>';
        echo '<a href="./index.php?do=listcharacters">Back</a><br/>';
        if($id == 'nan')
        {
            echo '<p class="error">You have to specify a valid ID!</p>';
        }
        else
        {
            echo 'Comming Soon (tm).';
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>

