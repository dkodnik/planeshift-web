<?
function edit_transform( $transform_id, $pattern )
{
    if ( $transform_id != '' )
    {
    echo "<H1> Changing the Transformation </H1>";
    $sql= "select * from trade_transformations where id=$transform_id";        
    $query = mysql_query2($sql);
    $result = mysql_fetch_array($query);
    $id     = $result['id'];
    $pattern_id     = $result['pattern_id'];
    $pattern_id     = $result['process_id'];
    $result_id      = $result['result_id'];
    $result_qty     = $result['result_qty'];
    $item_id        = $result['item_id'];
    $item_qty       = $result['item_qty'];
    $trans_points   = $result['trans_points'];
    
    $primary_skill_id           = $result['primary_skill_id'];
    $primary_min_skill          = $result['primary_min_skill'];
    $primary_max_skill          = $result['primary_max_skill'];    
    $primary_practice_points    = $result['primary_practice_points'];
    $primary_quality_factor     = $result['primary_quality_factor'];    
    
    $secondary_skill_id         = $result['secondary_skill_id'];    
    $secondary_min_skill        = $result['secondary_min_skill'];
    $secondary_max_skill        = $result['secondary_max_skill'];    
    $secondary_practice_points  = $result['secondary_practice_points'];
    $secondary_quality_factor   = $result['secondary_quality_factor'];
    }
    else
    {
    echo "<H1> Adding a new transformation </H1>";
    
    $pattern_id     = $pattern;
    $item_qty       = 1;
    $result_qty     = 1;
    $item_quality   = 1;
    $trans_points   = 1;
    $animation      = 0;
    $workitem_id    = 0;
    $equipment_id   = 0;
    
    $primary_skill_id           = -1;
    $primary_min_skill          = 0;
    $primary_max_skill          = 0;
    $primary_practice_points    = 0;
    $primary_quality_factor     = 0;
    
    $secondary_skill_id         = -1;
    $secondary_min_skill        = 0;
    $secondary_max_skill        = 0;
    $secondary_practice_points  = 0;
    $secondary_quality_factor   = 0;
    }
    ?>
    
    
    <CENTER>
    
    <FORM ACTION="index.php?page=view_tradeskills" METHOD=POST>
    <INPUT TYPE=HIDDEN NAME=ACTION VALUE=EDIT_TRANSFORM>
    <INPUT TYPE=HIDDEN NAME=transform_id VALUE=<?PHP echo $transform_id ?>>    
    <INPUT TYPE=HIDDEN NAME=pattern_id VALUE=<?PHP echo $pattern_id ?>>    
    <TABLE CELLPADDING=5 CELLSPACING=0>
    <TR>
        <TD>Source Item:</TD>
        <TD><?PHP DrawItemSelectBox( "source_item", $item_id )?> </TD>       
    </TR>        
    <TR>
        <TD>Source Item Count:</TD>
        <TD><INPUT TYPE=TEXT NAME=source_item_count VALUE=<?PHP echo $item_qty?>></TD>       
    </TR>        
    <TR>
        <TD>Transformed To Item:</TD>
        <TD><?PHP DrawItemSelectBox( "transformed_item", $result_id )?> </TD>       
    </TR>        
    <TR>
        <TD>Transformed Item Count:</TD>
        <TD><INPUT TYPE=TEXT NAME=transformed_item_count VALUE=<?PHP echo $result_qty?>></TD>       
    </TR>        
    
    <TR>
        <TD>Working Container:</TD>
        <TD><?PHP DrawItemSelectBox( "workitem_id", $workitem_id )?> </TD>       
    </TR>        
    
    <TR>
        <TD>Transform Time:</TD>
        <TD><INPUT TYPE=TEXT NAME=transform_time VALUE=<?PHP echo $trans_points?>></TD>       
    </TR>        
    
    <TR>
        <TD>Primary Skill</TD>
        <TD><?PHP DrawSkillSelectBox( "primary_skill", $primary_skill_id, true )?></TD>       
        <TD>Min:<INPUT TYPE=TEXT NAME=p_min_skill VALUE=<?PHP echo $primary_min_skill?>></TD>       
        <TD>Max:<INPUT TYPE=TEXT NAME=p_max_skill VALUE=<?PHP echo $primary_max_skill?>></TD>               
    </TR>        
    
    <TR>
        <TD>Secondary Skill</TD>
        <TD><?PHP DrawSkillSelectBox( "secondary_skill", $secondary_skill_id, true )?></TD>       
        <TD>Min:<INPUT TYPE=TEXT NAME=s_min_skill VALUE=<?PHP echo $secondary_min_skill?>></TD>       
        <TD>Max:<INPUT TYPE=TEXT NAME=s_max_skill VALUE=<?PHP echo $secondary_max_skill?>></TD>               
    </TR>        
    <?PHP
    if ( $transform_id != '' )
    {
        ?>
        <TR>
            <TD><INPUT TYPE=CHECKBOX NAME=delete>Delete</INPUT></TD>
        </TR>
        <?PHP
    }
    ?>
    <TR>        
        <TD COLSPAN=4 ALIGN=CENTER><INPUT TYPE=SUBMIT VALUE="Update"></TD>
    </TR>    
    </TABLE>
    </FORM>
    <CENTER>    
    
<?PHP    
}

//-----------------------------------------------------------------------------

function view_trade_pattern( $pattern_id )
{
    $sql= "select * from trade_patterns where id=$pattern_id";    
    $query = mysql_query2($sql);
    $result = mysql_fetch_array($query);

    $pattern_name = $result['pattern_name'];
    $pattern_description = $result["description"];
    
    ?>
    <CENTER>
    <H1> Editing the Trade Pattern: <FONT COLOR=#FFFF00> <?PHP echo $pattern_name ?></FONT></H1>
    <FORM ACTION="index.php?page=view_tradeskills" METHOD=POST>
    <INPUT TYPE=HIDDEN NAME=ACTION VALUE=EDIT_PATTERN>
    <INPUT TYPE=HIDDEN NAME=pattern_id VALUE=<?PHP echo $pattern_id ?>>
    <TABLE CELLPADDING=5 CELLSPACING=0>
    <TR>
        <TD>Pattern Name</TD>
        <TD><INPUT TYPE=TEXT NAME=pattern_name VALUE= <?PHP echo $pattern_name ?> ></TD>
    </TR>
    <TR>
        <TD VALIGN=TOP>Pattern Description</TD>
        <TD><TEXTAREA ROWS=10 COLS=50 NAME=pattern_description><?PHP echo $pattern_description ?> </TEXTAREA></TD>
    </TR>
    <TR>
        <TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE=SUBMIT VALUE="Update Pattern"></TD>
    </TR>    
    </TABLE>
    
    <H1> Transformations available in this pattern </H1>
    <TABLE CELLPADDING=5 CELLSPACING=0>
    <TH>ID</TH><TH>Process ID</TH><TH>Source</TH><TH>Source Qty</TH><TH>Result</TH><TH>Result Qty</TH><TH>Time</TH><TH>Penalty %</TH>
    <?PHP    
    
     $sql= "select * from trade_transformations where pattern_id=$pattern_id order by process_id, result_id";
    $result = mysql_query2($sql);

    $bgcolour1 = "#111111";
    $bgcolour2 = "#555555";
    
    $colour = $bgcolour1;
    while ($temp = mysql_fetch_array($result))
    {
        if ( $colour == $bgcolour1 )
            $colour = $bgcolour2;
        else
            $colour = $bgcolour1;
    
        $transformID = $temp['id'];                
        $process_id  = $temp['process_id'];
        $process_id_url  = "<A HREF=\"index.php?page=view_tradeskills&page2=view_process&process_id=".$temp['process_id']." \" >" .$temp['process_id']. "</A>";
        $itemName = GetItemName( $temp["item_id"] );
        $item_qty = $temp['item_qty'];
        $resultName = GetItemName( $temp['result_id'] );
        $result_qty =  $temp['result_qty'];
        $time =  $temp['trans_points'];
        $penalty =  $temp['penilty_pct'];
                                    
        ?>
        <TR BGCOLOR=<?PHP echo "$colour"?> >
        <TD><?PHP echo $transformID ?></TD>
        <TD><?PHP echo $process_id_url ?></TD>    
        <TD><?PHP echo $itemName ?></TD>
        <TD><?PHP echo $item_qty ?></TD>
        <TD><?PHP echo $resultName ?></TD>
        <TD><?PHP echo $result_qty ?></TD>
        <TD><?PHP echo $time ?></TD>
        <TD><?PHP echo $penalty ?></TD>
        <TD><A HREF="index.php?page=view_tradeskills&page2=edit_transform&transform_id=<?PHP echo $transformID?>">Edit</A></TD>            
        </TR>
    <?PHP
    }    
    ?>
    </TABLE>
    <A HREF="index.php?page=view_tradeskills&page2=edit_transform&pattern_id=<?PHP echo $pattern_id?>">Add New Transform</A>
    <?PHP    
}

function view_patterns()
{
    ?>
    
    <CENTER>
    <H1> Available Trade Patterns</H1>
    <TABLE BORDER=0 CELLPADDING=5 CELLSPACING=0>
    <TH></TH><TH>ID</TH><TH>Name</TH><TH>Description</TH><TH>Design Item</TH>
    
    <?PHP
    $sql= "select * from trade_patterns order by id";    
    $query = mysql_query2($sql);

    $bgcolour1 = "#111111";
    $bgcolour2 = "#555555";
    
    $colour = $bgcolour1;
    while ($temp = mysql_fetch_array($query))
    {   
        if ( $colour == $bgcolour1 )
            $colour = $bgcolour2;
        else
            $colour = $bgcolour1;
                    
        ?>
        <TR BGCOLOR=<?PHP echo "$colour"?> >
            <TD><A HREF="index.php?page=view_tradeskills&page2=view_pattern&pattern_id=<?PHP echo $temp['id'] ?>">Edit</A></TD>
            <TD><?PHP echo $temp['id']; ?></TD>
            <TD><?PHP echo $temp['pattern_name']; ?></TD>
            <TD><?PHP echo $temp['description']; ?></TD>
            <TD><?PHP echo GetItemName( $temp["designitem_id"] ); ?></TD>
        </TR>
    <?PHP        
    }
    ?>
    </TABLE>
    </CENTER>        
<?PHP
}

function view_trade_process( $process_id )
{
    $sql= "select * from trade_processes where process_id=$process_id";    
    $query = mysql_query2($sql);
    $result = mysql_fetch_array($query);

    $process_name = $result['name'];
    $process_description = $result["description"];
    $process_workitem = GetItemName( $result["workitem_id"] );
    $process_equipment = GetItemName( $result["equipment_id"] );
    $process_animation= $result["animation"];
    $process_render = $result["render_effect"];
    $process_constraints = $result["constraints"];
    $process_garbage = GetItemName( $result["garbage_id"] );
    $process_garbage_qty = $result["garbage_qty"];
    $primary_skill = GetSkillName( $result["primary_skill_id"] );
    $primary_min = $result["primary_min"];
    $primary_max = $result["primary_max"];
    $primary_practice = $result["primary_practice_points"];
    $primary_quality = $result["primary_quality_factor"];
    $secondary_skill = GetSkillName( $result["secondary_skill_id"] );
    $secondary_min = $result["secondary_min"];
    $secondary_max = $result["secondary_max"];
    $secondary_practice = $result["secondary_practice_points"];
    $secondary_quality = $result["secondary_quality_factor"];

    echo "ID: $process_id<br>";
    echo "Name: $process_name <br>";
    echo "Description: $process_description <br>";
    echo "Work Item: $process_workitem <br>";
    echo "Equipment: $process_equipment <br>";
    echo "Garbage: $process_garbage Qty: $process_garbage_qty <br>";
    echo "Primary skill: $primary_skill Min: $primary_min Max: $primary_max Practice: $primary_practice Quality: $primary_quality <br>";
    echo "Secondary skill: $secondary_skill Min: $secondary_min Max: $secondary_max Practice: $secondary_practice Quality: $secondary_quality <br>";
    echo "<br>";
    echo "Constraints: $process_constraints <br>";

    echo "Animation: $process_animation <br>";
    echo "Render Effect: $process_render <br>";

}


function HandleTransformEdit()
{                                     
    $pattern_id         = $_POST['pattern_id'];      
    $id                 = $_POST['transform_id'];
    $sourceItem         = $_POST['source_item'];
    $sourceQTY          = $_POST['source_item_count'];
    $transformItem      = $_POST['transformed_item'];
    $transformedQTY     = $_POST['transformed_item_count'];
    $workitem_id        = $_POST['workitem_id'];
    $time               = $_POST['transform_time'];
    $p_skill_id         = $_POST['primary_skill'];
    $p_max              = $_POST['p_max_skill'];
    $p_min              = $_POST['p_min_skill'];    
    $s_skill_id         = $_POST['secondary_skill'];
    $s_max              = $_POST['s_max_skill'];
    $s_min              = $_POST['s_min_skill'];  
    $delete             = $_POST['delete'];
    
    if ( $id != '' )
    {
        if ( $delete == 'on' )
        {
            $sql = "DELETE FROM trade_transformations WHERE id=$id";
        }
        else
        {
            $sql = "UPDATE trade_transformations SET result_id=$transformItem,result_qty=$transformedQTY,  
                                             item_id=$sourceItem,
                                             item_qty=$sourceQTY,
                                             trans_points=$time,
                                             workitem_id=$workitem_id,
                                             primary_skill_id=$p_skill_id,
                                             primary_min_skill=$p_min,
                                             primary_max_skill=$p_max,
                                             secondary_skill_id='$s_skill_id',
                                             secondary_min_skill=$s_min,
                                             secondary_max_skill=$s_max WHERE id=$id";
        }                                             
    }                                             
    else
    {
        $sql = "INSERT INTO trade_transformations(pattern_id, result_id,result_qty,item_id,item_qty,trans_points,workitem_id,
                                                  primary_skill_id,primary_min_skill,primary_max_skill,
                                                  secondary_skill_id, secondary_min_skill,secondary_max_skill)
                                                  VALUES(  $pattern_id, $transformItem,$transformedQTY,  
                                                            $sourceItem,
                                                            $sourceQTY,
                                                            $time,
                                                            $workitem_id,
                                                            $p_skill_id,
                                                            $p_min,
                                                            $p_max,
                                                            '$s_skill_id',
                                                            $s_min,
                                                            $s_max )";
    
    }
    mysql_query2($sql);
    
    view_trade_pattern( $pattern_id );    
}

function view_tradeskills()
{

    checkAccess('main', '', 'read');

    if ( $_POST['ACTION'] != '' )
    {
        switch( $_POST['ACTION'] )
        {
            case 'EDIT_PATTERN':
                $name = $_POST['pattern_name'];
                $description = $_POST['pattern_description'];
                $pattern_id = $_POST['pattern_id'];
                $sql = "UPDATE trade_patterns SET pattern_name='$name', description='$description' WHERE id=$pattern_id";
                mysql_query2($sql);
                view_trade_pattern( $pattern_id );
            break;
            
            case 'EDIT_TRANSFORM':
                HandleTransformEdit();
            break;                
        }    
    }
    else
    {
        if ($_GET['page2'] == 'trade_patterns' ||$_GET['page2']=='' )
        {
            view_patterns();
        }

        if ($_GET['page2'] == 'view_pattern')
        {
            view_trade_pattern( $_GET['pattern_id'] );
        }    

        if ($_GET['page2'] == 'view_process')
        {
            view_trade_process( $_GET['process_id'] );
        }

        if ($_GET['page2'] == 'edit_transform')
        {
            edit_transform( $_GET['transform_id'], $_GET['pattern_id'] );
        }    
        
    }
}
?>
