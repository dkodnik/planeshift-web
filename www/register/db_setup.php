<?PHP
/*
 * db_setup.php - Author: Greg von Beck
 *
 * Copyright (C) 2001 PlaneShift Team (info@planeshift.it,
 * http://www.planeshift.it)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation (version 2 of the License)
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * Creation Date : 10/6/03
 * Description : This page provides the db info to pages that need to access it.
 */
?>
<?PHP
include("../secure/db_config.php");

// function used to execute queries
function ExecQuery($query)
{
$result = mysql_query($query);

if(mysql_errno() != 0)
{
    if(mysql_errno() == 1062)
    {
        $own = substr($_SERVER['REQUEST_URI'],strrpos($_SERVER['REQUEST_URI'],"/")+1,strlen($_SERVER['REQUEST_URI']));
        if(strpos($own,"rocess") > 0)
        {
            $own = substr($own,7,strlen($own));
        }
        
        header("Location: ./$own?error=email");   
        exit();
    }
    if(mysql_errno() == 1064)
    {
        $own = substr($_SERVER['REQUEST_URI'],strrpos($_SERVER['REQUEST_URI'],"/")+1,strlen($_SERVER['REQUEST_URI']));
        if(strpos($own,"rocess") > 0)
        {
            $own = substr($own,7,strlen($own));
        }
       
        header("Location: ./$own?error=db");   
        exit();        
    }
    exit("A unhandled database error ocurred!\nPlease tell us about this on info@planeshift.it");
}   

return $result;  
}
?>
