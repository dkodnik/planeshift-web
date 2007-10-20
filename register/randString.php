<?PHP
/*
 * .php - Author: Greg von Beck
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
 * Description : This page creates random stings of a specified length
 */
?>
<?PHP
function randString($length=32)
{
    $newstring="";
    if($length>0)
    {
        while(strlen($newstring)<$length)
        {
            $randnum = mt_rand(0,61);
            if ($randnum < 10)
            {
	        $newstring.=chr($randnum+48);
	    }
            elseif ($randnum < 36)
            {
	        $newstring.=chr($randnum+55);
	    }
            else
            {
	        $newstring.=chr($randnum+61);
            }
        }
    }

    return $newstring;
}
?>
