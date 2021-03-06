<?php

require_once('PSBaseClass.php');

class PSGMCommandLogEntry {
    
    //
    // Member-Variables
    //
    var $ID;
    var $GMID;
    var $GMFirstName;
    var $Command;
    var $TargetPlayerID;
    var $TargetPlayerFirstName;
    var $TimeOfExecution;

    //
    // Constructor
    //
    function __construct() {
    }


    //
    // Functions
    //
    static function S_GetActionsOfGM($pGMID) {
        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT l.id, gm.id AS gm_id, gm.name AS gm_name, l.command, p.id AS player_id, p.name AS player_name, l.ex_time FROM gm_command_log l INNER JOIN characters gm ON gm.id = l.gm LEFT OUTER JOIN characters p ON p.id = l.player ';
        $where = '';
        PSBaseClass::S_AppendWhereCondition($where, 'gm', '=', $pGMID);

        $res = mysqli_query($conn, $sql . $where . ' ORDER BY ex_time DESC');
        if (!$res) {
            die($sql . $where . "<br>" . mysqli_error($conn));
        } else {
            $actions = array();

            while (($row = mysqli_fetch_array($res)) != null) {
                $action = new PSGMCommandLogEntry();

                $action->ID = $row['id'];
                $action->GMID = $row['gm_id'];
                $action->GMFirstName = $row['gm_name'];
                $action->Command = $row['command'];
                $action->TargetPlayerID = $row['player_id'];
                $action->TargetPlayerFirstName = $row['player_name'];
                $action->TimeOfExecution = $row['ex_time'];

                $action->__IsLoaded = true;
                array_push($actions, $action);
            }

            return $actions;
        }
    }
}

?>