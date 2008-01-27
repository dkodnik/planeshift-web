<?
function locations_actions($selected,$selectedtype,$selectedsector){
?>
<HTML>
<BODY>


<?PHP


// gets operation to perform
$operation = $_GET['operation'];

    /**
     * update script
     */
if ($operation == 'update'){
    $id = $_POST['id'];
    $loc_type = $_POST['loc_type'];
    $name = $_POST['name'];
    $prev_loc = $_POST['prev_loc'];
    $sector = $_POST['sector'];
    $loc_x = $_POST['x'];
    $loc_y = $_POST['y'];
    $loc_z = $_POST['z'];
    $radius = $_POST['radius'];
    $flags = $_POST['flags'];
    // insert script
    $query = "update sc_locations set type_id=$loc_type, name='$name', id_prev_loc_in_region='$prev_loc', loc_sector_id=$sector, x='$loc_x',y='$loc_y',z='$loc_z',radius='$radius',flags='$flags' where id='$id'";
    $result = mysql_query2($query); 
    // redirect
    ?><SCRIPT language="javascript">
          document.location = "index.php?page=listlocations&selectedsector=<?=$selectedsector?>";
       </script>
    <?PHP

} elseif ($operation == 'create'){
    $id = $_POST['id'];
    $loc_type = $_POST['loc_type'];
    $name = $_POST['name'];
    $prev_loc = $_POST['prev_loc'];
    $sector = $_POST['sector'];
    $loc_x = $_POST['x'];
    $loc_y = $_POST['y'];
    $loc_z = $_POST['z'];
    $radius = $_POST['radius'];
    $flags = $_POST['flags'];
    // create script
    $query = "insert into sc_locations set type_id=$loc_type, name='$name', id_prev_loc_in_region='$prev_loc', loc_sector_id=$sector, x='$loc_x',y='$loc_y',z='$loc_z',radius='$radius',flags='$flags'";
    $result = mysql_query2($query); 
    // redirect
    ?><SCRIPT language="javascript">
          document.location = "index.php?page=listlocations&selectedsector=<?=$selectedsector?>";
       </script>
    <?PHP
} elseif ($operation == 'delete'){
    $id = $_POST['id'];
    // insert script
    $query = "delete from sc_locations  where id='$id'";
    $result = mysql_query2($query); 
    // redirect
    ?><SCRIPT language="javascript">
          document.location = "index.php?page=listlocations&selectedsector=<?=$selectedsector?>";
       </script>
    <?PHP
} elseif ($operation == 'createtype'){
    $name = $_POST['name'];
    // create script
    $query = "insert into sc_location_type set name='$name'";
    $result = mysql_query2($query); 
    // redirect
    ?><SCRIPT language="javascript">
          document.location = "index.php?page=listlocations&selectedsector=<?=$selectedsector?>";
       </script>
    <?PHP
}else{ 
    // manage another operation here
    echo "Operation $operation not supported.";
}

?>



</body>
</html>

<?
}
?>
