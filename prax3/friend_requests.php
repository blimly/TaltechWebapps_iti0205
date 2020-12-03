<?php include ("inc/header.inc.php") ?>
<?php
// find friend requests
$friendRequests = mysqli_query($mysqli, "SELECT * FROM friend_requests WHERE user_to='$user'");
$numrows = mysqli_num_rows($friendRequests);
if ($numrows == 0) {
    echo "You have no friend requests";
    $user_to = "";
    $user_from = "";
} else {
    while ($row = mysqli_fetch_assoc($friendRequests)) {
        $id = $row['id'];
        $user_to = $row['user_to'];
        $user_from = $row['user_from'];
        echo '' . $user_from . " wants to be friends with you.";
        ?>
        <?php

        if (isset($_POST['acceptrequest_'.$user_from])) {
            $add_friend_check = mysqli_query($mysqli, "SELECT friends FROM users WHERE username='$user'");
            $get_friend_row = mysqli_fetch_assoc($add_friend_check);
            $friend_array = $get_friend_row['friends'];
            $friend_array_explode = explode(",", $friend_array);
            $friend_array_count = count($friend_array_explode);

            $add_friend_check_other = mysqli_query($mysqli, "SELECT friends FROM users WHERE username='$user_from'");
            $get_friend_row_other = mysqli_fetch_assoc($add_friend_check_other);
            $friend_array_other = $get_friend_row_other['friends'];
            $friend_array_explode_other = explode(",", $friend_array_other);
            $friend_array_count_other = count($friend_array_explode_other);

            if ($friend_array == "") {
                $friend_array_count = 0;
            }
            if ($friend_array_count == 0) {
                $add_friend_query = mysqli_query($mysqli, "UPDATE users SET friends=CONCAT('$friend_array', '$user_from') WHERE username='$user_to'");
                echo "query sent";
            } else {
                $add_friend_query = mysqli_query($mysqli, "UPDATE users SET friends=CONCAT('$friend_array', ',$user_from') WHERE username='$user_to'");
            }

            if ($friend_array_other == "") {
                $friend_array_count_other = 0;
            }
            if ($friend_array_count_other == 0) {
                $add_friend_query = mysqli_query($mysqli, "UPDATE users SET friends=CONCAT('$friend_array_other', '$user_to') WHERE username='$user_from'");
                echo "query sent";
            } else {
                $add_friend_query = mysqli_query($mysqli, "UPDATE users SET friends=CONCAT('$friend_array_other', ',$user_to') WHERE username='$user_from'");
            }
            $delete = mysqli_query($mysqli, "DELETE FROM friend_requests WHERE user_to='$user_to'&&user_from='$user_from'");
            header("Location: friend_requests.php");
        }
        if (isset($_POST['declinerequest_'.$user_from])) {
            $delete = mysqli_query($mysqli, "DELETE FROM friend_requests WHERE user_to='$user_to'&&user_from='$user_from'");
            header("Location: friend_requests.php");
        }
        ?>
        <form action="friend_requests.php" method="post" >
            <input type="submit" name="acceptrequest_<?php echo $user_from;?>" value="Accept Request" >
            <input type="submit" name="declinerequest_<?php echo $user_from;?>" value="Decline Request" >
        </form>
    <?php
    }
}
?>
