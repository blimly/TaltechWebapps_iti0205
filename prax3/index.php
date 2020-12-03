<?php include("./inc/header.inc.php"); ?>

<?php

// User registration.
$reg = @$_POST['reg'];
$fn = "";
$ln = "";
$un = "";
$em = "";
$pswd = "";
$pswd2 = "";
$d = ""; // sing up date
$u_check = "";

// form
$fn = strip_tags(@$_POST['fname']);
$ln = strip_tags(@$_POST['lname']);
$un = strip_tags(@$_POST['username']);
$em = strip_tags(@$_POST['email']);
$pswd = strip_tags(@$_POST['password']);
$pswd2 = strip_tags(@$_POST['password2']);
$d = date("Y-m-d");

if ($reg) {
    $u_check = $mysqli->query("SELECT username FROM users WHERE username='$un'"); // check if user already exists
    $check = mysqli_num_rows($u_check);
    if ($check == 0) {
        // check that all the fields are filled
        if ($fn && $ln && $un && $em && $pswd && $pswd2) {
            if ($pswd == $pswd2) {
                if (strlen($un) > 25 || strlen($fn) > 25 || strlen($ln) > 25) {
                    echo "The maximum limit for username/first name/last name is 25 characters";
                } else {
                    if (strlen($pswd) > 30 || strlen($pswd) < 5) {
                        echo "Your password must be between 5 and 30 characters long!";
                    }
                    else {
                        $pswd = md5($pswd);
                        $pswd2 = md5($pswd2);
                        $query = mysqli_query($mysqli, "INSERT INTO users VALUES ('0', '$un', '$fn', '$ln', '$em', '$pswd', '$d', '0', '','','','')");
                        die("<h2>Welcome to bookbook</h2>Login to your account to get started ...");
                    }
                }
            }
            else {
                echo "Your passwords don't match!";
            }
        } else {
            echo "Please fill out all the fields";
        }
    } else {
        echo "Username already taken ...";
    }
}

// User login.
if (isset($_POST["user_login"]) && isset($_POST["password_login"])) {
    $user_login = preg_replace('#[^A-Za-z0-9]#i', '', $_POST["user_login"]); // filter everything
    $password_login = preg_replace('#[^A-Za-z0-9]#i', '', $_POST["password_login"]); // filter everything
    $password_login_md5 = md5($password_login);
    $sql = $mysqli->query("SELECT id FROM users WHERE username='$user_login' AND password='$password_login_md5' LIMIT 1");

    $userCount = mysqli_num_rows($sql);
    if($userCount == 1) {
        while ($row = mysqli_fetch_array($sql)) {
            $id = $row["id"];
        }
        $_SESSION["id"] = $id;
        $_SESSION["user_login"] = $user_login;
        $_SESSION["password_login"] = $password_login;
        header("location: $user_login"); // redirect to homepage
    } else {
        echo "That information is incorrect, try again";
    }
    exit();
}
?>

<table>
    <tr>
        <td width="60%" valign="top">
            <h2>Already a Member? Sign in below</h2>
            <form action="index.php" method="POST">
                <input type="text" name="user_login" size="25" placeholder="Username" /><br /><br />
                <input type="password" name="password_login" size="25" placeholder="Password" /><br /><br />
                <input type="submit" name="login" value="Log In!" /><br /><br />
            </form>
        </td>
        <td width="40%" valign="top">
            <h2>Sign Up Below!</h2>
            <form action="index.php" method="POST">
                <input type="text" name="fname" size="25" placeholder="First Name" /><br /><br />
                <input type="text" name="lname" size="25" placeholder="Last Name" /><br /><br />
                <input type="text" name="username" size="25" placeholder="Username" /><br /><br />
                <input type="text" name="email" size="25" placeholder="Email" /><br /><br />
                <input type="password" name="password" size="25" placeholder="Password" /><br /><br />
                <input type="password" name="password2" size="25" placeholder="Re-Enter Password" /><br /><br />
                <input type="submit" name="reg" value="Sign Up!" /><br /><br />
            </form>
        </td>

    </tr>
</table>
<?php include("./inc/footer.inc.php"); ?> 