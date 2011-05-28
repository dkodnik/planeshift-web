<?php

function checkbooks()
{
    if(checkaccess('als', 'read'))
    {
        echo '<p class="header">Check Books</p>';
        
        $mode = (isset($_GET['mode']) && $_GET['mode'] == 'name' ? 'name' : 'category' );
        echo ($mode == 'name' ? '' : '<a href="./index.php?do=checkbooks&amp;mode=name">').'Search books by name / meshname (in action_locations)'.($mode == 'name' ? '' : '</a>').' | ';
        echo ($mode == 'category' ? '' : '<a href="./index.php?do=checkbooks&amp;mode=category">').'Search books by category'.($mode == 'category' ? '' : '</a>').'<br/><br/>';
        
        if($mode == 'name')
        {
            $sql = "SELECT id, name, response, sectorname FROM action_locations WHERE name LIKE '%books%' OR meshname LIKE '%book%'";
            $query = mysql_query2($sql);
            
            $result = array();
            while($row = mysql_fetch_array($query, MYSQL_ASSOC))
            {
                $result[] = $row;
            }
            mysql_free_result($query);
        }
        else
        {
            // A complicated way:
            // * first we get the category_id for Books
            // * next we get the books from item_stats
            // * now we get the parent_item_id (the container) from item_instances
            $sql = "SELECT i.parent_item_id FROM item_categories AS c, item_stats AS s, item_instances AS i WHERE c.name = 'Books' AND s.category_id = c.category_id AND i.item_stats_id_standard = s.id";
            $query = mysql_query2($sql);
            
            $sql = "SELECT id, name, response, sectorname FROM action_locations WHERE response LIKE '%<Container ID=%'";
            $query2 = mysql_query2($sql);
            
            $items = array();
            while($row = mysql_fetch_array($query2, MYSQL_ASSOC))
            {
                $items[] = $row;
            }
            mysql_free_result($query2);
            
            $result = array();
            while($row = mysql_fetch_array($query, MYSQL_ASSOC))
            {
                $id = $row['parent_item_id'];
                foreach($items as $i => $fields)
                {
                    $tmp = str_replace(array("\n", "\r"), '', $fields['response']); // Just to be sure...
                    if(strpos($tmp, '<Container ID=\''.$id.'\'/>') !== false) {
                        $result[] = $fields;
                        break;
                    }
                }
            }
            mysql_free_result($query);
        }
        
        // Start "streaming". From this point on every output will be transferred to the browser without buffering.
        ob_implicit_flush(1);
        ob_end_flush();
        
        if(count($result) == 0)
        {
            echo '<p class="error">No books found!</p>';
        }
        else
        {
            foreach($result as $row)
            {
                $pos = strpos($row['response'], 'Container ID');
                if($pos === false) continue;
                
                $containerid = substr($row['response'], $pos + 14, strpos($row['response'], "'", $pos + 14) - $pos - 14);
                $sql = "SELECT s.name AS sectorname, i.char_id_owner, i.parent_item_id, i.flags, c.name AS char_firstname, c.lastname AS char_lastname FROM item_instances AS i LEFT JOIN sectors AS s ON i.loc_sector_id = s.id LEFT JOIN characters AS c ON i.char_id_owner = c.id WHERE i.id = '$containerid' LIMIT 1";
                $query2 = mysql_query2($sql);
                
                if(mysql_num_rows($query2) == 0)
                {
                    echo '<p class="error">Action location #'.$row['id'].' "'.htmlentities($row['name']).'" specifies an invalid container #'.$containerid.'.</p>';
                    continue;
                }
                $row2 = mysql_fetch_array($query2, MYSQL_ASSOC);
                
                if($row2['char_id_owner'] != null && $row2['char_id_owner'] != 0)
                {
                    echo '<p class="error">Action location #'.$row['id'].' "'.htmlentities($row['name']).'" specifies a valid container #'.$containerid.' but the container is carried by player ';
                    echo '"'.(checkaccess('npcs', 'edit') ? '<a href="./index.php?do=npc_details&sub=main&npc_id='.$row['char_id_owner'].'">' : '').htmlentities($row['char_firstname']).' '.htmlentities($row['char_lastname']).(checkaccess('npcs', 'edit') ? '</a>' : '' ).'".</p>';
                    continue;
                }
                if($row2['parent_item_id'] != null && $row2['parent_item_id'] != 0)
                {
                    echo '<p class="error">Action location #'.$row['id'].' "'.htmlentities($row['name']).'" specifies a valid container #'.$containerid.' but is inside another container with id "'.$row2['parent_item_id'].'".</p>';
                    continue;
                }
                if($row2['sectorname'] != $row['sectorname'])
                {
                    echo '<p class="error">Action location #'.$row['id'].' "'.htmlentities($row['name']).'" specifies a valid container #'.$containerid.' but the container is inside another sector: "'.htmlentities($row2['sectorname']).'".</p>';
                    continue;
                }
                if(strpos($row2['flags'], 'NOPICKUP') === false)
                {
                    echo '<p class="error">Action location #'.$row['id'].' "'.htmlentities($row['name']).'" specifies a valid container #'.$containerid.' but the container is missing the "NOPICKUP" flag.</p>';
                    echo "   Tip: UPDATE item_instances SET flags = 'NOPICKUP NPCOWNED' WHERE id = '$containerid' LIMIT 1<br/>";
                    continue;
                }
                if(strpos($row2['flags'], 'NPCOWNED') === false)
                {
                    echo '<p class="error">Action location #'.$row['id'].' "'.htmlentities($row['name']).'" specifies a valid container #'.$containerid.' but the container is missing the "NPCOWNED" flag.</p>';
                    echo "   Tip: UPDATE item_instances SET flags = 'NOPICKUP NPCOWNED' WHERE id = '$containerid' LIMIT 1<br/>";
                    continue;
                }
                
                $sql = "SELECT flags, item_stats_id_standard FROM item_instances WHERE parent_item_id = '$containerid' ORDER BY item_stats_id_standard";
                $query3 = mysql_query2($sql);
                if(mysql_num_rows($query3) == 0)
                {
                    echo '<p class="error">Action location #'.$row['id'].' "'.htmlentities($row['name']).'" specifies a valid container #'.$containerid.' but the container seems empty.</p>';
                    continue;
                }
                
                while($row3 = mysql_fetch_array($query3, MYSQL_ASSOC))
                {
                    if(strpos($row3['flags'], 'NOPICKUP') === false)
                    {
                        echo '<p class="error">Action location #'.$row['id'].' "'.htmlentities($row['name']).'" specifies a valid container #'.$containerid.' but the item #'.$row3['id'].' contained is missing the "NOPICKUP" flag.</p>';
                        continue;
                    }
                    echo '   Book #'.$row3['item_stats_id_standard'].' in container #'.$containerid.' is ok.<br/>';
                }
                echo 'Action location #'.$row['id'].' "'.htmlentities($row['name']).'" specifies a valid container #'.$containerid.' and all checks are passed.<br/>';
            }
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>
