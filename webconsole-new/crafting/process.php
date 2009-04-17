<?php
function listprocess()
{
    if (checkaccess('crafting', 'read'))
    {
        echo '<p class="header">Process Information</p>';
        $result = mysql_query2("SELECT id, name FROM item_stats");
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            $i = $row['id'];
            $items["$i"] = $row['name'];
        }
        $items[0] = "";
        $result = mysql_query2("SELECT skill_id, name FROM skills");
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            $i = $row['skill_id'];
            $skills["$i"] = $row['name'];
        }
        $skills[0] = "";
        $query = "SELECT * FROM trade_processes";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $id = $row['process_id'];
        mysql_data_seek($result, 0);
        echo '<table><tr><th>Name</th><th>Sub-<br>Process</th><th>Animation</th><th>Item Used</th><th>Equipment Used</th><th>Constraints</th><th>Garbage Item</th><th>Primary Skill / Min / Max / Practice / Quality</th><th>Secondary Skill / Min / Max / Practice / Quality</th><th>Description</th>';
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
            $i = $row['workitem_id'];
            echo '<td>'.$items["$i"].'</td>';
            $i = $row['equipment_id'];
            echo '<td>'.$items["$i"].'</td>';
            echo '<td>'.$row['constraints'].'</td>';
            $i = $row['garbage_id'];
            echo '<td>'.$row['garbage_qty'].' '.$items["$i"].'</td>';
            $i = $row['primary_skill_id'];
            echo '<td>'.$skills["$i"].' / '.$row['primary_min_skill'].' / '.$row['primary_max_skill'].' / '.$row['primary_practice_points'].' / '.$row['primary_quality_factor'].'</td>';
            $i = $row['secondary_skill_id'];
            echo '<td>'.$skills["$i"].' / '.$row['secondary_min_skill'].' / '.$row['secondary_max_skill'].' / '.$row['secondary_practice_points'].' / '.$row['secondary_quality_factor'].'</td>';
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
    $query = "SELECT * FROM trade_processes WHERE process_id = '$id'";
    $result = mysql_query2($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    echo '- '.$row['name'].'</p>';
    mysql_data_seek($result, 0);
    echo '<table><tr><th>Sub-Process</th><th>Animation</th><th>Item Used</th><th>Equipment Used</th><th>Constraints</th><th>Garbage Item</th><th>Primary Skill / Min / Max / Practice / Quality</th><th>Secondary Skill / Min / Max / Practice / Quality</th><th>Description</th>';
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
      $i = $row['workitem_id'];
      echo '<td>'.$Items["$i"].'</td>';
      $i = $row['equipment_id'];
      echo '<td>'.$Items["$i"].'</td>';
      echo '<td>'.$row['constraints'].'</td>';
      $i = $row['garbage_id'];
      echo '<td>'.$row['garbage_qty'].' '.$Items["$i"].'</td>';
      $i = $row['primary_skill_id'];
      echo '<td>'.$Skills["$i"].' / '.$row['primary_min_skill'].' / '.$row['primary_max_skill'].' / '.$row['primary_practice_points'].' / '.$row['primary_quality_factor'].'</td>';
      $i = $row['secondary_skill_id'];
      echo '<td>'.$Skills["$i"].' / '.$row['secondary_min_skill'].' / '.$row['secondary_max_skill'].' / '.$row['secondary_practice_points'].' / '.$row['secondary_quality_factor'].'</td>';
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
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function editsubprocess(){
  if (checkaccess('crafting','edit')){
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
        $garbage_qty = mysql_real_escape_string($_POST['garbage_qty']);
        $primary_skill_id = mysql_real_escape_string($_POST['primary_skill_id']);
        $primary_min_skill = mysql_real_escape_string($_POST['primary_min_skill']);
        $primary_max_skill = mysql_real_escape_string($_POST['primary_max_skill']);
        $primary_practice_points = mysql_real_escape_string($_POST['primary_practice_points']);
        $primary_quality_factor = mysql_real_escape_string($_POST['primary_quality_factor']);
        $secondary_skill_id = mysql_real_escape_string($_POST['secondary_skill_id']);
        $secondary_skill_id = ($secondary_skill_id == -1 ? 0 : $secondary_skill_id); // change id to 0 if it is not provided by user (-1)
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
    else{
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
      $Items = PrepSelect('items');
      echo '<tr><td>Work Item:</td><td>'.DrawSelectBox('items', $Items, 'workitem_id', $row['workitem_id'], 'true').'</td></tr>';
      echo '<tr><td>Equipment:</td><td>'.DrawSelectBox('items', $Items, 'equipment_id', $row['equipment_id'], 'true').'</td></tr>';
      echo '<tr><td>Garbage Item:</td><td>'.DrawSelectBox('items', $Items, 'garbage_id', $row['garbage_id']).'</td></tr>';
      echo '<tr><td>Garbage Quantity:</td><td><input type="text" name="garbage_qty" value="'.$row['garbage_qty'].'" /></td></tr>';
      $Skills = PrepSelect('skill');
      echo '<tr><td>Primary Skill:</td><td>'.DrawSelectBox('skill', $Skills, 'primary_skill_id', $row['primary_skill_id']).'</td></tr>';
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
  }else{
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
        $garbage_qty = mysql_real_escape_string($_POST['garbage_qty']);
        $primary_skill_id = mysql_real_escape_string($_POST['primary_skill_id']);
        $primary_min_skill = mysql_real_escape_string($_POST['primary_min_skill']);
        $primary_max_skill = mysql_real_escape_string($_POST['primary_max_skill']);
        $primary_practice_points = mysql_real_escape_string($_POST['primary_practice_points']);
        $primary_quality_factor = mysql_real_escape_string($_POST['primary_quality_factor']);
        $secondary_skill_id = mysql_real_escape_string($_POST['secondary_skill_id']);
        $secondary_skill_id = ($secondary_skill_id == -1 ? 0 : $secondary_skill_id); // change id to 0 if it is not provided by user (-1)
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
        echo '<tr><td>Garbage Item:</td><td>'.DrawSelectBox('items', $items, 'garbage_id', '', false).'</td></tr>';
        echo '<tr><td>Garbage Quantity:</td><td><input type="text" name="garbage_qty" value="0"/></td></tr>';
        $skills = PrepSelect('skill');
        echo '<tr><td>Primary Skill:</td><td>'.DrawSelectBox('skill', $skills, 'primary_skill_id', '', false).'</td></tr>';
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
        }
        
        if ($subprocess_number == 0) // this is a main process
        {
            $query = "SELECT t.id, t.pattern_id, t.process_id, p.name, t.result_id, t.result_qty, t.item_id, t.item_qty, t.trans_points, t.penilty_pct, t.description FROM trade_transformations AS t LEFT JOIN trade_processes AS p ON t.process_id=p.process_id WHERE t.process_id='$process_id' GROUP BY pattern_id";
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
                    echo '<td>'.$row['penilty_pct'].'</td>';
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
