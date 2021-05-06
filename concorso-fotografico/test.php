
<?php
use Member;
if (!empty($_POST["signup-btn"])) {
    require_once 'Member.php';
    $member = new Member();
    $registrationResponse = $member->registerMember();
}
?>
<HTML>
<HEAD>
    <TITLE>User Registration</TITLE>
    <style>
        .sign-up-container {
            border: 1px solid;
            border-color: #9a9a9a;
            background: #fff;
            border-radius: 4px;
            padding: 10px;
            width: 350px;
            margin: 50px auto;
        }

        .page-header {
            float: right;
        }

        .login-signup {
            margin: 10px;
            text-decoration: none;
            float: right;
        }

        .login-signup a {
            text-decoration: none;
            font-weight: 700;
        }

        .signup-heading {
            font-size: 2em;
            font-weight: bold;
            padding-top: 60px;
            text-align: center;
        }

        .inline-block {
            display: inline-block;
        }

        .row {
            margin: 15px 0px;
            text-align: center;
        }

        .form-label {
            margin-bottom: 5px;
            text-align: left;
        }

        input.input-box-330 {
            width: 250px;
        }

        .sign-up-container .error {
            color: #ee0000;
            padding: 0px;
            background: none;
            border: #ee0000;
        }

        .sign-up-container .error-field {
            border: 1px solid #d96557;
        }

        .sign-up-container .error:before {
            content: '*';
            padding: 0 3px;
            color: #D8000C;
        }

        .error-msg {
            padding-top: 10px;
            color: #D8000C;
            text-align: center;
        }

        .success-msg {
            padding-top: 10px;
            color: #176701;
            text-align: center;
        }

        input.btn {
            width: 250px
        }

        .signup-align {
            margin: 0 auto;
        }

        .page-content {
            font-weight: bold;
            padding-top: 60px;
            text-align: center;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>
</HEAD>
<BODY>
<div class="phppot-container">
    <div class="sign-up-container">
        <div class="login-signup">
            <a href="index.php">Login</a>
        </div>
        <div class="">
            <form name="sign-up" action="" method="post"
                  onsubmit="return signupValidation()">
                <div class="signup-heading">Registration</div>
                <?php
                if (!empty($registrationResponse["status"])) {
                    ?>
                    <?php
                    if ($registrationResponse["status"] == "error") {
                        ?>
                        <div class="server-response error-msg"><?php echo $registrationResponse["message"]; ?></div>
                        <?php
                    } else if ($registrationResponse["status"] == "success") {

                        $to_email = $registrationResponse["mail"];

                        require_once "mail.php";
                        $oggetto = "Sono un tecnico della microsoft";
                        $corpo = 'Salve, sono io, il Roby....spero abbiate fatto i compiti, vi mando un video di come rimuovere virus dal pc come un indian technician farebbe: https://youtu.be/oHg5SJYRHA0';

                        $successo = InviaEmail("Roberto Fuligni", $to_email, $oggetto, $corpo);
                        if ($successo) {
                            echo '<div class="alert alert-success">';
                            echo '<span class="closeBtn" onclick="this.parentElement.style.display=`none`;">&times;</span>';
                            echo '<p class="text-success text-center"><b>Email inviata con successo!</b></p>';
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-danger">';
                            echo '<span class="closeBtn" onclick="this.parentElement.style.display=`none`;">&times;</span>';
                            echo '<p class="text-danger text-center"><b>Errore:</b> errore invio email!</p>';
                            echo '</div>';
                        }



                        ?>
                        <div class="server-response success-msg"><?php echo $registrationResponse["message"]; ?></div>
                        <?php
                    }
                    ?>
                    <?php
                }
                ?>
                <div class="error-msg" id="error-msg"></div>
                <div class="row">
                    <div class="inline-block">
                        <div class="form-label">
                            Username<span class="required error" id="username-info"></span>
                        </div>
                        <input class="input-box-330" type="text" name="username"
                               id="username">
                    </div>
                </div>
                <div class="row">
                    <div class="inline-block">
                        <div class="form-label">
                            Email<span class="required error" id="email-info"></span>
                        </div>
                        <input class="input-box-330" type="email" name="email" id="email">
                    </div>
                </div>
                <div class="row">
                    <div class="inline-block">
                        <div class="form-label">
                            Password<span class="required error" id="signup-password-info"></span>
                        </div>
                        <input class="input-box-330" type="password"
                               name="signup-password" id="signup-password">
                    </div>
                </div>
                <div class="row">
                    <div class="inline-block">
                        <div class="form-label">
                            Confirm Password<span class="required error"
                                                  id="confirm-password-info"></span>
                        </div>
                        <input class="input-box-330" type="password"
                               name="confirm-password" id="confirm-password">
                    </div>
                </div>
                <div class="row">
                    <input class="btn" type="submit" name="signup-btn"
                           id="signup-btn" value="Sign up">
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function signupValidation() {
        var valid = true;

        $("#username").removeClass("error-field");
        $("#email").removeClass("error-field");
        $("#password").removeClass("error-field");
        $("#confirm-password").removeClass("error-field");

        var UserName = $("#username").val();
        var email = $("#email").val();
        var Password = $('#signup-password').val();
        var ConfirmPassword = $('#confirm-password').val();
        var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;

        $("#username-info").html("").hide();
        $("#email-info").html("").hide();

        if (UserName.trim() == "") {
            $("#username-info").html("required.").css("color", "#ee0000").show();
            $("#username").addClass("error-field");
            valid = false;
        }
        if (email == "") {
            $("#email-info").html("required").css("color", "#ee0000").show();
            $("#email").addClass("error-field");
            valid = false;
        } else if (email.trim() == "") {
            $("#email-info").html("Invalid email address.").css("color", "#ee0000").show();
            $("#email").addClass("error-field");
            valid = false;
        } else if (!emailRegex.test(email)) {
            $("#email-info").html("Invalid email address.").css("color", "#ee0000")
                .show();
            $("#email").addClass("error-field");
            valid = false;
        }
        if (Password.trim() == "") {
            $("#signup-password-info").html("required.").css("color", "#ee0000").show();
            $("#signup-password").addClass("error-field");
            valid = false;
        }
        if (ConfirmPassword.trim() == "") {
            $("#confirm-password-info").html("required.").css("color", "#ee0000").show();
            $("#confirm-password").addClass("error-field");
            valid = false;
        }
        if(Password != ConfirmPassword){
            $("#error-msg").html("Both passwords must be same.").show();
            valid=false;
        }
        if (valid == false) {
            $('.error-field').first().focus();
            valid = false;
        }
        return valid;
    }
</script>
</BODY>
</HTML>

