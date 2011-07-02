<?php
class Navigation {
    function S_GetNavigation() {
        return <<<NAV
            <table class="table" style="margin-right:20px">
                <tr>
                    <th>Navigation</th>
                </tr>
                <tr>
                    <td><a href="accountsearch.php">Account search</a></td>
                </tr>
                <tr>
                    <td><a href="charsearch.php">Character search</a></td>
                </tr>
                <tr>
                    <td><a href="guildsearch.php">Guild search</a></td>
                </tr>
                <tr>
                    <td><a href="gmlist.php">GM-List</a></td>
                </tr>
                <tr>
                    <td><a href="namechanges.php">Name changes</a></td>
                </tr>
                <tr>
                    <td><a href="petitions.php">Open petitions</a></td>
                </tr>
                <tr>
                    <td><a href="petitions.php?type=2">Closed petitions</a></td>
                </tr>
                <tr>
                	<td><a href="gmevents.php">Review GM Events</a></td>
                </tr>
                <tr>
                    <td><a href="reportlogs.php">Report logs</a></td>
                </tr>
                <tr>
                    <td><a href="logs.php">Other logs</a></td>
                </tr>
            </table>
NAV;
    }
}
?>
