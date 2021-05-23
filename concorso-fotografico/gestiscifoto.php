<?php
require_once "classes/Util.php";
require_once "classes/Member.php";
require_once "classes/DBController.php";

session_start();
$current_id = $_SESSION["member_id"];

$util = new Util();
$member = new Member();
$db = new DBController();

$query = "SELECT member_verified FROM members WHERE member_email = ?";

if (isset($_SESSION['mail']))
    $mail = $_SESSION['mail'];
else {
    $mail = $_COOKIE['member_login'];
    $ris = $db->select($query, "s", array($_COOKIE['member_login']));
    if ($ris[0]['member_verified'] == false)
        $util->redirect("index.php");
}

if (isset($_SESSION['verified'])) {
    if (!$_SESSION['verified']) {
        $util->redirect("index.php");
    }
}

//Per caricare
if (isset($_POST['carica'])) {
    if (isset($_POST['desc'])) {
        $risposta = $member->caricaImmagine($mail, $_FILES['picture'], $_POST['desc']);

    }
}

//Per eliminare
if (isset($_POST['rimuovi'])) {
    if (isset($_POST['checkbox'])) {
        $risposta = $member->cancellaImmagine($_POST['checkbox']);
    }
}

//Per modifica
if (isset($_POST['edit'])) {
    if (isset($_POST['checkbox'])) {
        $risposta = $member->editImmagine($_POST['checkbox'], $_POST['desc']);
    }
}

$righe = $member->getFoto($mail);
?>

<!--LOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOL-->


<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Profilo</title>
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

    <style>
        .pulsanteImmagine {
            float: left;
            transition: .2s all;
            width: 50%;
            border: none;
            background-color: #149ddd;
            color: white;
            font-size: 160%;
        }

        .pulsanteImmagine:hover {
            transition: .2s all;
            background-color: #7fbfdd;
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

    <!-- ======= Breadcrumbs ======= -->
    <section class="breadcrumbs">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center">
                <h2>Profilo</h2>
                <ol>
                    <li><a href="index.php">Home</a></li>
                    <li>Profilo</li>
                </ol>
            </div>

        </div>
    </section><!-- End Breadcrumbs -->

    <section class="inner-page">
        <div class="container">

            <div class="fotoStesse">
                <!-- ======= SEZIONE FOTO ======= -->
                <section id="portfolio" class="portfolio section-bg">
                    <div class="container">

                        <div class="section-title">
                            <h2>Foto</h2>
                        </div>
                        <div class="row portfolio-container">


                            <?php

                            if ($righe != null) {
                                $lungh = count($righe);
                                for ($i = 0; $i < $lungh; $i++) {
                                    $idImmagine = $righe[$i]['photo_id'];

                                    echo ' <!--IMMAGINE INIZIO-->

                                                    <!-- MODALE MODIFICA DESCRIZIONE -->
                                                    <div class="modal fade" id="modificaDescrizione' . $idImmagine . '" tabindex="-1" role="dialog"
                                                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <form name="sign-up" action="gestiscifoto.php" method="post" enctype="multipart/form-data">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Modifica descrizione</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <span>Nuova Descrizione</span><br>
                                                                        <textarea style="resize: none;" rows="13" cols="55" name="desc" id="desc" maxlength="1000"
                                                                                  required=""> </textarea>
                                                                        <input type="hidden" name="edit">
                                                                        <input type="checkbox" name="checkbox[]" value="' . $idImmagine . '" checked style="display: none;">
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancella</button>
                                                                        <button type="submit" class="btn btn-primary">Modifica</button>
                                                                    </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--FINE MODALE-->

                                                <!-- MODALE CONFERMA -->
                                                <div class="modal fade" id="avvisoEliminazione' . $idImmagine . '" tabindex="-1" role="dialog"
                                                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <form name="sign-up" action="gestiscifoto.php" method="post" enctype="multipart/form-data">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel">Avviso</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <span>Sei sicuro di voler eliminare la foto?</span>
                                                                    <input type="hidden" name="rimuovi">
                                                                    <input type="checkbox" name="checkbox[]" value="' . $idImmagine . '" checked style="display: none;">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancella</button>
                                                                    <button type="submit" class="btn btn-primary">Conferma</button>
                                                                </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--FINE MODALE-->

                                            <div class="col-lg-4 col-md-6 portfolio-item filter-app">
                                                <div class="portfolio-wrap" style="height: 30%; ">
                                                    <img src="imgs/' . $righe[$i]['file_name'] . '" class="img-fluid" alt="" >
                                                    <div class="portfolio-links">
                                                        <button class="pulsanteImmagine" data-toggle="modal" data-target="#modificaDescrizione' . $idImmagine . '"><i
                                                                    class="bi bi-pencil-fill"></i></button>
                                                        <button class="pulsanteImmagine" data-toggle="modal" data-target="#avvisoEliminazione' . $idImmagine . '"><i
                                                                    class="bi bi-x-circle-fill"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--FINE IMMAGINE-->';
                                }

                            }

                            ?>




            </div>
        </div>

        <div class="caricaFoto">
            <!-- MODALE CARICAMENTO FOTO -->
            <div class="modal fade" id="modaleCaricamento" tabindex="-1" role="dialog"
                 aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form name="sign-up" action="gestiscifoto.php" method="post" enctype="multipart/form-data">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Carica un&apos; immagine</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input class="upload-image" type="file" name="picture" id="picture" accept="image/*"
                                       required>
                                <br>
                                <span>Descrizione</span><br>
                                <textarea style="resize: none;" rows="13" cols="55" name="desc" id="desc"
                                          maxlength="1000"
                                          required> </textarea>
                                <input type="hidden" name="carica" value="carica">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                <button type="submit" class="btn btn-primary">Carica</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>

        <!--FINE MODALE-->

        </div>
    </section><!-- End Portfolio Section -->
    </div>

    <!-- PULSANTE CARICAMENTO -->

    <button type="button" class="btn btn-primary caricafoto " data-toggle="modal" data-target="#modaleCaricamento"
            style="position: fixed; bottom: 70px; right: 10px; border-radius: 100%; width: 70px; height: 70px;">
        <i class="bi bi-plus" style="font-size: 240%;"></i>
    </button>


    </div>

    </div>
    </section>

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