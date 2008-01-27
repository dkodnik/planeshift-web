<?PHP
//--------------------------------

//---for names mapping look at the end of the file 

//-------------------------------

function update(){
	/**
	 * edit an action location
	 */
	$id = $_GET['id'];
	$sector = $_GET['sector'];
	$masterid = $_POST['masterid'];
    $name = $_POST['name'];
    $meshname = $_POST['meshname'];
    $polygon = $_POST['polygon'];
    $radius = $_POST['radius'];
    $triggertype = $_POST['triggertype'];
    $responsetype = $_POST['responsetype'];
    $response = $_POST['response'];

	$query = "update action_locations set master_id=$masterid, name='$name', meshname='$meshname',  polygon=$polygon, radius=$radius,";
	$query = $query . " triggertype='$triggertype', responsetype='$responsetype', response='$response' where id=$id";
	$result = mysql_query2($query);

	// redirect
	?><SCRIPT language="javascript">
          document.location = "index.php?page=descworld&sector=<?=$sector?>";
       </script>
    <?PHP
}

/*
*--------------------------------------------*

*--------------------------------------------*

*--------------------------------------------*
*/

function descworldactions(){

	echo'<body  >'; 
	// gets operation to perform
	$operation = $_GET['operation'];

	if ($operation == 'update'){
		update();
	}else if ($operation == 'delete'){
		delete();
	}else{ 
		// manage another operation here
		echo "Operation $operation not supported.";
	}

	echo'</body>';
}

?>
