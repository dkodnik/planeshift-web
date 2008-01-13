<?
function waypoints_actions(){
?>
<HTML>
<BODY>


<?PHP


// gets operation to perform
$operation = $_GET['operation'];
$area= $_REQUEST['area'];

    /**
     * update script
     */
if ($operation == 'update'){
    $id = $_POST['id'];
    $sector = $_POST['sector'];
    $name = $_POST['name'];
    $loc_x = $_POST['x'];
    $loc_y = $_POST['y'];
    $loc_z = $_POST['z'];
    $radius = $_POST['radius'];
    $wp_group = $_POST['wp_group'];
    // insert script
    $query = "update sc_waypoints set loc_sector_id=$sector, x='$loc_x',y='$loc_y',z='$loc_z',radius='$radius',name='$name',wp_group='$wp_group' where id='$id'";
    $result = mysql_query2($query); 
    // redirect
    ?><SCRIPT language="javascript">
          document.location = "index.php?page=listwaypoints&sector=<?PHP echo $area; ?>";
       </script>
    <?PHP

} elseif ($operation == 'create'){
    $id = $_POST['id'];
    $sector = $_POST['sector'];
    $name = $_POST['name'];
    $loc_x = $_POST['x'];
    $loc_y = $_POST['y'];
    $loc_z = $_POST['z'];
    $radius = $_POST['radius'];
    $wp_group = $_POST['wp_group'];
    // insert script
    $query = "insert into waypoints set loc_sector_id=$sector, x='$loc_x',y='$loc_y',z='$loc_z',radius='$radius',name='$name',wp_group='$wp_group'";
    $result = mysql_query2($query); 
    // redirect
    ?><SCRIPT language="javascript">
          document.location = "index.php?page=listwaypoints&sector=<?PHP echo $area; ?>";
       </script>
    <?PHP
} elseif ($operation == 'delete'){
    $id = $_POST['id'];
    // insert script
    $query = "delete from waypoints  where id='$id'";
    $result = mysql_query2($query); 
    // redirect
    ?><SCRIPT language="javascript">
          document.location = "index.php?page=listwaypoints&sector=<?PHP echo $area; ?>";
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
