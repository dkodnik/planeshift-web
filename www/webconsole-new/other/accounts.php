<?php

function listaccounts()
{
    if(checkaccess('other', 'read'))
    {
        $id = (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 'nan');
        
        if($id == 'nan')
        {
            $sql = 'SELECT COUNT(*) FROM accounts';
            $item_count = mysql_fetch_array(mysql_query2($sql), MYSQL_NUM);
            $item_count = $item_count[0];
        }
        else
        {
            $item_count = 1;
        }
        
        $nav = RenderNav(array('do' => 'listaccounts', 'id' => $id), $item_count);
        
        $sql = 'SELECT a.id, a.username, a.status, a.verificationid, b.end AS banned_until FROM accounts AS a LEFT JOIN bans AS b ON a.id = b.account';
        $sql.= ($id != 'nan' ? " WHERE id = '".$id."'" : ' ORDER BY id');
        $sql.= $nav['sql'];
        $query = mysql_query2($sql);
        
        echo '<p class="header">List Accounts</p>';
        
        echo $nav['html'];
        unset($nav);
        
        echo '<table>';
        echo '<tr><th>ID</th><th>Accountname</th><th>Account status</th><th>Verify ID</th><th>Actions</th></tr>';
        
        $color = 'b';
        while($row = mysql_fetch_array($query, MYSQL_ASSOC))
        {
            $color = ($color == 'a' ? 'b' : 'a');
            $status = '';
            if($row['status'] == 'A')
            {
                $status = 'Active';
            }
            elseif($row['status'] == 'U')
            {
                $status = 'Unactive';
            }
            elseif($row['status'] == 'B')
            {
                $status = 'Banned - until '.date('H:i d/m/Y', $row['banned_until']);
            }
            else
            {
                $status = 'Other';
            }
            
            echo '<tr class="color_'.$color.'">';
            echo '<td>'.$row['id'].'</td>';
            echo '<td>'.htmlentities($row['username']).'</td>';
            echo '<td>'.$status.'</td>';
            echo '<td>'.$row['verificationid'].'</td>';
            echo '<td>';
            echo '<a href="./index.php?do=viewaccount&id='.$row['id'].'">Details</a> ';
            //echo (checkaccess('other', 'edit') ? '<a href="./index.php?do=editaccount&id='.$row['id'].'">Edit</a> ' : '');
            //echo (checkaccess('other', 'delete') ? '<a href="./index.php?do=deleteaccount&id='.$row['id'].'">Delete</a> ' : '');
            echo '</td>';
        }
        echo '</table>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function viewaccount()
{
    if(checkaccess('other', 'read'))
    {
        $id = (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 'nan');
        
        echo '<p class="header">Account Details</p>';
        echo '<a href="./index.php?do=listaccounts">Back</a>';
        if($id == 'nan')
        {
            echo '<p class="error">You have to specify a valid ID!</p>';
            return false;
        }
        
        $sql = 'SELECT a.username, a.last_login, a.created_date, a.last_login_ip, a.security_level, a.verificationid, a.country, a.gender, a.birth, a.status, a.spam_points, a.advisor_points, a.advisor_ban, a.comments, b.end AS banned_until ';
        $sql.= 'FROM accounts AS a LEFT JOIN bans AS b ON a.id = b.account WHERE a.id = '.$id.' LIMIT 1';
        
        $row = mysql_fetch_array(mysql_query2($sql), MYSQL_ASSOC);
        $gender = 'Other';
        if($row['gender'] == 'M')
        {
            $gender = 'male';
        }
        elseif($row['gender'] == 'F')
        {
            $gender = 'female';
        }
        $status = 'Other';
        if($row['status'] == 'A')
        {
            $status = 'Active';
        }
        elseif($row['status'] == 'U')
        {
            $status = 'Unactive';
        }
        elseif($row['status'] == 'B')
        {
            $status = 'Banned - until '.date('H:i d/m/Y', $row['banned_until']);
        }
        
        echo '<table>';
        echo '<tr class="color_a"><td>ID: </td><td>'.$id.'</td></tr>';
        echo '<tr class="color_b"><td>Name: </td><td>'.htmlentities($row['username']).'</td></tr>';
        echo '<tr class="color_a"><td>Last login: </td><td>'.$row['last_login'].' ('.$row['last_login_ip'].')</td></tr>';
        echo '<tr class="color_b"><td>Created on: </td><td>'.$row['created_date'].'</td></tr>';
        echo '<tr class="color_a"><td>Security level: </td><td>'.$row['security_level'].'</td></tr>';
        echo '<tr class="color_b"><td>Verify ID: </td><td>'.$row['verificationid'].'</td></tr>';
        echo '<tr class="color_a"><td>Country: </td><td>'.htmlentities($row['country']).'</td></tr>';
        echo '<tr class="color_b"><td>Gender: </td><td>'.$gender.'</td></tr>';
        echo '<tr class="color_a"><td>Birth: </td><td>'.$row['birth'].'</td></tr>';
        echo '<tr class="color_b"><td>Status: </td><td>'.$status.'</td></tr>';
        echo '<tr class="color_a"><td>Spam points: </td><td>'.$row['spam_points'].'</td></tr>';
        echo '<tr class="color_b"><td>Advisor points: </td><td>'.$row['advisor_points'].'</td></tr>';
        echo '<tr class="color_a"><td>Advisor ban: </td><td>'.($row['advisor_ban'] ? 'Yes' : 'No').'</td></tr>';
        echo '<tr class="color_b"><td>Comments: </td><td><pre>'.htmlentities($row['comments']).'</pre></td></tr>';
        echo '</table><br/><br/>';
        
        echo '<a href="./index.php?do=listcharacters&account_id='.$id.'">List this account\'s characters</a>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>

