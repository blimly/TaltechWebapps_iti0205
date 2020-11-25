<?php

$con = new mysqli("localhost", "dbuser", "s$cret", "mydb");

if ($con->connect_errno) {

    printf("connection failed: %s\n", $con->connect_error());
    exit();
}

$res = $con->query("SELECT VERSION()");

if ($res) {

    $row = $res->fetch_row();
    echo $row[0];
}

$res->close();
$con->close();