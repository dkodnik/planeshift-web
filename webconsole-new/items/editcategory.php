<?php
function editcategory(){
    if (checkaccess('items', 'create')){
        if (isset($_GET['commit'])){
            $id = mysql_real_escape_string($_GET['id']);
            $name = mysql_real_escape_string($_POST['name']);
            $item_stat_id_repair_tool = mysql_real_escape_string($_POST['item_stat_id_repair_tool']);
            $is_repair_tool_consumed = mysql_real_escape_string($_POST['is_repair_tool_consumed']);
            $skill_id_repair = mysql_real_escape_string($_POST['skill_id_repair']);
            $identify_skill_id = mysql_real_escape_string($_POST['identify_skill_id']);
            $identify_min_skill = mysql_real_escape_string($_POST['identify_min_skill']);
            $query = "UPDATE item_categories SET name='$name', item_stat_id_repair_tool='$item_stat_id_repair_tool', is_repair_tool_consumed='$is_repair_tool_consumed', skill_id_repair='$skill_id_repair', identify_skill_id='$identify_skill_id', identify_min_skill='$identify_min_skill' WHERE category_id=".$id;
            $result = mysql_query2($query);

            echo '<SCRIPT language="javascript">';
            echo 'document.location = "index.php?do=editcategory&edit&id='.$id.'"';
            echo '</script>';

        }
        elseif (isset($_GET['edit']))
        {
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                echo '<p class="error">Invalid ID</p>';
                return;
            }
            $id = mysql_real_escape_string($_GET['id']);
            $query = "SELECT category_id, name, item_stat_id_repair_tool, is_repair_tool_consumed, skill_id_repair, identify_skill_id, identify_min_skill FROM item_categories WHERE category_id='$id'";
            $result = mysql_query2($query);
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            
            $item_result = PrepSelect('items');
            $skill_result = PrepSelect('skill');
            
            echo '<table border="1"><form action="./index.php?do=editcategory&amp;commit&amp;id='.$row['category_id'].'" method="post">';
            echo '<tr><th>Field</th><th>Value</th></tr>';
            echo '<tr><td>Name: </td><td><input type="text" name="name" value="'.$row['name'].'"/></td></tr>';
            echo '<tr><td>Repair Tool: </td><td>'.DrawSelectBox('items', $item_result, 'item_stat_id_repair_tool', $row['item_stat_id_repair_tool'], 'true').'</td></tr>';
            echo '<tr><td>Consume Repair Tool: </td><td>';
            if ($row['is_repair_tool_consumed'] == "Y")
            {
                echo '<select name="is_repair_tool_consumed"><option value="N">False</option><option value="Y" selected="true">True</option></select>';
            }
            else
            {
                echo '<select name="is_repair_tool_consumed"><option value="N" selected="true">False</option><option value="Y">True</option></select>';
            }
            echo '</td></tr>';
            echo '<tr><td>Repair Skill: </td><td>'.DrawSelectBox('skill', $skill_result, 'skill_id_repair', $row['skill_id_repair'], 'true').'</td></tr>';
            echo '<tr><td>Identify Skill: </td><td>'.DrawSelectBox('skill', $skill_result, 'identify_skill_id', $row['identify_skill_id'], 'true').'</td></tr>';
            echo '<tr><td>Min Identify Skill: </td><td><input type="text" name="identify_min_skill" value="'.$row['identify_min_skill'].'"/></td></tr>';
                
            echo '<tr><td></td><td><input type="submit" name="edit" value="Edit" /></td></tr>';
            echo '</form></table>';
        }
        else
        {
            $query = "SELECT category_id, name FROM item_categories ORDER BY name ASC";
            $result = mysql_query2($query);
            $q2 = 'SELECT c.category_id, COUNT(i.id) AS items FROM item_categories AS c LEFT JOIN item_stats AS i ON c.category_id=i.category_id GROUP by c.category_id';
            $r2 = mysql_query2($q2);
            while ($i_row = mysql_fetch_array($r2, MYSQL_ASSOC))
            {
                $C_id=$i_row['category_id'];
                $Count["$C_id"]['items']= $i_row['items'];
            }
            unset($r2);
            unset($i_row);
            $q2 = 'SELECT c.category_id, COUNT(i.category_id) AS merchants FROM item_categories AS c LEFT JOIN merchant_item_categories AS i ON c.category_id=i.category_id GROUP by c.category_id';
            $r2 = mysql_query2($q2);
            while ($i_row = mysql_fetch_array($r2, MYSQL_ASSOC))
            {
                $C_id=$i_row['category_id'];
                $Count["$C_id"]['merchants']= $i_row['merchants'];
            }
            echo '<table border="1"><tr><th>ID</th><th>Items</th><th>merchants</th><th>Category Name</th><th>Repair Tool</th><th>Actions</th></tr>'."\n";
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
            {
                $C_id = $row['category_id'];
                echo '<tr><td>'.$row['category_id'].'</td><td>'.$Count["$C_id"]['items'].'</td><td>'.$Count["$C_id"]['merchants'].'</td>';
                echo '<td><form action="./index.php?do=editcategory&amp;edit&amp;id='.$row['category_id'].'" method="post">'.$row['name'].'</td>';
                echo '<td><input type="submit" name="edit" value="Edit" /></form>'."\n";
                if (($Count["$C_id"]['items'] == 0) && ($Count["$C_id"]['merchants'] == 0) && (checkaccess('items', 'delete')))
                {
                    echo ' -- <a href="./index.php?do=deletecategory&amp;id='.$row['category_id'].'">Delete</a>';
                }
                echo '</td></tr>';
            }
            if (checkaccess('items', 'create'))
            {
                echo '<tr><td>N/A</td><td>&nbsp;</td><td>&nbsp;</td><td class="center"><form action="./index.php?do=createcategory" method="post"><input type="text" name="category" /><br/><input type="submit" name="submit" value="Create Category" /></form></td></tr>';
            }
            echo '</table>';
        }
    }
    else
    {
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function createcategory(){
  if (checkaccess('items', 'create')){
    $category = mysql_real_escape_string($_POST['category']);
    $query = "INSERT INTO item_categories SET name='$category'";
    $result = mysql_query2($query);
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
