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


<!-- LOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOL -->

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Montserrat:400,700'>

    <style>
        body {
            background-color: #e9e9e9;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            line-height: 1.25;
            letter-spacing: 1px;
        }

        * {
            box-sizing: border-box;
            transition: .25s all ease;
        }

        .login-container {
            display: block;
            position: relative;
            z-index: 0;
            margin: 4rem auto 0;
            padding: 5rem 4rem 0 4rem;
            width: 100%;
            max-width: 525px;
            min-height: 680px;
            background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/283591/login-background.jpg);
            background: no-repeat;
            box-shadow: 0 50px 70px -20px rgba(0, 0, 0, 0.85);
        }

        .login-container:after {
            content: '';
            display: inline-block;
            position: absolute;
            z-index: 0;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-image: radial-gradient(ellipse at left bottom, rgba(22, 24, 47, 1) 0%, rgba(38, 20, 72, .9) 59%, rgba(17, 27, 75, .9) 100%);
            box-shadow: 0 -20px 150px -20px rgba(0, 0, 0, 0.5);
        }

        .form-login {
            position: relative;
            z-index: 1;
            padding-bottom: 4.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.25);
        }

        .login-nav {
            position: relative;
            padding: 0;
            margin: 0 0 6em 1rem;
        }

        .login-nav__item {
            list-style: none;
            display: inline-block;
        }

        .login-nav__item+.login-nav__item {
            margin-left: 2.25rem;
        }

        .login-nav__item a {
            position: relative;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            text-transform: uppercase;
            font-weight: 500;
            font-size: 1.25rem;
            padding-bottom: .5rem;
            transition: .20s all ease;
        }

        .login-nav__item.active a,
        .login-nav__item a:hover {
            color: #ffffff;
            transition: .15s all ease;
        }

        .login-nav__item a:after {
            content: '';
            display: inline-block;
            height: 10px;
            background-color: rgb(255, 255, 255);
            position: absolute;
            right: 100%;
            bottom: -1px;
            left: 0;
            border-radius: 50%;
            transition: .15s all ease;
        }

        .login-nav__item a:hover:after,
        .login-nav__item.active a:after {
            background-color: rgb(17, 97, 237);
            height: 2px;
            right: 0;
            bottom: 2px;
            border-radius: 0;
            transition: .20s all ease;
        }

        .login__label {
            display: block;
            padding-left: 1rem;
        }

        .login__label,
        .login__label--checkbox {
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            font-size: .75rem;
            margin-bottom: 1rem;
        }

        .login__label--checkbox {
            display: inline-block;
            position: relative;
            padding-left: 1.5rem;
            margin-top: 2rem;
            margin-left: 1rem;
            color: #ffffff;
            font-size: .75rem;
            text-transform: inherit;
        }

        .login__input {
            color: white;
            text-align: center;
            font-size: 1.15rem;
            width: 100%;
            padding: .5rem 1rem;
            border: 2px solid transparent;
            outline: none;
            border-radius: 1.5rem;
            background-color: rgba(255, 255, 255, 0.25);
            letter-spacing: 1px;
        }

        .login__input:hover,
        .login__input:focus {
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.5);
            background-color: transparent;
        }

        .login__input+.login__label {
            margin-top: 1.5rem;
        }

        .login__input--checkbox {
            position: absolute;
            top: .1rem;
            left: 0;
            margin: 0;
        }

        .login__submit {
            color: #ffffff;
            font-size: 1rem;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 1rem;
            padding: .75rem;
            border-radius: 2rem;
            display: block;
            width: 100%;
            background-color: rgba(17, 97, 237, .75);
            border: none;
            cursor: pointer;
        }

        .login__submit:hover {
            background-color: rgba(17, 97, 237, 1);
        }

        .login__forgot {
            display: block;
            margin-top: 3rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.75);
            font-size: .75rem;
            text-decoration: none;
            position: relative;
            z-index: 1;
        }

        .login__forgot:hover {
            color: rgb(17, 97, 237);
        }

        .errore{
            text-align: center;
            padding: 20px 0 20px 0;
            margin: 40px;
            background: rgba(255, 0, 0, .3);
            border: 1px red solid;
            border-radius: 40px;
        }

        .errore span{
            color: white;
        }
    </style>
</head>

<body>
<div class="login-container">
    <form action="" class="form-login">
        <ul class="login-nav">
            <li class="login-nav__item active">
                <a href="#">Login</a>
            </li>
            <li class="login-nav__item">
                <a href="#">Registrati</a>
            </li>
        </ul>
        <label for="login-input-user" class="login__label">Email</label>
        <input id="login-input-user" class="login__input" type="email" />
        <label for="login-input-password" class="login__label">Password</label>
        <input id="login-input-password" class="login__input" type="password" />
        <label for="login-sign-up" class="login__label--checkbox">
            <input id="login-sign-up" type="checkbox" class="login__input--checkbox" />
            Ricordami
        </label>
        <button class="login__submit" type="submit">Accedi</button>

        <?php if(isset($message)) {
            echo '<div class="errore">
                        <span>' . $message . '</span>
                  </div>';
        } ?>
    </form>
    <a class="login__forgot">&copy; George Patrut, 2021 </a>
</div>

</body>

</html>