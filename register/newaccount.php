<?PHP
/*
 * newaccount.php - Author: Greg von Beck
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
 * Description : This page accepts info for a new account.
 */
?>

<?include("start.php");?>

<?PHP include "db_setup.php"?>

<script language="javascript" src="validationLibrary.js">
</script>

<script language=javascript>
function validate()
{
    // realname should not be empty
    if(!isString(document.createaccount.realname, "Real Name", true))
    {
        return false;
    }
    //verify email is in the correct format and is not empty
    else if(!isEmail(document.createaccount.email, "E-Mail", true))
    {
        return false;
    }
    //verify that email and email2 are the same so that we know the 
    //e-mail is correct
    else if(document.createaccount.email.value != document.createaccount.email2.value)
    {
        alert("E-Mail fields do not match!");
	document.createaccount.email2.focus();
        return false;
    }
    return true;
}
</script>
<br>
<form name="createaccount" action="processnewaccount.php" method="post" onsubmit="return validate()">
<table>
<?PHP

if(isset($_GET['error']))
{
    if ($_GET['error'] == "email") {
      echo "<TR><TD colspan=2><FONT color=RED><B>The email you selected is already in use.  Please select another.</B></FONT></TD></TR>";

    } elseif($_GET['error'] == "username")
    {
        echo "<TR><TD colspan=2><font color=red><B>The E-Mail you entered is already associated with an account.</B></FONT></TD></TR>";
    }
}
?>

<TR><TD colspan=2>Accounts in PlaneShift are free of charge. No one will ask you money for the creation of an account or for ANY other service related to PlaneShift.<br> PlaneShift staff will never ask your password via email.<br><br></TD></TR>

    <TR>
        <TH align=right>Real Name: </TH>
	<TD><input name=realname maxlength=30></td>
    </TR>
    <TR>
        <TH align=right>E-Mail: </TH>
	<TD><input name=email maxlength=255></td>
    </TR>
    <TR>
        <TH align=right>Verify E-Mail: </TH>
	<TD><input name=email2 maxlength=255></td>
    </TR>
    <TR>
        <TD colspan=2><HR></TD>
    </TR>
    <TR>
        <TD colspan=2>
	    The following data is optional and 
	    will be used for statistical purposes.<BR>
	    <BR>
	</TD>
    <TR>
        <TH align=right>Country</TH>
	<TD><Select name=country>
	  <?
	  // Print the countries
	  include("settings.php");
	  PrintCountries();
	  ?>

	</TD>
    </TR>
    <TR>
        <TH align=right>Gender</TH>
	<TD><Select name=gender>
    <option value="N"></option>
    <option value="M">Male</option>
		<option value="F">Female</option>
	</TD>
    </TR>
    <TR>
        <TH align=right>Year of birth</TH>
	<TD><Select name=age>
    <option value="N"></option>
<option value=1900>1900</option>
<option value=1901>1901</option>
<option value=1902>1902</option>
<option value=1903>1903</option>
<option value=1904>1904</option>
<option value=1905>1905</option>
<option value=1906>1906</option>
<option value=1907>1907</option>
<option value=1908>1908</option>
<option value=1909>1909</option>
<option value=1910>1910</option>
<option value=1911>1911</option>
<option value=1912>1912</option>
<option value=1913>1913</option>
<option value=1914>1914</option>
<option value=1915>1915</option>
<option value=1916>1916</option>
<option value=1917>1917</option>
<option value=1918>1918</option>
<option value=1919>1919</option>
<option value=1920>1920</option>
<option value=1921>1921</option>
<option value=1922>1922</option>
<option value=1923>1923</option>
<option value=1924>1924</option>
<option value=1925>1925</option>
<option value=1926>1926</option>
<option value=1927>1927</option>
<option value=1928>1928</option>
<option value=1929>1929</option>
<option value=1930>1930</option>
<option value=1931>1931</option>
<option value=1932>1932</option>
<option value=1933>1933</option>
<option value=1934>1934</option>
<option value=1935>1935</option>
<option value=1936>1936</option>
<option value=1937>1937</option>
<option value=1938>1938</option>
<option value=1939>1939</option>
<option value=1940>1940</option>
<option value=1941>1941</option>
<option value=1942>1942</option>
<option value=1943>1943</option>
<option value=1944>1944</option>
<option value=1945>1945</option>
<option value=1946>1946</option>
<option value=1947>1947</option>
<option value=1948>1948</option>
<option value=1949>1949</option>
<option value=1950>1950</option>
<option value=1951>1951</option>
<option value=1952>1952</option>
<option value=1953>1953</option>
<option value=1954>1954</option>
<option value=1955>1955</option>
<option value=1956>1956</option>
<option value=1957>1957</option>
<option value=1958>1958</option>
<option value=1959>1959</option>
<option value=1960>1960</option>
<option value=1961>1961</option>
<option value=1962>1962</option>
<option value=1963>1963</option>
<option value=1964>1964</option>
<option value=1965>1965</option>
<option value=1966>1966</option>
<option value=1967>1967</option>
<option value=1968>1968</option>
<option value=1969>1969</option>
<option value=1970>1970</option>
<option value=1971>1971</option>
<option value=1972>1972</option>
<option value=1973>1973</option>
<option value=1974>1974</option>
<option value=1975>1975</option>
<option value=1976>1976</option>
<option value=1977>1977</option>
<option value=1978>1978</option>
<option value=1979>1979</option>
<option value=1980>1980</option>
<option value=1981>1981</option>
<option value=1982>1982</option>
<option value=1983>1983</option>
<option value=1984>1984</option>
<option value=1985>1985</option>
<option value=1986>1986</option>
<option value=1987>1987</option>
<option value=1988>1988</option>
<option value=1989>1989</option>
<option value=1990>1990</option>
<option value=1991>1991</option>
<option value=1992>1992</option>
<option value=1993>1993</option>
<option value=1994>1994</option>
<option value=1995>1995</option>
<option value=1996>1996</option>
<option value=1997>1997</option>
<option value=1998>1998</option>
<option value=1999>1999</option>
<option value=2000>2000</option>
<option value=2001>2001</option>
<option value=2002>2002</option>
<option value=2003>2003</option>
<option value=2004>2004</option>
<option value=2005>2005</option>
<option value=2006>2006</option>
<option value=2007>2007</option>

	</TD>
    </TR>
    <TR>
        <TD colspan=2><HR></TD>
    </TR>
    <TR>
        <TD colspan=2>
	    When you click "Create Account" an e-mail will be<BR>
	    sent to your address with a link to verify your account.<BR>
	    Once you do this you will be able to set your password<BR>
	    and access the PlaneShift server.<BR>
	    <BR>
	</TD>
    </TR>
    <TR>
        <TD colspan="2" align="center"><input type="submit" value="Create Account"></TD>
    </TR>
</TABLE>
</FORM>


<?include("end.php");?>