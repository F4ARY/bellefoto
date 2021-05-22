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

$ver = false;

if(isset($_SESSION['admin'])) {
    $ver = true;
    if ($_SESSION['admin'] != true) {
        $util->redirect("dashboard.php");
    }
}

if(isset($_COOKIE['admin'])) {
    $ver = true;
    if ($_COOKIE['admin'] != true) {
        $util->redirect("dashboard.php");
    }
}

if(!$ver)
    $util->redirect("dashboard.php");

if (isset($_SESSION["member_id"]))
    $current_id = $_SESSION["member_id"];
else
    $current_id = $member->getId($_COOKIE['member_login']);

if (isset($_POST["ignora"])) {
    $query = "UPDATE photo SET segnalazione = 0 WHERE photo_id = ?";
    $paramType = 'i';
    $paramValue = array(
        $_POST["photo_id"]
    );
    $db_handle->update($query, $paramType, $paramValue);
}

if (isset($_POST["nascondi"])) {
    $query = "UPDATE photo SET hidden = 1 WHERE photo_id = ?";
    $paramType = 'i';
    $paramValue = array(
        $_POST["photo_id"]
    );
    $db_handle->update($query, $paramType, $paramValue);

    $id = $member->getIdFromPhoto($_POST["photo_id"]);

    $member->bloccaMembro24($id);

    $query = "INSERT INTO message (context, sender_id, receiver_id) VALUES (?, ?, ?)";
    $messaggio =  "Sei stato bloccato per 24 ore a causa di una segnalazione ad una tua foto";
    $paramType = 'sii';
    $paramValue = array(
        $messaggio,
        $current_id,
        $id
    );
    $db_handle->insert($query, $paramType, $paramValue);
}

if (isset($_POST["espelli"])) {
    $id = $member->getIdFromPhoto($_POST["photo_id"]);
    $query = "UPDATE photo SET hidden = 1 WHERE member_id = ?";
    $paramType = 'i';
    $paramValue = array(
        $id
    );
    $db_handle->update($query, $paramType, $paramValue);
    $db_handle->baseUpdate('UPDATE members SET is_locked = 1 WHERE member_id = '.$id. ';');

    require_once "classes/mail.php";

    $mail = $db_handle->runBaseQuery("SELECT member_email FROM members WHERE member_id = ".$id. ";");

    $oggetto = "Espulsione Concorso Fotografico";
    $corpo = 'Le comunichiamo che è stato espulso dal concorso fotografico per comportamento inopportuno.';

    $successo = InviaEmail("Concorso Fotografico", $mail[0]['member_email'], $oggetto, $corpo);
}

$query = "SELECT 
            members.member_id,
            member_email,
            SUM(1_stella + 2_stelle + 3_stelle + 4_stelle + 5_stelle) AS 'Voti totali'
          FROM photo
          INNER JOIN members ON photo.member_id = members.member_id
          GROUP BY photo.member_id
          ORDER BY 'Voti totali' DESC
          LIMIT 10;
          
          ";

$righe = $db_handle->runBaseQuery("SELECT * FROM members");
$foto = $db_handle->runBaseQuery("SELECT * FROM photo WHERE segnalazione = true;");
$classifica = $db_handle->runBaseQuery($query);

?>
<HTML>
<HEAD>
    <TITLE>Admin</TITLE>
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
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
      integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
      crossorigin=""/>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
        integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
        crossorigin=""></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="phppot-container">

    <h2 style="text-align: center"><a href="dashboard.php">Home</a></h2>
    <form action="scaricapdf.php" style="text-align: center">
        <input type="submit" value="Stampa PDF" />
    </form>



    <div class="sign-up-container" style="position: center; width: 75%;">
        <div class="">
            <div class="signup-heading">Membri</div>

            <div class="row">
                <div style="overflow-x:auto;">
                    <table border="1" style=" margin-left: auto;margin-right: auto;">
                        <tbody><tr><td>ID</td> <td>Cognome</td><td>Nome</td><td>Email</td><td>Token</td><td>Foto Profilo</td><td>Verificato</td><td>Admin</td><td>Bloccato</td></tr><br>

                        <?php
                        $ver = 'no';
                        $ad = 'no';
                        $lck = 'no';

                        if($righe != null)
                        {
                            $lungh = count($righe);
                            for($i = 0; $i < $lungh; $i++)
                            {

                                if($righe[$i]['member_verified'] == 1)
                                    $ver = 'si';
                                else
                                    $ver = 'no';

                                if($righe[$i]['is_admin'] == 1)
                                    $ad = 'si';
                                else
                                    $ad = 'no';
                                if($righe[$i]['is_locked'] == 1)
                                    $lck = 'si';
                                else
                                    $lck = 'no';



                                echo '<tr><td>'.$righe[$i]['member_id'].'</td><td>'.$righe[$i]['member_surname'].'</td><td>'.$righe[$i]['member_name'].'</td><td>'.$righe[$i]['member_email'].'</td><td>'.$righe[$i]['member_token'].'</td><td>'.$righe[$i]['member_profile_picture'].'</td><td>'.$ver.'</td><td>'.$ad.'</td> <td>'.$lck.'</td></tr><br>';
                            }

                        }

                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="sign-up-container" style="position: center; width: 75%;">
            <div class="">
                <div class="signup-heading">Foto Segnalate</div>

                <div class="row">
                    <div style="overflow-x:auto;">


                            <?php
                            $hidden = 'no';

                            if($foto != null)
                            {
                                echo '<table border="1" style=" margin-left: auto;margin-right: auto;">';
                                echo'<tbody><tr><td>ID</td> <td>Nome</td><td>Descrizione</td><td>Media Voti</td><td>ID Fotografo</td><td>Nascosta</td><td>Foto</td></tr><br>';
                                $lungh = count($foto);
                                for($i = 0; $i < $lungh; $i++)
                                {
                                    if($foto[$i]['segnalazione'] == 1) {

                                        if ($foto[$i]['hidden'] == 1)
                                            $hidden = 'si';
                                        else
                                            $hidden = 'no';


                                        echo '<tr><td>' . $foto[$i]['photo_id'] . '</td><td>' . $foto[$i]['file_name'] . '</td><td>' . $foto[$i]['description'] . '</td><td>' . $foto[$i]['media'] . '</td><td>' . $foto[$i]['member_id'] . '</td><td>' . $hidden . '</td><td style=" height:10%;"><img style="max-height:100%; max-width:100%"  alt="' . $foto[$i]['file_name'] . '" src="imgs/' . $foto[$i]['file_name'] . '"></a></td></tr><br>';

                                        //Nascondi (MANCA MSG DI BAN PRIVATO)
                                        echo "<tr><td style='text-align: center'><form id='nascondi' method='post'><input type='hidden' name='photo_id' value='" . $foto[$i]["photo_id"] . "'/><input type='hidden' name='nascondi' value='1'/><input type='submit' name='submit_nascondi' value='Nascondi foto'/></form></td>";

                                        //Ignora segnalazione
                                        echo "<td style='text-align: center'><form id='ignora' method='post'><input type='hidden' name='photo_id' value='" . $foto[$i]["photo_id"] . "'/><input type='hidden' name='ignora' value='1'/><input type='submit' name='ignora' value='Ignora Segnalazione'/></form></td>";

                                        //Espelli utente
                                        echo "<td style='text-align: center'><form id='espelli' method='post'><input type='hidden' name='photo_id' value='" . $foto[$i]["photo_id"] . "'/><input type='hidden' name='espelli' value='1'/><input type='submit' name='espelli' value='Espelli Membro'/></form></td>";


                                        echo "</tr>";
                                    }



                                }

                            }else
                                echo '<tr><td>Nessuna foto è stata segnalata</td></tr>';


                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
<?php if($classifica != null){ ?>
    <div class="sign-up-container" style="position: center; width: 75%;">
        <canvas id="myChart"></canvas>
    </div>
    <?php } ?>

    <script>
        // === include 'setup' then 'config' above ===
        const DATA_COUNT = 7;
        const NUMBER_CFG = {count: DATA_COUNT, min: -100, max: 100};

        const labels = [
            <?php
               for($i = 0; $i < count($classifica); $i++)
                       echo "'".$classifica[$i]['member_email']."',"; ?>
        ];
        const data = {
            labels: labels,
            datasets: [
                {
                    label: 'Voti totali',

                    data: [

                        <?php
                        for($i = 0; $i < count($classifica); $i++)
                                echo $classifica[$i]['Voti totali'].","; ?>



                    ],

                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: '<?php echo $util->randomHex(); ?>',
                },

            ]
        };


        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Numero maggiore di votazioni (Top 10)'
                    }
                }
            },
        };



        var myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
    </script>


    <?php

    $logs_photo = $db_handle->runBaseQuery("
    SELECT log_vote.member_id, member_email, log_vote.photo_id, log_vote.vote, file_name, data_log
    FROM log_vote
    INNER JOIN members
    ON log_vote.member_id = members.member_id
    INNER JOIN photo
    ON log_vote.photo_id = photo.photo_id
    ORDER BY data_log DESC;
    
    
    ");

    ?>


    <div class="sign-up-container" style="position: center; width: 75%;">
        <div class="">
            <div class="signup-heading">Storico Votazioni</div>

            <div class="row">
                <div style="overflow: auto">
                    <?php

                    if($logs_photo != null)
                    {
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;">';
                        echo '<tbody><tr><td>Utente</td> <td>Foto votata</td><td>Voto assegnato</td><td>Data</td><td>ID Utente</td><td>ID Foto</td></tr><br>';

                        $lungh = count($logs_photo);
                        for($i = 0; $i < $lungh; $i++)
                        {
                            echo '<tr><td>'.$logs_photo[$i]['member_email'].'</td><td>'.$logs_photo[$i]['file_name']. ' </td><td>'.$logs_photo[$i]['vote']. ' </td><td>'.$logs_photo[$i]['data_log'].' </td><td>'.$logs_photo[$i]['member_id'].' </td><td>'.$logs_photo[$i]['photo_id'].' </td></tr>';
                        }
                        echo '</table>';
                    } else
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;"><tr><td>Nessun log di votazione presente</td></tr></table>';
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="sign-up-container" style="position: center; width: 75%;">
        <div class="">
            <div class="signup-heading">Storico immagini caricate</div>

            <div class="row">
                <div style="overflow: auto">
                    <?php
                    $query = "SELECT * FROM log_carica_foto ORDER BY data_log DESC";
                    $result = $db_handle->runBaseQuery($query);

                    if ($result != null)
                    {   
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;">';
                        foreach ($result as $riga) {
                            echo "<tr><td>Utente: ".$member->getEmailByID($riga["member_id"])." ha caricato un'immagine</td><td>".$riga["data_log"]."</td></tr>";
                        }
                        echo '</table>';
                    } else
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;"><tr><td>Nessuna immagine caricata</td></tr></table>';
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="sign-up-container" style="position: center; width: 75%;">
        <div class="">
            <div class="signup-heading">Storico descrizione aggiornata</div>

            <div class="row">
                <div style="overflow: auto">
                    <?php
                    $query = "SELECT * FROM log_foto_update ORDER BY data_log DESC";
                    $result = $db_handle->runBaseQuery($query);

                    if ($result != null)
                    {   
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;">';
                        foreach ($result as $riga) {
                            echo "<tr><td>Utente: ".$member->getEmailByID($riga["member_id"])." ha aggiornato una descrizione in: '".$riga["description"]."'</td><td>".$riga["data_log"]."</td></tr>";
                        }
                        echo '</table>';
                    } else
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;"><tr><td>Nessuna descrizione aggiornata</td></tr></table>';
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="sign-up-container" style="position: center; width: 75%;">
        <div class="">
            <div class="signup-heading">Storico segnalazioni</div>

            <div class="row">
                <div style="overflow: auto">
                    <?php
                    $query = "SELECT * FROM log_segnalazioni ORDER BY data_log DESC";
                    $result = $db_handle->runBaseQuery($query);

                    if ($result != null)
                    {   
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;">';
                        foreach ($result as $riga) {
                            echo "<tr><td>L'immagine con ID: ".$riga["photo_id"]." dell'Utente: ".$member->getEmailByID($riga["member_id"])." è stata segnalata</td><td>".$riga["data_log"]."</td></tr>";
                        }
                        echo '</table>';
                    } else
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;"><tr><td>Nessuna segnalazione</td></tr></table>';
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="sign-up-container" style="position: center; width: 75%;">
        <div class="">
            <div class="signup-heading">Storico nuovo commento</div>

            <div class="row">
                <div style="overflow: auto">
                    <?php
                    $query = "SELECT * FROM log_commenti ORDER BY data_log DESC";
                    $result = $db_handle->runBaseQuery($query);

                    if ($result != null)
                    {   
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;">';
                        foreach ($result as $riga) {
                            echo "<tr><td>Nuovo commento sotto l'immagine con ID: ".$riga["photo_id"]." scritto dall'Utente: ".$member->getEmailByID($riga["member_id"])."</td><td>".$riga["data_log"]."</td></tr>";
                        }
                        echo '</table>';
                    } else
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;"><tr><td>Nessun nuovo commento</td></tr></table>';
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="sign-up-container" style="position: center; width: 75%;">
        <div class="">
            <div class="signup-heading">Storico foto eliminate</div>

            <div class="row">
                <div style="overflow: auto">
                    <?php
                    $query = "SELECT * FROM log_elimina_foto ORDER BY data_log DESC";
                    $result = $db_handle->runBaseQuery($query);

                    if ($result != null)
                    {   
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;">';
                        foreach ($result as $riga) {
                            echo "<tr><td>L'immagine con ID: ".$riga["photo_id"]." dell'Utente: ".$member->getEmailByID($riga["member_id"])." è stata eliminata</td><td>".$riga["data_log"]."</td></tr>";
                        }
                        echo '</table>';
                    } else
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;"><tr><td>Nessuna foto eliminata</td></tr></table>';
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="sign-up-container" style="position: center; width: 75%;">
        <div class="">
            <div class="signup-heading">Storico messaggi</div>

            <div class="row">
                <div style="overflow: auto">
                    <?php
                    $query = "SELECT * FROM log_messaggi ORDER BY data_log DESC";
                    $result = $db_handle->runBaseQuery($query);

                    if ($result != null)
                    {   
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;">';
                        foreach ($result as $riga) {
                            echo "<tr><td>L'Utente: ".$member->getEmailByID($riga["sender_id"])." ha inviato un messaggio privato a: ".$member->getEmailByID($riga["receiver_id"])."</td><td>".$riga["data_log"]."</td></tr>";
                        }
                        echo '</table>';
                    } else
                        echo '<table border="1" style=" margin-left: auto;margin-right: auto;"><tr><td>Nessun messaggio privato</td></tr></table>';
                    ?>
                </div>
            </div>
        </div>
    </div>
