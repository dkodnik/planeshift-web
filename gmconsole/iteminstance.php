<?php

    require_once('config.php');
    require_once('classes/Navigation.php');
    require_once('classes/PSItem.php');
    session_start();
    
    // is the user logged in?
    if (!isset($_SESSION["__SECURITY_LEVEL"])) {
        header("Location: index.php?origin=iteminstance");
        exit;
    }

    // get variables
    $itemId = $_POST['itemId'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title>Item instance</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_RELURI?>"/>
        <link rel="stylesheet" type="text/css" href="<?=$__CSS_ADDON_RELURI?>"/>
        <script language="javascript" type="text/javascript">
            function setCharId(charId)
            {
                document.getElementById("charId").value = charId;
                document.getElementById("charDetailsForm").submit();
            }
        </script>
    </head>
    <body>
        <table>
            <tr>
                <td style="vertical-align:top;">
<?php
                    echo Navigation::S_GetNavigation();
?>
                </td>
                <td style="width:800px">
<?php
                    if(!is_numeric($itemId)) {
                        die("Could not load guild, no ID specified by parent page.");
                    }

                    $item = new PSItem($itemId);
                    $itemOwner = $item->GetItemOwner();
                    $itemCreator = $item->GetItemCreator();
?>
                    <input type="button" onclick="javascript:window.history.back();" value="Back"/><br/>
                    <h2 class="yellowtitlebig">Item details (specific to this instance)</h2>
                    <table class="table">
                        <tr>
                            <th>Attribute</th>
                            <th>Value</th>
                        </tr>
                        <tr>
                            <td>Item owner</td>
                            <td>
<?php
                                if (!$itemOwner) {
                                    echo '<i>orphaned item</i>';
                                } else {
                                    echo '<a href="javascript:setCharId(\'' . $itemOwner->ID . '\');">';
                                    echo $itemOwner->FirstName;
                                    echo ((!$itemOwner->LastName) ? '' : ' ' . $itemOwner->LastName);
                                    echo '</a>';
                                }
?>
                            </td>
                        </tr>
                        <tr>
                            <td>Item creator</td>
                            <td>
<?php
                                if (!$itemCreator) {
                                    echo '<i>not a crafted item</i>';
                                } else {
                                    echo '<a href="javascript:setCharId(\'' . $itemCreator->ID . '\');">';
                                    echo $itemCreator->FirstName;
                                    echo ((!$itemCreator->LastName) ? '' : ' ' . $itemCreator->LastName);
                                    echo '</a>';
                                }
?>
                            </td>
                        </tr>
                        <tr>
                            <td>Location in parent container</td>
                            <td><?=$item->LocationInParent?></td>
                        </tr>
                        <tr>
                            <td>Stack count</td>
                            <td><?=$item->StackCount?></td>
                        </tr>
                        <tr>
                            <td>Item quality</td>
                            <td><?=$item->ItemQuality?></td>
                        </tr>
                        <tr>
                            <td>Decay resistance</td>
                            <td><?=$item->DecayResistance?></td>
                        </tr>
                        <tr>
                            <td>Location</td>
                            <td><?=$item->GetHumanReadableItemLocation()?></td>
                        </tr>
                        <tr>
                            <td>Equipped in slot</td>
                            <td><?=($item->Location != 'E') ? '<i>not equipped</i>' : $item->EquippedInSlot?></td>
                        </tr>
                    </table>
                    <hr/>
                    <h2 class="yellowtitlebig">Item details (general)</h2>
                    <table class="table">
                        <tr>
                            <th>Attribute</th>
                            <th>Value</th>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td><?=$item->Name?></td>
                        </tr>
                        <tr>
                            <td>Item category name</td>
                            <td><?=$item->CategoryName?></td>
                        </tr>
                        <tr>
                            <td>Weight</td>
                            <td><?=$item->Weight?></td>
                        </tr>
                        <tr>
                            <td>Can be placed in these slots</td>
                            <td><?=$item->ValidSlots?></td>
                        </tr>
                        <tr>
                            <td>Weapon speed</td>
                            <td><?=$item->WeaponSpeed?></td>
                        </tr>
                        <tr>
                            <td>Weapon damage (slash)</td>
                            <td><?=$item->WeaponDamageSlash?></td>
                        </tr>
                        <tr>
                            <td>Weapon damage (blunt)</td>
                            <td><?=$item->WeaponDamageBlunt?></td>
                        </tr>
                        <tr>
                            <td>Weapon damage (pierce)</td>
                            <td><?=$item->WeaponDamagePierce?></td>
                        </tr>
                    </table>
                    <form id="charDetailsForm" action="chardetails.php" method="post">
                        <input type="hidden" id="charId" name="charId"/>
                    </form>
                </td>
            </tr>
        </table>
    </body>
</html>