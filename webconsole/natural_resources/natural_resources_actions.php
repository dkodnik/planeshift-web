<?
function natural_resource_actions(){
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
    $sector = $_POST['sector'];
    $loc_x = $_POST['loc_x'];
    $loc_y = $_POST['loc_y'];
    $loc_z = $_POST['loc_z'];
    $radius = $_POST['radius'];
    $visible_radius = $_POST['visible_radius'];
    $probability = $_POST['probability'];
    $skill = $_POST['skill'];
    $skill_level = $_POST['skill_level'];
    $item_cat_id = $_POST['item_cat_id'];
    $item_quality = $_POST['item_quality'];
    $animation = $_POST['animation'];
    $anim_duration_seconds = $_POST['anim_duration_seconds'];
    $item_id_reward = $_POST['item_id_reward'];
    $reward_nickname = $_POST['reward_nickname'];
    // insert script
    $query = "update natural_resources set loc_sector_id=$sector, loc_x='$loc_x',loc_y='$loc_y',loc_z='$loc_z',radius='$radius',visible_radius='$visible_radius',probability='$probability',skill='$skill',skill_level='$skill_level',item_cat_id='$item_cat_id',item_quality='$item_quality',animation='$animation',anim_duration_seconds='$anim_duration_seconds',item_id_reward='$item_id_reward',reward_nickname='$reward_nickname' where id='$id'";
    $result = mysql_query2($query); 
    // redirect
    ?><SCRIPT language="javascript">
          document.location = "index.php?page=listnatural_resources";
       </script>
    <?PHP

} elseif ($operation == 'create'){
    $id = $_POST['id'];
    $sector = $_POST['sector'];
    $loc_x = $_POST['loc_x'];
    $loc_y = $_POST['loc_y'];
    $loc_z = $_POST[''];
    $radius = $_POST['radius'];
    $visible_radius = $_POST['visible_radius'];
    $probability = $_POST['probability'];
    $skill = $_POST['skill'];
    $skill_level = $_POST['skill_level'];
    $item_cat_id = $_POST['item_cat_id'];
    $item_quality = $_POST['item_quality'];
    $animation = $_POST['animation'];
    $anim_duration_seconds = $_POST['anim_duration_seconds'];
    $item_id_reward = $_POST['item_id_reward'];
    $reward_nickname = $_POST['reward_nickname'];
    // insert script
    $query = "insert into natural_resources set loc_sector_id=$sector, loc_x='$loc_x',loc_y='$loc_y',loc_z='$loc_z',radius='$radius',visible_radius='$visible_radius',probability='$probability',skill='$skill',skill_level='$skill_level',item_cat_id='$item_cat_id',item_quality='$item_quality',animation='$animation',anim_duration_seconds='$anim_duration_seconds',item_id_reward='$item_id_reward',reward_nickname='$reward_nickname'";
    $result = mysql_query2($query); 
    // redirect
    ?><SCRIPT language="javascript">
          document.location = "index.php?page=listnatural_resources";
       </script>
    <?PHP
} elseif ($operation == 'delete'){
    $id = $_POST['id'];
    // insert script
    $query = "delete from natural_resources  where id='$id'";
    $result = mysql_query2($query); 
    // redirect
    ?><SCRIPT language="javascript">
          document.location = "index.php?page=listnatural_resources";
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
