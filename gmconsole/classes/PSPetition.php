<?php

require_once('PSBaseClass.php');

class PSPetition extends PSBaseClass {

    //
    // Member variables
    //
    var $ID;
    var $CreatedDate;
    var $PetitionerID;
    var $PetitionerFirstName;
    var $Status;
    var $Petition;
    var $CaseworkerID;
    var $CaseworkerFirstName;
    var $EscalationLevel;
	var $Resolution;


    //
    // Constructor
    //
    function PSPetition() {
    }


    //
    // Functions
    //
    function S_GetPetitions($type = 1) {
        $conn = PSBaseClass::S_GetConnection();

		// base query
        $sql = 'SELECT p.id, p.created_date, c.id AS petitioner_id, c.name AS petitioner, p.status, p.petition, gm.id AS caseworker_id, gm.name AS caseworker, p.escalation_level,p.resolution FROM petitions p INNER JOIN characters c ON c.id = p.player LEFT OUTER JOIN characters gm ON gm.id = p.assigned_gm WHERE LOWER(p.status) ';

		// if type = 1 then extract open and in progress petition
		if ($type == 1)
			$sql .= 'IN (\'open\', \'in progress\')';

		// if type = 2 then extract closed petitions
		if ($type == 2)
           $sql .= 'IN (\'closed\')';

        $res = mysql_query($sql . " ORDER BY created_date DESC LIMIT 100", $conn);
        if (!$res) {
            die($sql . $where . "<br>" . mysql_error());
        } else {
            $petitions = array();

            while (($row = mysql_fetch_array($res)) != null) {
                $petition = new PSPetition();

                $petition->ID = $row['id'];
                $petition->CreatedDate = $row['created_date'];
                $petition->PetitionerID = $row['petitioner_id'];
                $petition->PetitionerFirstName = $row['petitioner'];
                $petition->Status = $row['status'];
                $petition->Petition = $row['petition'];
                $petition->CaseworkerID = $row['caseworker_id'];
                $petition->CaseworkerFirstName = $row['caseworker'];
                $petition->EscalationLevel = $row['escalation_level'];
				$petition->Resolution = $row['resolution'];

                $petition->__IsLoaded = true;
                array_push($petitions, $petition);
            }

            return $petitions;
        }
    }
}

?>