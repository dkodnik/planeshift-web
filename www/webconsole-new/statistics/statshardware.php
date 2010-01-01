<?php

function statshardware()
{
    if(checkaccess('statistics', 'read'))
    {
        echo '<p class="header">Operating Systems</p>';

		$sql = "SELECT operating_system,count(operating_system) as result FROM accounts group by operating_system";
		$query = mysql_query2($sql);

		if(mysql_num_rows($query) < 1)
		{
			echo '<p class="error">No data found!</p>';
		}
        $line2 = '';
        $line3 = '';
		echo '<table><tr>';
        while($result = mysql_fetch_array($query, MYSQL_ASSOC))
        {
            echo '<th>'.htmlentities($result['operating_system']).'</th>';
            $line2 .= '<td>';
            $line2 .= '<img src="img/bluebar2.gif" width="20" height="'.($result['result'] / 1).'" />';
            $line2 .= '</td>';
            $line3 .= '<td>'.(is_numeric($result['result']) ? $result['result'] : '').'</td>';
        }

        echo '</tr><tr class="color_a">'.$line2.'</tr><tr class="color_b">'.$line3.'</tr></table>';

        echo '<p class="header">Graphics Cards</p>';

		$sql = "SELECT graphics_card,count(graphics_card) as result FROM accounts group by graphics_card";
		$query2 = mysql_query2($sql);

		if(mysql_num_rows($query2) < 1)
		{
			echo '<p class="error">No data found!</p>';
		}
        $line2 = '';
        $line3 = '';
		echo '<table><tr>';
        while($result2 = mysql_fetch_array($query2, MYSQL_ASSOC))
        {
            echo '<th>'.htmlentities($result2['graphics_card']).'</th>';
            $line2 .= '<td>';
            $line2 .= '<img src="img/bluebar2.gif" width="20" height="'.($result2['result'] / 1).'" />';
            $line2 .= '</td>';
            $line3 .= '<td>'.(is_numeric($result2['result']) ? $result2['result'] : '').'</td>';
        }

        echo '</tr><tr class="color_a">'.$line2.'</tr><tr class="color_b">'.$line3.'</tr></table>';

        echo '<p class="header">Graphics Version</p>';

		$sql = "SELECT graphics_version,count(graphics_version) as result FROM accounts group by graphics_version";
		$query2 = mysql_query2($sql);

		if(mysql_num_rows($query2) < 1)
		{
			echo '<p class="error">No data found!</p>';
		}
        $line2 = '';
        $line3 = '';
		echo '<table><tr>';
        while($result2 = mysql_fetch_array($query2, MYSQL_ASSOC))
        {
            echo '<th>'.htmlentities($result2['graphics_version']).'</th>';
            $line2 .= '<td>';
            $line2 .= '<img src="img/bluebar2.gif" width="20" height="'.($result2['result'] / 1).'" />';
            $line2 .= '</td>';
            $line3 .= '<td>'.(is_numeric($result2['result']) ? $result2['result'] : '').'</td>';
        }

        echo '</tr><tr class="color_a">'.$line2.'</tr><tr class="color_b">'.$line3.'</tr></table>';

    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>