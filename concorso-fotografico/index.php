<?php
session_start();

require_once "Auth.php";
require_once "Util.php";
require_once "Member.php";

$auth = new Auth();
$db_handle = new DBController();
$util = new Util();
$member = new Member();

require_once "authCookieSessionValidate.php";

if ($isLoggedIn) {
    if(isset($_POST["member_email"]) && isset($_POST["member_password"])){

        $email = $_POST["member_email"];
        $password = $_POST["member_password"];

        $user = $auth->getMemberByUsername($email);
        $ver = $user[0]["member_verified"];

        if($ver == 1) {
            $util->redirect("dashboard.php");
        }else
            $util->redirect("verifica.php");
    }
    else
        $util->redirect("dashboard.php");
}

if (!empty($_POST["login"])) {
    $isAuthenticated = false;
    $email = $_POST["member_email"];
    $password = $_POST["member_password"];

    $user = $auth->getMemberByUsername($email);
    if ($user == null || $user == "") {
        $message = "Invalid Login";
    } else if (password_verify($password, $user[0]["member_password"])) {
        $isAuthenticated = true;
    }
  
    if ($isAuthenticated) {
        $_SESSION["member_id"] = $user[0]["member_id"];

        // Set Auth Cookies if 'Remember Me' checked
        if (!empty($_POST["remember"])) {
            setcookie("member_login", $email, $cookie_expiration_time);

            $random_password = $util->getToken(16);
            setcookie("random_password", $random_password, $cookie_expiration_time);

            $random_selector = $util->getToken(32);
            setcookie("random_selector", $random_selector, $cookie_expiration_time);

            $random_password_hash = password_hash($random_password, PASSWORD_DEFAULT);
            $random_selector_hash = password_hash($random_selector, PASSWORD_DEFAULT);

            $expiry_date = date("Y-m-d H:i:s", $cookie_expiration_time);

            // mark existing token as expired
            $userToken = $auth->getTokenByUsername($email, 0);
            if (!empty($userToken[0]["id"])) {
                $auth->markAsExpired($userToken[0]["id"]);
            }
            // Insert new token
            $auth->insertToken($email, $random_password_hash, $random_selector_hash, $expiry_date);
        } else {
            $util->clearAuthCookie();
        }
        $ver = $user[0]['member_verified'];
        if($ver == 1) {
            $util->redirect("dashboard.php");
            }
        else {
            $util->redirect("verifica.php");
        }
    } else {
        $message = "Invalid Login";
    }
}
?>
<style>
    body {
        font-family: Arial;
    }

    #frmLogin {
        padding: 20px 40px 40px 40px;
        background: #d7eeff;
        border: #acd4f1 1px solid;
        color: #333;
        border-radius: 2px;
        width: 300px;
    }

    .field-group {
        margin-top: 15px;
    }

    .input-field {
        padding: 12px 10px;
        width: 100%;
        border: #A3C3E7 1px solid;
        border-radius: 2px;
        margin-top: 5px
    }

    .form-submit-button {
        background: #3a96d6;
        border: 0;
        padding: 10px 0px;
        border-radius: 2px;
        color: #FFF;
        text-transform: uppercase;
        width: 100%;
    }

    .error-message {
        text-align: center;
        color: #FF0000;
    }
</style>

<form action="" method="post" id="frmLogin">
    <div class="error-message"><?php if(isset($message)) { echo $message; } ?></div>
    <div class="field-group">
        <div>
            <label for="login">E-Mail</label>
        </div>
        <div>
            <input name="member_email" type="text"
                   value="<?php if(isset($_COOKIE["member_login"])) { echo $_COOKIE["member_login"]; } ?>"
                   class="input-field">
        </div>
    </div>
    <div class="field-group">
        <div>
            <label for="password">Password</label>
        </div>
        <div>
            <input name="member_password" type="password"
                   value="<?php if(isset($_COOKIE["member_password"])) { echo $_COOKIE["member_password"]; } ?>"
                   class="input-field">
        </div>
    </div>
    <div class="field-group">
        <div>
            <input type="checkbox" name="remember" id="remember"
                <?php if(isset($_COOKIE["member_login"])) { ?> checked
                <?php } ?> /> <label for="remember-me">Remember me</label>
        </div>
    </div>
    <div class="field-group">
        <div>
            <input type="submit" name="login" value="Login" class="form-submit-button"></span>
        </div>
    </div>
</form>