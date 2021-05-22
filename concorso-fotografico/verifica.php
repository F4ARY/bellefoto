<?php
session_start();

require_once "classes/Member.php";
require_once "classes/Util.php";
require_once "classes/Auth.php";

if(isset($_POST['member_email']) && isset($_POST['member_token']))
{
    $auth = new Auth();
    $member = new Member();
    $util = new Util();
    $token = $_POST['member_token'];
    $email = $_POST['member_email'];
    if($member->verify($email, $token)){
        $_SESSION['verified'] = true;
        $user = $auth->getMemberByUsername($email);
        $admin = $user[0]["is_admin"];
        $_SESSION['mail'] = $_POST['member_email'];
        if($admin == 1)
        {
                $_SESSION['admin'] = true;
                $util->redirect("admin.php");
        }
        else
            $util->redirect("dashboard.php");
    }
    else {
        $message = "Errore: email o token non validi";
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

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="frmLogin">

    <div class="error-message"><?php if(isset($message)) { echo $message; } ?></div>
    <div class="field-group">
        <div>
            <label for="member_email">Email</label>
        </div>
        <div>
            <input name="member_email" type="email" class="input-field">
        </div>
        <br>
        <div>
            <label for="member_token">Token di verifica</label>
        </div>
        <div>
            <input name="member_token" type="text" class="input-field">
        </div>
    </div>
    <div class="field-group">
        <div>
            <input type="submit" name="verify" value="Verifica"
                   class="form-submit-button"></span>
        </div>
    </div>
    </form>
</div>