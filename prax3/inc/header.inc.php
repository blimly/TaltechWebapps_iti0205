<?php include( "./inc/connect.inc.php") ?>

<!doctype html />

<html>

<head>
    <title>Prax3</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>

<body>
    <div class="header-menu">
        <div id="wrapper">
            <div class="logo">
                <img src="./img/logo.png" />
            </div>
            <div class="search_box">
                <form action="search.php" method="GET" id="search">
                    <input type="text" name="q" size="60" placeholder="Search..." />

                </form>
            </div>
            <div id="menu">
                <a href="#">Home</a>
                <a href="#">About</a>
                <a href="#">Sign Up</a>
                <a href="#">Sign In</a>
            </div>
        </div>
    </div>