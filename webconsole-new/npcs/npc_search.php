<?
function npc_search(){
  if (checkaccess('npcs', 'read')){
    if (isset($_POST['commit'])){
      if (isset($_SESSION['searchstring'])){
        unset($_SESSION['searchstring']);
      }
      include('./npcs/listnpcs.php');
      if ($_POST['pid'] != ''){
        $pid = mysql_real_escape_string($_POST['pid']);
        $string = " AND c.id='$pid'";
        listnpcs(TRUE, $string);
        $_SESSION['searchstring']=$string;
      }else if ($_POST['name'] != ''){
        $name = explode(' ', $_POST['name'], 3);
        $name[0] = mysql_real_escape_string($name[0]);
        if (strpos($name[0], '*') === FALSE){
          $string = " AND c.name='$name[0]'";
        }else{
          $name[0] = str_replace('*', '%', $name[0]);
          $string = " AND c.name LIKE '$name[0]'";
        }
        if (isset($name[1])){
          $name[1] = mysql_real_escape_string($name[1]);
          if (strpos($name[1], '*') === FALSE){
            $string = $string . " AND c.lastname='$name[1]'";
          }else{
            $name[1] = str_replace('*', '%', $name[1]);
            $string = $string . " AND c.lastname LIKE '$name[1]'";
          }
        }
        listnpcs(TRUE, $string);
        $_SESSION['searchstring']=$string;
      }else if ($_POST['sectorid'] != ''){
        $sec = mysql_real_escape_string($_POST['sectorid']);
        $string = " AND c.loc_sector_id='$sec'";
        listnpcs(TRUE, $string);
        $_SESSION['searchstring']=$string;
      }else{
      }
    }else if(isset($_GET['sort']) && isset($_SESSION['searchstring'])){
      include('./npcs/listnpcs.php');
      listnpcs(TRUE, $_SESSION['searchstring']);
    }else{
      echo '<p class="bold">Only enter one field to search by<br/>Use * for WildCard</p>';
      echo '<form action="./index.php?do=searchnpc" method="post"><p>';
      echo 'Search by PID: <input type="text" name="pid" /><br/>';
      echo 'Search by Name: <input type="text" name="name" /><br/>';
      $Sectors = PrepSelect('sectorid');
      echo 'Locate By Sector:' . DrawSelectBox('sectorid', $Sectors, 'sectorid' , '', TRUE). '<br/>';
      echo '<input type="submit" name="commit" value="Search" />';
      echo '</p></form>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
