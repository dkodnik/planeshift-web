<?php

function liststats()
{
    if(checkaccess('other', 'read'))
    {
        echo '<p class="header">Statistics</p>';
        
        $groupid = (isset($_GET['groupid']) && is_numeric($_GET['groupid']) ? $_GET['groupid'] : 'nan');
        $op = (isset($_GET['op']) && ($_GET['op'] == 'add' || $_GET['op'] == 'calc')  ? $_GET['op'] : 'list');
        $period = (isset($_GET['period']) && is_numeric($_GET['period']) ? $_GET['period'] : 'nan');
        
        if($groupid == 'nan')
        {
            echo '<p class="error">You have to specify a valid Group ID!</p>';
            return;
        }
        
        if($op == 'add')
        {
            if(checkaccess('other', 'create'))
            {
                $sql = 'SELECT COUNT(*) FROM accounts';
                $sql = "INSERT INTO wc_statistics (groupid, periodname, query) VALUES ('$groupid', '".getNextQuarterPeriod($groupid)."', '$sql')";
                mysql_query2($sql);
            }
            else
            {
                echo '<p class="error">You are not authorized to add a statistic!</p>';
            }
        }
        elseif($op == 'calc')
        {
            if(checkaccess('other', 'edit'))
            {
                if($period == 'nan')
                {
                    echo '<p class="error">You have to specify a valid Period ID!</p>';
                }
                else
                {
                    $sql = "SELECT query FROM wc_statistics WHERE id = '$period'";
                    $query = mysql_query2($sql);
                    
                    if(mysql_num_rows($query) < 1)
                    {
                        echo '<p class="error">No period found!</p>';
                    }
                    else
                    {
                        $sql = mysql_fetch_array($query, MYSQL_ASSOC);
                        $sql = $sql['query'];
                        $query = mysql_query2($sql);
                        
                        $result = mysql_fetch_array($query, MYSQL_NUM);
                        $sql = "UPDATE wc_statistics SET result = '".mysql_real_escape_string($result[0])."' WHERE id = '$period'";
                        mysql_query2($sql);
                    }
                }
            }
            else
            {
                echo '<p class="error">You are not authorized to use these functions</p>';
            }
        }
        
        $sql = "SELECT id, periodname, result, query FROM wc_statistics WHERE groupid = '$groupid' ORDER BY periodname";
        $query = mysql_query2($sql);
        
        echo '<a href="./index.php?do=liststats&op=add&groupid='.$groupid.'">Add a new entry</a><br/>';
        echo '<table><tr>';
        
        $line2 = '';
        $line3 = '';
        while($result = mysql_fetch_array($query, MYSQL_ASSOC))
        {
            echo '<th>'.htmlentities($result['periodname']).'</th>';
            $line2 .= '<td>';
            if(is_numeric($result['result']))
            {
                $line2 .= '<img src="img/bluebar2.gif" width="20" height="'.($result['result'] / 1).'" />';
            }
            else
            {
                $line2 .= '<a href="./index.php?do=liststats&groupid='.$groupid.'&period='.$result['id'].'&op=calc">Calculate</a>';
            }
            $line2 .= '</td>';
            $line3 .= '<td>'.(is_numeric($result['result']) ? $result['result'] : '').'</td>';
        }
        
        echo '</tr><tr class="color_a">'.$line2.'</tr><tr class="color_b">'.$line3.'</tr></table>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function getNextQuarterPeriod($groupid) {
    $sql = "SELECT MAX(periodname) AS max FROM wc_statistics WHERE groupid = '$groupid' ORDER BY periodname";

    $result = mysql_fetch_array(mysql_query2($sql), MYSQL_ASSOC);
    $max = $result['max'];
    
    $year = substr($max, 0, 4);
    $quarter = substr($max, 5, 6);
    
    if($quarter == 'Q4')
    {
      $year = $year+1;
      $quarter = 'Q1';
    }
    else
    {
      $quarter = 'Q'. (substr($quarter, 1, 2) + 1);
    }

    return $year.' '.$quarter;
}

?>