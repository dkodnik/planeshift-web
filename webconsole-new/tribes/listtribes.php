<?php

function listtribes()
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    $query = 'SELECT t.*, tr.name AS recipe_name, s.name AS sector FROM tribes AS t LEFT JOIN sectors AS s ON t.home_sector_id=s.id LEFT JOIN tribe_recipes AS tr ON tr.id=t.tribal_recipe';

    if (isset($_GET['sort']))
    {
        if ($_GET['sort'] == 'id')
        {
            $query .= ' ORDER BY id';
        }
        else if ($_GET['sort'] == 'name')
        {
            $query .= ' ORDER BY name';
        }
    }
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0)
    {
        echo '<table border="1">';
        echo '<tr><th><a href="./index.php?do=listtribes&amp;sort=id">ID</a></th><th><a href="./index.php?do=listtribes&amp;sort=name">Name</a></th><th>Home Position</th><th>Home Radius</th><th><a href="./index.php?do=listtribes&amp;sort=sector">Home Sector</a></th><th>Max size</th><th>Wealth Resource Name</th><th>Wealth Resource Nick</th><th>Wealth Resource Area</th><th>Wealth Gather Need</th><th>Wealth Resource Growth</th><th>Wealth Resource Growth Active</th><th>Wealth Resource Growth Active Limit</th><th>Reproduction Cost</th><th>NPC idle behavior</th><th>Tribal Recipe</th></tr>';

        while ($row = mysql_fetch_array($result))
        {
            echo '<tr>';
            echo '<td>'.$row['id'].'</td>';
            echo '<td><a href="./index.php?do=tribe_details&amp;sub=main&amp;tribe_id='.$row['id'].'">'.$row['name'].'</a></td>';
            echo '<td>'.$row['home_x'].' / '.$row['home_y'].' / '.$row['home_z'].'</td>';
            echo '<td>'.$row['home_radius'].'</td>';
            echo '<td>'.$row['sector'].'</td>';
            echo '<td>'.$row['max_size'].'</td>';
            echo '<td>'.$row['wealth_resource_name'].'</td>';
            echo '<td>'.$row['wealth_resource_nick'].'</td>';
            echo '<td>'.$row['wealth_resource_area'].'</td>';
            echo '<td>'.$row['wealth_gather_need'].'</td>';
            echo '<td>'.$row['wealth_resource_growth'].'</td>';
            echo '<td>'.$row['wealth_resource_growth_active'].'</td>';
            echo '<td>'.$row['wealth_resource_growth_active_limit'].'</td>';
            echo '<td>'.$row['reproduction_cost'].'</td>';
            echo '<td>'.$row['npc_idle_behavior'].'</td>';
            echo '<td><a href="./index.php?do=listrecipes&amp;id='.$row['tribal_recipe'].'">'.$row['recipe_name'].'</a></td>';
            echo '</tr>';
        }
        echo '</table>';
        if (checkaccess('npcs', 'create')) 
        {
            echo '<br /><hr />';
            echo '<p>Create New Tribe: </p>';
            echo '<form action="./index.php?do=edittribes" method="post">';
            echo '<table border="1">';
            echo '<tr><th>Field</th><th>Value</th></tr>';
            echo '<tr><td>Name</td><td><input type="text" name="name" /></td></tr>';
            $sectors = PrepSelect('sectorid');
            echo '<tr><td>Home Sector</td><td>'.DrawSelectBox('sectorid', $sectors, 'home_sector_id', '').'</td></tr>';
            echo '<tr><td>Home X</td><td><input type="text" name="home_x" /></td></tr>';
            echo '<tr><td>Home Y</td><td><input type="text" name="home_y" /></td></tr>';
            echo '<tr><td>Home Z</td><td><input type="text" name="home_z" /></td></tr>';
            echo '<tr><td>Home Radius</td><td><input type="text" name="home_radius" /></td></tr>';
            echo '<tr><td>Max Size</td><td><input type="text" name="max_size" /></td></tr>';
            echo '<tr><td>Resource Name</td><td><input type="text" name="wealth_resource_name" /></td></tr>';
            echo '<tr><td>Resource Nick</td><td><input type="text" name="wealth_resource_nick" /></td></tr>';
            echo '<tr><td>Resource Area</td><td><input type="text" name="wealth_resource_area" /></td></tr>';
            echo '<tr><td>Gather Need</td><td><input type="text" name="wealth_gather_need" /></td></tr>';
            echo '<tr><td>Resource Growth</td><td><input type="text" name="wealth_resource_growth" /></td></tr>';
            echo '<tr><td>Resource Growth Active</td><td><input type="text" name="wealth_resource_growth_active" /></td></tr>';
            echo '<tr><td>Resource Growth Active Limit</td><td><input type="text" name="wealth_resource_growth_active_limit" /></td></tr>';
            echo '<tr><td>Reproduction Cost</td><td><input type="text" name="reproduction_cost" /></td></tr>';
            echo '<tr><td>NPC Idle Behavior</td><td><input type="text" name="npc_idle_behavior" /></td></tr>';
            $tribe_recipe = PrepSelect('tribe_recipe');
            echo '<tr><td>Tribal Recipe</td><td>'.DrawSelectBox('tribe_recipe', $tribe_recipe, 'tribal_recipe', '').'</td></tr>';
            echo '<tr><td colspan="2"><input type="submit" name="commit" value="Create Tribe" /></td></tr>';
            echo '</table>';
            echo '</form>';
        }
    }
    else
    {
        echo '<p class="error">No NPCs Found</p>';
    }
}

function edittribes()
{
    if (!checkaccess('npcs', 'edit'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    if (!isset($_POST['commit'])) 
    {
        echo '<p class="error">Invalid Instruction</p>';
        return;
    }
    
    if ($_POST['commit'] == 'Create Tribe')
    {
        if (!checkaccess('npcs', 'create'))
        {
            echo '<p class="error">You are not authorized to use these functions</p>';
            return;
        }
        $name = mysql_real_escape_string($_POST['name']);
        $home_sector_id = mysql_real_escape_string($_POST['home_sector_id']);
        $home_x = mysql_real_escape_string($_POST['home_x']);
        $home_y = mysql_real_escape_string($_POST['home_y']);
        $home_z = mysql_real_escape_string($_POST['home_z']);
        $home_radius = mysql_real_escape_string($_POST['home_radius']);
        $max_size = mysql_real_escape_string($_POST['max_size']);
        $wealth_resource_name = mysql_real_escape_string($_POST['wealth_resource_name']);
        $wealth_resource_nick = mysql_real_escape_string($_POST['wealth_resource_nick']);
        $wealth_resource_area = mysql_real_escape_string($_POST['wealth_resource_area']);
        $wealth_gather_need = mysql_real_escape_string($_POST['wealth_gather_need']);
        $wealth_resource_growth = mysql_real_escape_string($_POST['wealth_resource_growth']);
        $wealth_resource_growth_active = mysql_real_escape_string($_POST['wealth_resource_growth_active']);
        $wealth_resource_growth_active_limit = mysql_real_escape_string($_POST['wealth_resource_growth_active_limit']);
        $reproduction_cost = mysql_real_escape_string($_POST['reproduction_cost']);
        $npc_idle_behavior = mysql_real_escape_string($_POST['npc_idle_behavior']);
        $tribal_recipe = mysql_real_escape_string($_POST['tribal_recipe']);
        $query = "INSERT INTO tribes SET name='$name', home_sector_id='$home_sector_id', home_x='$home_x', home_y='$home_y', home_z='$home_z', home_radius='$home_radius', max_size='$max_size', wealth_resource_name='$wealth_resource_name', wealth_resource_nick='$wealth_resource_nick', wealth_resource_area='$wealth_resource_area', wealth_gather_need='$wealth_gather_need', wealth_resource_growth='$wealth_resource_growth', wealth_resource_growth_active='$wealth_resource_growth_active', wealth_resource_growth_active_limit='$wealth_resource_growth_active_limit', reproduction_cost='$reproduction_cost', npc_idle_behavior='$npc_idle_behavior', tribal_recipe='$tribal_recipe'";
        mysql_query2($query);
        echo '<p class="error">Tribe Successfully Created</p>';
        unset($_POST);
        listtribes();
        return;
    }
    else if ($_POST['commit'] == 'Delete')
    {
        if (!checkaccess('npcs', 'delete'))
        {
            echo '<p class="error">You are not authorized to use these functions</p>';
            return;
        }
        if (!isset($_POST['id']) || !is_numeric($_POST['id']))
        {
            echo '<p class="error">Invalid ID, cannot delete.</p>';
            return;
        }
        $id = mysql_real_escape_string($_POST['id']);
        $query = "SELECT name FROM tribes WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result);
        echo '<p>You are about to delete the tribe named '.$row['name'].' id('.$id.') and *all* associated assets, knowledge, memories, resources and members.<br />';
        echo 'This action is permanent and can not be undone.</p>';
        echo '<form action="./index.php?do=edittribes" method="post">';
        echo '<p><input type="hidden" name="id" value="'.$id.'" /><input type="submit" name="commit" value="Confirm Delete" /></p>';
        echo '</form>';
    }
    else if ($_POST['commit'] == 'Confirm Delete')
    {
        if (!checkaccess('npcs', 'delete'))
        {
            echo '<p class="error">You are not authorized to use these functions</p>';
            return;
        }
        if (!isset($_POST['id']) || !is_numeric($_POST['id']))
        {
            echo '<p class="error">Invalid ID, cannot delete.</p>';
            return;
        }
        
        $id = mysql_real_escape_string($_POST['id']);
        $query = "DELETE FROM sc_tribe_assets WHERE tribe_id='$id'";
        mysql_query2($query);
        $query = "DELETE FROM sc_tribe_knowledge WHERE tribe_id='$id'";
        mysql_query2($query);
        $query = "DELETE FROM sc_tribe_memories WHERE tribe_id='$id'";
        mysql_query2($query);
        $query = "DELETE FROM sc_tribe_resources WHERE tribe_id='$id'";
        mysql_query2($query);
        $query = "DELETE FROM tribe_members WHERE tribe_id='$id'";
        mysql_query2($query);
        
        $query = "DELETE FROM tribes WHERE id='$id' LIMIT 1";
        mysql_query2($query);
        echo '<p class="error">Tribe With ID '.$id.' And All Associated Data Successfully Deleted</p>';
        unset($_POST);
        listtribes();
        return;
    }
}

?>
