<?php

require_once('PSBaseClass.php');

class PSRace extends PSBaseClass {
    
    //
    // Member-Variables (incomplete, but sufficient for now)
    //
    var $ID;
    var $Name;
    var $Sex;


    //
    // Constructor
    //
    function PSRace($pID = -1) {
        if ($pID > -1) {
            $this->ID = $pID;
            $this->Load();
        }
    }


    //
    // Functions
    //
    function Load() {
        if ($this->ID == null || $this->ID < 0) {
            die('Cannot load a race if the object trying to load it has an uninitialized property "ID"');
        }

        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT * FROM race_info';
        $where = '';
        PSBaseClass::S_AppendWhereCondition($where, 'id', '=', $this->ID);

        $res = mysql_query($sql . $where, $conn);
        if (!$res) {
            die($sql . $where . mysql_error());
        }
        else {
            // since it's the ID, there's only one character
            $row = mysql_fetch_array($res);

            $this->Name = $row['name'];
            $this->Sex = $row['sex'];

            $this->__IsLoaded = true;
        }
    }

}

?>