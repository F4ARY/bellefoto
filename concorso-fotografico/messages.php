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
    if ($ris[0]['member_verified'] == false)
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


?>


<!--LOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOL-->

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Messaggi Privati</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
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
            echo $datiUtented[0]["member_profile_picture"]; ?>" style="width: 150px; height: 150px" alt=""
                 class="img-fluid rounded-circle">
            <h1 class="text-light"><a href="dashboard.php"><?php echo $member->getNameById($current_id) ?></a></h1>
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
                <h2>Messaggi Privati</h2>
                <ol>
                    <li><a href="dashboard.php">Home</a></li>
                    <li>Messaggi Privati</li>
                </ol>
            </div>

        </div>
    </section><!-- End Breadcrumbs -->

    <section class="inner-page ">
        <div class="container d-flex justify-content-center">
            <!-- ======= Testimonials Section ======= -->
            <section id="testimonials" class="testimonials section-bg">
                <div class="container">

                    <div class="section-title">
                        <h2>Messaggi</h2>
                    </div>

                    <div class="testimonials-slider swiper-container" data-aos="fade-up" data-aos-delay="100">
                        <div class="swiper-wrapper">


                            <?php

                            $query = "SELECT * FROM message WHERE  receiver_id = " . $current_id ;

                            $messaggiArray = $ds->select($query);

                            if ($messaggiArray != null) {
                                foreach ($messaggiArray as $messaggio) {

                                    $utented = $member->getEmailByID($messaggio['sender_id']);
                                    $datiUtented = $member->getMember($utented);

                                    echo '<!-- INIZIO MESSAGGIO-->
                            <div class="swiper-slide">
                                <div class="testimonial-item" data-aos="fade-up">
                                    <p>
                                        <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                                        ' . $messaggio['context'] .'
                                        <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                                    </p>
                                    <img src="pfp/' . $datiUtented[0]["member_profile_picture"] . '" class="testimonial-img"
                                         alt="" style="width: 90px; height: 90px">
                                    <h3>' . $member->getNameById($messaggio['sender_id']) . '</h3>
                                </div>
                            </div><!-- FINE MESSAGGIO -->
';

                                }
                            }

                            ?>

                        </div>
                        <div class="swiper-pagination"></div>
                    </div>

                </div>
            </section><!-- End Testimonials Section -->
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

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>

</body>

</html>