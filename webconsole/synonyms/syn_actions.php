<?PHP
function syn_actions(){ 
	// gets operation to perform
	$operation = $_GET['operation'];

	/**
	 * delete a synonym
	 */
	if ($operation == 'delete'){
		$word = $_POST['word'];
		$syn = $_POST['syn'];

		$query = "delete from npc_synonyms where word='$word' and synonym_of='$syn'";
		$result = mysql_query2($query); 
		// redirect
		?><SCRIPT language="javascript">
          document.location = "index.php?page=listsynonyms";
       </script>
    <?PHP

		/**
		 * add a synonym
		 */
	}else if ($operation == 'add'){
		$word = $_POST['word'];
		$syn = $_POST['syn'];
		$moregen = $_POST['moregen'];
		if(!empty($moregen))
			$syn="";

		$query = "insert into npc_synonyms values('$word','$syn','$moregen')";
		$result = mysql_query2($query); 
		// redirect
		?><SCRIPT language="javascript">
          document.location = "index.php?page=listsynonyms";
       </script>
    <?PHP

		/**
		 * delete ALL synonyms
		 */
	}else if ($operation == 'delall'){
		$query = "delete from npc_synonyms";
		$result = mysql_query2($query); 
		// redirect
		?><SCRIPT language="javascript">
          document.location = "index.php?page=listsynonyms";
       </script>
    <?PHP

		/**
		 * load synonyms from file
		 */
	}else if ($operation == 'uploadfile'){
		if(is_uploaded_file($_FILES['file']['tmp_name'])){
			$lines = file($_FILES['file']['tmp_name']);
			unlink($_FILES['file']['tmp_name']);

			$count = 0;
			foreach($lines as $num => $line){
				$tok1 = strtok($line, ",");
				$tok2 = strtok(",");
				if ($tok2 == ''){
					echo "invalid line: $line";
					return;
				}
				// escape ' chars
				$tok1esc = str_replace("'", "\\'", $tok1);
				$tok2esc = str_replace("'", "\\'", $tok2);
				// remove end of line
				$tok1esc = str_replace("\r","", $tok1esc);
				$tok2esc = str_replace("\r","", $tok2esc);
				$tok1esc = str_replace("\n","", $tok1esc);
				$tok2esc = str_replace("\n","", $tok2esc);
				$query = "insert into npc_synonyms values('$tok1esc','$tok2esc')";
				$result = mysql_query2($query);
				$count++;
			}

			echo "$count synonyms inserted";
		}else{
			echo "File not valid";
		}
	}else{ 
		// manage another operation here
		echo "Operation $operation not supported.";
	}
}

?>