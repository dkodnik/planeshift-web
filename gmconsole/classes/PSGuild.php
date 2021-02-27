<?php

require_once('PSBaseClass.php');
require_once('PSCharacter.php');

class PSGuild extends PSBaseClass {

    //
    // Member-Variables
    //
    var $ID;
    var $Name;
    var $FounderID;
    var $WebPage;
    var $DateCreated;
    var $KarmaPoints;
    var $SecrecyIndicator;
    var $Motd;
    var $AllianceID;
    

    //
    // Constructor
    //
    function __construct($pID = 0) {
        if ($pID > 0) {
            $this->ID = $pID;
            $this->Load();
        }
    }


    //
    // Functions
    //
    function Load() {
        if (!$this->ID) {
            die('Cannot load a guild if the object trying to load it has an uninitialized property "ID"');
        }

        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT * FROM guilds';
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
            $this->FounderID = $row['char_id_founder'];
            $this->WebPage = $row['web_page'];
            $this->DateCreated = $row['date_created'];
            $this->KarmaPoints = $row['karma_points'];
            $this->SecrecyIndicator = $row['secret_ind'];
            $this->Motd = $row['motd'];
            $this->AllianceID = $row['alliance'];

            // Load completed successfully
            $this->__IsLoaded = true;
        }
    }


    //
    // Returns the Founder of this guild as PSCharacter-object
    //
    function GetFounder() {
        if (!$this->__IsLoaded) {
            die('Cannot load the guild\' founder from an uninitialized guild-object');
        }
        if (!$this->FounderID) {
            return null;
        } else {
            return new PSCharacter($this->FounderID);
        }
    }


    //
    // Returns the current leader of this guild as PSCharacter-object
    //
    function GetLeader() {
        if (!$this->__IsLoaded) {
            die('Cannot load the guild leader from an uninitialized guild-object');
        }

        return PSCharacter::S_GetLeaderOfGuild($this->ID);
    }


    function GetMembers($order) {
        if (!$this->__IsLoaded) {
            die('Cannot load the guild members from an uninitialized guild-object');
        }

        return PSCharacter::S_GetMembersOfGuild($this->ID, $order);

    }


    static function S_Find($guildName) {
        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT * FROM guilds';
        $where = '';
        PSBaseClass::S_AppendWhereCondition($where, 'name', 'LIKE', $guildName);

        $res = mysqli_query($conn, $sql . $where . " ORDER BY name LIMIT 100");
        if (!$res) {
            die($sql . $where . mysqli_error($conn));
        } else {
            $guilds = array();

            while (($row = mysqli_fetch_array($res)) != null) {
                $guild = new PSGuild();

                $guild->ID = $row['id'];
                $guild->Name = $row['name'];
                $guild->FounderID = $row['char_id_founder'];
                $guild->WebPage = $row['web_page'];
                $guild->DateCreated = $row['date_created'];
                $guild->KarmaPoints = $row['karma_points'];
                $guild->SecrecyIndicator = $row['secret_ind'];
                $guild->Motd = $row['motd'];
                $guild->AllianceID = $row['alliance'];

                $guild->__IsLoaded = true;
                array_push($guilds, $guild);
            }

            return $guilds;
        }
    }
}

?>