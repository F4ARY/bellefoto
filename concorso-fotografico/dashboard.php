<?php 
session_start();

require_once "Member.php";
require_once "Util.php";
require_once "authCookieSessionValidate.php";

if(!$isLoggedIn) {
    header("Location: ./");
} else {
    $util = new Util();
    $ds = new DBController();
    $member = new Member();
    $current_id = $_SESSION["member_id"];

    // echo "<p>Elimina: ".$_POST["elimina"]."</p>";
    // echo "<p>Vota: ".$_POST["submit_voto"]."</p>";
    // echo "<p>Modifica: ".$_POST["modifica"]."</p>";
    // echo "<p>Segnala: ".$_POST["segnala"]."</p>";
    // echo "<p>Commenta: ".$_POST["commenta"]."</p>";
    // echo "<p>Foto: ".$_POST["photo_id"]."</p>";

    if (isset($_POST["elimina"])) {
        $arr = array(
            $_POST["photo_id"]
        );
        $risposta = $member->cancellaImmagine($arr);
        echo "<p>".$risposta["message"]."</p>";
    }

    if (isset($_POST["submit_voto"])) {
        $query = "SELECT vote FROM vote WHERE member_id = ? AND photo_id = ?";
        $paramType = "ii";
        $paramValue = array(
            $current_id,
            $_POST["photo_id"]
        );
        $votoPresente = $ds->select($query, $paramType, $paramValue);

        $query = "UPDATE photo SET ";
        if (empty($votoPresente[0]["vote"])) {
            $inserisciVoto = "INSERT INTO vote (member_id, photo_id, vote) VALUES (?, ?, ?)";
            $paramType = "iii";
            $paramValue = array(
                $current_id,
                $_POST["photo_id"],
                $_POST["submit_voto"]
            );
            $ds->insert($inserisciVoto, $paramType, $paramValue);
        } else if ($votoPresente[0]["vote"] > 0 && $votoPresente[0]["vote"] != $_POST["submit_voto"]) {
            switch ($votoPresente[0]["vote"]) {
                case 1: 
                    $query .= "1_stella = 1_stella - 1, ";
                    break;
                case 2:  
                    $query .= "2_stelle = 2_stelle - 1, ";
                    break;
                case 3: 
                    $query .= "3_stelle = 3_stelle - 1, ";
                    break;
                case 4:  
                    $query .= "4_stelle = 4_stelle - 1, ";
                    break;
                case 5:  
                    $query .= "5_stelle = 5_stelle - 1, ";
                    break;
            }

            $aggiornaVoto = "UPDATE vote SET vote = ? WHERE member_id = ? AND photo_id = ?";
            $paramType = "iii";
            $paramValue = array(
                $_POST["submit_voto"],
                $current_id,
                $_POST["photo_id"]
            );
            $ds->update($aggiornaVoto, $paramType, $paramValue);
        }
        
        if (empty($votoPresente[0]["vote"]))
            $votoPresente[0]["vote"] = 0;

        if ($votoPresente[0]["vote"] != $_POST["submit_voto"]) {
            switch ($_POST["submit_voto"]) {
                case 1: 
                    $query .= "1_stella = 1_stella + 1 ";
                    break;
                case 2:  
                    $query .= "2_stelle = 2_stelle + 1 ";
                    break;
                case 3: 
                    $query .= "3_stelle = 3_stelle + 1 ";
                    break;
                case 4:  
                    $query .= "4_stelle = 4_stelle + 1 ";
                    break;
                case 5:  
                    $query .= "5_stelle = 5_stelle + 1 ";
                    break;
            }
            $query .= "WHERE photo_id = ?";
            $paramType = 'i';
            $paramValue = array(
                $_POST["photo_id"]
            );
            $ds->update($query, $paramType, $paramValue);

            echo "<p>Voto inserito/aggiornato</p>";
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
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
      integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
      crossorigin=""/>
      
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
        integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
        crossorigin=""></script>
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
        $cont = 0;
        foreach (array_reverse($resultArray) as $arr) {
            // if img != nascosta, mostra

            // Mostra immagine
            echo "<tr><td><img src='imgs/".$arr["file_name"]."' style='max-width: 512px'/></td>";

            // Mappa se presente
            if ($arr['lat'] != NULL && $arr['lng'] != NULL)
            {
                $imgLat = $arr['lat'];
                $imgLng = $arr['lng'];

                echo '<td><div id="map'.$cont.'" style="display: flex;justify-content: center;align-items: center;position: center;height: 300px; width: 300px" class="sign-up-container"></div></td>'
?>
                <script>
                    var map = L.map('<?php echo "map".$cont; ?>').setView([<?php echo $imgLat; ?>, <?php echo $imgLng; ?>], 4);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright%22%3EOpenStreetMap</a> contributors'
                    }).addTo(map);

                    L.marker([<?php echo $imgLat; ?>, <?php echo $imgLng; ?>]).addTo(map)
                        .bindPopup("Img: <?php echo $arr['file_name']. '<br> Lat: '.$imgLat. '<br> Long: '.$imgLng?>")
                        .openPopup();
                </script>
<?php
            }
            echo "</tr>";

            // Mostra elimina solo se stesso utente
            if ($current_id == $arr["member_id"]) {
                echo "<tr><td><form id='form_elimina_foto' method='post'><input type='hidden' name='photo_id' value='".$arr["photo_id"]."'/><input type='hidden' name='elimina' value='1'/><input type='submit' name='submit_elimina' value='Elimina foto'/></form></td></tr>";
            }
            
            // Mostra media voto
            if (!empty(trim($arr["media"]))) {
                echo "<tr><td>Votazione media: ".$arr["media"]."</td></tr>";
            } else {
                echo "<tr><td>Ancora nessun voto registrato</td></tr>";
            }
            
            // Votazione foto solo se utente diverso
            if ($current_id != $arr["member_id"]) {
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
                    $datiUtente = $member->getMember($utente);
                    echo "<tr><td>".$utente."</td><td><img src='pfp/".$datiUtente[0]["member_profile_picture"]."' style='max-width: 32px' /></td><td>".$comm["comment"]."</td></tr>";
                }
            } else {
                echo "<tr><td>Ancora nessun commento</tr>";
            }

            $cont++;
        }
        echo "</table>";
    } else {
        if (isset($_POST["ricerca"]))
            echo "<p>Nessun risultato per: ".$_POST["ricerca"]."<p><a href='index.php'>Annulla ricerca</a>";
    }

    echo "</div>";
?>
</div>