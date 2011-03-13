<?php
function listprocess()
{
    if (checkaccess('crafting', 'read'))
    {
        /*
        Notice: skill '0' is sword skill, however, this is the only table that does that, I don't believe this is "good", but for now
        there is not too much that can be done about it. -1 is "none".
        */
        $query = "SELECT t.process_id, t.subprocess_number, t.name, t.animation, t.render_effect, t.workitem_id, i.name AS workitem_name, i.category_id AS work_cat_id, t.equipment_id, ii.name AS equipment_name, ii.category_id AS equipment_cat_id, t.constraints, t.garbage_id, iii.name AS garbage_name, iii.category_id AS garbage_cat_id, t.garbage_qty, t.primary_skill_id, s.name AS primary_skill_name, t.primary_min_skill, t.primary_max_skill, t.primary_practice_points, t.primary_quality_factor, t.secondary_skill_id, ss.name AS secondary_skill_name, t.secondary_min_skill, t.secondary_max_skill, t.secondary_practice_points, t.secondary_quality_factor, t.description FROM trade_processes as t LEFT JOIN skills AS s ON t.primary_skill_id=s.skill_id LEFT JOIN skills AS ss ON t.secondary_skill_id=ss.skill_id LEFT JOIN item_stats AS i ON i.id=t.workitem_id LEFT JOIN item_stats AS ii ON ii.id=t.equipment_id LEFT JOIN item_stats AS iii ON iii.id=t.garbage_id";
        if(isset($_GET['sort']) && $_GET['sort'] == 'name')
        {
            $query .= ' ORDER BY t.name ASC';
        }
        else 
        {
            $query .= ' ORDER BY s.name, t.primary_min_skill, ss.name, t.secondary_min_skill, t.name, t.process_id, t.subprocess_number';
        }
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $id = $row['process_id'];
        mysql_data_seek($result, 0);
        echo '<table><tr><th><a href="./index.php?do=listprocess&amp;sort=name">Name</a></th><th>Sub-<br>Process</th><th>Animation</th><th>Work Item</th><th>Equipment Used</th><th>Constraints</th><th colspan="2">Garbage Item</th><th>Primary Skill / Min / Max / Practice / Quality</th><th>Secondary Skill / Min / Max / Practice / Quality</th><th>Description</th>';
        if (checkaccess('crafting', 'edit')){
            echo '<th>Actions</th>';
        }
        echo '</tr>';
        $alt= FALSE;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            $alt = !$alt;
            if ($alt)
            {
                echo '<tr class="color_a">';
            }
            else
            {
                echo '<tr class="color_b">';
            }
            echo '<td><a href="./index.php?do=process&id='.$row['process_id'].'">'.$row['name'].'</a></td>';
            echo '<td>'.$row['subprocess_number'].'</td>';
            echo '<td>'.$row['animation'].'</td>';
            echo '<td><a href="./index.php?do=listitems&override1&category='.$row['work_cat_id'].'&item='.$row['workitem_id'].'">'.$row['workitem_name'].'</a></td>';
            echo '<td><a href="./index.php?do=listitems&override1&category='.$row['equipment_cat_id'].'&item='.$row['equipment_id'].'">'.$row['equipment_name'].'</a></td>';
            echo '<td>'.$row['constraints'].'</td>';
            echo '<td>'.$row['garbage_qty'].' </td><td> <a href="./index.php?do=listitems&override1&category='.$row['garbage_cat_id'].'&item='.$row['garbage_id'].'">'.$row['garbage_name'].'</a></td>';
            echo '<td>'.$row['primary_skill_name'].' / '.$row['primary_min_skill'].' / '.$row['primary_max_skill'].' / '.$row['primary_practice_points'].' / '.$row['primary_quality_factor'].'</td>';
            echo '<td>'.$row['secondary_skill_name'].' / '.$row['secondary_min_skill'].' / '.$row['secondary_max_skill'].' / '.$row['secondary_practice_points'].' / '.$row['secondary_quality_factor'].'</td>';
            echo '<td>'.$row['description'].'</td>';
            if (checkaccess('crafting','edit'))
            {
                echo '<td><a href="./index.php?do=editsubprocess&amp;id='.$row['process_id'].'&amp;sub='.$row['subprocess_number'].'">Edit</a></td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        if (checkaccess('crafting','create')){
            echo '<a href="./index.php?do=createprocess">Create new Process</a>';
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function editprocess(){
  if (checkaccess('crafting', 'read')){
    $id = mysql_real_escape_string($_GET['id']);
    echo '<p class="header">Process Information';
    $result = mysql_query2("SELECT id, name FROM item_stats");
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $i = $row['id'];
      $Items["$i"] = $row['name'];
    }
    $Items[0] = "";
    $result = mysql_query2("SELECT skill_id, name FROM skills");
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $i = $row['skill_id'];
      $Skills["$i"] = $row['name'];
    }
    $Skills[0] = "";
            $query = "SELECT t.process_id, t.subprocess_number, t.name, t.animation, t.render_effect, t.workitem_id, i.name AS workitem_name, i.category_id AS work_cat_id, t.equipment_id, ii.name AS equipment_name, ii.category_id AS equipment_cat_id, t.constraints, t.garbage_id, iii.name AS garbage_name, iii.category_id AS garbage_cat_id, t.garbage_qty, t.primary_skill_id, s.name AS primary_skill_name, t.primary_min_skill, t.primary_max_skill, t.primary_practice_points, t.primary_quality_factor, t.secondary_skill_id, ss.name AS secondary_skill_name, t.secondary_min_skill, t.secondary_max_skill, t.secondary_practice_points, t.secondary_quality_factor, t.description FROM trade_processes as t LEFT JOIN skills AS s ON t.primary_skill_id=s.skill_id LEFT JOIN skills AS ss ON t.secondary_skill_id=ss.skill_id LEFT JOIN item_stats AS i ON i.id=t.workitem_id LEFT JOIN item_stats AS ii ON ii.id=t.equipment_id LEFT JOIN item_stats AS iii ON iii.id=t.garbage_id WHERE process_id = '$id' ORDER BY s.name, t.primary_min_skill, ss.name, secondary_min_skill, t.name";
    $result = mysql_query2($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo '- '.$row['name'].'</p>';
    mysql_data_seek($result, 0);
    echo '<table><tr><th>Sub-Process</th><th>Animation</th><th>Work Item</th><th>Equipment Used</th><th>Constraints</th><th colspan="2">Garbage Item</th><th>Primary Skill / Min / Max / Practice / Quality</th><th>Secondary Skill / Min / Max / Practice / Quality</th><th>Description</th>';
    if (checkaccess('crafting', 'edit')){
      echo '<th>Actions</th>';
    }
    echo '</tr>';
    $Alt= FALSE;
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $Alt = !$Alt;
      if ($Alt){
        echo '<tr class="color_a">';
      }else{
        echo '<tr class="color_b">';
      }
      echo '<td>'.$row['subprocess_number'].'</td>';
      echo '<td>'.$row['animation'].'</td>';
      echo '<td><a href="./index.php?do=listitems&override1&category='.$row['work_cat_id'].'&item='.$row['workitem_id'].'">'.$row['workitem_name'].'</a></td>';
      echo '<td><a href="./index.php?do=listitems&override1&category='.$row['equipment_cat_id'].'&item='.$row['equipment_id'].'">'.$row['equipment_name'].'</a></td>';
      echo '<td>'.$row['constraints'].'</td>';
      echo '<td>'.$row['garbage_qty'].' </td><td> <a href="./index.php?do=listitems&override1&category='.$row['garbage_cat_id'].'&item='.$row['garbage_id'].'">'.$row['garbage_name'].'</a></td>';
      echo '<td>'.$row['primary_skill_name'].' / '.$row['primary_min_skill'].' / '.$row['primary_max_skill'].' / '.$row['primary_practice_points'].' / '.$row['primary_quality_factor'].'</td>';
      echo '<td>'.$row['secondary_skill_name'].' / '.$row['secondary_min_skill'].' / '.$row['secondary_max_skill'].' / '.$row['secondary_practice_points'].' / '.$row['secondary_quality_factor'].'</td>';
      echo '<td>'.$row['description'].'</td>';
      if (checkaccess('crafting','edit')){
        echo '<td><a href="./index.php?do=editsubprocess&amp;id='.$id.'&amp;sub='.$row['subprocess_number'].'">Edit</a></td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    if (checkaccess('crafting','create')){
        echo '<a href="./index.php?do=createprocess&amp;id='.$id.'">Create new Sub-process</a>';
    }
    $query = "SELECT t.id, t.process_id, t.pattern_id, pat.pattern_name, p.name, t.result_id, i.name AS result_name, c.name AS result_cat, c.category_id AS result_cat_id, t.result_qty, t.item_id, ii.name AS item_name, cc.name AS item_cat, cc.category_id AS item_cat_id, t.item_qty, t.trans_points, t.penalty_pct, t.description FROM trade_transformations AS t LEFT JOIN item_stats AS i ON i.id=t.result_id LEFT JOIN item_stats AS ii ON ii.id=t.item_id LEFT JOIN trade_processes AS p ON t.process_id=p.process_id LEFT JOIN item_categories AS c ON i.category_id=c.category_id LEFT JOIN item_categories AS cc ON ii.category_id=cc.category_id LEFT JOIN trade_patterns AS pat ON t.pattern_id=pat.id WHERE t.process_id='$id' ORDER BY pattern_id";
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0)  
    {
        echo '<p>Transformations using this process:</p>';
        echo '<table><tr><th>Pattern</th><th colspan="2">Source Item</th><th>Category</th><th>Process</th><th colspan="2">Result Item</th><th>Category</th><th>Time</th><th>Result Q</th><th>Actions</th></tr>';
        $alt = false;
        while ($row=mysql_fetch_array($result, MYSQL_ASSOC)){
            $alt = !$alt;
            if ($alt)
            {
            echo '<tr class="color_a">';
            }
            else
            {
              echo '<tr class="color_b">';
            }
            $pattern_name = ($row['pattern_id'] != 0 ? $row['pattern_name'] : "patternless");
            echo '<td><a href="./index.php?do=editpattern&id='.$row['pattern_id'].'">'.$pattern_name.'</a></td>';
            $item_name = ($row['item_name'] == "NULL" ? ($row['item_id'] != 0 ? "BROKEN" : "") :$row['item_name']); // Item name is broken if NULL was returned and ID is not 0, if ID was 0, name is "", else name the name found in the database.
            if (checkaccess('items','edit'))
            {
                echo '<td>'.$row['item_qty'].' </td><td> <a href="./index.php?do=listitems&override1&category='.$row['item_cat_id'].'&item='.$row['item_id'].'">'.$item_name.'</a> </td>';
            }
            else
            {
                echo '<td>'.$row['item_qty'].' </td><td> '.$item_name.' </td>';
            }
            echo '<td>'.$row['item_cat'].'</td>';
            echo '<td><a href="./index.php?do=process&amp;id='.$row['process_id'].'">'.$row['name'].'</a></td>';
            $result_name = ($row['result_name'] == "NULL" ? ($row['result_id'] != 0 ? "BROKEN" : "") :$row['result_name']); // Item name is broken if NULL was returned and ID is not 0, if ID was 0, name is "", else name the name found in the database.
            if (checkaccess('items','edit'))
            {
                echo '<td>'.$row['result_qty'].' </td><td> <a href="./index.php?do=listitems&override1&category='.$row['result_cat_id'].'&item='.$row['result_id'].'">'.$result_name.'</a> </td>';
            }
            else
            {
                echo '<td>'.$row['result_qty'].' </td><td> '.$result_name.'</td>';
            }
            echo '<td>'.$row['result_cat'].'</td>';
            echo '<td>'.$row['trans_points'].'</td>';
            echo '<td>'.$row['penalty_pct'].'</td>';
            echo '<td><a href="./index.php?do=transform&amp;id='.$row['id'].'">Edit</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    else
    {
        echo '<p>No transforms use this process.</p>';
    }
  }
  else
  {
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function editsubprocess()
{
  if (checkaccess('crafting','edit'))
  {
    if (isset($_POST['commit']) && ($_POST['commit'] == "Update Process"))
    {
        $process_id = mysql_real_escape_string($_GET['id']);
        $subprocess_number = mysql_real_escape_string($_GET['sub']);
        $name = mysql_real_escape_string($_POST['name']);
        $animation = mysql_real_escape_string($_POST['animation']);
        $workitem_id = mysql_real_escape_string($_POST['workitem_id']);
        $workitem_id = ($workitem_id == '' ? 0 : $workitem_id); // change id to 0 if it is not provided by user ('')
        $equipment_id = mysql_real_escape_string($_POST['equipment_id']);
        $equipment_id = ($equipment_id == '' ? 0 : $equipment_id);
        $garbage_id = mysql_real_escape_string($_POST['garbage_id']);
        $garbage_id = ($garbage_id == '' ? 0 : $garbage_id);
        $garbage_qty = mysql_real_escape_string($_POST['garbage_qty']);
        $primary_skill_id = mysql_real_escape_string($_POST['primary_skill_id']);
        $primary_min_skill = mysql_real_escape_string($_POST['primary_min_skill']);
        $primary_max_skill = mysql_real_escape_string($_POST['primary_max_skill']);
        $primary_practice_points = mysql_real_escape_string($_POST['primary_practice_points']);
        $primary_quality_factor = mysql_real_escape_string($_POST['primary_quality_factor']);
        $secondary_skill_id = mysql_real_escape_string($_POST['secondary_skill_id']);
        $secondary_min_skill = mysql_real_escape_string($_POST['secondary_min_skill']);
        $secondary_max_skill = mysql_real_escape_string($_POST['secondary_max_skill']);
        $secondary_practice_points = mysql_real_escape_string($_POST['secondary_practice_points']);
        $secondary_quality_factor = mysql_real_escape_string($_POST['secondary_quality_factor']);
        $description = mysql_real_escape_string($_POST['description']);
        $query = "UPDATE trade_processes SET name='$name', animation='$animation', workitem_id='$workitem_id', equipment_id='$equipment_id', garbage_id='$garbage_id', garbage_qty='$garbage_qty', primary_skill_id='$primary_skill_id', primary_min_skill='$primary_min_skill', primary_max_skill='$primary_max_skill', primary_practice_points='$primary_practice_points', primary_quality_factor='$primary_quality_factor', secondary_skill_id='$secondary_skill_id', secondary_min_skill='$secondary_min_skill', secondary_max_skill='$secondary_max_skill', secondary_practice_points='$secondary_practice_points', secondary_quality_factor='$secondary_quality_factor', description='$description' WHERE process_id='$process_id' AND subprocess_number='$subprocess_number'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        editsubprocess();
    }
    else
    {
      $id = mysql_real_escape_string($_GET['id']);
      $sub = mysql_real_escape_string($_GET['sub']);
      $delete = (checkaccess('crafting','delete') ? '<a href="./index.php?do=deleteprocess&id='.$id.'&sub='.$sub.'">Delete</a>' : '');
      $query = "SELECT * FROM trade_processes WHERE process_id = '$id' AND subprocess_number='$sub'";
      $result = mysql_query2($query);
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      echo '<p class="header">Edit Sub-Proccess</p>';
      echo '<form action="./index.php?do=editsubprocess&amp;id='.$id.'&amp;sub='.$sub.'" method="post">';
      echo '<table><tr><th>Field</th><th>Value</th></tr>';
      echo '<tr><td>Process ID:</td><td>'.$row['process_id'].'</tr>';
      echo '<tr><td>SubProcess ID:</td><td>'.$row['subprocess_number'].'</td></tr>';
      echo '<tr><td>Name:</td><td><input type="hidden" name="name" value="'.$row['name'].'">'.$row['name'].'</td></tr>';
      echo '<tr><td>Description:</td><td><input type="text" name="description" value="'.$row['description'].'" /></td></tr>';
      echo '<tr><td>Animation:</td><td><input type="text" name="animation" value="'.$row['animation'].'" /></td></tr>';
      echo '<tr><td>Work Item:</td><td>'.DrawItemSelectBox('workitem_id', $row['workitem_id'], false).'</td></tr>';
      echo '<tr><td>Equipment:</td><td>'.DrawItemSelectBox('equipment_id', $row['equipment_id'], false).'</td></tr>';
      echo '<tr><td>Garbage Item:</td><td>'.DrawItemSelectBox('garbage_id', $row['garbage_id']).'</td></tr>';
      echo '<tr><td>Garbage Quantity:</td><td><input type="text" name="garbage_qty" value="'.$row['garbage_qty'].'" /></td></tr>';
      $Skills = PrepSelect('skill');
      echo '<tr><td>Primary Skill:</td><td>'.DrawSelectBox('skill', $Skills, 'primary_skill_id', $row['primary_skill_id'], true).'</td></tr>';
      echo '<tr><td>Primary Minimum Skill Level:</td><td><input type="text" name="primary_min_skill" value="'.$row['primary_min_skill'].'"/></td></tr>';
      echo '<tr><td>Primary Maximum Skill Level:</td><td><input type="text" name="primary_max_skill" value="'.$row['primary_max_skill'].'"/></td></tr>';
      echo '<tr><td>Primary Practice Points:</td><td><input type="text" name="primary_practice_points" value="'.$row['primary_practice_points'].'"/></td></tr>';
      echo '<tr><td>Primary Quality Factor:</td><td><input type="text" name="primary_quality_factor" value="'.$row['primary_quality_factor'].'"/></td></tr>';
      echo '<tr><td>Secondary Skill:</td><td>'.DrawSelectBox('skill', $Skills, 'secondary_skill_id', $row['secondary_skill_id'], true).'</td></tr>';
      echo '<tr><td>Secondary Minimum Skill Level:</td><td><input type="text" name="secondary_min_skill" value="'.$row['secondary_min_skill'].'"/></td></tr>';
      echo '<tr><td>Secondary Maximum Skill Level:</td><td><input type="text" name="secondary_max_skill" value="'.$row['secondary_max_skill'].'"/></td></tr>';
      echo '<tr><td>Secondary Practice Points:</td><td><input type="text" name="secondary_practice_points" value="'.$row['secondary_practice_points'].'"/></td></tr>';
      echo '<tr><td>Secondary Quality Factor:</td><td><input type="text" name="secondary_quality_factor" value="'.$row['secondary_quality_factor'].'"/></td></tr>';
      echo '<tr><td>'.$delete.'</td><td><input type=submit name="commit" value="Update Process"/></td></tr>';
      echo '</table></form>';
    }
  }
  else
  {
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function createprocess()
{
    if (checkaccess('crafting','create') && isset($_POST['commit']) && $_POST['commit'] == "Create Process")
    {
        $name = mysql_real_escape_string($_POST['name']);
        $animation = mysql_real_escape_string($_POST['animation']);
        $workitem_id = mysql_real_escape_string($_POST['workitem_id']);
        $workitem_id = ($workitem_id == '' ? 0 : $workitem_id); // change id to 0 if it is not provided by user ('')
        $equipment_id = mysql_real_escape_string($_POST['equipment_id']);
        $equipment_id = ($equipment_id == '' ? 0 : $equipment_id);
        $constraints = mysql_real_escape_string($_POST['constraints']);
        $garbage_id = mysql_real_escape_string($_POST['garbage_id']);
        $garbage_id = ($garbage_id == '' ? 0 : $garbage_id);
        $garbage_qty = mysql_real_escape_string($_POST['garbage_qty']);
        $primary_skill_id = mysql_real_escape_string($_POST['primary_skill_id']);
        $primary_min_skill = mysql_real_escape_string($_POST['primary_min_skill']);
        $primary_max_skill = mysql_real_escape_string($_POST['primary_max_skill']);
        $primary_practice_points = mysql_real_escape_string($_POST['primary_practice_points']);
        $primary_quality_factor = mysql_real_escape_string($_POST['primary_quality_factor']);
        $secondary_skill_id = mysql_real_escape_string($_POST['secondary_skill_id']);
        $secondary_min_skill = mysql_real_escape_string($_POST['secondary_min_skill']);
        $secondary_max_skill = mysql_real_escape_string($_POST['secondary_max_skill']);
        $secondary_practice_points = mysql_real_escape_string($_POST['secondary_practice_points']);
        $secondary_quality_factor = mysql_real_escape_string($_POST['secondary_quality_factor']);
        $description = mysql_real_escape_string($_POST['description']);
        if (isset($_POST['process_id'])) // we are adding a sub-process, determine the highest number and make this 1 above that.
        {
            $process_id = mysql_real_escape_string($_POST['process_id']);
            $query = "SELECT MAX(subprocess_number) FROM trade_processes WHERE process_id='$process_id'";
            $result = mysql_query2($query);
            $row = mysql_fetch_row($result);
            $subprocess_number = $row[0]+1;
        }
        else // we are adding a process, determine the highest number and add 1, set sub to 0.
        {
            $query = "SELECT MAX(process_id) FROM trade_processes";
            $result = mysql_query2($query);
            $row = mysql_fetch_row($result);
            $process_id = $row[0]+1;
            $subprocess_number = 0;
        }
        $query = "INSERT INTO trade_processes (process_id, subprocess_number, name, animation, workitem_id, equipment_id, constraints, garbage_id, garbage_qty, primary_skill_id, primary_min_skill, primary_max_skill, primary_practice_points, primary_quality_factor, secondary_skill_id, secondary_min_skill, secondary_max_skill, secondary_practice_points, secondary_quality_factor, description) VALUES ('$process_id', '$subprocess_number', '$name', '$animation', '$workitem_id', '$equipment_id', '$constraints', '$garbage_id', '$garbage_qty', '$primary_skill_id', '$primary_min_skill', '$primary_max_skill', '$primary_practice_points', '$primary_quality_factor', '$secondary_skill_id', '$secondary_min_skill', '$secondary_max_skill', '$secondary_practice_points', '$secondary_quality_factor', '$description')";
        mysql_query2($query);
        echo '<p class="error">Process added succesfully.</p>';
        unset($_POST);
        createprocess();
    }
    elseif (checkaccess('crafting','create'))
    {
        $id = (isset($_GET['id']) ? $_GET['id'] : "");
        
        echo '<p class="header">Create Process</p>';
        echo '<form action="./index.php?do=createprocess" method="post"><table>';
        if ($id != "") // if ID was supplied, we are making a sub-process.
        {
            $query = "SELECT name FROM trade_processes WHERE process_id='".mysql_real_escape_string($id)."'";
            $result = mysql_query2($query);
            if (mysql_num_rows($result) < 1)
            {
                echo '<p class="error">Invalid process_id ('.$id.') supplied, could not create sub-process.</p>';
                return;
            }
            $row = mysql_fetch_array($result);
            $name = $row['name'];
            echo '<tr><td>Process ID:</td><td><input type="hidden" name="process_id" value="'.$id.'" />'.$id.'</td></tr>';
            echo '<tr><td>Process Name:</td><td><input type="hidden" name="name" value="'.$name.'" />'.$name.'</td></tr>';
        }
        else
        {
            echo '<tr><td>Process Name:</td><td><input type="text" name="name" /></td></tr>';
        }
        echo '<tr><td>Description:</td><td><input type="text" name="description" /></td></tr>';
        echo '<tr><td>Animation:</td><td><input type="text" name="animation" /></td></tr>';
        $items = PrepSelect('items');
        echo '<tr><td>Work Item:</td><td>'.DrawSelectBox('items', $items, 'workitem_id', '', true).'</td></tr>';
        echo '<tr><td>Equipment:</td><td>'.DrawSelectBox('items', $items, 'equipment_id', '', true).'</td></tr>';
        echo '<tr><td>Constraints:</td><td><input type="text" name="constraints" /></td></tr>';
        echo '<tr><td>Garbage Item:</td><td>'.DrawSelectBox('items', $items, 'garbage_id', '', true).'</td></tr>';
        echo '<tr><td>Garbage Quantity:</td><td><input type="text" name="garbage_qty" value="0"/></td></tr>';
        $skills = PrepSelect('skill');
        echo '<tr><td>Primary Skill:</td><td>'.DrawSelectBox('skill', $skills, 'primary_skill_id', '', true).'</td></tr>';
        echo '<tr><td>Primary Minimum Skill Level:</td><td><input type="text" name="primary_min_skill" value="0"/></td></tr>';
        echo '<tr><td>Primary Maximum Skill Level:</td><td><input type="text" name="primary_max_skill" value="0"/></td></tr>';
        echo '<tr><td>Primary Practice Points:</td><td><input type="text" name="primary_practice_points" value="0"/></td></tr>';
        echo '<tr><td>Primary Quality Factor:</td><td><input type="text" name="primary_quality_factor" value="0"/></td></tr>';
        echo '<tr><td>Secondary Skill:</td><td>'.DrawSelectBox('skill', $skills, 'secondary_skill_id', '', true).'</td></tr>';
        echo '<tr><td>Secondary Minimum Skill Level:</td><td><input type="text" name="secondary_min_skill" value="0"/></td></tr>';
        echo '<tr><td>Secondary Maximum Skill Level:</td><td><input type="text" name="secondary_max_skill" value="0"/></td></tr>';
        echo '<tr><td>Secondary Practice Points:</td><td><input type="text" name="secondary_practice_points" value="0"/></td></tr>';
        echo '<tr><td>Secondary Quality Factor:</td><td><input type="text" name="secondary_quality_factor" value="0"/></td></tr>';
        echo '<tr><td></td><td><input type=submit name="commit" value="Create Process"/></td></tr>';
        echo '</table></form>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function deleteprocess()
{
    if (checkaccess('crafting','delete') && isset($_POST['submit']))
    {
        $process_id = mysql_real_escape_string($_GET['id']);
        $subprocess_number = mysql_real_escape_string($_GET['sub']);
        if (!is_numeric($process_id) || !is_numeric($subprocess_number))
        {
            echo '<p class="error">Invalid (sub)process ID.</p>';
            return;
        }
        
        $password = mysql_real_escape_string($_POST['passd']);
        $username = mysql_real_escape_string($_SESSION['username']);
        $query = "SELECT COUNT(username) FROM accounts WHERE username='$username' AND password=MD5('$password')";
        $result = mysql_query2($query);
        $row = mysql_fetch_row($result);
        if ($row[0] == 1)
        {
            if ($subprocess_number == 0)
            {
                $query = "DELETE FROM trade_processes WHERE process_id='$process_id'";
                mysql_query2($query);
                echo '<p class="error">Process with ID '.$process_id.' was succesfully deleted.</p>';
            }
            else 
            {
                $query = "DELETE FROM trade_processes WHERE process_id='$process_id' AND subprocess_number='$subprocess_number' LIMIT 1";
                mysql_query2($query);
                echo '<p class="error">Process with ID '.$process_id.' and subprocess ID '.$subprocess_number.' was succesfully deleted.</p>';
            }
            unset($_POST);
            listprocess();
        }
        else
        {
            echo '<p class="error">Password check failed - Did Not Delete Process</p>';
        }
    }
    elseif (checkaccess('crafting','delete'))
    {
        $process_id = mysql_real_escape_string($_GET['id']);
        $subprocess_number = mysql_real_escape_string($_GET['sub']);
        if (!is_numeric($process_id) || !is_numeric($subprocess_number))
        {
            echo '<p class="error">Invalid (sub)process ID.</p>';
            return;
        }
        $query = "SELECT name FROM trade_processes WHERE process_id='$process_id'";
        $result = mysql_query2($query);
        if (mysql_num_rows($result) < 1) // process_id does not exist
        {
            echo '<p class="error">No process with ID '.$process_id.'</p>';
            return;
        }
        $row = mysql_fetch_array($result);
        $process_name = $row['name'];

        $query = "SELECT id, name FROM item_stats";
        $result = mysql_query2($query);
        while ($row=mysql_fetch_array($result, MYSQL_ASSOC)){
            $iid=$row['id'];
            $items["$iid"]=$row['name'];
            $items[0] = "";
        }
        
        if ($subprocess_number == 0) // this is a main process
        {
            $query = "SELECT t.id, t.pattern_id, t.process_id, p.name, t.result_id, t.result_qty, t.item_id, t.item_qty, t.trans_points, t.penalty_pct, t.description FROM trade_transformations AS t LEFT JOIN trade_processes AS p ON t.process_id=p.process_id LEFT JOIN item_stats AS i ON i.id=t.result_id WHERE t.process_id='$process_id' ORDER BY pat.pattern_name, i.name";
            $result = mysql_query2($query);
            if (mysql_num_rows($result) > 0)  // there still are dependencies, do not offer to delete anything.
            {
                echo '<p class="error">You can NOT delete this process ('.$process_name.'), since the following transforms still use it.</p>';
                echo '<table><tr><th>Source Item</th><th>Process</th><th>Result Item</th><th>Time</th><th>Resultant Quality</th><th>Actions</th></tr>';
                $alt = FALSE;
                while ($row=mysql_fetch_array($result, MYSQL_ASSOC))
                {
                    $alt = !$alt;
                    if ($alt)
                    {
                        echo '<tr class="color_a">';
                    }
                    else
                    {
                        echo '<tr class="color_b">';
                    }
                    $item_id=$row['item_id'];
                    echo '<td>'.$row['item_qty'].' '.$items["$item_id"].'</td>';
                    echo '<td>'.$row['name'].'</td>';
                    $result_id=$row['result_id'];
                    echo '<td>'.$row['result_qty'].' '.$items["$result_id"].'</td>';
                    echo '<td>'.$row['trans_points'].'</td>';
                    echo '<td>'.$row['penalty_pct'].'</td>';
                    echo '<td><a href="./index.php?do=transform&amp;id='.$row['id'].'">Edit</a></td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            else
            {
                echo '<p>You are about to permanently delete process id '.$process_id.' ('.$process_name.') and all known subprocesses</p>';
                echo '<form action="./index.php?do=deleteprocess&id='.$process_id.'&sub='.$subprocess_number.'" method="post">Enter your password to confirm: <input type="password" name="passd" /><input type="submit" name="submit" value="Confirm Delete"></form>';
            }
        }
        else // we have a sub-process, we can delete those without question.
        {
            echo '<p>You are about to permanently delete sub process number: '.$subprocess_number.' from process id '.$process_id.' ('.$process_name.')</p>';
            echo '<form action="./index.php?do=deleteprocess&id='.$process_id.'&sub='.$subprocess_number.'" method="post">Enter your password to confirm: <input type="password" name="passd" /><input type="submit" name="submit" value="Confirm Delete"></form>';
        }    
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>
