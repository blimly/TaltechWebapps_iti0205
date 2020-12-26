<?php include("./inc/header.inc.php");

$user = $_SESSION["user_login"];

$search_text = strip_tags(@$_GET['q']);
$search_text = preg_replace("#[^0-9a-z]#i", "", $search_text);

$username_query = mysqli_query($mysqli, "SELECT * from users WHERE
                          username LIKE '%$search_text%' 
                       OR first_name LIKE '%$search_text%' 
                       OR last_name LIKE '%$search_text%'
                        OR location LIKE '%$search_text%'");
if (mysqli_num_rows($username_query) == 0) {
   echo "Found nothing.";
} else {
    while ($row = mysqli_fetch_array($username_query)) {
        $username = $row['username'];
        $firstname = $row['first_name'];
        $lastname = $row['last_name'];
        $location = $row['location'];
        $getFriendQuery = mysqli_query($mysqli, "SELECT * FROM users WHERE username='$username' LIMIT 1");
        $getFriendRow = mysqli_fetch_assoc($getFriendQuery);
        $profilePic = $row['profile_pic'];
        echo "<div class='searchResult'> <a href='$username'>";
        if ($profilePic == "") {
            echo "<img src='img/default_profile.png' alt='$username' title='$username' height='60' width='60'>";
        } else {
            echo "<img src='userdata/profile_pics/$profilePic' alt='$username' title='$username' height='60' width='60'>";
        }
       echo "   " . $firstname . " " . $lastname . "<div class='location'>" . $location . " </div></a></div>";
    }
}

?>
