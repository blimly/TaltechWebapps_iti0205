<?php

include ("inc/header.inc.php");
if (!$user) {
    die ("Log in to see this page");
}
?>
<?php
    $senddata_pass = strip_tags(@$_POST['updatepass']);
    $old_password = strip_tags(@$_POST['oldpassword']);
    $new_password = strip_tags(@$_POST['newpassword']);
    $new_password2 = strip_tags(@$_POST['newpassword2']);
    if ($senddata_pass) {
        $password_query = mysqli_query($mysqli, "SELECT * FROM users WHERE username='$user'");
        while ($row = mysqli_fetch_assoc($password_query)) {
            $db_password = $row['password'];
            if (md5($old_password) == $db_password) {
                if ($new_password == $new_password2) {
                    $new_password_md5 = md5($new_password);
                    $password_update = mysqli_query($mysqli, "UPDATE users SET password='$new_password_md5' WHERE username='$user'");
                    echo "Password updated";
                } else {
                    echo "The new passwords don't match";
                }

            } else {
                echo "The old password is incorrect";
            }
        }
    }

    $senddata_info = @$_POST['updateinfo'];
    $get_info = mysqli_query($mysqli, "SELECT first_name, last_name, location, bio FROM users WHERE username='$user'");
    $get_row = mysqli_fetch_assoc($get_info);
    $db_firstname = $get_row['first_name'];
    $db_lastname = $get_row['last_name'];
    $db_location = $get_row['location'];
    $db_bio = $get_row['bio'];
    if ($senddata_info) {
        $firstname = strip_tags(@$_POST['fname']);
        $lastname = strip_tags(@$_POST['lname']);
        $location = strip_tags(@$_POST['location']);
        $aboutyou = strip_tags(@$_POST['aboutyou']);
        if (strlen($firstname) < 2 || strlen($lastname) < 2) {
            echo "Name must be longer";
        }
        $info_submit_query = mysqli_query($mysqli, "UPDATE users SET first_name='$firstname', last_name='$lastname', location='$location', bio='$aboutyou' WHERE  username='$user'");
        header("Location: $user");
    }

    if (isset($_FILES['profilepic'])) {
        $pic = @$_FILES['profilepic'];
        $pic_name = $pic['name'];
        if (($pic['type'] == "image/jpeg" ||  $pic['type'] == "image/png") && $pic['size'] < 1048576) { // 1mb
            $chars = "abcdefghijklmnopqrstvuwzyz";
            $rand_dir_name = substr(str_shuffle($chars), 0, 15);
            if (mkdir("userdata/profile_pics/$rand_dir_name", 0777, true)) {
                echo "created dir";
            } else {
                echo "failed to create dir";
            }
            if (file_exists("userdata/profile_pics/$rand_dir_name/" . $pic_name)) {
                echo "Profile pic already exists.";
            } else {
                move_uploaded_file($pic['tmp_name'], "userdata/profile_pics/$rand_dir_name/" . $pic_name);
                $pic_query = mysqli_query($mysqli, "UPDATE users SET profile_pic='$rand_dir_name/$pic_name' WHERE username='$user'");
                header("Location: $user");
            }
        } else {
            echo "Invalid file";
        }
    }


?>
<h2>Edit your account settings bellow</h2>
<hr/>
<p><b>Upload a profile photo</b></p><br/>
<form action="" method="post" enctype="multipart/form-data">
    <p>Your file must be a .png or .jpg.</p>
    <p>The file must be less than 1MB in size.</p>
    <input type="file" name="profilepic"/><br/>
    <input type="submit" name="updatephoto" value="Upload Image"/><br/>
    <hr/><b/>
</form>
<form action="settings.php" method="post">
    <p><b>Change Your password</b></p> <br/>
        <input type="password" size="30" name="oldpassword" id="oldpassword" placeholder="Your Old Password"><br/>
        <input type="password" size="30" name="newpassword" id="newpassword" placeholder="Your New Password"><br/>
        <input type="password" size="30" name="newpassword2" id="newpassword2" placeholder="Repeat New Password"><br/>
        <input type="submit" name="updatepass" id="updatepass" value="Update Password"><br/>
    <hr/><b/>
</form>
<form action="settings.php" method="post">
    <p><b>Uptate profile info</b></p> <br/>
        <input type="text" size="30" name="fname" id="fname" placeholder="First name" value="<?php echo $db_firstname?>"><br/>
        <input type="text" size="30" name="lname" id="lname" placeholder="Last name" value="<?php echo $db_lastname?>"><br/>
        <input type="text" size="30" name="location" id="location" placeholder="Location" value="<?php echo $db_location?>"><br/>
        <textarea name="aboutyou" id="aboutyou" cols="50" rows="5" placeholder="About you" ><?php echo $db_bio?></textarea><br/>
        <input type="submit" name="updateinfo" id="updateinfo" value="Update Info"><br/>
    <hr/><b/>
</form>
<br/>
<br/>