<?php
require_once "Util.php";
require_once "Member.php";




session_start();

$util = new Util();
$member = new Member();

$mail = $_SESSION['mail'];



if(!$_SESSION['verified'])
{
    $util->redirect("index.php");
}

//Per caricare
if(isset($_POST['carica'])) {
    if (isset($_POST['desc'])) {
        $risposta = $member->caricaImmagine($mail, $_FILES['picture'], $_POST['desc']);

    }
}

//Per eliminare
if(isset($_POST['rimuovi'])) {
    if (isset($_POST['checkbox'])) {
        $risposta = $member->cancellaImmagine($_POST['checkbox']);
    }
}

//Per modifica
if(isset($_POST['edit'])) {
    if (isset($_POST['checkbox'])) {
        $risposta = $member->editImmagine($_POST['checkbox'], $_POST['desc']);
    }
}

$righe = $member->getFoto($mail);




?>

<HTML>
<HEAD>
    <TITLE>Gestione Foto</TITLE>
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

    <?php
    if (!empty($risposta["status"])) {
        if ($risposta["status"] == "success") {
            ?>
            <div class="server-response success-msg"><?php echo $risposta["message"]; ?></div>
            <?php
        }
    }
    ?>


    <div class="sign-up-container" style="display: flex;justify-content: center;align-items: center;">

        <div class="">
            <form name="sign-up" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <div class="signup-heading">Carica Foto</div>




                <div class="row">
                    <div class="inline-block">
                        <div class="form-label">
                            <span id="picture-info"></span>
                        </div>
                        <input class="upload-image" type="file" name="picture"
                               id="picture" accept="image/*" required>
                    </div>
                </div>
                <div class="row">
                    <div class="inline-block">
                        <div class="form-label">
                            Descrizione<span class="required error" id="desc-info"></span>
                        </div>
                        <textarea style="resize: none;" cols="33" name="desc"  id="desc" maxlength="255" required > </textarea>
                    </div>
                </div>
                <input type="hidden" name="carica" id="carica" value="carica"/>
                <div class="row">
                    <input class="btn" type="submit" name="signup-btn"
                           id="signup-btn" value="Carica">
                </div>
            </form>
        </div>
    </div>

        <!-- Rimuovi -->

    <div class="sign-up-container" style="display: flex;justify-content: center;align-items: center;">
        <div class="">
            <form name="sign-up" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <div class="signup-heading">Rimuovi Foto</div>




                <div class="row">
                    <div class="inline-block">
                        <?php

                            if($righe != null)
                            {
                                $lungh = count($righe);
                                for($i = 0; $i < $lungh; $i++)
                                {
                                    echo '<input type="checkbox" name="checkbox[]" value="'.$righe[$i]['photo_id'].'">'.$righe[$i]['file_name'].' <br>';
                                }

                            }

                            ?>


                    </div>
                </div>



                <input type="hidden" name="rimuovi" id="rimuovi" value="rimuovi" />
                <div class="row">
                    <input class="btn" type="submit" name="signup-btn"
                           id="signup-btn" value="Rimuovi">
                </div>
            </form>
        </div>
    </div>

        <!-- Modifica desc -->
    <div class="sign-up-container" style="display: flex;justify-content: center;align-items: center;">
        <div class="">
            <form name="sign-up" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <div class="signup-heading">Modifica Descrizione</div>



                <div class="row">
                    <div class="inline-block">
                        <?php

                        if($righe != null)
                        {
                            $lungh = count($righe);
                            for($i = 0; $i < $lungh; $i++)
                            {
                                echo '<input type="checkbox" name="checkbox[]" value="'.$righe[$i]['photo_id'].'">'.$righe[$i]['file_name'].' <br>';
                            }

                        }

                        ?>


                    </div>
                </div>

                <div class="row">
                    <div class="inline-block">
                        <div class="form-label">
                            Descrizione<span class="required error" id="desc-info"></span>
                        </div>
                        <textarea style="resize: none;" cols="33" name="desc"  id="desc" maxlength="255" required > </textarea>
                    </div>
                </div>
                <input type="hidden" name="edit" id="edit" value="edit" />
                <div class="row">
                    <input class="btn" type="submit" name="signup-btn"
                           id="signup-btn" value="Modifica">
                </div>
            </form>
        </div>


    </div>

    <!-- tabella desc -->

    <div class="sign-up-container" style="position: center; width: 75%;">
        <div class="">
                <div class="signup-heading">Descrizioni</div>



                <div class="row">
                    <div style="overflow-x:auto;">
                        <table border="1" style=" margin-left: auto;margin-right: auto;">
                        <tbody><tr><td>Descrizione</td> <td>Nome Immagine</td><td>Immagine</td></tr><br>

                        <?php

                        if($righe != null)
                        {
                            $lungh = count($righe);
                            for($i = 0; $i < $lungh; $i++)
                            {
                                echo '<tr><td>'.$righe[$i]['description'].'</td><td>'.$righe[$i]['file_name'].'</td><td style=" height:10%;"><img style="max-height:100%; max-width:100%"  alt="'.$righe[$i]['file_name'].'" src="imgs/'.$righe[$i]['file_name'].'"></a></td></tr><br>';
                            }

                        }

                        ?>

                            </tbody></table>

                    </div>
                </div>



        </div>


    </div>




</div>


</BODY>
</HTML>

