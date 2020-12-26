<?php include("./inc/header.inc.php"); ?>

<?php
$username = mysqli_real_escape_string($mysqli, $_GET['u']);
if (ctype_alnum($username)) {
    $check = $mysqli->query("SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) == 1) {
        $get = mysqli_fetch_assoc($check);
        $username = $get['username'];
        $firstname = $get['first_name'];
        $lastname = $get['last_name'];
    } else {
        echo "<meta http-equiv='refresh' content='0; url=/index.php'>";
        exit();
    }
}

foreach ($_POST as $key => $value) {
    $s = str_getcsv($key);
    if (count($s) == 2 && $s[0] === "newComment") {
        $post_id = $s[1];
        $post_body = strip_tags($_POST['newComment,' . $post_id . '']);
        if ($post_body != "") {
            $add_post_q = mysqli_query($mysqli, "INSERT INTO post_comments VALUES ('0', '$post_body', '$user', '$user', '$post_id' )");
        }
        header('Location: '. $username);
    }
    if (count($s) == 3 && $s[0] === "likebutton") {
        $post_id = $s[1];
        if ($s[2] == 'liked') {
            $add_post_q = mysqli_query($mysqli, "DELETE FROM likes WHERE post_id='$post_id' AND user_likes='$user'");
        } else {
            $add_post_q = mysqli_query($mysqli, "INSERT INTO likes VALUES ('0', '$user', '$post_id')");
        }
        header('Location: '. $username);
    }
}

if (isset($_POST['post_msg'])) {
    $post = $_POST['post_msg'];
    $date_added = date("Y-m-d H-m-s");
    $added_by = "$user";
    $user_posted_to = $username;
    if ($post != "") {
        $sqlCommand = "INSERT INTO posts VALUES ('0', '$post', '$date_added', '$added_by', '$user_posted_to')";
        $query = mysqli_query($mysqli, $sqlCommand);
    }
}

$check_pic = mysqli_query($mysqli, "SELECT profile_pic FROM users WHERE username='$username'");
$get_pic_row = mysqli_fetch_assoc($check_pic);
$profile_pic_db = $get_pic_row['profile_pic'];
if ($profile_pic_db == "") {
    $profile_pic = "img/default_profile.png";
} else {
    $profile_pic = "userdata/profile_pics/" . $profile_pic_db;
}

$friendMsg = "";
if (isset($_POST['addFriend'])) {
    $friendrequest = $_POST['addFriend'];
    $user_to = $username;
    $user_from = $user;
    if ($user_to == $user_from) {
        $friendMsg = "You can not be friends with yourself";
    } else {
        $create_request = mysqli_query($mysqli, "INSERT INTO friend_requests VALUES ('0', '$user_from', '$user_to')");
        $friendMsg = "Your friend request has been sent";
    }
}

if (isset($_POST['removeFriend'])) {
    $add_friend_check = mysqli_query($mysqli, "SELECT friends FROM users WHERE username='$user'");
    $get_row = mysqli_fetch_assoc($add_friend_check);
    $friend_array = $get_row['friends'];
    $finalFriendString = implode(array_diff(str_getcsv($friend_array), array($username)));
    mysqli_query($mysqli, "UPDATE users SET friends='$finalFriendString' WHERE username='$user'");

    $add_friend_check_other = mysqli_query($mysqli, "SELECT friends FROM users WHERE username='$username'");
    $get_row_other = mysqli_fetch_assoc($add_friend_check_other);
    $friend_array_other = $get_row_other['friends'];
    $finalFriendString_other = implode(',', array_diff(str_getcsv($friend_array_other), array($user)));
    mysqli_query($mysqli, "UPDATE users SET friends='$finalFriendString_other' WHERE username='$username'");
    $friendMsg = "Removed friend";
}

?>
<div class="postForm">
    <form action="<?php echo $username; ?>" method="post">
        <textarea id="post_msg" name="post_msg" rows="2" cols="70" style="width: 85%"></textarea>
        <input type="submit" name="send" value="Post" style="margin-top: 22px; float: right; margin-right: 10px"/>
    </form>
</div>
<div class="profilePosts">
    <?php
    $getposts = mysqli_query($mysqli, "SELECT * FROM posts WHERE user_posted_to='$username' ORDER BY id DESC LIMIT 10");
    while ($row = mysqli_fetch_assoc($getposts)) {
        $id = $row['id'];
        $body = $row['body'];
        $date_added = $row['date_added'];
        $added_by = $row['added_by'];
        $user_posted_to = $row['user_posted_to'];

        // likes
        $like_query = mysqli_query($mysqli, "SELECT * FROM likes WHERE post_id='$id'");
        $post_total_likes = 0;
        $user_has_liked = false;
        while ($lrow = mysqli_fetch_assoc($like_query)) {
            $post_total_likes++;
            $like_user = $lrow['user_likes'];
            if ($like_user == $user) {
                $user_has_liked = true;
            }
        }
        if ($user_has_liked) {
            ?>
            <div class='likebtn'>
                <form action='<?php echo $username; ?>' method='post'>
                    <input type='submit' name='likebutton,<?php echo $id ?>,liked' value='UnLike'
                           style='background-color: #fff'>
                </form>
            </div>
            <?php
        } else {
            ?>
            <div class='likebtn'>
                <form action='<?php echo $username; ?>' method='post'>
                    <input type='submit' name='likebutton,<?php echo $id ?>,notliked' value='Like'>
                </form>
            </div>
            <?php
        }

        // post
        echo "<div class='newsFeedPost'>
                <div class='postinfo'><a href='$added_by'>$added_by</a> - $date_added  Likes: $post_total_likes</div>
                <div class='postdata'>$body<br/></div>";

        $get_comments = mysqli_query($mysqli, "SELECT * FROM post_comments WHERE post_id='$id' ORDER BY ID ASC");
        while ($comments = mysqli_fetch_assoc($get_comments)) {

            $comment_body = $comments['post_body'];
            $comment_to = $comments['posted_to'];
            $comment_by = $comments['posted_by'];
            echo "<div class='newsFeedPostComments'><a href='$comment_by'>$comment_by</a>: $comment_body </div>";
        }
    ?>
        <form action='<?php echo $username; ?>' method='post' name='postComment'>
                <input type='text' name='newComment,<?php echo $id ?>' placeholder='Comment...' style='margin-left: 20px; width: 77%'>
                <input id='sendComment' type='submit' name='sendComment' value='Comment '>
            </form>
            </div>
<?php
    }
    ?>
</div>
<img src="<?php echo $profile_pic; ?>" height="190" width="190"/>
<br/>
<form action="<?php echo $username; ?>" method="post">
    <?php
    $friendsArray = "";
    $countFriends = "";
    $friendsArray12 = "";
    $select_friends_query = mysqli_query($mysqli, "SELECT friends FROM users WHERE username='$username'");
    $friendRow = mysqli_fetch_assoc($select_friends_query);
    $friendsArray = $friendRow['friends'];
    if ($friendsArray != "") {
        $friendsArray = explode(",", $friendsArray);
        $countFriends = count($friendsArray);
        $friendsArray12 = array_slice($friendsArray, 0, 12);
        if (in_array($user, $friendsArray)) {
            $friendButton = '<input type="submit" name="removeFriend" value="Remove Friend">';
        } else {
            $friendButton = '<input type="submit" name="addFriend" value="Add Friend">';
        }
        if ($user == $username) {
            $friendButton = "";
        }
    } else {
        $friendButton = '<input type="submit" name="addFriend" value="Add Friend">';
    }
    echo $friendButton;
    ?>
</form>
<p><?php echo $friendMsg; ?></p>
<div class="textHeader"><?php echo $firstname . ' ' . $lastname; ?></div>
<div class="profileLeftSideContent">
    <?php
    $about_query = mysqli_query($mysqli, "SELECT bio FROM users WHERE username='$username'");
    $get_result = mysqli_fetch_assoc($about_query);
    echo $get_result['bio'];
    ?>
    <hr/>
    <?php
    $about_query = mysqli_query($mysqli, "SELECT location FROM users WHERE username='$username'");
    $get_result = mysqli_fetch_assoc($about_query);
    echo "Location: " . $get_result['location'];
    ?>
</div>
<div class="textHeader"><?php echo $firstname; ?>'s Friends</div>
<div class="profileLeftSideContent">
    <?php
    if ($countFriends != 0) {
        foreach ($friendsArray as $key => $value) {
            $i++;
            $getFriendQuery = mysqli_query($mysqli, "SELECT * FROM users WHERE username='$value' LIMIT 1");
            $getFriendRow = mysqli_fetch_assoc($getFriendQuery);
            $friendUsername = $getFriendRow['username'];
            $friendProfilePic = $getFriendRow['profile_pic'];
            if ($friendProfilePic == "") {
                echo "<a href='$friendUsername'><img src='img/default_profile.png' alt='$friendUsername' title='$friendUsername's Profile' height='40' width='40'></a>";
            } else {
                echo "<a href='$friendUsername'><img src='userdata/profile_pics/$friendProfilePic' alt='$friendUsername' title='$friendUsername' height='40' width='40'></a>";
            }
        }

    } else {
        echo $username . " has no friends";
    }
    ?>
</div>