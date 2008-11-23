<?php

require_once('PSBaseClass.php');
require_once('PSCharacter.php');

class PSItem extends PSBaseClass {
    
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Membver-Variables
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    // First the ones from 'item_instances'
    var $ID;
    var $OwnerID;
    var $LocationInParent;
    var $StackCount;
    var $CreatorID;
    var $ItemQuality;
    var $DecayResistance;
    // Now the information from 'item_stats'
    var $Name;
    var $Weight;
    var $ValidSlots;
    var $WeaponSpeed;
    var $WeaponDamageSlash; 
    var $WeaponDamageBlunt;
    var $WeaponDamagePierce;
    var $WeaponDamageForce; // There's more information, but not currently used AFAIK -- Uyaem, 2006/07/18
    // Now the information from 'item_category'
    var $CategoryName;


// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Constructor
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function PSItem($pID = 0) {
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
            die('Cannot load an item if the object trying to load it has an uninitialized property "ID"');
        }

        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT inst.id AS inst_id, inst.char_id_owner AS inst_owner, inst.location_in_parent AS inst_location_in_parent, inst.stack_count AS inst_stack_count, inst.creator_mark_id AS inst_creator_mark_id, inst.item_quality AS inst_item_quality, inst.decay_resistance AS inst_decay_resistance, stats.name AS stats_name, stats.weight AS stats_weight, stats.valid_slots AS stats_valid_slots, stats.weapon_speed AS stats_weapon_speed, stats.dmg_slash AS stats_dmg_slash, stats.dmg_blunt AS stats_dmg_blunt, stats.dmg_pierce AS stats_dmg_pierce, cat.name AS cat_name ';
        $sql .= 'FROM item_instances inst ';
        $sql .= 'INNER JOIN item_stats stats ON inst.item_stats_id_standard = stats.id ';
        $sql .= 'INNER JOIN item_categories cat ON stats.category_id = cat.category_id ';

        $where = '';
        PSBaseClass::S_AppendWhereCondition($where, 'inst.id', '=', $this->ID);

        $res = mysql_query($sql . $where, $conn);
        if (!$res) {
            die($sql . $where . mysql_error());
        }
        else {
            // Only one item will be returned
            $row = mysql_fetch_array($res);

            $this->ID = $row['inst_id'];
            $this->OwnerID = $row['inst_owner'];
            $this->LocationInParent = $row['inst_location_in_parent'];
            $this->StackCount = $row['inst_stack_count'];
            $this->CreatorID = $row['inst_creator_mark_id'];
            $this->ItemQuality = $row['inst_item_quality'];
            $this->DecayResistance = $row['inst_decay_resistance'];
            $this->Name = $row['stats_name'];
            $this->Weight = $row['stats_weight'];
            $this->ValidSlots = $row['stats_valid_slots'];
            $this->WeaponSpeed = $row['stats_weapon_speed'];
            $this->WeaponDamageSlash = $row['stats_dmg_slash'];
            $this->WeaponDamageBlunt = $row['stats_dmg_blunt'];
            $this->WeaponDamagePierce = $row['stats_dmg_pierce'];
            $this->CategoryName = $row['cat_name'];

            // Loaded successfully
            $this->__IsLoaded = true;
        }
    }


    //
    // Returns the owner of the item as PSCharacter-object, or null if the item instance is orphaned
    //
    function GetItemOwner() {
        if (!$this->__IsLoaded) {
            die('Cannot load the account of an uninitialized character-object');
        }

        if (!$this->OwnerID) {
            return null;
        } else {
            return new PSCharacter($this->OwnerID);
        }
    }


    //
    // Returns the creator or crafter of this item as PSCharacter-object or null if it's not a crafted object
    function GetItemCreator() {
        if (!$this->__IsLoaded) {
            die('Cannot load the account of an uninitialized character-object');
        }

        if (!$this->CreatorID) {
            return null;
        } else {
            return new PSCharacter($this->CreatorID);
        }
    }


    function GetHumanReadableItemLocation() {

	// bulk slots
        if ($this->LocationInParent>15)
           return "Bulk ". ($this->LocationInParent - 15);

	// equipped slots
        switch($this->LocationInParent) {
            case '0':
                return 'righthand';
                break;
            case '1':
                return 'lefthand';
                break;
            case '3':
                return 'leftfinger';
                break;
            case '4':
                return 'rightfinger';
                break;
            case '5':
                return 'head';
                break;
            case '6':
                return 'neck';
                break;
            case '7':
                return 'back';
                break;
            case '8':
                return 'arms';
                break;
            case '9':
                return 'gloves';
                break;
            case '10':
                return 'boots';
                break;
            case '11':
                return 'legs';
                break;
            case '12':
                return 'belt';
                break;
            case '13':
                return 'bracers';
                break;
            case '14':
                return 'torso';
                break;
            case '15':
                return 'mind';
                break;
            default:
                return '[unknown]';
                break;
        }
    }


// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Static Functions
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function S_GetInventoryOfPlayer($playerID) {
        if (!$playerID) {
            die('Cannot load the inventory of a player who does not exist.');
        }

        $conn = PSBaseClass::S_GetConnection();

        $sql = 'SELECT inst.id AS inst_id, inst.char_id_owner AS inst_owner, inst.location_in_parent AS inst_location_in_parent, inst.stack_count AS inst_stack_count, inst.creator_mark_id AS inst_creator_mark_id, inst.item_quality AS inst_item_quality, inst.decay_resistance AS inst_decay_resistance, stats.name AS stats_name, stats.weight AS stats_weight, stats.valid_slots AS stats_valid_slots, stats.weapon_speed AS stats_weapon_speed, stats.dmg_slash AS stats_dmg_slash, stats.dmg_blunt AS stats_dmg_blunt, stats.dmg_pierce AS stats_dmg_pierce, cat.name AS cat_name ';
        $sql .= 'FROM item_instances inst ';
        $sql .= 'INNER JOIN item_stats stats ON inst.item_stats_id_standard = stats.id ';
        $sql .= 'INNER JOIN item_categories cat ON stats.category_id = cat.category_id ';

        $where = '';
        PSBaseClass::S_AppendWhereCondition($where, 'char_id_owner', '=', $playerID);

        $res = mysql_query($sql . $where . ' ORDER BY inst_location_in_parent ASC', $conn);
        if (!$res) {
            die($sql . $where . mysql_error());
        }
        else {
            $items = array();

            while (($row = mysql_fetch_array($res)) != null) {
                $item = new PSItem();

                $item->ID = $row['inst_id'];
                $item->OwnerID = $row['inst_owner'];
                $item->LocationInParent = $row['inst_location_in_parent'];
                $item->StackCount = $row['inst_stack_count'];
                $this->CreatorID = $row['inst_creator_mark_id'];
                $item->ItemQuality = $row['inst_item_quality'];
                $this->DecayResistance = $row['inst_decay_resistance'];
                $item->Name = $row['stats_name'];
                $item->Weight = $row['stats_weight'];
                $item->ValidSlots = $row['stats_valid_slots'];
                $item->WeaponSpeed = $row['stats_weapon_speed'];
                $item->WeaponDamageSlash = $row['stats_dmg_slash'];
                $item->WeaponDamageBlunt = $row['stats_dmg_blunt'];
                $item->WeaponDamagePierce = $row['stats_dmg_pierce'];
                $item->CategoryName = $row['cat_name'];

                $item->__IsLoaded = true;
                array_push($items, $item);
            }

            return $items;
        }
    }

}

?>