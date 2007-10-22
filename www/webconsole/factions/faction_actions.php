<?
function faction_actions(){
?>
<HTML>
<BODY>


<?PHP


    checkAccess('main', '', 'read');
    
    // gets operation to perform
    $operation = $_GET['operation'];

    /**
      * update script
      */
    if ($operation == 'update'){
        $id = $_POST['id'];
        $weight = $_POST['weight']; 
        // insert script
        $query = "update factions set faction_weight='$weight' where id='$id'";
        $result = mysql_query2($query); 
        // redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=listfactions";
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
