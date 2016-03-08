<?php

// Only call tribedetails directly, the others do not peform access checks for read, nor do they check IDs.
function tribedetails()
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to view Tribe details</p>';
        return;
    }
    $uri_string = './index.php?do=tribe_details';
    if (!isset($_GET['tribe_id']) || !is_numeric($_GET['tribe_id']))
    {
        echo '<p class="error">Invalid ID</p>';
        return;
    }
    $uri_string = $uri_string.'&amp;tribe_id='.$_GET['tribe_id'];
    
    $id = escapeSqlString($_GET['tribe_id']);
    $query = "SELECT name FROM tribes WHERE id='$id'";
    $result = mysql_query2($query);
    $row = fetchSqlAssoc($result);
    echo '<p class="bold" style="float: left; margin: 0pt 5px 0pt 0pt;">Tribe: '.$id.' - '.$row['name'].'</p>'."\n";
    if (checkaccess('npcs', 'delete'))
    {
        // notice this form directs to listtribes.php -> edittribes
        echo '<form action="index.php?do=edittribes" method="post">'."\n";
        echo '<p style="margin: 0pt 5px 0pt 0pt;"><input type="hidden" name="id" value="'.$id.'" /><input type="submit" name="commit" value="Delete" /></p>'."\n";
        echo '</form>'."\n";
    }
    echo "<br/>\n";
    echo '<div class="menu_npc">'."\n";
    echo '<a href="'.$uri_string.'&amp;sub=main">Main</a><br/>'."\n";
    echo '<a href="'.$uri_string.'&amp;sub=members">Members</a><br/>'."\n";
    echo '<a href="'.$uri_string.'&amp;sub=assets">Assets</a><br/>'."\n";
    echo '<a href="'.$uri_string.'&amp;sub=knowledge">Knowledge</a><br/>'."\n";
    echo '<a href="'.$uri_string.'&amp;sub=memories">Memories</a><br/>'."\n";
    echo '</div><div class="main_npc">'."\n";
    if (isset($_GET['sub']))
    {
        switch ($_GET['sub'])
        {
            case 'main':
                tribeDetailsMain();
                break;
            case 'members':
                tribeMembers();
                break;
            case 'assets':
                tribeAssets();
                break;
            case 'knowledge':
                tribeKnowledge();
                break;
            case 'memories':
                tribeMemories();
                break;
            default:
                echo '<p class="error">Please Select an Action</p>';
        }
    }
    else
    {
        echo '<p class="error">Please Select an Action</p>';
    }
    echo '</div>'."\n";
}

function tribeDetailsMain()
{
    // block unauthorized access
    if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) 
    {
        echo '<p class="error">You are not authorized to edit Tribes</p>';
        return;
    }
    if (!isset($_POST['commit']))
    {
        $id = escapeSqlString($_GET['tribe_id']);
        $query = "SELECT * FROM tribes WHERE id='$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        echo '<form action="./index.php?do=tribe_details&amp;sub=main&amp;tribe_id='.$id.'" method="post"><table>';
        echo '<tr><td>Name:</td><td><input type="text" name="name" value="'.$row['name'].'" /></td></tr>';
        $Sectors = PrepSelect('sectorid');
        echo '<tr><td>Home:</td>';
        echo '<td>';
        echo '<table><tr><th>Sector</th><th>X</th><th>Y</th><th>Z</th><th>Radius</th></tr>';
        echo '<tr><td>'.DrawSelectBox('sectorid', $Sectors, 'home_sector_id', $row['home_sector_id']).'</td><td><input type="text" name="home_x" value="'.$row['home_x'].'" size="5"/></td>';
        echo '<td><input type="text" name="home_y" value="'.$row['home_y'].'" size="5"/></td>';
        echo '<td><input type="text" name="home_z" value="'.$row['home_z'].'" size="5"/></td>';
        echo '<td><input type="text" name="home_radius" value="'.$row['home_radius'].'" size="5"/></td></tr></table></td></tr>';
        echo '<tr><td>Max Size</td><td><input type="text" name="max_size" value="'.$row['max_size'].'" /></td></tr>';
        echo '<tr><td>Wealth reource name:</td><td><input type="text" name="wealth_resource_name" value="'.$row['wealth_resource_name'].'" /></td></tr>';
        echo '<tr><td>Wealth Resource Nick</td><td><input type="text" name="wealth_resource_nick" value="'.$row['wealth_resource_nick'].'" /></td></tr>';
        echo '<tr><td>Wealth Resource Area</td><td><input type="text" name="wealth_resource_area" value="'.$row['wealth_resource_area'].'" /></td></tr>';
        echo '<tr><td>Wealth Gather Need</td><td><input type="text" name="wealth_gather_need" value="'.$row['wealth_gather_need'].'" /></td></tr>';
        echo '<tr><td>Wealth Resource Growth</td><td><input type="text" name="wealth_resource_growth" value="'.$row['wealth_resource_growth'].'" /></td></tr>';
        echo '<tr><td>Wealth Resource Growth Active</td><td><input type="text" name="wealth_resource_growth_active" value="'.$row['wealth_resource_growth_active'].'" /></td></tr>';
        echo '<tr><td>Wealth Resource Growth Active Limit</td><td><input type="text" name="wealth_resource_growth_active_limit" value="'.$row['wealth_resource_growth_active_limit'].'" /></td></tr>';
        echo '<tr><td>Reproduction Cost</td><td><input type="text" name="reproduction_cost" value="'.$row['reproduction_cost'].'" /></td></tr>';
        echo '<tr><td>NPC Idle Behavior</td><td><input type="text" name="npc_idle_behavior" value="'.$row['npc_idle_behavior'].'" /></td></tr>';
        $tribe_recipe = PrepSelect('tribe_recipe');
        echo '<tr><td>Tribal Recipe</td><td>'.DrawSelectBox('tribe_recipe', $tribe_recipe, 'tribal_recipe', $row['tribal_recipe']).'</td></tr>';
        echo '<tr><td colspan="2"><input type="hidden" name="id" value="'.$id.'" /><input type="submit" name="commit" value="Update" /></td></tr>';
        echo '</table></form>';
    }
    else
    {
        $id = escapeSqlString($_POST['id']);
        $name = escapeSqlString($_POST['name']);
        $home_sector_id = escapeSqlString($_POST['home_sector_id']);
        $home_x = escapeSqlString($_POST['home_x']);
        $home_y = escapeSqlString($_POST['home_y']);
        $home_z = escapeSqlString($_POST['home_z']);
        $home_radius = escapeSqlString($_POST['home_radius']);
        $max_size = escapeSqlString($_POST['max_size']);
        $wealth_resource_name = escapeSqlString($_POST['wealth_resource_name']);
        $wealth_resource_nick = escapeSqlString($_POST['wealth_resource_nick']);
        $wealth_resource_area = escapeSqlString($_POST['wealth_resource_area']);
        $wealth_gather_need = escapeSqlString($_POST['wealth_gather_need']);
        $wealth_resource_growth = escapeSqlString($_POST['wealth_resource_growth']);
        $wealth_resource_growth_active = escapeSqlString($_POST['wealth_resource_growth_active']);
        $wealth_resource_growth_active_limit = escapeSqlString($_POST['wealth_resource_growth_active_limit']);
        $reproduction_cost = escapeSqlString($_POST['reproduction_cost']);
        $npc_idle_behavior = escapeSqlString($_POST['npc_idle_behavior']);
        $tribal_recipe = escapeSqlString($_POST['tribal_recipe']);
        $query = "UPDATE tribes SET name='$name', home_sector_id='$home_sector_id', home_x='$home_x', home_y='$home_y', home_z='$home_z', home_radius='$home_radius', max_size='$max_size', wealth_resource_name='$wealth_resource_name', wealth_resource_nick='$wealth_resource_nick', wealth_resource_area='$wealth_resource_area', wealth_gather_need='$wealth_gather_need', wealth_resource_growth='$wealth_resource_growth', wealth_resource_growth_active='$wealth_resource_growth_active', wealth_resource_growth_active_limit='$wealth_resource_growth_active_limit', reproduction_cost='$reproduction_cost', npc_idle_behavior='$npc_idle_behavior', tribal_recipe='$tribal_recipe' WHERE id='$id'";
        mysql_query2($query);
        echo '<p class="error">Tribe Successfully Updated</p>';
        unset($_POST);
        tribe_main();
    }
}

function tribeMembers()
{
    // delegate this functionality. the $_GET['tribe_id'] will make this list the right items.
    include "listtribemembers.php";
    listtribemembers();
}

function tribeAssets()
{
    // block unauthorized access
    if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) 
    {
        echo '<p class="error">You are not authorized to edit Tribes</p>';
        return;
    }
    // this already got validated in the main method above.
    $tribeId = escapeSqlString($_GET['tribe_id']);    
    
    $enumStatus = array('ASSET_STATUS_NOT_APPLICABLE', 'ASSET_STATUS_NOT_USED', 'ASSET_STATUS_INCONSTRUCTION', 'ASSET_STATUS_CONSTRUCTED');
    $enumTypes = array('ASSET_TYPE_ITEM', 'ASSET_TYPE_BUILDING', 'ASSET_TYPE_BUILDINGSPOT');
    $makeEnumDropdown = function ($name, $enumArray, $selected = -1) 
    {
        $output = '';
        $output .= '<select name="'.$name.'">';
        foreach ($enumArray as $key => $value)
        {
            $output .= '<option value="'.$key.'" '.($key == $selected ? 'selected="selected"' : '').'>'.$value.'</option>';
        }
        $output .= '</select>';
        return $output;
    };
    
    // after the handling of commit, the script will resume with the listing of all assets for this tribe.
    if (isset($_POST['commit']) && $_POST['commit'] == 'Create Asset')
    {
        $name = escapeSqlString($_POST['name']);
        $type = escapeSqlString($_POST['type']);
        $coordX = escapeSqlString($_POST['coordx']);
        $coordY = escapeSqlString($_POST['coordy']);
        $coordZ = escapeSqlString($_POST['coordz']);
        $sector_id = escapeSqlString($_POST['sector_id']);
        $itemStatsId = escapeSqlString(($_POST['itemid'] == '' ? '0' : $_POST['itemid']));
        $quantity = escapeSqlString($_POST['quantity']);
        $status = escapeSqlString($_POST['status']);
        $itemID = 0;
        if ($itemStatsId != 0)
        {
            $sql = "INSERT INTO item_instances (item_stats_id_standard, flags) VALUES ('$itemStatsId', 'NOPICKUP')";
            mysql_query2($sql);
            $itemID = sqlInsertId();
        }
        $sql = "INSERT INTO sc_tribe_assets (tribe_id, name, type, coordX, coordY, coordZ, sector_id, itemID, quantity, status) VALUES ('$tribeId', '$name', '$type', '$coordX', '$coordY', '$coordZ', '$sector_id', '$itemID', '$quantity', '$status')";
        mysql_query2($sql);
        echo '<p class="error">Asset added.</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        $assetId = escapeSqlString($_GET['asset_id']);
        $sql = "SELECT ii.char_id_owner, ta.itemID FROM sc_tribe_assets AS ta LEFT JOIN item_instances AS ii ON ii.id = ta.itemID WHERE ta.id='$assetId'";
        $result = mysql_query2($sql);
        $row = fetchSqlAssoc($result);
        $itemId = $row['itemID'];
        // only delete the item instance if it has no owner.
        if ($row['char_id_owner'] == '' || $row['char_id_owner'] == 0)
        {
            $sql = "DELETE FROM item_instances WHERE id='$itemId'";
            mysql_query2($sql);
        }
        $sql = "DELETE FROM sc_tribe_assets WHERE id='$assetId'";
        mysql_query2($sql);
        echo '<p class="error">Delete succesfull</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Save Changes')
    {
        $assetId = escapeSqlString($_GET['asset_id']);
        $name = escapeSqlString($_POST['name']);
        $type = escapeSqlString($_POST['type']);
        $coordX = escapeSqlString($_POST['coordx']);
        $coordY = escapeSqlString($_POST['coordy']);
        $coordZ = escapeSqlString($_POST['coordz']);
        $sector_id = escapeSqlString($_POST['sector_id']);
        $itemStatsId = escapeSqlString(($_POST['itemid'] == '' ? '0' : $_POST['itemid']));
        $oldId = escapeSqlString($_POST['item_id_old']);
        $quantity = escapeSqlString($_POST['quantity']);
        $status = escapeSqlString($_POST['status']);
        $itemIdStatement = '';
        if ($oldId != $itemStatsId)
        {
            // delete the old item_instance if no one owns it, we don't want to polute the database.
            $sql = "SELECT ii.char_id_owner, ta.itemID FROM sc_tribe_assets AS ta LEFT JOIN item_instances AS ii ON ii.id = ta.itemID WHERE ta.id='$assetId'";
            $result = mysql_query2($sql);
            $row = fetchSqlAssoc($result);
            $itemId = $row['itemID'];
            // only delete the item instance if it has no owner.
            if ($row['char_id_owner'] == '' || $row['char_id_owner'] == 0)
            {
                $sql = "DELETE FROM item_instances WHERE id='$itemId'";
                mysql_query2($sql);
            }
            // make the new instance
            $sql = "INSERT INTO item_instances (item_stats_id_standard, flags) VALUES ('$itemStatsId', 'NOPICKUP')";
            mysql_query2($sql);
            $itemID = sqlInsertId();
            $itemIdStatement = "itemID='$itemID',";
        }
        // update the asset values
        $sql = "UPDATE sc_tribe_assets SET name='$name', type='$type', coordX='$coordX', coordY='$coordY', coordZ='$coordZ', sector_id='$sector_id', $itemIdStatement quantity='$quantity', status='$status' WHERE id = '$assetId'";
        mysql_query2($sql);
        echo '<p class="error">Update succesfull</p>';
    }
    
    // if we print something for any of these actions, nothing else gets printed (no assets list).
    if (isset($_GET['action']) && $_GET['action'] == 'edit')
    {
        $assetId = escapeSqlString($_GET['asset_id']);
        $sql = "SELECT ta.id, ta.name, ta.type, ta.coordX, ta.coordY, ta.coordZ, ta.sector_id, ii.item_stats_id_standard, ta.quantity, ";
        $sql .= "ta.status FROM sc_tribe_assets AS ta LEFT JOIN item_instances AS ii ON ta.itemID = ii.id WHERE ta.id = '$assetId'";
        $result = mysql_query2($sql);
        $row = fetchSqlAssoc($result);
    
        $sectors = prepselect('sectorid');
        echo '<p>Editing Asset: </p>';
        echo '<form action="./index.php?do=tribe_details&amp;sub=assets&amp;tribe_id='.$tribeId.'&amp;asset_id='.$assetId.'" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>ID</td><td>'.$row['id'].'</td></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" value="'.htmlentities($row['name']).'" /></td></tr>';
        echo '<tr><td>Type</td><td>'.$makeEnumDropdown('type', $enumTypes, $row['type']).'</td></tr>';
        echo '<tr><td>X</td><td><input type="text" name="coordx" value="'.$row['coordX'].'" /></td></tr>';
        echo '<tr><td>Y</td><td><input type="text" name="coordy" value="'.$row['coordY'].'" /></td></tr>';
        echo '<tr><td>Z</td><td><input type="text" name="coordz" value="'.$row['coordZ'].'" /></td></tr>';
        echo '<tr><td>Sector</td><td>'.DrawSelectBox('sectorid', $sectors, 'sector_id', $row['sector_id']).'</td></tr>';
        echo '<tr><td>Item</td><td><input type="hidden" name="item_id_old" value="'.$row['item_stats_id_standard'].'" />'.DrawItemSelectBox('itemid', $row['item_stats_id_standard'], true, true).'</td></tr>';
        echo '<tr><td>Quantity</td><td><input type="text" name="quantity" value="'.$row['quantity'].'" /></td></tr>';
        echo '<tr><td>Status</td><td>'.$makeEnumDropdown('status', $enumStatus, $row['type']).'</td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Save Changes" /></td></tr>';
        echo '</table>';
        echo '</form>';
        return;
    }
    elseif (isset($_GET['action']) && $_GET['action'] == 'delete')
    {
        $assetId = escapeSqlString($_GET['asset_id']);
        $sql = "SELECT ta.name, ii.char_id_owner, ta.itemID FROM sc_tribe_assets AS ta LEFT JOIN item_instances AS ii ON ii.id = ta.itemID WHERE ta.id='$assetId'";
        $result = mysql_query2($sql);
        $row = fetchSqlAssoc($result);
        echo '<p class="error">You are about to delete tribe asset "'.htmlentities($row['name']).'" ';
        if ($row['char_id_owner'] == '' || $row['char_id_owner'] == 0)
        {
            echo 'and its associate item instance.';
        }
        else
        {
            echo 'its associate item instance was picked up by a (<a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$row['char_id_owner'].'">player</a>) and will not be deleted.';
        }
        echo '</p><form action="./index.php?do=tribe_details&amp;sub=assets&amp;tribe_id='.$tribeId.'&amp;asset_id='.$assetId.'" method="post">';
        echo '<div><input type="submit" name="commit" value="Confirm Delete" /></div>';
        echo '</form>';
        return;
    }
    
    // notice itemID may refer to a deleted instance, resulting in null values for item name.
    $sql = "SELECT ta.id, ta.name, ta.type, ta.coordX, ta.coordY, ta.coordZ, s.name AS sector_name, ta.itemID, ist.name AS item_name, ta.quantity, ";
    $sql .= "ta.status FROM sc_tribe_assets AS ta LEFT JOIN sectors AS s ON ta.sector_id = s.id LEFT JOIN item_instances AS ii ON ta.itemID = ii.id ";
    $sql .= "LEFT JOIN item_stats AS ist ON ii.item_stats_id_standard = ist.id WHERE tribe_id = '$tribeId' ORDER BY ta.name";
    
    $sql2 = "SELECT COUNT(*) FROM sc_tribe_assets WHERE tribe_id = '$tribeId'";
    $item_count = fetchSqlRow(mysql_query2($sql2));
    $nav = RenderNav('do=tribe_details&sub=assets&tribe_id='.$tribeId, $item_count[0]);
    $sql .= $nav['sql'];
    echo $nav['html'];
    unset($nav);
    
    $result = mysql_query2($sql);
    
    if (sqlNumRows($result) == 0)
    {
        echo '<p class="error">No assets found for this tribe.</p>';
    }
    else
    {   
        echo '<table>'."\n";
        echo '<tr><th>ID</th><th>Name</th><th>Type</th><th>Coords</th><th>Sector</th><th>Item</th><th>Quantity</th><th>Status</th><th>Actions</th></tr>'."\n";
        
        $alt = false;
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
            echo '<td>'.$row['id'].'</td>';
            echo '<td>'.htmlentities($row['name']).'</td>';
            echo '<td>'.$enumTypes[$row['type']].'</td>';
            echo '<td>'.$row['coordX'].'/'.$row['coordY'].'/'.$row['coordZ'].'</td>';
            echo '<td>'.$row['sector_name'].'</td>';
            echo '<td>'.htmlentities(($row['item_name'] != '' ? $row['item_name'] : ($row['itemID'] == 0 ? '0' : $row['itemID'].' (deleted)'))).'</td>';
            echo '<td>'.$row['quantity'].'</td>';
            echo '<td>'.$enumStatus[$row['status']].'</td>';
            echo '<td>';
            if (checkAccess('npcs', 'edit'))
            {
                $url = './index.php?do=tribe_details&amp;sub=assets&amp;tribe_id='.$tribeId.'&amp;asset_id='.$row['id'];
                echo '<a href="'.$url.'&amp;action=edit">Edit</a> - <a href="'.$url.'&amp;action=delete">Delete</a>';
            }
            echo '</td>';
            echo '</tr>'."\n";
        }
        echo '</table>'."\n";
    }
    if (checkAccess('npcs', 'edit'))
    {
        $sectors = prepselect('sectorid');
        echo '<hr/><p>Create new Asset: </p>';
        echo '<form action="./index.php?do=tribe_details&amp;sub=assets&amp;tribe_id='.$tribeId.'" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" /></td></tr>';
        echo '<tr><td>Type</td><td>'.$makeEnumDropdown('type', $enumTypes).'</td></tr>';
        echo '<tr><td>X</td><td><input type="text" name="coordx" /></td></tr>';
        echo '<tr><td>Y</td><td><input type="text" name="coordy" /></td></tr>';
        echo '<tr><td>Z</td><td><input type="text" name="coordz" /></td></tr>';
        echo '<tr><td>Sector</td><td>'.DrawSelectBox('sectorid', $sectors, 'sector_id', '').'</td></tr>';
        echo '<tr><td>Item</td><td>'.DrawItemSelectBox('itemid', false, true, true).'</td></tr>';
        echo '<tr><td>Quantity</td><td><input type="text" name="quantity" /></td></tr>';
        echo '<tr><td>Status</td><td>'.$makeEnumDropdown('status', $enumStatus).'</td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Create Asset" /></td></tr>';
        echo '</table>';
        echo '</form>';
    }
}

function tribeKnowledge()
{
    // block unauthorized access
    if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) 
    {
        echo '<p class="error">You are not authorized to edit Tribes</p>';
        return;
    }
    // this already got validated in the main method above.
    $tribeId = escapeSqlString($_GET['tribe_id']);  
    
    // after the handling of commit, the script will resume with the listing of all knowledge for this tribe.
    if (isset($_POST['commit']) && $_POST['commit'] == 'Create Knowledge')
    {
        $knowledge = escapeSqlString($_POST['knowledge']);
        $sql = "INSERT INTO sc_tribe_knowledge (tribe_id, knowledge) VALUES ('$tribeId', '$knowledge')";
        mysql_query2($sql);
        echo '<p class="error">Knowledge added.</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        $knowledgeId = escapeSqlString($_GET['knowledge_id']);
        $sql = "DELETE FROM sc_tribe_knowledge WHERE id='$knowledgeId'";
        mysql_query2($sql);
        echo '<p class="error">Delete succesfull</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Save Changes')
    {
        $knowledgeId = escapeSqlString($_GET['knowledge_id']);
        $knowledge = escapeSqlString($_POST['knowledge']);
        $sql = "UPDATE sc_tribe_knowledge SET knowledge='$knowledge' WHERE id = '$knowledgeId'";
        mysql_query2($sql);
        echo '<p class="error">Update succesfull</p>';
    }
    
    // if we print something for any of these actions, nothing else gets printed (no knowledge list).
    if (isset($_GET['action']) && $_GET['action'] == 'edit')
    {
        // edit form
        $knowledgeId = escapeSqlString($_GET['knowledge_id']);
        $sql = "SELECT id, knowledge FROM sc_tribe_knowledge WHERE id = '$knowledgeId'";
        $result = mysql_query2($sql);
        $row = fetchSqlAssoc($result);
    
        echo '<p>Editing Knowledge: </p>';
        echo '<form action="./index.php?do=tribe_details&amp;sub=knowledge&amp;tribe_id='.$tribeId.'&amp;knowledge_id='.$knowledgeId.'" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>ID</td><td>'.$row['id'].'</td></tr>';
        echo '<tr><td>Knowledge</td><td><input type="text" name="knowledge" value="'.htmlentities($row['knowledge']).'" /></td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Save Changes" /></td></tr>';
        echo '</table>';
        echo '</form>';
        return;
    }
    elseif (isset($_GET['action']) && $_GET['action'] == 'delete')
    {
        // confirm delete
        $knowledgeId = escapeSqlString($_GET['knowledge_id']);
        echo '<p class="error">You are about to delete tribe Knowledge id "'.$knowledgeId.'" </p>';
        echo '<form action="./index.php?do=tribe_details&amp;sub=knowledge&amp;tribe_id='.$tribeId.'&amp;knowledge_id='.$knowledgeId.'" method="post">';
        echo '<div><input type="submit" name="commit" value="Confirm Delete" /></div>';
        echo '</form>';
        return;
    }
    
    // Display the main list
    $sql = "SELECT id, knowledge FROM sc_tribe_knowledge WHERE tribe_id='$tribeId' ORDER BY id";
    $result = mysql_query2($sql);
    
    if (sqlNumRows($result) == 0)
    {
        echo '<p class="error">No Knowledge found for this tribe.</p>';
    }
    else
    {
        // main list
        echo '<table>'."\n";
        echo '<tr><th>ID</th><th>Knowledge</th><th>Actions</th></tr>'."\n";
        
        $alt = false;
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
            echo '<td>'.$row['id'].'</td>';
            echo '<td>'.htmlentities($row['knowledge']).'</td>';
            echo '<td>';
            if (checkAccess('npcs', 'edit'))
            {
                $url = './index.php?do=tribe_details&amp;sub=knowledge&amp;tribe_id='.$tribeId.'&amp;knowledge_id='.$row['id'];
                echo '<a href="'.$url.'&amp;action=edit">Edit</a> - <a href="'.$url.'&amp;action=delete">Delete</a>';
            }
            echo '</td>';
            echo '</tr>'."\n";
        }
        echo '</table>'."\n";
    }
    if (checkAccess('npcs', 'edit'))
    {
        // create form
        echo '<hr/><p>Create new Knowledge: </p>';
        echo '<form action="./index.php?do=tribe_details&amp;sub=knowledge&amp;tribe_id='.$tribeId.'" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Knowledge</td><td><input type="text" name="knowledge" /></td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Create Knowledge" /></td></tr>';
        echo '</table>';
        echo '</form>';
    }
}

function tribeMemories()
{
    // block unauthorized access
    if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) 
    {
        echo '<p class="error">You are not authorized to edit Tribes</p>';
        return;
    }
    // this already got validated in the main method above.
    $tribeId = escapeSqlString($_GET['tribe_id']);  
    
    // after the handling of commit, the script will resume with the listing of all memories for this tribe.
    if (isset($_POST['commit']) && $_POST['commit'] == 'Create Memory')
    {
        $name = escapeSqlString($_POST['name']);
        $loc_x = escapeSqlString($_POST['loc_x']);
        $loc_y = escapeSqlString($_POST['loc_y']);
        $loc_z = escapeSqlString($_POST['loc_z']);
        $sector_id = escapeSqlString($_POST['sector_id']);
        $radius = escapeSqlString($_POST['radius']);
        $sql = "INSERT INTO sc_tribe_memories (tribe_id, name, loc_x, loc_y, loc_z, sector_id, radius) VALUES ('$tribeId', '$name', '$loc_x', '$loc_y', '$loc_z', '$sector_id', '$radius')";
        mysql_query2($sql);
        echo '<p class="error">Memory added.</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Save Changes')
    {
        $memoryId = escapeSqlString($_GET['memory_id']);
        $name = escapeSqlString($_POST['name']);
        $loc_x = escapeSqlString($_POST['loc_x']);
        $loc_y = escapeSqlString($_POST['loc_y']);
        $loc_z = escapeSqlString($_POST['loc_z']);
        $sector_id = escapeSqlString($_POST['sector_id']);
        $radius = escapeSqlString($_POST['radius']);
        $sql = "UPDATE sc_tribe_memories SET name='$name', loc_x='$loc_x', loc_y='$loc_y', loc_z='$loc_z', sector_id='$sector_id', radius='$radius' WHERE id = '$memoryId'";
        mysql_query2($sql);
        echo '<p class="error">Update succesfull</p>';
    }
    elseif (isset($_GET['action']) && $_GET['action'] == 'delete')
    { // this one is a little different, since we do not ask for delete confirmation with memories.
        $memoryId = escapeSqlString($_GET['memory_id']);
        $sql = "DELETE FROM sc_tribe_memories WHERE id='$memoryId'";
        mysql_query2($sql);
        echo '<p class="error">Delete succesfull</p>';
    }
    
    // if we print something for any of these actions, nothing else gets printed (no memories list).
    if (isset($_GET['action']) && $_GET['action'] == 'edit')
    {
        // edit form
        $memoryId = escapeSqlString($_GET['memory_id']);
        $sql = "SELECT id, name, loc_x, loc_y, loc_z, sector_id, radius FROM sc_tribe_memories WHERE id = '$memoryId'";
        $result = mysql_query2($sql);
        $row = fetchSqlAssoc($result);
    
        $sectors = prepselect('sectorid');
        echo '<p>Editing Memory: </p>';
        echo '<form action="./index.php?do=tribe_details&amp;sub=memories&amp;tribe_id='.$tribeId.'&amp;memory_id='.$memoryId.'" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>ID</td><td>'.$row['id'].'</td></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" value="'.htmlentities($row['name']).'" /></td></tr>';
        echo '<tr><td>Loc x</td><td><input type="text" name="loc_x" value="'.htmlentities($row['loc_x']).'" /></td></tr>';
        echo '<tr><td>Loc y</td><td><input type="text" name="loc_y" value="'.htmlentities($row['loc_y']).'" /></td></tr>';
        echo '<tr><td>loc z</td><td><input type="text" name="loc_z" value="'.htmlentities($row['loc_z']).'" /></td></tr>';
        echo '<tr><td>Sector</td><td>'.DrawSelectBox('sectorid', $sectors, 'sector_id', $row['sector_id']).'</td></tr>';
        echo '<tr><td>Radius</td><td><input type="text" name="radius" value="'.htmlentities($row['radius']).'" /></td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Save Changes" /></td></tr>';
        echo '</table>';
        echo '</form>';
        return;
    }
    
    // Display the main list
    $sql = "SELECT tm.id, tm.name, tm.loc_x, tm.loc_y, tm.loc_z, s.name AS sector_name, tm.radius FROM sc_tribe_memories AS tm LEFT JOIN sectors AS s ON s.id = tm.sector_id WHERE tribe_id='$tribeId' ORDER BY tm.name";
    $result = mysql_query2($sql);
    
    if (sqlNumRows($result) == 0)
    {
        echo '<p class="error">No Memories found for this tribe.</p>';
    }
    else
    {
        // main list
        echo '<table>'."\n";
        echo '<tr><th>ID</th><th>Name</th><th>Loc x/y/z</th><th>Sector</th><th>Radius</th><th>Actions</th></tr>'."\n";
        
        $alt = false;
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
            echo '<td>'.$row['id'].'</td>';
            echo '<td>'.htmlentities($row['name']).'</td>';
            echo '<td>'.$row['loc_z'].'/'.$row['loc_y'].'/'.$row['loc_z'].'</td>';
            echo '<td>'.$row['sector_name'].'</td>';
            echo '<td>'.$row['radius'].'</td>';
            echo '<td>';
            if (checkAccess('npcs', 'edit'))
            {
                $url = './index.php?do=tribe_details&amp;sub=memories&amp;tribe_id='.$tribeId.'&amp;memory_id='.$row['id'];
                echo '<a href="'.$url.'&amp;action=edit">Edit</a> - <a href="'.$url.'&amp;action=delete">Delete</a>';
            }
            echo '</td>';
            echo '</tr>'."\n";
        }
        echo '</table>'."\n";
    }
    if (checkAccess('npcs', 'edit'))
    {
        // create form
        $sectors = prepselect('sectorid');
        echo '<hr/><p>Create new Memory: </p>';
        echo '<form action="./index.php?do=tribe_details&amp;sub=memories&amp;tribe_id='.$tribeId.'" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" /></td></tr>';
        echo '<tr><td>Loc x</td><td><input type="text" name="loc_x" /></td></tr>';
        echo '<tr><td>Loc y</td><td><input type="text" name="loc_y" /></td></tr>';
        echo '<tr><td>loc z</td><td><input type="text" name="loc_z" /></td></tr>';
        echo '<tr><td>Sector</td><td>'.DrawSelectBox('sectorid', $sectors, 'sector_id', '').'</td></tr>';
        echo '<tr><td>Radius</td><td><input type="text" name="radius" /></td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Create Memory" /></td></tr>';
        echo '</table>';
        echo '</form>';
    }
}

?>
