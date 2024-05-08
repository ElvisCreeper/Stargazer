<?php
//solar system
$subjectID = $_REQUEST["subject"];
$_SESSION["subjectID"] = $subjectID;
$_SESSION["subjectName"] = $_REQUEST["subjectName"];
$sth = DBHandler::getPDO()->prepare("SELECT Name, id FROM Topic WHERE SubjectId = $subjectID");
$sth->execute();
echo ('<table>');
foreach ($sth->fetchAll() as $row) {
    echo "<tr>";
    echo "<td><input type='button' onclick='getTabs(this.value, {$row["id"]})' value='{$row["Name"]}' /></td>";
    echo '</tr>';
}
echo ('</table>');
