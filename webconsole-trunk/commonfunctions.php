<?PHP
function mysql_query2($a){

	$result = mysql_query($a) or die(mysql_error());
	return $result;
}

//-----------------------------------------------------------------------------

function extract_tags($tagged_text) {

    $numkey = 0;
    do {
        switch (strpos($tagged_text, '<')) {
            case 0:
                $strpart = substr($tagged_text, 0, strpos($tagged_text, '>') + 1);
                $tagged_text = substr($tagged_text, strpos($tagged_text, '>') + 1);
                $tags_n_text[$numkey] = $strpart;
                break;

            case (FALSE):
                break;

            case (! 0):
                $strpart = substr($tagged_text, 0, strpos($tagged_text, '<'));
                $tagged_text = substr($tagged_text, strpos($tagged_text, '<'));
                $tags_n_text[$numkey] = $strpart;
                break;
            }
        $numkey++;
        } while (strstr($tagged_text, '<') !== false);
    return $tags_n_text;
    }

//------------------------------------------------------------------------------

function DrawSelectBox($type, $selectName, $selectedID, $includeNULL = false) {

    $base_item_max = 10000;

// $type is a string that determines what values are used in the query and the
// <SELECT> block.  Later, we can add more possible $type values for more
// situations where appropriate.  To minimize typographical errors, the
// following line converts the value of $type to lowercase.

    $type = strtolower($type);

// We will create an array within the next lines that defines the values
// that are used in the query and the <SELECT> block.

    $typevals["item"] = array("NULL" => '""', "query" => "SELECT id, name FROM item_stats WHERE id < $base_item_max ORDER BY name");
    $typevals["skill"] = array("NULL" => '"-1"', "query" => "SELECT skill_id, name FROM skills ORDER BY name");
    $typevals["itemcat"] = array("NULL" => '"-1"', "query" => "SELECT category_id, name FROM item_categories");
    $typevals["icon"] = array("NULL" => '"0"', "query" => "SELECT MIN(id), string FROM common_strings WHERE common_strings.string Like '%_icon.dds' GROUP BY id");
    $typevals["mesh"] = array("NULL" => '"0"', "query" => "SELECT MIN(id), string FROM common_strings WHERE common_strings.string Like '%#%' GROUP BY id");

// Now the $typevals array contains (five) smaller arrays.  We'll breakout the
// values from the $typevals array and use these to determine how the rest
// of the function plays out.

    $nullval = $typevals[$type]["NULL"];
    $query = $typevals[$type]['query'];

    $result = mysql_query2($query);

    echo '<SELECT name=' . $selectName . '>';
  
    if ($includeNULL == true)
        echo '<OPTION value=' . $nullval . '>NONE</OPTION>';
    
    while ($row = mysql_fetch_row($result)) {
        echo '<OPTION value="' . $row[0] . '"';
        if ($selectedID == $row[0])
            echo " SELECTED";
        echo '>' . $row[1] . '</OPTION>';
        }
    echo "</SELECT>";

    }

//------------------------------------------------------------------------------

function DrawItemSelectBox($selectName, $selectedID, $includeNULL = false) {

    DrawSelectBox("item", $selectName, $selectedID, $includeNULL);

    }

function DrawSkillSelectBox($selectName, $selectedID, $includeNULL = false) {

    DrawSelectBox("skill", $selectName, $selectedID, $includeNULL);

    }

function ItemCategorySelect($selectName) {

    DrawSelectBox("itemcat", $selectName, NULL, true);

    }

function CreateSkillOptionList($selectName) {

    DrawSelectBox("skill", $selectName, NULL, true);

    }

//-----------------------------------------------------------------------------

function GetItemName($item_id) {

    $query = 'SELECT name FROM item_stats WHERE id = ' . $item_id;
    $result = mysql_query2($query);
    $row = mysql_fetch_assoc($result);
    return $row['name'];

    }

function GetSkillName($skill_id) {

    $query = 'SELECT name FROM skills WHERE skill_id = ' . $skill_id;
    $result = mysql_query2($query);
    $row = mysql_fetch_assoc($result);
    return $row['name'];

    }

//-----------------------------------------------------------------------------

function FlagCheckArea($name) {

    echo '<TABLE>';
    echo '<TR>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="MELEEWEAPON">MELEEWEAPON</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="ARMOR">ARMOR</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="RANGEWEAPON">RANGEWEAPON</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="SHIELD">SHIELD</TD>';
    echo '</TR>';
    echo '<TR>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="CONTAINER">CONTAINER</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="CANTRANSFORM">CANTRANSFORM</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="USESAMMO">USESAMMO</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="STACKABLE">STACKABLE</TD>';
    echo '</TR>';
    echo '<TR>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="TRIA">TRIA</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="HEXA">HEXA</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="OCTA">OCTA</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="CIRCLE">CIRCLE</TD>';
    echo '</TR>';
    echo '<TR>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="AMMO">AMMO</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="GLYPH">GLYPH</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="CONSUMABLE">CONSUMABLE</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="READABLE">READABLE</TD>';
    echo '</TR>';
    echo '</TABLE>';        

    }


function ValidSlotCheckArea($name) {

    echo '<TABLE>';
    echo '<TR>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="BULK">BULK</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="RIGHTHAND">RIGHTHAND</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="LEFTHAND">LEFTHAND</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="HEAD">HEAD</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="RIGHTFINGER">RIGHTFINGER</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="LEFTFINGER">LEFTFINGER</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="NECK">NECK</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="BACK">BACK</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="ARMS">ARMS</TD>';
    echo '<TR>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="GLOVES">GLOVES</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="BOOTS">BOOTS</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="LEGS">LEGS</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="BELT">BELT</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="BRACERS">BRACERS</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="TORSO">TORSO</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="MIND">MIND</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="BANK">BANK</TD>';
    echo '</TR>';
    echo '<TR>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="CRYSTAL">CRYSTAL</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="AZURE">AZURE</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="RED">RED</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="DARK">DARK</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="BROWN">BROWN</TD>';
    echo '<TD><INPUT TYPE=CHECKBOX NAME="BLUE">BLUE</TD>';
    echo '</TR>';
    echo '</TABLE>';

    }

//-----------------------------------------------------------------------------

function getNextId($table, $field) {

    $query = "SELECT max($field) FROM $table";
    $result = mysql_query2($query_string);
    $row = mysql_fetch_array($result, MYSQL_NUM);
    $newid = $row[0] + 1;
    return $newid;

    }

/**----------------------------------------------------------------------------
 * Use to validate login and check access rules                              *
 * MUST be called by any page                                                *
 * objecttype: can be a page name like 'main' or an objecttype like 'npc'    *
 * arrayattrs: is the array of field values extracted from db for the object *
 *      inspected                                                            *
 * operation: can be read, create, edit, delete                              *
----------------------------------------------------------------------------**/

function checkAccess($objecttype, $arrayattrs, $operation) {

    if ($_SESSION['loggedin'] != 'yes') {
        header("Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php?goto=" . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']));
        exit;
	}

    $usersec_level = $_SESSION['sec_level'];

// Security 0 can never access.

    if ($usersec_level == '0'){
        echo "<HTML><BODY><H1>Not Authorized</H1>You are not authorized to view this page.  If you believe you are getting this message in error, contact your server's administrator.</BODY></HTML>";
        exit;
	}

// Admin (security 50) can always access.

    if ($usersec_level==50)
        return;

    $usersec_level_class = substr($usersec_level, 0, 1);
    $usersec_level_dept = substr($usersec_level, 1, 1);

// Check access based on $function.

    $function = "a_" . $operation; 

// Check if object type is known.

    $query = "select objecttype, fieldname, fieldvalue, $function from accessrules where objecttype='$objecttype' order by $function";

    $result = mysql_query2($query);
    $rows = mysql_num_rows($result); 

// Deny access if no rule is present.

    if ($rows == 0 && $usersec_level != 50){
        echo '<HTML><BODY><H1>Access Rule Not Found</H1>Access rules to this feature ("' . $objecttype . '", Access type ' . $operation . ') could not be located.<br>To access this page you need to have accesslevel 50.</BODY></HTML>';
        exit;
        }
	
// Consider all rules.
    $canAccess = true;
    $sec_level = "";
    while ($row = mysql_fetch_row($result)) {
        $sec_level_class = substr($row[3], 0, 1);
        $sec_level_dept = substr($row[3], 1, 1);
        $canAccess = sec_check_class($usersec_level_class, $sec_level_class);
        $canAccess = sec_check_dept($usersec_level_dept, $sec_level_dept);
        }

    if ($canaccess == false) {
        echo '<HTML><BODY><H1>Operation Denied</H1>You do not have the requisite access rights to perform this operation.</BODY></HTML>';
        exit;
        }
    }

function sec_check_class($usersec_level_class, $sec_level_class) {
    $canAccess = true;
    if ($sec_level_class > $usersec_level_class)
        $canAccess = false;
    return $canAccess;
    }

function sec_check_dept($usersec_level_dept, $sec_level_dept) {
    $canAccess = true;
    if ($sec_level_dept != 0 || $sec_level_dept != $usersec_level_dept)
        $canAccess = false;
    return $canAccess;

    }


function outputHtmlHeader(){
	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<HTML>
    <HEAD>
        <TITLE>PlaneShift - Administrator console</TITLE>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
    </HEAD>
    <BODY BGCOLOR=#052F2E text="#57B9CB" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">
        <TABLE>
            <TR> 
                <TD> 
<?php
    $page = $_GET['page'];
    $category = $_GET['category'];
    if ($page != '' && $page != 'logout')
        echo "<P><A HREF=\"index.php?category={$category}\">Back to Server Console Index</A></P>";

    if ($page != '' && $category != '')
        echo "<P><A HREF=\"index.php?category={$_GET['category']}\">Back to {$_GET['category']}</A></P>";

    }

function outputHtmlFooter() {

    $page = $_GET['page'];
    $category = $_GET['category'];
    if ($page != '' && $page != 'logout')
        echo "<P><A HREF=\"index.php?category={$category}\">Back to Server Console Index</A></P>";

?>
                </TD>
            </TR>
        </TABLE>
    </BODY>
</HTML><?php

    }

// function getNextQuarterPeriod($groupid) {

//    $query = "select max(periodname) from statistics where groupid=".$groupid;
//    $result = mysql_query2($query);
//
//    while ($row = mysql_fetch_array($result, MYSQL_NUM))
//    {
//        $periodname = $row[0];
//    }
//    
//	if ($periodname=="")
//		$periodname="2004 Q3"; // we start from this date
//
//	$year = substr($periodname,0,4);
//	$quarter = substr($periodname,6,6);
//	
//	if ($quarter=="4") {
//		$year = $year +1;
//		$quarter = "1";
//	} else
//		$quarter = $quarter + 1;
//
//	return $year." Q".$quarter;
// }

?>
