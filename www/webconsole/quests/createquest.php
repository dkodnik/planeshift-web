<?PHP
function createquest(){

    checkAccess('quest', '', 'read');

    $type = $_GET['type']; 

       echo "<h3>Creation of a <b>script based</b> quest</h3><br>";
    	echo "<FORM action=index.php?page=questscript_actions METHOD=POST><b>Quest ID:</b> autogenerated <BR>";
    	echo "<b>Quest name:</b> <input type=text name=name size=60> <BR>";
    	echo "<b>Quest description: </b> <textarea name=description rows=5 cols=50></textarea><BR><BR>";
    	echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=createquest>";
    	echo "<INPUT TYPE=HIDDEN NAME=type VALUE=new>";
    	echo "<INPUT TYPE=SUBMIT NAME=submit VALUE=create>";
    	echo '</FORM>';
    	echo '<br><br>';
}

?>
