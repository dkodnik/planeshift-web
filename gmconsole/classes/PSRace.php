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
    function __construct($pID = -1) {
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

        $res = mysqli_query($conn, $sql . $where);
        if (!$res) {
            die($sql . $where . mysqli_error($conn));
        }
        else {
            // since it's the ID, there's only one character
            $row = mysqli_fetch_array($res);

            $this->Name = $row['name'];
            $this->Sex = $row['sex'];

            $this->__IsLoaded = true;
        }
    }

}

?>