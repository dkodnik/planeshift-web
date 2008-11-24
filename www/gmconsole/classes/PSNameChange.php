<?php

require_once('PSBaseClass.php');

class PSNameChange extends PSBaseClass {

    //
    // Member-Variables
    //
    var $TimeOfExecution;
    var $ExecutingGM;
    var $OldFirstName;
    var $NewFirstName;
    var $NewLastName;


    //
    // Constructor
    //
    function PSNameChange() {
    }


    function S_GetLatest($page = 0) {
        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT l.command, l.ex_time, gm.name FROM gm_command_log l INNER JOIN characters gm ON l.gm = gm.id';
        $where = '';
        PSBaseClass::S_AppendWhereCondition($where, 'command', 'LIKE', '/changename %');

        $res = mysql_query($sql . $where . ' ORDER BY ex_time DESC LIMIT ' . ($page * 100) . ', 100', $conn);
        if (!$res) {
            die($sql . $where . ' ORDER BY ex_time DESC LIMIT ' . ($page * 100) . ', 100<br/>' . mysql_error());
        }
        else {
            $namechanges = array();

            while (($row = mysql_fetch_array($res)) != null) {
                $namechange = new PSNameChange();

                // First, remove multiple space characters because they will cause trouble when splitting
                $commandParts = explode(" ", preg_replace('/[ ]+/', ' ', $row['command']));
                
                $namechange->TimeOfExecution = $row['ex_time'];
                $namechange->ExecutingGM = $row['name'];
                $namechange->OldFirstName = $commandParts[1];
                if($commandParts[2] == 'force' || $commandParts[2] == 'forceall')
                {
                    $namechange->NewFirstName = $commandParts[3];
                    $namechange->NewLastName = $commandParts[4];
                }
                else
                {
                    $namechange->NewFirstName = $commandParts[2];
                    $namechange->NewLastName = $commandParts[3];
                }
                
                $namechange->__IsLoaded = true;
                array_push($namechanges, $namechange);
            }

            return $namechanges;
        }
    }

}
