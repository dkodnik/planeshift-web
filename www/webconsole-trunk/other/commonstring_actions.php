<?
function commonstring_actions(){
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
	$string = $_POST['string'];

	// insert script
	$query = "update common_strings set string='$string' where id='$id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=list_commonstrings";
       </script>
    <?PHP

}else if ($operation == 'add'){
	$id = $_POST['id'];
        for ( $counter = 1; $counter <= 10; $counter ++) {  
	   $pos = "string".$counter;
	   $string = $_POST[$pos];
           if ($string != ''){
	      // insert script
	      $query = "insert into common_strings (string) values ('$string')";
	      $result = mysql_query2($query); 
           }
        }
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=list_commonstrings";
       </script>
    <?PHP

}else if ($operation == 'delete'){
	$id = $_POST['id'];
	// insert script
	$query = "delete from common_strings where id='$id'";
	$result = mysql_query2($query); 
	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=list_commonstrings";
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
