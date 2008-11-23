<?php

require_once('PSBaseClass.php');
require_once('PSAccount.php');
require_once('PSGuild.php');
require_once('PSGMCommandLogEntry.php');
require_once('PSItem.php');
require_once('PSRace.php');
require_once('PSQuests.php');

class PSCharacter extends PSBaseClass {

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Member-Variables
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    var $ID;
    var $FirstName;
    var $LastName;
    var $RaceGenderID;
    var $CharacterType;
    var $StaminaPhysical;
    var $StaminaMental;
    var $STR;
    var $AGI;
    var $END;
    var $INT;
    var $WIL;
    var $CHA;
    var $HP;
    var $MANA;
    var $MoneyCircles;
    var $MoneyOctas;
    var $MoneyHexas;
    var $MoneyTrias;
    var $GuildID;
    var $GuildLevel;
    var $LastLogin;
    var $AccountID;
    var $TimeConnectedInSeconds;
    var $ExperiencePoints;
    var $ProgressionPoints;
    var $DuelPoints;
    var $Description;


// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Constructor
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function PSCharacter($pID = 0) {
        if ($pID > 0) {
            $this->ID = $pID;
            $this->Load();
        }
    }

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Functions
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    function Load() {
        if (!$this->ID) {
            die('Cannot load a character if the object trying to load it has an uninitialized property "ID"');
        }

        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT * FROM characters';
        $where = '';
        PSBaseClass::S_AppendWhereCondition($where, 'id', '=', $this->ID);

        $res = mysql_query($sql . $where, $conn);
        if (!$res) {
            die($sql . $where . mysql_error());
        }
        else {
            // since it's the ID, there's only one character
            $row = mysql_fetch_array($res);

            $this->ID = $row['id'];
            $this->FirstName = $row['name'];
            $this->LastName = $row['lastname'];
            $this->RaceGenderID = $row['racegender_id'];
            $this->CharacterType = $row['character_type'];
            $this->StaminaPhysical = $row['stamina_physical'];
            $this->StaminaMental = $row['stamina_mental'];
            $this->STR = $row['base_strength'];
            $this->AGI = $row['base_agility'];
            $this->END = $row['base_endurance'];
            $this->INT = $row['base_intelligence'];
            $this->WIL = $row['base_will'];
            $this->CHA = $row['base_charisma'];
            $this->HP = $row['mod_hitpoints'];
            $this->MANA = $row['mod_mana'];
            $this->MoneyCircles = $row['money_circles'];
            $this->MoneyOctas = $row['money_octas'];
            $this->MoneyHexas = $row['money_hexas'];
            $this->MoneyTrias = $row['money_trias'];
            $this->GuildID = $row['guild_member_of'];
            $this->GuildLevel = $row['guild_level'];
            $this->LastLogin = $row['last_login'];
            $this->AccountID = $row['account_id'];
            $this->TimeConnectedInSeconds = $row['time_connected_sec'];
            $this->ExperiencePoints = $row['experience_points'];
            $this->ProgressionPoints = $row['progression_points'];
            $this->DuelPoints = $row['duel_points'];
            $this->Description = $row['description'];

            // Load completed successfully
            $this->__IsLoaded = true;
        }
    }


    //
    // Returns the account of the character as PSAccount-object.
    // Returns null when the character isn't member of a guild
    //
    function GetAccount() {
        if (!$this->__IsLoaded) {
            die('Cannot load the account of an uninitialized character-object');
        }

        if (!$this->AccountID) {
            return null;
        } else {
            return new PSAccount($this->AccountID);
        }
    }


    //
    // Returns the guild of the character as PSGuild-object.
    // Returns null when the character isn't member of a guild
    //
    function GetGuild() {
        if (!$this->__IsLoaded) {
            die('Cannot load the guild of an uninitialized character-object');
        }

        if (!$this->GuildID) {
            return null;
        } else {
            return new PSGuild($this->GuildID);
        }
    }


    //
    // Returns the race information of this character as PSRace-object
    //
    function GetRace() {
        if (!$this->__IsLoaded) {
            die('Cannot load the guild of an uninitialized character-object');
        }

        if ($this->RaceGenderID == null || $this->RaceGenderID < 0) {
            return null;
        } else {
            return new PSRace($this->RaceGenderID);
        }
    }


    //
    // Returns all GM actions that this character has done as an array of PSGMCommandLogEntry-objects.
    // The array is empty, if the character is no GM or has never issued any GM command.
    // This function only makes sense if the character in question actually is a GM, or has ever been one.
    //
    function GetGMCommandLog() {
        if (!$this->__IsLoaded) {
            die('Cannot load the guild of an uninitialized character-object');
        }

        return PSGMCommandLogEntry::S_GetActionsOfGM($this->ID);
    }

    //
    // Returns all quests that this character has done as an array of PSQuests-objects.
    // The array is empty, if the character has never received any quest.
    //
    function GetQuestEntries() {
        if (!$this->__IsLoaded) {
            die('Cannot load the guild of an uninitialized character-object');
        }

        return PSQuests::S_GetQuestEntries($this->ID);
    }

    //
    // Returns all items the player owns, be it in his inventory or an equipment slot, as array of PSItem-objects
    // The array is empty, but not null, if the character does not own any items.
    //
    function GetInventory() {
        if (!$this->__IsLoaded) {
            die('Cannot load the guild of an uninitialized character-object');
        }

        return PSItem::S_GetInventoryOfPlayer($this->ID);
    }


    //
    // Returns the character type of the current character as a human-readable expression.
    //
    function GetHumanReadableCharType() {
        switch ($this->CharacterType) {
            case 0:
                return 'Player';
                break;
            case 1:
                return 'NPC';
                break;
            case 2:
                return 'Pet';
                break;
            default:
                return '[unknown]';
                break;
        }
    }


// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Static Functions
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    //
    // Searches for characters that fulfill the given search parameters.
    // Returns an array of character objects ordered by name in ascending order. For performance reasons, a maximum of
    // 100 characters is returned. If no character match the search parameters, an empty yet initialized array is returned.
    // This function can be called statically.
    //
    function S_Find($firstName, $lastName, $charType = -1, $lastIP = '') {
        require_once('PSBaseClass.php');
        $conn = PSBaseClass::S_GetConnection();

        // EXPERIMENTAL: Only do the join if needed!
        if ($lastIP == '') {
            $sql = 'SELECT c.* FROM characters c';
        } else {
            $sql = 'SELECT c.* FROM characters c INNER JOIN accounts a ON c.account_id = a.id';
        }

        $where = '';
		if ($firstName!=null && $firstName!="")
			PSBaseClass::S_AppendWhereCondition($where, 'c.name', '=', $firstName);
		if ($lastName!=null && $lastName!="")
			PSBaseClass::S_AppendWhereCondition($where, 'c.lastname', '=', $lastName);
        if ($charType != -1) {
            PSBaseClass::S_AppendWhereCondition($where, 'c.character_type', '=', $charType);
        }
        if ($lastIP!=null && $lastIP != '') {
            PSBaseClass::S_AppendWhereCondition($where, 'a.last_login_ip', '=', $lastIP);
        }

        $res = mysql_query($sql . $where . ' ORDER BY name LIMIT 100', $conn);
        if (!$res) {
            die($sql . $where . mysql_error());
        }
        else {
            $characters = array();

            while (($row = mysql_fetch_array($res)) != null) {
                $char = new PSCharacter();
            
                $char->ID = $row['id'];
                $char->FirstName = $row['name'];
                $char->LastName = $row['lastname'];
                $char->RaceGenderID = $row['racegender_id'];
                $char->CharacterType = $row['character_type'];
                $char->StaminaPhysical = $row['stamina_physical'];
                $char->StaminaMental = $row['stamina_mental'];
                $char->MoneyCircles = $row['money_circles'];
                $char->MoneyOctas = $row['money_octas'];
                $char->MoneyHexas = $row['money_hexas'];
                $char->MoneyTrias = $row['money_trias'];
                $char->GuildID = $row['guild_member_of'];
                $char->GuildLevel = $row['guild_level'];
                $char->LastLogin = $row['last_login'];
                $char->AccountID = $row['account_id'];
                $char->TimeConnectedInSeconds = $row['time_connected_sec'];
                $char->ExperiencePoints = $row['experience_points'];
                $char->ProgressionPoints = $row['progression_points'];
                $char->DuelPoints = $row['duel_points'];
                $char->Description = $row['description'];

                $char->__IsLoaded = true;
                array_push($characters, $char);
            }

            return $characters;
        }
    }


    //
    // Returns the leader of a guild. If there's more than one leader (database inconsistency?), the first one read from the
    // database will be returned.
    // This function is intended to be called statically.
    //
    function S_GetLeaderOfGuild($guildId) {
        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT * FROM characters';
        $where = '';
        PSBaseClass::S_AppendWhereCondition($where, 'guild_member_of', '=', $guildId);
        PSBaseClass::S_AppendWhereCondition($where, 'guild_level', '=', 9);

        $res = mysql_query($sql . $where, $conn);
        if (!$res) {
            die($sql . $where . "<br>" . mysql_error());
        } else {
            // In theory each guild has only one leader. If this is not the case (because of erroneous/incosistent data in the database,
            // we will simply return the first leader that is returned.
            $row = mysql_fetch_array($res);

            $char = new PSCharacter();

            $char->ID = $row['id'];
            $char->FirstName = $row['name'];
            $char->LastName = $row['lastname'];
            $char->RaceGenderID = $row['racegender_id'];
            $char->CharacterType = $row['character_type'];
            $char->StaminaPhysical = $row['stamina_physical'];
            $char->StaminaMental = $row['stamina_mental'];
            $char->MoneyCircles = $row['money_circles'];
            $char->MoneyOctas = $row['money_octas'];
            $char->MoneyHexas = $row['money_hexas'];
            $char->MoneyTrias = $row['money_trias'];
            $char->GuildID = $row['guild_member_of'];
            $char->GuildLevel = $row['guild_level'];
            $char->LastLogin = $row['last_login'];
            $char->AccountID = $row['account_id'];
            $char->TimeConnectedInSeconds = $row['time_connected_sec'];
            $char->ExperiencePoints = $row['experience_points'];
            $char->ProgressionPoints = $row['progression_points'];
            $char->DuelPoints = $row['duel_points'];
            $char->Description = $row['description'];

            $char->__IsLoaded = true;
            return $char;
        }
    }


    //
    // Returns all members of a guild as PSCharacter-objects. They are sorted by guild level (descending), then first name (ascending)
    // If the guild has no members, an empty array is returned.
    //
    function S_GetMembersOfGuild($guildId) {
        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT * FROM characters';
        $where = '';
        PSBaseClass::S_AppendWhereCondition($where, 'guild_member_of', '=', $guildId);

        $res = mysql_query($sql . $where . ' ORDER BY guild_level DESC, name ASC', $conn);
        if (!$res) {
            die($sql . $where . "<br>" . mysql_error());
        } else {
            $chars = array();
            while (($row = mysql_fetch_array($res)) != null) {
                $char = new PSCharacter();

                $char->ID = $row['id'];
                $char->FirstName = $row['name'];
                $char->LastName = $row['lastname'];
                $char->RaceGenderID = $row['racegender_id'];
                $char->CharacterType = $row['character_type'];
                $char->StaminaPhysical = $row['stamina_physical'];
                $char->StaminaMental = $row['stamina_mental'];
                $char->MoneyCircles = $row['money_circles'];
                $char->MoneyOctas = $row['money_octas'];
                $char->MoneyHexas = $row['money_hexas'];
                $char->MoneyTrias = $row['money_trias'];
                $char->GuildID = $row['guild_member_of'];
                $char->GuildLevel = $row['guild_level'];
                $char->LastLogin = $row['last_login'];
                $char->AccountID = $row['account_id'];
                $char->TimeConnectedInSeconds = $row['time_connected_sec'];
                $char->ExperiencePoints = $row['experience_points'];
                $char->ProgressionPoints = $row['progression_points'];
                $char->DuelPoints = $row['duel_points'];
                $char->Description = $row['description'];

                $char->__IsLoaded = true;
                array_push($chars, $char);
            }

            return $chars;
        }
    }


    function S_GetCharactersOfAccount($accountID) {
        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT * FROM characters';
        $where = '';
        PSBaseClass::S_AppendWhereCondition($where, 'account_id', '=', $accountID);

        $res = mysql_query($sql . $where . ' ORDER BY name ASC', $conn);
        if (!$res) {
            die($sql . $where . "<br>" . mysql_error());
        } else {
            $chars = array();

            while (($row = mysql_fetch_array($res)) != null) {
                $char = new PSCharacter();

                $char->ID = $row['id'];
                $char->FirstName = $row['name'];
                $char->LastName = $row['lastname'];
                $char->RaceGenderID = $row['racegender_id'];
                $char->CharacterType = $row['character_type'];
                $char->StaminaPhysical = $row['stamina_physical'];
                $char->StaminaMental = $row['stamina_mental'];
                $char->MoneyCircles = $row['money_circles'];
                $char->MoneyOctas = $row['money_octas'];
                $char->MoneyHexas = $row['money_hexas'];
                $char->MoneyTrias = $row['money_trias'];
                $char->GuildID = $row['guild_member_of'];
                $char->GuildLevel = $row['guild_level'];
                $char->LastLogin = $row['last_login'];
                $char->AccountID = $row['account_id'];
                $char->TimeConnectedInSeconds = $row['time_connected_sec'];
                $char->ExperiencePoints = $row['experience_points'];
                $char->ProgressionPoints = $row['progression_points'];
                $char->DuelPoints = $row['duel_points'];
                $char->Description = $row['description'];

                $char->__IsLoaded = true;
                array_push($chars, $char);
            }

            return $chars;
        }
    }
}
?>