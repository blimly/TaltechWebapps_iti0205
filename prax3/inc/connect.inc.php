<?php
#mysql_connect("localhost", "root", "") or die("Couldn't connect to SQL server");
#mysql_select_db("findfriends") or die("Couldn't select DB");
$mysqli = new mysqli("127.0.0.1", "markaa", "pass", "bookbook_db");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
    //echo "Connected successfully!";
}
?>
