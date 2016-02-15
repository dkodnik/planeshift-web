<?php
function npc_search()
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    if (isset($_POST['commit']))
    {
        // this function uses a session variable, because listnpcs does not keep track of our search paramters.
        if (isset($_SESSION['searchstring'])) 
        {
            unset($_SESSION['searchstring']);
        }
        // notice the following cases use functionality from listnpcs, they should all call that function to show results.
        include('./npcs/listnpcs.php');
        $string = (is_numeric($_POST['char_type']) ? "WHERE c.character_type='{$_POST['char_type']}'" : "WHERE c.character_type='1'");
        if ($_POST['pid'] != '')
        {
            $pid = escapeSqlString($_POST['pid']);
            $string .= " AND c.id='$pid'";
            listnpcs('', $string);
            $_SESSION['searchstring'] = $string;
        }
        else if ($_POST['name'] != '')
        {
            $name = escapeSqlString($_POST['name']);
            $like = (strpos($name, '*') === false ? '=' : ' LIKE ');  // for use in the sql query.
            $name = str_replace('*', '%', $name);
            $nameParts = explode(' ', $name);
            // in case of only one name, this will be the same as firstName.
            $lastName = escapeSqlString($nameParts[count($nameParts) - 1]);
            // if there are both first and last names, assign all except the last to "firstName", otherwise (one name only), assign the same to both.
            $firstName = escapeSqlString(count($nameParts) > 1 ? substr($name, 0, -(strlen($lastName) + 1)) : $name);
            
            if (count($nameParts) > 1) 
            {
                $string .= " AND ((c.name$like'$firstName' AND c.lastname$like'$lastName') OR c.name$like'$firstName $lastName')";
            }
            else
            {
                $string .= " AND (c.name$like'$firstName' OR c.lastname$like'$lastName')";
            }
            listnpcs('', $string);
            $_SESSION['searchstring'] = $string;
        }
        else if ($_POST['sectorid'] != '')
        {
            $sec = escapeSqlString($_POST['sectorid']);
            $string .= " AND c.loc_sector_id='$sec'";
            listnpcs('', $string);
            $_SESSION['searchstring'] = $string;
        }
        else if ($_POST['raceid'] != '')
        {
            $raceid = escapeSqlString($_POST['raceid']);
            $string .= " AND r.id='$raceid'";
            listnpcs('', $string);
            $_SESSION['searchstring'] = $string;
        }
        else
        {
            echo '<p class="error">Invalid post values.</p>';
            return;
        }
    }
    else if(isset($_GET['sort']) && isset($_SESSION['searchstring']))
    {
        include('./npcs/listnpcs.php');
        listnpcs('', $_SESSION['searchstring']);
    }
    else
    {
        echo '<p class="bold">Only use one field to search<br/>Use * for WildCard</p>'."\n";
        echo '<form action="./index.php?do=searchnpc" method="post"><table>'."\n";
        echo '<tr><td>Search by PID: </td><td><input type="text" name="pid" /></td></tr>'."\n";
        echo '<tr><td>Search by Name: </td><td><input type="text" name="name" /></td></tr>'."\n";
        $sectors = PrepSelect('sectorid');
        echo '<tr><td>Locate by Sector: </td><td>' . DrawSelectBox('sectorid', $sectors, 'sectorid' , '', true). '</td></tr>'."\n";
        $races = PrepSelect('races');
        echo '<tr><td>Locate by Race: </td><td>'.DrawSelectBox('races', $races, 'raceid' , '', true).'</td></tr>'."\n";
        echo '<tr><td><input type="hidden" name="char_type" value="'.(isset($_GET['char_type']) ? $_GET['char_type'] : 1).'" />';
        echo '<input type="submit" name="commit" value="Search" /></td><td></td></tr>';
        echo '</table></form>';
    }
}
?>
