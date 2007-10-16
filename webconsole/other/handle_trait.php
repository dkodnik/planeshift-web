<?PHP
include ("../../secure/db_config.php");
include ('../commonfunctions.php');
include('../util.php');

$link = mysql_connect($db_hostname, $db_username, $db_password);
mysql_select_db($db_name, $link);

$race_id  = $_POST['race_id'];
$trait_id = $_POST['trait_id'];
$name     = $_POST['trait_name'];
$only_npc = $_POST['only_npc'];
$mat      = $_POST['cstr_id_material'];
$delete   = $_POST['delete_box'];
$action   = $_POST['action'];
$cstr_id_texture = $_POST['cstr_id_texture'];
$area     = $_POST['area'];
$shader   = $_POST['shader'];

//echo "$name $trait $only_npc $mat $delete";

if ( $delete == 'on' )
{
    $query = "DELETE FROM traits WHERE id=$trait_id";
    mysql_query($query);    
}

if ( $action == "update" )
{
   
    switch ( $area )
    {
        case "FACE":
        {
            $query = "UPDATE traits SET only_npc=$only_npc, name='$name', cstr_id_material=$mat, cstr_id_texture=$cstr_id_texture WHERE id=$trait_id";
            break;
        }

        case "HAIR_COLOR":
        {
            $query = "UPDATE traits SET only_npc=$only_npc, name='$name', shader='$shader' WHERE id=$trait_id";
            break; 
        }
    }

    mysql_query($query);    
}

else if ( $action == "new" )
{
    $cstr_id_texture;
    
    $query = "SELECT id FROM common_strings where string='Head'";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_NUM);
    $cstr_id_mesh = $row[0];



    switch ( $area )
    {
        case "FACE":
        {
            $query = "INSERT INTO  traits(next_trait, 
                                  race_id,
                                  only_npc,
                                  location, 
                                  name, 
                                  cstr_id_mesh, 
                                  cstr_id_material, 
                                  cstr_id_texture) 
                                VALUES(  '-1', 
                                         $race_id, 
                                         $only_npc, 
                                         'FACE',
                                          '$name',
                                          $cstr_id_mesh, 
                                          $mat, 
                                          $cstr_id_texture)";
            break;
        }

        case "HAIR_COLOR":
        {
           $query = "INSERT INTO  traits(next_trait, 
                                  race_id,
                                  only_npc,
                                  location, 
                                  name, 
                                  shader ) 
                                VALUES(  '-1', 
                                         $race_id, 
                                         $only_npc, 
                                         'HAIR_COLOR',
                                          '$name',
                                          '$shader')";
  
            break;
        }

    }
    mysql_query($query);
}

        
Header("Location: ../index.php?page=list_traits&function=list&race_id=$race_id");

?>
