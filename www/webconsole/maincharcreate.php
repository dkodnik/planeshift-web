<?
function maincharcreate(){

    checkAccess('main', '', 'read');
    
	?>
	
	<BODY BGCOLOR=#052F2E text="57B9CB" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" link="#FFFFFF" vlink="#FFFFFF" alink="#FFFFFF">
	<CENTER>
	<TABLE BORDER=0 CELLPADDING=5 CELLSPACING=0>
	<TH> Race </TH><TH> Initial CP </TH> <TH> X Start </TH> <TH> Y Start </TH> <TH> Z Start </TH><TH> Sector </TH>
	<TH> STR </TH><TH> END </TH> <TH> AGI </TH> <TH> INT </TH> <TH> WIL </TH><TH> CHA </TH>
	<TH> Phys. Regen. Still </TH><TH> Phys. Regen. Walk </TH> <TH> Ment. Regen. Still </TH> <TH> Ment. Regen. Walk </TH>
	<FORM METHOD=post ACTION="index.php?page=adjustraces" METHOD=POST>   
	<?PHP

	$query = "select * from race_info where id < 12";
	$result = mysql_query2($query);

	$switch = false;

	while ($line = mysql_fetch_array($result, MYSQL_BOTH)){
		if ($switch)
			$bgcolour = '#222222';
		else
			$bgcolour = '#444444';

		$switch = !$switch;

		echo "<TR BGCOLOR=$bgcolour>";
		echo "<TD>$line[name]</TD>";
		echo "<TD ALIGN=CENTER><INPUT TYPE=TEXT NAME='$line[name]_initialcp' VALUE=$line[initial_cp] MAXLENGTH=3 SIZE=3></TD>";
		echo "<TD ALIGN=CENTER><INPUT TYPE=TEXT NAME='$line[name]_start_x'   VALUE=$line[start_x] MAXLENGTH=8 SIZE=8></TD>";
		echo "<TD ALIGN=CENTER><INPUT TYPE=TEXT NAME='$line[name]_start_y'   VALUE=$line[start_y] MAXLENGTH=8 SIZE=8></TD>";
		echo "<TD ALIGN=CENTER><INPUT TYPE=TEXT NAME='$line[name]_start_z'   VALUE=$line[start_z] MAXLENGTH=8 SIZE=8></TD>";
		echo "<TD ALIGN=CENTER><SELECT NAME=$line[name]_sectorName>";

		$sectorQuery = "select id,name from sectors";
		$sectorResult = mysql_query2($sectorQuery);
		while ($sectorline = mysql_fetch_array($sectorResult, MYSQL_BOTH)){
			if ($line[start_sector_id] == $sectorline[id])
				echo "<OPTION SELECTED>$sectorline[name]</OPTION>";
			else
				echo "<OPTION>$sectorline[name]</OPTION>";
		}
		echo '</SELECT></TD>';
		echo "<TD ALIGN=CENTER>$line[start_str]</TD>";
		echo "<TD ALIGN=CENTER>$line[start_end]</TD>";
		echo "<TD ALIGN=CENTER>$line[start_agi]</TD>";
		echo "<TD ALIGN=CENTER>$line[start_int]</TD>";
		echo "<TD ALIGN=CENTER>$line[start_will]</TD>";
		echo "<TD ALIGN=CENTER>$line[start_cha]</TD>";
		echo "<TD ALIGN=CENTER><INPUT TYPE=TEXT NAME='$line[name]_regen1' VALUE=$line[base_physical_regen_still] MAXLENGTH=5 SIZE=5></TD>";
		echo "<TD ALIGN=CENTER><INPUT TYPE=TEXT NAME='$line[name]_regen2' VALUE=$line[base_physical_regen_walk] MAXLENGTH=5 SIZE=5></TD>";
		echo "<TD ALIGN=CENTER><INPUT TYPE=TEXT NAME='$line[name]_regen3' VALUE=$line[base_mental_regen_still] MAXLENGTH=5 SIZE=5></TD>";
		echo "<TD ALIGN=CENTER><INPUT TYPE=TEXT NAME='$line[name]_regen4' VALUE=$line[base_mental_regen_walk] MAXLENGTH=5 SIZE=5></TD>";
		echo '</TR>';
	}

	?>
	</TABLE>
	<INPUT TYPE=submit VALUE="Submit changes" VALUE="changebasic">
	</FORM>
	</CENTER>
	<?PHP
	echo '<br><br>';
}

?>
