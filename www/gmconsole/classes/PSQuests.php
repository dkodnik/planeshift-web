<?php

require_once('PSBaseClass.php');

class PSQuests {
    
    //
    // Member-Variables
    //
    var $ID;
	var $Name;
    var $Status;
    var $Lockout;

    //
    // Constructor
    //
    function PSQuests() {
    }


    //
    // Functions
    //
    function S_GetQuestEntries($pID) {
        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT q.id, q.name,c.status, c.remaininglockout from quests q, character_quests c where q.id=c.quest_id and player_id='.$pID;


        $res = mysql_query($sql , $conn);
        if (!$res) {
            die($sql . $where . "<br>" . mysql_error());
        } else {
            $actions = array();

            while (($row = mysql_fetch_array($res)) != null) {
                $action = new PSQuests();

                $action->ID = $row['id'];
				$action->Name = $row['name'];
                $action->Status = $row['status'];
                $action->Lockout = $row['remaininglockout'];

                $action->__IsLoaded = true;
                array_push($actions, $action);
            }

            return $actions;
        }
    }

    function S_GetQuestStepEntries($pID, $qID) {
        $conn = PSBaseClass::S_GetConnection();

		$min = 10000+(100*$qID);
		$max = 10000+(100*$qID)+90;

        $sql = 'SELECT quest_id, status, remaininglockout, last_response from character_quests where player_id='.$pID.' and quest_id>'.$min.' and quest_id<'.$max. ' order by quest_id';

        $res = mysql_query($sql , $conn);
        if (!$res) {
            die($sql . $where . "<br>" . mysql_error());
        } else {
            $actions = array();

            while (($row = mysql_fetch_array($res)) != null) {
                $action = new PSQuests();

                $action->ID = $row['quest_id'];
                $action->Status = $row['status'];
                $action->Lockout = $row['remaininglockout'];

                $action->__IsLoaded = true;
                array_push($actions, $action);
            }

            return $actions;
        }
    }
	
}

?>