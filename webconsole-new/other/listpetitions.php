<?php

function sort_link($column, $label, $sort_col, $sort_dir)
{
    $html = '<a href="./index.php?do=listpetitions&page='.@$_GET['page'].'&items_per_page='.@$_GET['items_per_page'].'&sort_column='.$column;
    if($sort_col == $column && $sort_dir == 'ASC')
    {
        $html .= '&sort_dir=DESC';
    }
    else
    {
        $html .= '&sort_dir=ASC';
    }
    $html .= '">'.$label;
    if($sort_col == $column)
    {
        $html .= '<img src="img/s_'.strtolower($sort_dir).'.png" border="0" />';
    }
    $html .= '</a>';
    return $html;
}

function msg_cut($str, $length)
{
    return (strlen($str) > $length ? substr($str, 0, $length-3).'...': $str);
}

function listpetitions() {
    if(!CheckAccess('other', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    
    $petition = @$_GET['petition'];
    if(!is_numeric($petition))
    {
        $sort_column = (!empty($_GET['sort_column']) ? $_GET['sort_column'] : 'id');
        $sort_dir = (@$_GET['sort_dir'] == 'DESC' ? 'DESC' : 'ASC');
        
        $sql = 'SELECT COUNT(*) FROM petitions';
        $item_count = mysql_fetch_array(mysql_query2($sql), MYSQL_NUM);
        
        $nav = RenderNav(array('do' => 'listpetitions', 'sort_column' => $sort_column, 'sort_dir' => $sort_dir), $item_count[0]);
        
        $sql = 'SELECT p.*, c.name AS player_name, c2.name AS gm_name FROM petitions as p LEFT JOIN characters AS c ON c.id = p.player LEFT JOIN characters AS c2 ON c2.id = p.assigned_gm';
        $sql.= ' ORDER BY '.$sort_column.' '.$sort_dir;
        $sql.= $nav['sql'];
        $query = mysql_query2($sql);
        
        echo '<p class="header">List Petitions</p>';
        
        echo $nav['html'];
        unset($nav);
        echo '<br />';
        
        
        echo '<table><tr>';
        echo '<th>'.sort_link('id', 'ID', $sort_column, $sort_dir).'</th>';
        echo '<th>'.sort_link('player_name', 'Player', $sort_column, $sort_dir).'</th>';
        echo '<th>'.sort_link('petition', 'Content', $sort_column, $sort_dir).'</th>';
        echo '<th>'.sort_link('status', 'Status', $sort_column, $sort_dir).'</th>';
        echo '<th>'.sort_link('category', 'Category', $sort_column, $sort_dir).'</th>';
        echo '<th>'.sort_link('created_date', 'Created', $sort_column, $sort_dir).'</th>';
        echo '<th>'.sort_link('closed_date', 'Closed', $sort_column, $sort_dir).'</th>';
        echo '<th>'.sort_link('gm_name', 'GM', $sort_column, $sort_dir).'</th>';
        echo '<th>'.sort_link('resolution', 'Resolution', $sort_column, $sort_dir).'</th>';
        echo '<th>'.sort_link('escalation_level', 'Escalation Level', $sort_column, $sort_dir).'</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        
        $mode = 'b';
        while($row = mysql_fetch_array($query, MYSQL_ASSOC))
        {
           $mode = ($mode == 'a' ? 'b' : 'a');
           echo '<tr class="color_'.$mode.'">';
           echo '<td>'.$row['id'].'</td>';
           echo '<td>'.htmlentities($row['player_name']).'</td>';
           echo '<td>'.htmlentities(msg_cut($row['petition'], 50)).'</td>';
           echo '<td>'.htmlentities($row['status']).'</td>';
           echo '<td>'.htmlentities($row['category']).'</td>';
           echo '<td>'.$row['created_date'].'</td>';
           echo '<td>'.($row['closed_date'] == '0000-00-00 00:00:00' ? 'never' : $row['closed_date']).'</td>';
           echo '<td>'.htmlentities($row['assigned_gm'] == '-1' ? 'none' : $row['gm_name'] ).'</td>';
           echo '<td>'.htmlentities(msg_cut($row['resolution'], 20)).'</td>';
           echo '<td>'.$row['escalation_level'].'</td>';
           echo '<td><a href="./index.php?do=listpetitions&petition='.$row['id'].'">Details</a></td>';
        }
        echo '</table>';
    }
    else
    {
        $id = @$_GET['petition'];
        if(!is_numeric($id))
        {
            echo '<p class="error">You have to specify a valid petition ID!</p>';
            return;
        }
        echo '<p class="header">Information about the Petition #'.$id.'</p>';
        
        $sql = 'SELECT p.*, c.name AS player_name, c2.name AS gm_name FROM petitions as p LEFT JOIN characters AS c ON c.id = p.player LEFT JOIN characters AS c2 ON c2.id = p.assigned_gm WHERE p.id = '.$id.' LIMIT 1';
        $row = mysql_fetch_array(mysql_query2($sql), MYSQL_ASSOC);
        
        echo '<a href="./index.php?do=listpetitions">Back</a><br/>';
        echo '<table>';
        echo '<tr class="color_a"><td>Player</td><td>'.htmlentities($row['player_name']).'</td></tr>';
        echo '<tr class="color_b"><td>Content</td><td>'.htmlentities($row['petition']).'</td></tr>';
        echo '<tr class="color_a"><td>Status</td><td>'.htmlentities($row['status']).'</td></tr>';
        echo '<tr class="color_b"><td>Category</td><td>'.htmlentities($row['category']).'</td></tr>';
        echo '<tr class="color_a"><td>Created</td><td>'.$row['created_date'].'</td></tr>';
        echo '<tr class="color_b"><td>Closed</td><td>'.($row['closed_date'] == '0000-00-00 00:00:00' ? 'never' : $row['closed_date']).'</td></tr>';
        echo '<tr class="color_a"><td>Assigned GM</td><td>'.($row['assigned_gm'] == '-1' ? 'none' : htmlentities($row['gm_name'])).'</td></tr>';
        echo '<tr class="color_b"><td>Resolution</td><td>'.htmlentities($row['resolution']).'</td></tr>';
        echo '<tr class="color_a"><td>Escalation Level</td><td>'.$row['escalation_level'].'</td></tr>';
        echo '</table>';
    }
}

?>
