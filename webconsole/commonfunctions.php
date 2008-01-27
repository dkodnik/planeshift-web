<?PHP
function mysql_query2($a){

	$result = mysql_query($a) or die(mysql_error());
	return $result;
}

function DrawItemSelectBox( $name, $selectedID, $includeNULL = false )
{
    $base_item_max = 10000;
    $query = "select id,name from item_stats where id<$base_item_max ORDER BY name";
    $result = mysql_query2($query);

    echo "<SELECT name=$name>";
  
    if ( $includeNULL == true )      
        echo "<OPTION value=''>NONE</OPTION>";
    
    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
        $selected = "";
        if ( $selectedID == $line[0] )
            $selected = "SELECTED";
        echo "<OPTION value=$line[0] $selected>$line[1]</OPTION>";
    }
    
    echo "</SELECT>";
}

//-----------------------------------------------------------------------------

function DrawSkillSelectBox( $selectName, $selectedID, $includeNULL = false )
{
    $query = 'select skill_id,name from skills ORDER BY name';
    $result = mysql_query2($query);

    echo "<SELECT name=$selectName>";
  
    if ( $includeNULL == true )      
        echo "<OPTION value='-1'>NONE</OPTION>";
    
    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
        $selected = "";
        if ( $selectedID == $line[0] )
            $selected = "SELECTED";
        echo "<OPTION value=$line[0] $selected>$line[1]</OPTION>";
    }
    
    echo "</SELECT>";
}

//-----------------------------------------------------------------------------


function GetItemName( $item_id )
{
    $sql= "select name from item_stats where id =$item_id";    
    $line = mysql_fetch_array(mysql_query2($sql));
    return $line[0];
}

function GetSkillName( $skill_id )
{
    $sql= "select name from skills where skill_id =$skill_id";    
    $line = mysql_fetch_array(mysql_query2($sql));
    return $line[0];
}

/** Creates a select list for all the possible item categories.
 */
function ItemCategorySelect( $name )
{
    $query = 'select category_id, name from item_categories ';
    $result = mysql_query2($query);

    echo "<SELECT name=$name>";
    echo "<OPTION value=-1>NONE</OPTION>";
    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
        echo "<OPTION value=$line[0]>$line[1]</OPTION>";
    }
}

/** Creates a check box area for the valid flags an item can have.
*/
function FlagCheckArea( $name )
{
    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=4>";
    echo "<TR>";
    echo "<TD><INPUT TYPE=CHECKBOX NAME=MELEEWEAPON>MELEEWEAPON</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=ARMOR>ARMOR</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=RANGEWEAPON>RANGEWEAPON</TD>";        
    echo "<TD><INPUT TYPE=CHECKBOX NAME=SHIELD>SHIELD</TD>";    
    echo "</TR>";
    echo "<TR>";
    echo "<TD><INPUT TYPE=CHECKBOX NAME=CONTAINER>CONTAINER</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=CANTRANSFORM>CANTRANSFORM</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=USESAMMO>USESAMMO</TD>";        
    echo "<TD><INPUT TYPE=CHECKBOX NAME=STACKABLE>STACKABLE</TD>";    
    echo "</TR>";
    echo "<TR>";
    echo "<TD><INPUT TYPE=CHECKBOX NAME=TRIA>TRIA</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=HEXA>HEXA</TD>";            
    echo "<TD><INPUT TYPE=CHECKBOX NAME=OCTA>OCTA</TD>";        
    echo "<TD><INPUT TYPE=CHECKBOX NAME=CIRCLE>CIRCLE</TD>";    
    
    echo "</TR>";
    echo "<TR>";
    echo "<TD><INPUT TYPE=CHECKBOX NAME=AMMO>AMMO</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=GLYPH>GLYPH</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=CONSUMABLE>CONSUMABLE</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=READABLE>READABLE</TD>";        
    echo "</TR>";
    echo "</TABLE>";        
        
}


function ValidSlotCheckArea( $name )
{
    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=4>";
    echo "<TR>";
    echo "<TD><INPUT TYPE=CHECKBOX NAME=BULK>BULK</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=RIGHTHAND>RIGHTHAND</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=LEFTHAND>LEFTHAND</TD>";        
    echo "<TD><INPUT TYPE=CHECKBOX NAME=HEAD>HEAD</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=RIGHTFINGER>RIGHTFINGER</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=LEFTFINGER>LEFTFINGER</TD>";        
    echo "<TD><INPUT TYPE=CHECKBOX NAME=NECK>NECK</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=BACK>BACK</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=ARMS>ARMS</TD>";        
    echo "<TR>";
    echo "<TD><INPUT TYPE=CHECKBOX NAME=GLOVES>GLOVES</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=BOOTS>BOOTS</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=LEGS>LEGS</TD>";        
    echo "<TD><INPUT TYPE=CHECKBOX NAME=BELT>BELT</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=BRACERS>BRACERS</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=TORSO>TORSO</TD>";            
    echo "<TD><INPUT TYPE=CHECKBOX NAME=MIND>MIND</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=BANK>BANK</TD>";    
    echo "</TR>";
    echo "<TR>";
    echo "<TD><INPUT TYPE=CHECKBOX NAME=CRYSTAL>CRYSTAL</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=AZURE>AZURE</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=RED>RED</TD>";        
    echo "<TD><INPUT TYPE=CHECKBOX NAME=DARK>DARK</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=BROWN>BROWN</TD>";    
    echo "<TD><INPUT TYPE=CHECKBOX NAME=BLUE>BLUE</TD>";        
    echo "</TR>";
    
    echo "</TABLE>";
}


function CreateSkillOptionList( $selectName )
{
    $query = 'select skill_id,name from skills ';
    $result = mysql_query2($query);

    echo '<SELECT name=$selectName>';
    echo "<OPTION value=-1>NONE</OPTION>";
    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
        echo "<OPTION value=$line[0]>$line[1]</OPTION>";
    }
}



function getNextId($table, $field){
	$query_string = "select max($field) from $table";
	$result = mysql_query2($query_string);
	$line = mysql_fetch_array($result, MYSQL_NUM);
	$newid = $line[0] + 1;

	return $newid;
}
/**
 * //unused
 * function writeHead($name)
 * {
 * ?>
 * <HEAD>
 * <Title>PlaneShift ServerConsole - <?=$name?></title>
 * </head>
 * <?PHP
 * }
 */

/**
 * Use to validate login and check access rules
 * MUST be called by any page
 * objecttype: can be a page name like 'main' or an objecttype like: npc
 * arrayattrs: is the array of field values extracted from db for the object inspected
 * operation: can be read, create, edit, delete
 */
function checkAccess($objecttype, $arrayattrs, $operation){
	if($_SESSION['loggedin'] != 'yes'){
		header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php?goto=" . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']));
		exit();
	}
	// if security level is 0, exit
	$usersec_level = $_SESSION['sec_level'];
	$usersec_level_class = substr($usersec_level, 0, 1);
	$usersec_level_dept = substr($usersec_level, 1, 1);

	if ($usersec_level == '0'){
		echo "<HTML>YOU CAN'T ACCESS THIS PAGE!!! You access level is 0</HTML>";
		exit();
	}

    // admin (security 50) can always access
    if ($usersec_level==50)
      return;


	/**
	 * * check access based on function *
	 */
	$function = "a_" . $operation; 
	// check if object is known
	//echo "$objecttype <br>";
	$query = "select objecttype, fieldname, fieldvalue, $function from accessrules where objecttype='$objecttype' order by $function";
	$result = mysql_query2($query);

	$rows = mysql_num_rows($result); 
	// negate access if no rule is present and not admin
	if ($rows == 0 && $usersec_level != 50){
		echo "<HTML>Access rules to this page ('$objecttype', Access type $operation) couldn't be located.<br>To access this page you need to have accesslevel 50</HTML>";
		exit();
	} 
	
	// consider all rules
	$canAccess = false;
	$sec_level = "";
	while ($line = mysql_fetch_array($result, MYSQL_NUM)){
		$sec_level_class = substr($line[3], 0, 1);
		$sec_level_dept = substr($line[3], 1, 1); 
		// if sec lev required is greater than the one of the user
		if($sec_level_class > $usersec_level_class){ 
			// store the max sec level required
			if ($sec_level == '' or $sec_level < $line[3]){
				$sec_level = $line[3];
			}
			$canAccess = false; 
			// if sec lev required is less than  or equal to the one of the user
		}else if($sec_level_class <= $usersec_level_class){
			$canAccess = true;
		}

		if (!$canAccess){
			echo "<HTML>Access to this page is restricted.<br>";
			echo "Your access level $usersec_level_class is not enough.<br>";
			echo "You need atleast access level $sec_level_class .</HTML>";
			exit();
		} 
		// now check department
		$canAccess = false; 
		// rule for any dep
		if ($sec_level_dept == 0){
			$canAccess = true;
		}else{
			if ($sec_level_dept == $usersec_level_dept){
				$canAccess = true;
			}else{
				echo "<HTML>Access to this page is restricted.<br>";
				echo "Your access level $usersec_level_class is ok, but you have to be part of department $sec_level_dept .<br>";
				echo "</HTML>";
				exit();
			}
		}
	}
}

function outputHtmlHeader(){
	?>
<HTML>
	<HEAD>
		<TITLE>PlaneShift - Administrator console</TITLE>
		<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
	</HEAD>
<BODY BGCOLOR=#052F2E text="57B9CB" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">
<table border=0 width="997" border="0" cellpadding="0" cellspacing="0">
  <tr> 
  <td width="100%" valign="top"> 
            
	
<?php
	if ($_GET['page'] != '' && $_GET['page'] != 'logout'){
		echo "<p><A HREF='index.php'>Back to Server Console Index</A></p>";
	}
	if ($_GET['page'] != '' && $_GET['category'] !=  ''){
		echo "<p><A HREF='index.php?category={$_GET['category']}'>Back to {$_GET['category']}</A></p>";
	}
}

function outputHtmlFooter(){
	if ($_GET['page'] != '' && $_GET['page'] != 'logout'){
		echo "<p><A HREF='index.php'>Back to Server Console Index</A></p>";
	}
	?>          
            <div align="center"><font size="2" class="testo">Copyright &copy; 2001-2004 
              PlaneShift Team<br>
              All material in this site under <a href="http://www.planeshift.it/pslicense.html" target="right"><font color="#CC0000">PlaneShift 
              License</font></a></font> </div>
    </td>
  </tr>
</table>
</BODY>
</HTML>
<?PHP

}

/**
 * if($pageRequiresAdminAccess)
 * {
 * if(!$_SESSION['isAdmin'])
 * {
 * echo "FUNCTION BLOCKED";
 * exit();
 * }
 * }
 * 
 * if($pageRequiresLeaderAccess)
 * {
 * $leader_db_link = mysql_pconnect($_SESSION['db_hostname'],
 * $_SESSION['db_username'],
 * $_SESSION['db_password']);
 * 
 * mysql_select_db($_SESSION['db_name']);
 * 
 * $leader_query_string = "Select leader from users where uid = " . $_SESSION['uid'];
 * 
 * $leader_result = mysql_query($leader_query_string);
 * if(mysql_errno() != 0)
 * {
 * echo $leader_query_string . "\n<BR><BR>\n";
 * echo mysql_errno() . ": " . mysql_error();
 * exit();
 * }
 * $line = mysql_fetch_array($leader_result, MYSQL_ASSOC);
 * 
 * if(!$line['leader'] && !$_SESSION['isAdmin'])
 * {
 * echo "FUNCTION BLOCKED";
 * exit();
 * }
 * 
 * mysql_close($leader_db_link);
 * }
 */


function getNextQuarterPeriod($groupid) {

    $query = "select max(periodname) from statistics where groupid=".$groupid;
    $result = mysql_query2($query);

    while ($line = mysql_fetch_array($result, MYSQL_NUM))
    {
        $periodname = $line[0];
    }
    
	if ($periodname=="")
		$periodname="2004 Q3"; // we start from this date

	$year = substr($periodname,0,4);
	$quarter = substr($periodname,6,6);
	
	if ($quarter=="4") {
		$year = $year +1;
		$quarter = "1";
	} else
		$quarter = $quarter + 1;

	return $year." Q".$quarter;
}

?>
