<?php
session_start();

require_once "classes/Member.php";
require_once "classes/Util.php";
require_once "classes/authCookieSessionValidate.php";

$util = new Util();
$ds = new DBController();
$member = new Member();

if (!$isLoggedIn) {
    header("Location: ./");
} else {
    if (isset($_SESSION["member_id"]))
        $current_id = $_SESSION["member_id"];
    else
        $current_id = $member->getId($_COOKIE['member_login']);

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
        echo "<p>" . $risposta["message"] . "</p>";
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
}
?>

<!-- LOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOL -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Concorso Fotografico</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link
            href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
            rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
          integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
          crossorigin=""/>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""></script>

    <style>
        .votazione {
            margin-top: 20px;
            margin-left: 1.2%;
        }

        .votazione input {
            border-radius: 100%;
            transition: .2s all;
            width: 45px;
            height: 45px;
            border: none;
            margin: 0 15px;
            background: rebeccapurple;
            font-family: "Raleway", sans-serif;
            font-size: 140%;
            text-align: center;
            color: white;
            text-shadow: black 2px 2px;
        }

        .votazione input:hover {
            transition: .2s all;
            width: 35px;
            height: 35px;
        }

        .pulsantifoto {
            border: none;
            background: none;
            color: #122f57;
        }
    </style>
</head>

<body>

<!-- ======= Mobile nav toggle button ======= -->
<i class="bi bi-list mobile-nav-toggle d-xl-none"></i>

<!-- ======= Header ======= -->
<header id="header">
    <div class="d-flex flex-column">
        <div class="profile">
            <img src="pfp/<?php
            $utented = $member->getEmailByID($current_id);
            $datiUtented = $member->getMember($utented);
            echo $datiUtented[0]["member_profile_picture"];?>" style="width: 150px; height: 150px" alt="" class="img-fluid rounded-circle">
            <h1 class="text-light"><a href="dashboard.php"><?php echo $member->getNameById($current_id)?></a></h1>
        </div>

        <nav id="navbar" class="nav-menu navbar">
            <ul>
                <li><a href="dashboard.php" title="Torna alla home" class="nav-link scrollto active"><i
                                class="bx bx-home"></i> <span>Home</span></a></li>
                <li><a href="gestiscifoto.php" class="nav-link scrollto"><i class="bx bx-user"></i> <span>Profilo</span></a>
                </li>
                <li><a href="messages.php" class="nav-link scrollto"><i class="bx bx-envelope"></i> <span>Messaggi privati</span></a>
                </li>
                <li><a href="logout.php" class="nav-link scrollto"><i class="bx bx-door-open"></i> <span>Esci</span></a>
                </li>
            </ul>
        </nav><!-- .nav-menu -->
    </div>
</header><!-- End Header -->

<main id="main">

    <!-- ======= About Section ======= -->
    <section id="about" class="about">
        <div class="container">

            <form id="form_ricerca" method="post" style="margin: 50px;">
                <input type="textbox" placeholder="Ricerca per descrizione" name="ricerca"
                       style="width: 40%; margin-left: 24%; height: 40px;">
                <input type="hidden" name="filtra" value="1"><br>
                <input class="btn btn-primary" type="submit" name="submit_ricerca" value="Cerca"
                       style="margin-left:39%; margin-top: 20px; width: 10%;">
            </form>

            <div class="section-title">
                <h2>Immagini concorso</h2>
            </div>

            <?php
            $query = "SELECT * FROM photo";
            if (isset($_POST["filtra"])) {
                $query = "SELECT * FROM photo WHERE description LIKE '%" . $_POST["ricerca"] . "%'";
            }
            $resultArray = $ds->select($query);
            $in = false;
            if (!empty($resultArray)) {
                $cont = 0;
                foreach (array_reverse($resultArray) as $arr) {
                    $idFoto = $arr['photo_id'];
                    if (!$arr['hidden']) {
                        if ($arr['lat'] != "0") {

                            echo '<!-- MODALE INFORMAZIONI POSIZIONE-->
        <div class="modal fade" id="infoaggiuntive' . $idFoto . '" tabindex="-1" role="dialog"
          aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Posizione</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div id="map' . $idFoto . '" style="display: flex;justify-content: center;align-items: center;position: center;height: 350px; width: 450px"></div>
                <script>
                        var map = L.map("map' . $idFoto . '").setView([' . $arr['lat'] . ', ' . $arr['lng'] . '], 5);

            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: \'Mappa concorso\'
                        }).addTo(map);

                        L.marker([' . $arr['lat'] . ', ' . $arr['lng'] . ']).addTo(map)
                            .bindPopup("Posizione nella quale è stata scattata la foto")
            .openPopup();
            </script>
              </div>
            </div>
          </div>
        </div>
        <!-- Fine modale-->';
                        }

                        echo '<!-- MODALE MESSAGGIO AL AUTORE-->
        <div class="modal fade" id="dm' . $idFoto . '" tabindex="-1" role="dialog"
          aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Invia un direct message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <h6>Scrivi qui il messaggio che vuoi mandare al autore della foto</h6>
                <form id="form_messaggio" method="post">
                  <input type="text" name="message" style="width: 100%;">
                  <input type="hidden" name="receiver_id" value="' . $arr["member_id"] . '">
                  <input type="hidden" name="messaggio" value="1">
                  <input class="btn btn-primary" type="submit" name="submit_messaggio" value="Invia" style="margin-top: 10px;">
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Fine modale-->';

                        echo ' <!-- MODALE INFORMAZIONI COMMENTI-->
        <div class="modal fade" id="modCommenti' . $idFoto . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
          aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Commenti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="sezioneCommenti">
                  ';
                        $query = "SELECT comment, member_id FROM comment WHERE photo_id =" . $arr["photo_id"];
                        $commenti = $ds->select($query);
                        if (!empty($commenti)) {
                            foreach ($commenti as $comm) {
                                $id = $comm["member_id"];
                                $utente = $member->getEmailByID($id);
                                $datiUtente = $member->getMember($utente);

                                $nomeCompleto = $member->getNameById($id);
                                echo '
                  <div class="row" style="margin-bottom: 20px;">
                    <div class="col-2" style="text-align: center;">
                      <img src="pfp/' . $datiUtente[0]["member_profile_picture"] . '" style="border-radius: 100%; width: 60px; height: 60px;">
                      <span><b>' . $nomeCompleto . '</b></span>
                    </div>
                    <div class="col-9">
                      <span>' . $comm["comment"] . '</span>
                    </div>
                  </div>
';
                            }
                        } else {
                            echo "<span><b>Non c'è ancora nessun commento</b></span>";
                        }

                        echo '<div class="row">
                    <form id="form_commenta" method="post">
                      <input type="textbox" name="comment" style="width: 75%;">
                      <input type="hidden" name="photo_id" value="'. $idFoto .'">
                      <input type="hidden" name="commenta" value="1">
                      <input class="btn btn-primary" type="submit" name="submit_commenta" value="Commenta">
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Fine modale-->';

                        if ($arr['media'] != null) {
                            $valutazione = $arr['media'];
                        } else $valutazione = "0";

                        $nomeCaricato = $member->getNameById($arr['member_id']);

                        echo '<div class="row" style="margin-bottom: 40px;">
          <div class="col-lg-4" data-aos="fade-right">
            <a href="imgs/' . $arr['file_name'] . '"><img src="imgs/' . $arr['file_name'] . '" class="img-fluid" alt="" "></a>
          </div>
          <div class="col-lg-8 pt-4 pt-lg-0 content" data-aos="fade-left">
          <h3>' . $nomeCaricato . ' | ' . $valutazione . ' <i class="bi bi-star-fill"></i></h3>
            <p>
                ' . $arr['description'] . '
            </p>
            <ul>';
                        if ($arr['lat'] != "0") {
                            echo '<li><i class="bi bi-geo-alt-fill"></i><button type="button" class="pulsantifoto" data-toggle="modal"
                  data-target="#infoaggiuntive' . $idFoto . '">
                  Informazioni posizione
                </button></li>';
                        }

                        echo '<li><i class="bi bi-chat-dots-fill"></i><button type="button" class="pulsantifoto" data-toggle="modal"
                  data-target="#modCommenti' . $idFoto . '">
                  Commenti
                </button></li>
              <li><i class="bi bi-chat-left-text-fill"></i><button type="button" class="pulsantifoto"
                  data-toggle="modal" data-target="#dm' . $idFoto . '">
                  Invia messaggio al autore
                        </button></li>
              <li>
                <i class="bi bi-flag-fill"></i>
                <form method="post">
                  <input type="hidden" name="photo_id" value="' . $idFoto . '">
                  <input type="hidden" name="segnala" value="1">
                  <input type="submit" name="submit_segnala" value="Segnala foto"
                    style="border: none; background: none; color: #122f57;">
                </form>
              </li>';

                        if ($current_id == $arr["member_id"]) {

                            echo '<li>
                <i class="bi bi-x-circle-fill"></i>
                <form id="form_elimina_foto" method="post">
                  <input type="hidden" name="photo_id" value="' . $idFoto . '">
                  <input type="hidden" name="elimina" value="1">
                  <input type="submit" name="submit_elimina" value="Elimina foto" style="border: none; background: none; color: #122f57;">
                </form>
              </li>';

                        }

                        echo '
            </ul>
          </div>
          <div class="votazione" data-aos="fade-right">
            <form method="POST">
              <input type="hidden" name="photo_id" value="' . $idFoto . '">
              <input type="submit" value="1" name="submit_voto" style="background: #c1cad6;">
              <input type="submit" value="2" name="submit_voto" style="background: #8391a7;">
              <input type="submit" value="3" name="submit_voto" style="background: #4c6991;">
              <input type="submit" value="4" name="submit_voto" style="background: #224069;">
              <input type="submit" value="5" name="submit_voto" style="background: #122f57;">
            </form>
          </div>
        </div>

        <!-- ======= FINE IMMAGINE ======= -->';
                    }
                }
            }
            ?>
        </div>
    </section>
    <!-- End About Section -->


</main><!-- End #main -->

<!-- ======= Footer ======= -->
<footer id="footer">
    <div class="container">
        <div class="copyright">
            &copy; Copyright <strong><span>George Patrut</span></strong>
        </div>
    </div>
</footer><!-- End  Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/vendor/purecounter/purecounter.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/vendor/typed.js/typed.min.js"></script>
<script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="assets/js/modal.js"></script>
<script src="assets/js/util.js"></script>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>

</body>

</html>