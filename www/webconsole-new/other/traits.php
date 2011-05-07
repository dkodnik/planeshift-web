<?php

/*
 * Builds a select element from its name, current option and possible options
 */
function buildSelect($name, $current, $options)
{
    $xml = sprintf('<select name="%s">', $name);
    foreach($options as $key => $value)
    {
        $xml .= '<option ';
        if ($current == $key) $xml .= 'selected="selected" ';
        $xml .= sprintf('value="%s">%s</option>', $key, $value);
    }
    $xml .= '</select>';
    return $xml;
}

function selectOnlyNPC($current)
{
    return buildSelect('only_npc', $current, array(0 => 'no', 1 => 'yes'));
}

function selectLocationT($current)
{
    return buildSelect('location', $current, array('FACE' => 'face', 
        'HAIR_COLOR' => 'hair color', 'HAIR_STYLE' => 'hair style',
        'BEARD_STYLE' => 'beard style', 'SKIN_TONE' => 'skin tone', 
        'EYE_COLOR' => 'eye color', 'ITEM' => 'item'));
}


/******************************************************************************
Show the traits for a particular race
 race_id  The race id from the race_info table we want to see the traits for.
******************************************************************************/
function show_traits($race_id)
{
    if (!CheckAccess('other', 'read'))
    {
        echo '<p class="error">You are not authorised to use these functions.</p>';
        return;
    }
    $race_id = mysql_real_escape_string($race_id);
    $query = "SELECT  name FROM  race_info WHERE id = '$race_id'";
    $result = mysql_query2($query);

    $line = mysql_fetch_array($result, MYSQL_NUM);

    echo '<h1>' . $line[0] . '</h1>';

    // Information about Traits: Name => array(display name, mesh, material, texture, shader)
    // mesh/material/texture/shader: true => input field, false => ignored, string => use this always as value
    $traits = array(
/*      NAME            => array(display name   mesh    material    texture     shader  ) */
        'FACE'          => array('Face',        'Head', true,       true,       false   ),
        'HAIR_COLOR'    => array('Hair Color',  false,  false,      false,      true    ),
        'HAIR_STYLE'    => array('Hair Style',  true,   false,      false,      true    ),
        'BEARD_STYLE'   => array('Beard Style', true,   false,      false,      true    ),
        'SKIN_TONE'     => array('Skin Tone',   true,   true,       true,       false   ),
        'EYE_COLOR'     => array('Eye Color',   false,  false,      false,      true    ),
        'ITEM'          => array('Item',        true,   true,       true,       false   )
        );
    echo '<ul>';
    foreach($traits as $name => $display)
    {
        printf('<li><a href="#%s">%ss</a></li>', strtolower($name), $display[0]);
    }
    echo '</ul>';
    $columns = array(1 => 'Mesh', 2 => 'Material', 3 => 'Texture', 4 => 'Shader');
    
    foreach($traits as $name => $properties)
    {
        printf('<a name="%s"></a>', strtolower($name));
        printf('<h2>%s</h2>', $properties[0]); // display name
        $location = mysql_real_escape_string($name); // NAME
        // we already escaped $race_id
        $query = "SELECT id, next_trait, only_npc, name, cstr_mesh as Mesh, cstr_material as Material, cstr_texture as Texture, shader as Shader FROM traits WHERE location = '$location' AND race_id = '$race_id'";
        $result = mysql_query2($query);
        echo '<table border="1px"><tr><th>ID</th><th>Name</th><th>Next ID</th><th>NPC Only</th>';
        foreach($columns as $id => $display) // loop through different columns,
        // check if we need table headers for those (see $traits above)
        {
            if($properties[$id] === true)
            {
                printf('<th>%s</th>', $display);
            }
        }
        echo '<th>Delete</th><th>Update</th></tr>' . "\n";
        while($row = mysql_fetch_object($result))
        {
            echo '<form action="index.php?do=handletrait" method="post">' . "\n";
            printf('<input type="hidden" name="race_id" value="%s" />', $race_id);
            printf('<input type="hidden" name="location" value="%s" />', $location);
            printf('<input type="hidden" name="trait_id" value="%s" />', $row->id);
            printf('<tr><td>%s</td>', $row->id);
            printf('<td><input name="name" type="text" value="%s" /></td>', $row->name);
            printf('<td><input name="next_trait" type="text" size="4" value="%s" /></td>', $row->next_trait);
            echo '<td>' . SelectOnlyNPC($row->only_npc) . '</td>';
            foreach($columns as $id => $name) // loop through different columns,
            // check if we need to display input fields for those (see $traits above)
            {
                if($properties[$id] === true)
                {
                    printf('<td><input name="%s" type="text" value="%s" /></td>', $name, $row->$name);
                }
                elseif($properties[$id] !== false)
                {
                    printf('<input name="%s" type="hidden" value="%s" />', $name, $row->$name);
                }
            }
            echo '<td><input type="checkbox" name="delete" /></td>';
            echo '<td><input type="submit" name="submit" value="Update" /></td>';
            echo '</tr>' . "\n";
            echo '</form>' . "\n";
        }
        echo '<form action="index.php?do=handletrait" method="post">' . "\n";
        printf('<input type="hidden" name="race_id" value="%s" />', $race_id);
        printf('<input type="hidden" name="location" value="%s" />', $location);
        echo '<input type="hidden" name="trait_id" value="-1" />';
        echo '<tr><td>&nbsp;</td>';
        echo '<td><input name="name" type="text" value="" /></td>';
        echo '<td><input name="next_trait" type="text" size="4" value="" /></td>';
        echo '<td>' . SelectOnlyNPC(0) . '</td>';
        foreach($columns as $id => $name) // loop through different columns,
        // check if we need to display input fields for those (see $traits above)
        {
            if($properties[$id] === true)
            {
                printf('<td><input name="%s" type="text" value="" /></td>', $name);
            }
            elseif($properties[$id] !== false)
            {
                printf('<input name="%s" type="hidden" value="%s" />', $name, $properties[$id]);
            }
        }
        echo '<td></td>';
        echo '<td><input type="submit" name="submit" value="Add" /></td>';
        echo '</tr>' . "\n";
        echo '</form>' . "\n";
        echo '</table>' . "\n";
    }
}

/*****************************************************************************
Shows a list of the different races so you can pick one.
 *****************************************************************************/
function show_races()
{
    if (!CheckAccess('other', 'read'))
    {
        echo '<p class="error">You are not authorised to use these functions.</p>';
        return;
    }
    if (isset($_GET['function']) && $_GET['function'] == 'list' )
    {
        show_traits( $_GET['race_id'] );  
    }
    else
    {
    $query = "SELECT id, name, sex FROM race_info WHERE id < 23 ORDER BY id ASC";
    $result = mysql_query2($query);
   
    echo "<P>Select the race that you want to change the traits on";
    echo "<CENTER>";
    echo "<TABLE>";
    echo "<TR><TD>";
    echo "<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=0>";
    echo "<TH>ID</TH><TH>Race</TH><TH>Gender</TH>";
    
    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
        echo "<TR><TD>" . $line[0] . "</TD><TD><A HREF=index.php?do=showraces&amp;function=list&amp;race_id=" . $line[0] . ">" . $line[1] . "</A></TD><TD>" . $line[2] . "</TD></TR>";
        if($line[0] == 11)
        {
            echo "</TABLE>";
            echo "</TD><TD VALIGN=TOP>";
            echo "<TABLE BORDER=1 CELLPADDING=5 CELLSPACING=0>";
            echo "<TH>ID</TH><TH>Race</TH><TH>Gender</TH>";
        }

    }
    echo "</TABLE>";

    echo "</TD></TR></TABLE>";
    echo "</CENTER>";
    }
}

function list_traits()
{

    if (!checkaccess('other', 'read'))
    {
        echo '<p class="error">You are not authorised to use these functions.</p>';
        return;
    }

    $query = "SELECT id, next_trait, race_id, only_npc, location, name, cstr_mesh, cstr_material, cstr_texture FROM traits";
    $result = mysql_query2($query);
    echo '  <TABLE BORDER=1>';
    echo '  <TH> ID </TH> ';
    echo '  <TH> Next </TH> ';
    echo '  <TH> Race/Location - Only NPC / Name </TH> ';
    echo '  <TH> Mesh/Material/Texture </TH> ';
    echo '  <TH> Functions </TH> ';
    $races = PrepSelect('races');
    
    while ($line = mysql_fetch_array($result, MYSQL_NUM)){
        echo '<TR>';
        echo '<FORM ACTION=index.php?do=trait_actions&amp;operation=update METHOD=POST>';
        echo '<TD><INPUT TYPE="hidden" NAME="id" VALUE="'.$line[0].'" />'.$line[0].'</TD>';
        echo '<TD><INPUT SIZE="5" TYPE="text" NAME="next_trait" VALUE="'.$line[1].'"></TD>';
        echo '<TD><TABLE><TR><TD>'.DrawSelectBox('races', $races, 'race_id', $line[2], false).'</TD>';
        echo '<TD>'.SelectOnlyNPC($line[3]).'</TD></TR>';
        echo '<TR><TD>'.SelectLocationT($line[4],'location').'</TD>';
        echo '<TD><INPUT TYPE="text" NAME="name" VALUE="'.$line[5].'" /></TD></TR></TABLE></TD>';
        echo '<TD><input type="text" name="cstr_mesh" value="'.$line[6].'" /><BR />';
        echo '<input type="text" name="cstr_material" value="'.$line[7].'" /><BR />';
        echo '<input type="text" name="cstr_texture" value="'.$line[8].'" /></TD>';
        echo '<TD><TABLE><TR><TD><INPUT TYPE="SUBMIT" NAME="submit" VALUE="Update" /></FORM></TD>';
        echo '<TD><FORM ACTION="index.php?do=trait_actions&amp;operation=delete" METHOD="POST">';
        echo '<INPUT TYPE="hidden" NAME="id" VALUE="'.$line[0].'" />';
        echo '<INPUT TYPE="SUBMIT" NAME="submit" VALUE="Delete" /></FORM></TD></TR></TABLE>';
        echo '</TD></TR>';
    }
    echo '<TR>';
    echo '<FORM ACTION="index.php?do=trait_actions&amp;operation=add" METHOD="POST">';
    echo '<TD></TD>';
    echo '<TD><INPUT SIZE="5" TYPE="text" NAME="next_trait" /></TD>';
    echo '<TD><TABLE><TR><TD>'.DrawSelectBox('races', $races, 'race_id', '', false).'</TD>';
    echo '<TD>'.SelectOnlyNPC('0').'</TD></TR>';
    echo '<TR><TD>'.SelectLocationT('EYE_COLOR','location').'</TD>';
    echo '<TD><INPUT TYPE="text" NAME="name" /></TD></TR></TABLE></TD>';
    echo '<TD><input type="text" name="cstr_mesh" /><BR />';
    echo '<input type="text" name="cstr_material" /><BR />';
    echo '<input type="text" name="cstr_texture" /></TD>';
    echo '<TD><INPUT TYPE="SUBMIT" NAME="submit" VALUE="Add"></FORM>';

    echo '</FORM></TD></TR>';
    echo '</TABLE><br><br>';

    echo '<br><br>';
}
/* Updates/Adds/Deletes Traits from the Traits Per Race Module */
function handle_trait() {
    if (!checkaccess('other', 'edit'))
    {
        echo '<p class="error">You are not authorised to use these functions.</p>';
        return;
    }
    if(!isset($_POST['race_id']))
    {
        return show_races();
    }
    /* Those are required for all traits */
    if(!isset($_POST['submit']) or !isset($_POST['name']) or 
        !isset($_POST['next_trait']) or !isset($_POST['only_npc']) or 
        !isset($_POST['location']) or !isset($_POST['trait_id']))
    {
        echo '<p class="error">Missing Parameter.</p>';
        return;
    }
    $race_id    = $_POST['race_id'];
    $trait_id   = $_POST['trait_id'];
    $name       = $_POST['name'];
    $operation  = $_POST['submit'];
    $next_trait = $_POST['next_trait'];
    $only_npc   = $_POST['only_npc'];
    $location   = $_POST['location'];
    
    if(isset($_POST['delete']))
    {
        $operation = 'Delete';
    }
    /* Those are optional */
    $mesh       = (isset($_POST['Mesh'])) ? $_POST['Mesh'] : '';
    $material   = (isset($_POST['Material'])) ? $_POST['Material'] : '';
    $texture    = (isset($_POST['Texture'])) ? $_POST['Texture'] : '';
    $shader     = (isset($_POST['Shader'])) ? $_POST['Shader'] : '';
    
    /* Escape everything */
    $race_id    = mysql_real_escape_string($race_id);
    $trait_id   = mysql_real_escape_string($trait_id);
    $name       = mysql_real_escape_string($name);
    $next_trait = mysql_real_escape_string($next_trait);
    $location   = mysql_real_escape_string($location);
    $only_npc   = mysql_real_escape_string($only_npc);
    $mesh       = mysql_real_escape_string($mesh);
    $material   = mysql_real_escape_string($material);
    $texture    = mysql_real_escape_string($texture);
    $shader     = mysql_real_escape_string($shader);

    switch($operation)
    {
        case 'Update':
            $query = "UPDATE traits SET 
                name = '$name', 
                next_trait = '$next_trait', 
                race_id = '$race_id', 
                only_npc = '$only_npc', 
                location = '$location',
                cstr_mesh = '$mesh',
                cstr_material = '$material',
                cstr_texture = '$texture',
                shader = '$shader'
                WHERE id = '$trait_id'";
                printf('<p>Updating Trait #%s ...', $trait_id);
            break;
        case 'Add':
            if (!checkaccess('other', 'create'))
            {
                echo '<p class="error">You are not authorised to use these functions.</p>';
                return;
            }
            $query = "INSERT INTO traits (name, next_trait, race_id, only_npc, location, cstr_mesh, cstr_material, cstr_texture, shader)
                      VALUES ('$name', '$next_trait', '$race_id', '$only_npc', '$location', '$mesh', '$material', '$texture', '$shader')";
                echo '<p> Inserting new Trait ...';
            break;
        case 'Delete':
            if (!checkaccess('other', 'delete'))
            {
                echo '<p class="error">You are not authorised to use these functions.</p>';
                return;
            }
            $query = "DELETE FROM traits WHERE id = '$trait_id' LIMIT 1";
            printf('<p>Deleting Trait #%s ...', $trait_id);
            break;
        default:
            echo '<p class="error">Unknown operation.</p>';
            return;
    }
    $result = mysql_query2($query);
    if($result == false)
    {
        echo ' <span style="color:red">failed</span></p>';
    }
    else
    {
        echo ' <span style="color:green">done</span></p>';
    }
    show_traits($race_id);
}

function trait_actions()
{
    // gets operation to perform
    $operation = $_GET['operation'];

    /**
     * update script
     */
    if ($operation == 'update')
    {
        $id = $_POST['id'];
        $next_trait = $_POST['next_trait'];
        $race_id = $_POST['race_id'];
        $location = $_POST['location'];
        $name = $_POST['name'];
        $only_npc = $_POST['only_npc'];
        $cstr_mesh = $_POST['cstr_mesh'];
        $cstr_material = $_POST['cstr_material'];
        $cstr_texture = $_POST['cstr_texture'];

        // insert script
        $query = "update traits set next_trait='$next_trait', race_id='$race_id',only_npc='$only_npc',location='$location',name='$name' ,cstr_mesh='$cstr_mesh' ,cstr_material='$cstr_material' ,cstr_texture='$cstr_texture' where id='$id'";
        $result = mysql_query2($query); 
        // redirect
    }
    else if ($operation == 'add')
    {
        $next_trait = ($_POST['next_trait'] == '' ? -1 : $_POST['next_trait']);
        $race_id = $_POST['race_id'];
        $only_npc = $_POST['only_npc'];
        $location = $_POST['location'];
        $name = $_POST['name'];
        $cstr_mesh = $_POST['cstr_mesh'];
        $cstr_material = $_POST['cstr_material'];
        $cstr_texture = $_POST['cstr_texture'];

        // insert script
        $query = "insert into traits (next_trait,race_id,only_npc,location,name,cstr_mesh,cstr_material,cstr_texture) values ('$next_trait','$race_id','$only_npc','$location','$name','$cstr_mesh','$cstr_material','$cstr_texture')";
        $result = mysql_query2($query); 
        // redirect

    }
    else if ($operation == 'delete')
    {
        $id = mysql_real_escape_string($_POST['id']);
        $query = "delete from traits where id='$id'";
        $result = mysql_query2($query);
    }
    else
    { 
        // manage another operation here
        echo "Operation $operation not supported.";
    }
    unset($_POST);
    list_traits();
}
?>
