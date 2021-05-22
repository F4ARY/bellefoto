<?php

session_start();

require_once "classes/Auth.php";
require_once "classes/Util.php";
require_once "classes/Member.php";

$auth = new Auth();
$ds = new DBController();
$util = new Util();
$member = new Member();

require_once "classes/authCookieSessionValidate.php";

$query = "SELECT member_verified FROM members WHERE member_email = ?";

if (isset($_SESSION['mail']))
    $mail = $_SESSION['mail'];
else {
    $mail = $_COOKIE['member_login'];
    $ris = $ds->select($query, "s", array($_COOKIE['member_login']));
    if($ris[0]['member_verified'] == false)
        $util->redirect("index.php");
}

if (isset($_SESSION['verified'])) {
    if (!$_SESSION['verified']) {
        $util->redirect("index.php");
    }
}

if (isset($_SESSION["member_id"]))
    $current_id = $_SESSION["member_id"];
else
    $current_id = $member->getId($_COOKIE['member_login']);

echo "<p>Sono l'utente con id: ".$current_id."</p>";
echo "<p>La mia email è: ".$member->getEmailByID($current_id)."</p>";

if (isset($_POST["messaggio"])) {
    $time = "now()";
    $query = "INSERT INTO message (context, sender_id, receiver_id) VALUES (?, ?, ?)";
    $paramType = "sii";
    $paramValue = array(
        $_POST["message"],
        $current_id,
        $_POST["receiver_id"]            
    );
    $ds->insert($query, $paramType, $paramValue);
    echo "<p>Messaggio inviato</p>";
}
?>
<div>
    <form action="dashboard.php" style="padding-left: 5px">
        <input type="submit" value="Dashboard" />
    </form>
    <form action="gestiscifoto.php" style="padding-left: 5px">
        <input type="submit" value="Gestisci Foto" />
    </form>
    <form action="logout.php" style="padding-left: 5px">
        <input type="submit" value="Log out" />
    </form>
        <?php
        $query = "SELECT * FROM message WHERE sender_id = ? OR receiver_id = ? ORDER BY recsen_id";
        $paramType = "ii";    
        $paramValue = array (
            $current_id,
            $current_id         
        );
        $messaggiArray = $ds->select($query, $paramType, $paramValue);

        $lastSender = 0;
        $lastReceiver = 0;
        $lastChat = 0;
        $email = 0;
        if($messaggiArray != null) {
            echo "<table border='1'>";
            foreach ($messaggiArray as $messaggio) {
                // Stampo email dell'utente
                if ($lastChat != $messaggio["recsen_id"]) {
                    if ($email != 0 && $current_id == $lastSender) {
                        echo "<tr><td>Invia un messaggio: </td><td><form id='form_messaggio' method='post'><input type='textbox' name='message'/><input type='hidden' name='receiver_id' value='" . $lastReceiver . "'/><input type='hidden' name='messaggio' value='1'/><input type='submit' name='submit_messaggio' value='Invia'/></form></td></tr>";
                    } else if ($email != 0 && $current_id == $lastReceiver) {
                        echo "<tr><td>Invia un messaggio: </td><td><form id='form_messaggio' method='post'><input type='textbox' name='message'/><input type='hidden' name='receiver_id' value='" . $lastSender . "'/><input type='hidden' name='messaggio' value='1'/><input type='submit' name='submit_messaggio' value='Invia'/></form></td></tr>";
                    }

                    if ($messaggio["sender_id"] != $current_id) {
                        $email = $member->getEmailByID($messaggio["sender_id"]);
                        echo "<tr><td>Utente: " . $email . "</td></tr>";
                    } else {
                        $email = $member->getEmailByID($messaggio["receiver_id"]);
                        echo "<tr><td>Utente: " . $email . "</td></tr>";
                    }
                }

                // Stampo messaggio
                echo "<tr><td>" . $messaggio["time"] . "</td>";

                // Se il messaggio è stato inviato dall'utente corrente, align del messaggio a destra
                if ($messaggio["sender_id"] == $current_id) {
                    echo "<td style='text-align: right;'>" . $messaggio["context"] . "</td><td style='text-align: right;'>Io</td></tr>";
                } else {
                    echo "<td>" . $messaggio["context"] . "</td><td>" . $email . "</td></tr>";;
                }


                // Variabile d'appoggio, se cambia il sender, viene stampata la sua email
                $lastChat = $messaggio["recsen_id"];
                $lastSender = $messaggio["sender_id"];
                $lastReceiver = $messaggio["receiver_id"];
            }

            if ($current_id == $lastSender) {
                echo "<tr><td>Invia un messaggio: </td><td><form id='form_messaggio' method='post'><input type='textbox' name='message'/><input type='hidden' name='receiver_id' value='" . $lastReceiver . "'/><input type='hidden' name='messaggio' value='1'/><input type='submit' name='submit_messaggio' value='Invia'/></form></td></tr>";
            } else {
                echo "<tr><td>Invia un messaggio: </td><td><form id='form_messaggio' method='post'><input type='textbox' name='message'/><input type='hidden' name='receiver_id' value='" . $lastSender . "'/><input type='hidden' name='messaggio' value='1'/><input type='submit' name='submit_messaggio' value='Invia'/></form></td></tr>";
            }
            echo "</table>";
        }
        
        ?>
</div>