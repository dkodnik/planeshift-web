<?php

function checkMindItemUsage()
{
    if (!checkaccess('crafting', 'read'))
	{
		echo '<p class="error">You are not authorized to use these functions</p>';
		return;
	}
	if (!isset($_GET['full_list'])) 
	{
		echo '<p class="error">This page takes a lot of server resources to load, only use it if you really need it. If you only need to edit ';
		echo '(or view) 1 item, please use the list below, or change it directly at the patterns page.<br/>';
		echo 'click <a href="./index.php?do=checkminditemusage&amp;full_list">here</a> to load the full page. </p>';
	}
	
	$query = "SELECT id, name, category_id FROM item_stats WHERE stat_type = 'B' AND valid_slots LIKE '%MIND%' ORDER BY name";
	$result = mysql_query2($query);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		echo '<p><a href="./index.php?do=listitems&amp;override1&amp;category='.$row['category_id'].'&amp;item='.$row['id'].'">'.$row['name'].'</a><br/>';
		if(isset($_GET['full_list']))
		{
			$item_id = $row['id'];
			// Don't make "iss" "is" (like it should logically be) since "is" is a reserved keyword in mysql.
			$query_vendor = "SELECT DISTINCT c.id, c.name, c.lastname FROM merchant_item_categories AS m LEFT JOIN characters AS c ON c.id=m.player_id LEFT JOIN item_instances AS i ON i.char_id_owner=m.player_id LEFT JOIN item_stats AS iss ON iss.id=i.item_stats_id_standard WHERE i.location_in_parent > '15' AND i.item_stats_id_standard='$item_id' AND iss.category_id=m.category_id ORDER BY iss.name";
			$result_vendor = mysql_query2($query_vendor);
			
			$item_name = $row['name'];
			// checking if any quest gives this as a reward. (Or an item with an identical name.)
			$query_quest = "SELECT q.id, q.name FROM quests AS q LEFT JOIN quest_scripts AS qs ON q.id=qs.quest_id WHERE CONVERT(qs.script USING latin1) REGEXP '[\\n](Give)[^\\n]*$item_name' ORDER BY q.name ASC";
			$result_quest = mysql_query2($query_quest);
			
			if (mysql_num_rows($result_vendor) == 0 && mysql_num_rows($result_quest) == 0)
			{
				echo '<span class="error">No vendors sell this item, and it is not rewarded in any quest either.</span></p>';
				continue;
			}
			if (mysql_num_rows($result_vendor) == 0)
			{
				echo 'There are no vendors selling this item.<br/>';
			}
			else
			{
				echo 'The following vendors sell this item: ';
				while ($row_vendor = mysql_fetch_array($result_vendor, MYSQL_ASSOC))
				{
					if (checkaccess('npcs', 'read'))
					{
						echo '<a href="./index.php?do=npc_details&sub=main&npc_id='.$row_vendor['id'].'">'.$row_vendor['name'].' '.$row_vendor['lastname'].'</a> ';
					}
					else
					{
						echo $row_vendor['name'].' '.$row_vendor['lastname'].'  ';
					}
				}
				echo '<br/>';
			}
			if (mysql_num_rows($result_quest) == 0)
			{
				echo 'There are no quests giving this item.<br/>';
			}
			else
			{
				echo 'The following quests give this item: ';
				while ($row_quest = mysql_fetch_array($result_quest, MYSQL_ASSOC))
				{
					echo $row_quest['name'].' (';
					if (checkaccess('quests', 'read'))
					{
						echo '<a href="./index.php?do=readquest&amp;id='.$row_quest['id'].'">Read</a>';
					}
					if (checkaccess('quests', 'edit'))
					{
						echo ' || <a href="./index.php?do=editquest&amp;id='.$row_quest['id'].'">Edit</a>) ';
					}
				}
			}
			echo '</p>';
		}
	}
}

?>
