<?php
session_start();


require_once "classes/Auth.php";
require_once "classes/Util.php";
require_once "classes/Member.php";

$auth = new Auth();
$db_handle = new DBController();
$util = new Util();
$member = new Member();

require_once "classes/authCookieSessionValidate.php";

if ($isLoggedIn) {
    if(isset($_POST["member_email"]) && isset($_POST["member_password"])){

        $email = $_POST["member_email"];
        $password = $_POST["member_password"];

        $user = $auth->getMemberByUsername($email);
        $ver = $user[0]["member_verified"];
        $admin = $user[0]["is_admin"];

        if($ver == 1) {
            $_SESSION['verified'] = true;
            $_SESSION['mail'] = $_POST['member_email'];
        }

        if($admin == 1)
        {
            $_SESSION['admin'] = true;
            $_SESSION['verified'] = true;

            $util->redirect("admin.php");
        }

        if($_SESSION['verified'] == false)
            $util->redirect("verifica.php");

    }
    else{
        $_SESSION['verified'] = true;
        $util->redirect("dashboard.php");

    }
}


if (!empty($_POST["login"])) {
    $isAuthenticated = false;
    $email = $_POST["member_email"];
    $password = $_POST["member_password"];

    $user = $auth->getMemberByUsername($email);
    if ($user == null || $user == "") {
        $message = "Login non valido";
    } else if($user[0]['is_locked'] == true){
        $message = "Membro bloccato: Controllare la casella postale in caso di espulsione definitiva/Aspettare 24h";
    } else if (password_verify($password, $user[0]["member_password"])) {
        $isAuthenticated = true;

    }
  
    if ($isAuthenticated) {
        $_SESSION["member_id"] = $user[0]["member_id"];
        $admin = $user[0]["is_admin"];

        // Set Auth Cookies if 'Remember Me' checked
        if (!empty($_POST["remember"])) {
            setcookie("member_login", $email, $cookie_expiration_time);
            $user = $auth->getMemberByUsername($email);
            $admin = $user[0]["is_admin"];

            if($admin == 1)
                    setcookie("admin", true, $cookie_expiration_time);

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
            $_SESSION['verified'] = true;
            $_SESSION['mail'] = $_POST['member_email'];
        }

        if($admin == 1)
        {
            $_SESSION['verified'] = true;
            $_SESSION['admin'] = true;
            $util->redirect("admin.php");
        }

        if($_SESSION['verified'] == true)
            $util->redirect("dashboard.php");
        else {
            $_SESSION['mail'] = $_POST['member_email'];
            $util->redirect("verifica.php");
        }
    } else {
        if($user[0]['is_locked'] == true) {
            $message = "Membro bloccato: Controllare la casella postale in caso di espulsione definitiva/Aspettare 24h";
        }
        else
            $message = "Login non valido";
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

<div class="sign-up-container" style="display: flex;justify-content: center;align-items: center;">

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
        <div>
            <br>
            <a href="signup.php">Registrati</a>
        </div>
    </div>
    <div class="field-group">
        <div>
            <input type="submit" name="login" value="Login" class="form-submit-button"></span>
        </div>
    </div>
</form>
</div>