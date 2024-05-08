<?php
//galaxy
$sth = DBHandler::getPDO()->prepare("SELECT * FROM Subject");
$sth->execute();
echo ('<table>');
foreach ($sth->fetchAll() as $row) {
    echo "<tr>";
    echo "<td><input type='button' onclick='getTopics(this.value ,{$row["id"]})' value='{$row["Name"]}' /></td>";
    echo '</tr>';
}
echo ('</table>');