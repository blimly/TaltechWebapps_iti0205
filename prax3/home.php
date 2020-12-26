<?php include("./inc/header.inc.php");
include ("./inc/prevent_attacks.php");

if (!isset($_SESSION["user_login"])) {
    echo "<meta http-equiv='refresh' content='0; url=/index.php'>";
} else {
    $user = $_SESSION["user_login"];
    ?>
    <div class="newsFeed">
        <h2>News Feed</h2>
    </div>
    <?php

    if (isset($_POST['sendComment'])) {
        foreach($_POST as $key=>$value) {
            $s = str_getcsv($key);
            if (count($s) == 2 && $s[0] === "newComment") {
                $post_id = $s[1];
                $post_body = strip_tags($_POST['newComment,'.$post_id.'']);
                if ($post_body != "") {
                    $add_post_q = mysqli_query($mysqli, "INSERT INTO post_comments VALUES ('0', '$post_body', '$user', '$user', '$post_id' )");
                }
            }
        }
        header('Location: home.php');
    }
    foreach($_POST as $key=>$value) {
        $s = str_getcsv($key);
        if (count($s) == 3 && $s[0] === "likebutton") {
            $post_id = $s[1];
            if ($s[2] == 'liked') {
                $add_post_q = mysqli_query($mysqli, "DELETE FROM likes WHERE post_id='$post_id' AND user_likes='$user'");
            } else {
                $add_post_q = mysqli_query($mysqli, "INSERT INTO likes VALUES ('0', '$user', '$post_id')");
            }
        }
        header('Location: home.php');
    }

    $getposts = mysqli_query($mysqli, "SELECT * FROM posts  ORDER BY id DESC LIMIT 10");
    $user_friends_query = mysqli_query($mysqli, "SELECT friends FROM users WHERE username='$user'");
    $user_friends_row = mysqli_fetch_assoc($user_friends_query)['friends'];
    $user_friends_array = str_getcsv($user_friends_row);
    while ($row = mysqli_fetch_assoc($getposts)) {
        $id = $row['id'];
        $body = $row['body'];
        $date_added = $row['date_added'];
        $added_by = $row['added_by'];
        $user_posted_to = $row['user_posted_to'];

        if (in_array($added_by, $user_friends_array)) {

            $like_query = mysqli_query($mysqli, "SELECT * FROM likes WHERE post_id='$id'");
            $post_total_likes = 0;
            $user_has_liked = false;
            while($lrow = mysqli_fetch_assoc($like_query)) {
                $post_total_likes++;
                $like_user = $lrow['user_likes'];
                if ($like_user == $user) {
                    $user_has_liked = true;
                }
            }
            if ($user_has_liked) {
               ?>
                <div class='likebtn'>
                    <form action='home.php' method='post'>
                        <input type='submit' name='likebutton,<?php echo $id ?>,liked' value='UnLike' style='background-color: #fff'>
                    </form>
                </div>
                <?php
            } else {
                ?>
                <div class='likebtn'><form action='home.php' method='post'>
                        <input type='submit' name='likebutton,<?php echo $id?>,notliked' value='Like'>
                </form></div>
                <?php
            }
            echo "<div class='newsFeedPost'>
                        <div class='postinfo'><a href='$added_by'>$added_by</a> posted this on <a href='$user_posted_to'>$user_posted_to</a> at $date_added  Likes: $post_total_likes</div>
                        <div class='postdata'>$body<br/></div>";
            $get_comments = mysqli_query($mysqli, "SELECT * FROM post_comments WHERE post_id='$id' ORDER BY ID ASC");
            while ($comments = mysqli_fetch_assoc($get_comments)) {

                $comment_body = $comments['post_body'];
                $comment_to = $comments['posted_to'];
                $comment_by = $comments['posted_by'];
                echo "<div class='newsFeedPostComments'><a href='$comment_by'>$comment_by</a>: $comment_body </div>";
            }
            ?>
            <form action='home.php' method='post' name='postComment'>
                <input type='text' name='newComment,<?php echo $id ?>' placeholder='Comment...' style='margin-left: 20px; width: 83%'>
                <input id='sendComment' type='submit' name='sendComment' value='Comment '>
            </form>
            </div>
            <?php
        }
    }
}
?>
