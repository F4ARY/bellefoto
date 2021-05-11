<?php 
session_start();

require_once "Member.php";
require_once "Util.php";
require_once "authCookieSessionValidate.php";

if(!$isLoggedIn) {
    header("Location: ./");
}
else {
    $util = new Util();
    $ds = new DBController();
    $member = new Member();
    $current_id = $_SESSION["member_id"];

    // echo "<p>Elimina: ".$_POST["elimina"]."</p>";
    // echo "<p>Vota: ".$_POST["submit_voto"]."</p>";
    // echo "<p>Modifica: ".$_POST["modifica"]."</p>";
    // echo "<p>Segnala: ".$_POST["segnala"]."</p>";
    // echo "<p>Commenta: ".$_POST["commenta"]."</p>";

    if (isset($_POST["elimina"])) {
        $arr = array(
            $_POST["photo_id"]
        );
        $risposta = $member->cancellaImmagine($arr);
        echo "<p>".$risposta["message"]."</p>";
    }
    
    if (isset($_POST["submit_voto"])) {
        $votoPresente = 0;

        if (!isset($_COOKIE["votazioni"])) {
            $arrayVotazioni = array(
                0 => array(
                    "member_id_voted" => $current_id,
                    "photo_id_voted" => $_POST["photo_id"]
                )
            );
            print_r($arrayVotazioni);
            setcookie("votazioni", json_encode($arrayVotazioni));
            print_r(json_decode($_COOKIE["votazioni"]));
        } else {
            $arrayVotazioni = json_decode($_COOKIE["votazioni"], true);
            $nuoviDati = array(
                0 => array(
                    "member_id_voted" => $current_id,
                    "photo_id_voted" => $_POST["photo_id"]
                )
            );
            $arrayVotazioni = array_push($nuoviDati);
            setcookie("votazioni", json_encode($arrayVotazioni));
        }
        
        if ($votoPresente != 1) {  
            $query = "UPDATE photo SET ";
            switch ($_POST["submit_voto"]) {
                case 1: 
                    $query .= "1_stella = 1_stella + 1 WHERE PHOTO_ID = ?";
                    break;
                case 2:  
                    $query .= "2_stelle = 2_stelle + 1 WHERE PHOTO_ID = ?";
                    break;
                case 3: 
                    $query .= "3_stelle = 2_stelle + 1 WHERE PHOTO_ID = ?";
                    break;
                case 4:  
                    $query .= "4_stelle = 4_stelle + 1 WHERE PHOTO_ID = ?";
                    break;
                case 5:  
                    $query .= "5_stelle = 5_stelle + 1 WHERE PHOTO_ID = ?";
                    break;
            }
            $paramType = 'i';
            $paramValue = array(
                $_POST["photo_id"]
            );
            $ds->update($query, $paramType, $paramValue);
            echo "<p>Voto aggiunto</p>";
        } else {
            echo "<p>Voto già presente</p>";
        }
    }
    
    if (isset($_POST["modifica"])) {
        $query = "UPDATE photo SET description = ? WHERE photo_id = ?";
        $paramType = 'si';
        $paramValue = array(
            $_POST["new_description"],
            $_POST["photo_id"]
        );
        $ds->update($query, $paramType, $paramValue);
        echo "<p>Descrizione aggiornato</p>";
    }

    if (isset($_POST["segnala"])) {
        $query = "UPDATE photo SET segnalazione = 1 WHERE photo_id = ?";
        $paramType = 'i';
        $paramValue = array(
            $_POST["photo_id"]
        );
        $ds->update($query, $paramType, $paramValue);
        echo "<p>Segnalazione effettuata</p>";
    }

    if (isset($_POST["commenta"])) {
        $query = "INSERT INTO comment (comment, member_id, photo_id) VALUES (?, ?, ?)";
        $paramType = "sii";
        $paramValue = array(
            $_POST["comment"],
            $current_id,
            $_POST["photo_id"]
        );
        $ds->insert($query, $paramType, $paramValue);
        echo "<p>Commento inserito</p>";
    }
}
?>
<style>
.member-dashboard {
    padding: 40px;
    background: #D2EDD5;
    color: #555;
    border-radius: 4px;
    display: inline-block;
}

.member-dashboard a {
    color: #09F;
    text-decoration: none;
}
</style>
<div class="member-dashboard">
    You have Successfully logged in!. <a href="logout.php">Logout</a>
</div>
<div>
<form id="form_ricerca" method="post">
    <input type="textbox" placeholder="Ricerca per descrizione" name="ricerca"/>
    <input type="hidden" name="filtra" value="1"/>
    <input type="submit" name="submit_ricerca" value="Cerca"/>
</form>
<?php
    $query = "SELECT * FROM photo";
    if (isset($_POST["filtra"])) {
        $query = "SELECT * FROM photo WHERE description LIKE '%".$_POST["ricerca"]."%'";
    }
    $resultArray = $ds->select($query);
    /*
    if (!empty($resultArray))
        print_r(array_reverse($resultArray));
    */
    echo "<div>";
    echo "<table border='1' style='max-width: 70%'>";
    if (!empty($resultArray)) {
        foreach (array_reverse($resultArray) as $arr) {
            // Mostra immagine
            echo "<tr><td><img src='imgs/".$arr["file_name"]."' style='max-width: 512px'/></td>";
            
            // Mostra elimina solo se stesso utente
            if ($current_id == $arr["member_id"]) {
                echo "<td><form id='form_elimina_foto' method='post'><input type='hidden' name='photo_id' value='".$arr["photo_id"]."'/><input type='hidden' name='elimina' value='1'/><input type='submit' name='submit_elimina' value='Elimina foto'/></form></td>";
            }
            echo "</tr>";
            
            // Mostra media voto
            if (!empty(trim($arr["media"]))) {
                echo "<tr><td>Votazione media: ".$arr["media"]."</td></tr>";
            } else {
                echo "<tr><td>Ancora nessun voto registrato</td></tr>";
            }
            
            // Votazione foto solo se utente diverso
            if (/*$current_id != $arr["member_id"]*/true) {
                echo "<tr><td>Lascia un voto</td></tr>";
                echo "<tr><td><form id='form_vota' method='post'><input type='hidden' name='photo_id' value='".$arr["photo_id"]."'/><input type='submit' name='submit_voto' value='1'/><input type='submit' name='submit_voto' value='2'/><input type='submit' name='submit_voto' value='3'/><input type='submit' name='submit_voto' value='4'/><input type='submit' name='submit_voto' value='5'/></form></tr>";
            }
            
            // Segnala foto
            echo "<tr><td><form id='form_segnala_foto' method='post'><input type='hidden' name='photo_id' value='".$arr["photo_id"]."'/><input type='hidden' name='segnala' value='1'/><input type='submit' name='submit_segnala' value='Segnala foto'/></form></td></tr>";
            
            // Descrizione se presente e modifica se stesso utente
            if (!empty(trim($arr["description"]))) {
                echo "<tr><td>Descrizione: ".$arr["description"]."</td>";
                if ($current_id == $arr["member_id"]) {
                    echo "<td><form id='form_modifica_descrizione' method='post'><input type='hidden' name='photo_id' value='".$arr["photo_id"]."'/><input type='hidden' name='modifica' value='1'/><input type='textbox' name='new_description'/><input type='submit' name='submit_modifica' value='Modifica descrizione'/></form></td>";
                }
                echo "</tr>";
            }

            // Commento
            echo "<tr><td>Lascia un commento: <form id='form_commenta' method='post'><input type='textbox' name='comment'/><input type='hidden' name='photo_id' value='".$arr["photo_id"]."'/><input type='hidden' name='commenta' value='1'/><input type='submit' name='submit_commenta' value='Commenta'/></form></td></tr>";
            $query = "SELECT comment, member_id FROM comment WHERE photo_id =".$arr["photo_id"];
            $commenti = $ds->select($query);
            if (!empty($commenti)) {
                echo "<tr><td>Commenti</td>";
                foreach ($commenti as $comm) {
                    $id = $comm["member_id"];
                    $utente = $member->getEmailByID($id);
                    echo "<tr><td>".$utente."</td><td>".$comm["comment"]."</td></tr>";
                }
            } else {
                echo "<tr><td>Ancora nessun commento</tr>";
            }
        }
        echo "</table>";
    } else {
        echo "<p>Nessun risultato per: ".$_POST["ricerca"]."<p><a href='index.php'>Annulla ricerca</a>";
    }

    echo "</div>";
?>
</div>