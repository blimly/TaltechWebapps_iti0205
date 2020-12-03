<?php include( "./inc/connect.inc.php") ?>
<?php
session_start();
if (isset($_SESSION["user_login"])) {
    $user= $_SESSION["user_login"];
} else {
    $user= "";
}
?>

<!doctype html>

<html>

<head>
    <title>bookbook by markaa</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../css/style.css" />
</head>

<body>
    <div class="header-menu">
        <div id="wrapper">
            <div class="logo">
                <img src="../img/logo.png" alt="bookbook"/>
            </div>
            <div class="search_box">
                <form action="search.php" method="GET" id="search">
                    <input type="text" name="q" id="q" size="60" placeholder="Search..." />
                </form>
            </div>
            <div id="menu">
                <a href="#">About</a>
                <?php
                if ($user) {
                    echo '
                        <a href="home.php">Home</a>
                        <a href="'.$user.'">Profile</a>
                        <a href="friend_requests.php">Friend Requests</a>
                        <a href="settings.php">Settings</a>
                        <a href="logout.php">Log out</a>';
                } else {
                    echo '
                        <a href="index.php">Sign Up</a>
                        <a href="index.php">Sign In</a>';
                }
                ?>
            </div>
        </div>
    </div>
    <div id="wrapper">