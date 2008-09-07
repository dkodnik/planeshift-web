<?
function searchnpc(){
  include('util.php');

  checkAccess('listnpc', '', 'read');

  $id = $_POST['id'];
  $name = $_POST['name'];
  $sector = $_POST['sector'];
  if ($id!=null and $id!="") {
    $query = "select id from characters where id=$id";
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $npcid = $line[0];
    
    if ($npcid != 0) {
		// redirect
		?><SCRIPT language="javascript">
          document.location = "index.php?page=viewnpc&id=<?=$npcid?>";
       </script>
    <?PHP
    } else {
      echo "NOT FOUND!";
    }

  } else if ($name!=null and $name!="") {
    if (strstr($name,"*")) {
	    $name = str_replace("*","%",$name);
	    if ($name[0] == "%") {
		    $name = substr($name, 1);
	    }

      $query = "select id from characters where name like '$name'";
    } else {
      $query = "select id from characters where name='$name'";
    }
    $result = mysql_query2($query);
    $line = mysql_fetch_array($result, MYSQL_NUM);
    $npcid = $line[0];
    
    if ($npcid != 0) {
		// redirect
		?><SCRIPT language="javascript">
          document.location = "index.php?page=viewnpc&id=<?=$npcid?>";
       </script>
    <?PHP
    } else {
      echo "NOT FOUND!";
    }

  } else if ($sector!=null and $sector!="") {
		// redirect
		?><SCRIPT language="javascript">
          document.location = "index.php?page=listnpcsinv&sector=<?=$sector?>";
       </script>
    <?PHP

  }


  echo "  <FORM action=\"index.php?page=searchnpc\" METHOD=POST>";
  echo "  ID: <INPUT type=text name=id><br>";
  echo "  Name: <INPUT type=text name=name> (you can use wildcard *)<br> Sector: ";
  SelectSectors("","sector");
  echo " <br><br><INPUT type=submit value=search>";
  
  echo "</FORM>";

}

?>

      
      
      

