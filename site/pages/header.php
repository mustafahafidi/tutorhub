<?php
use TutorHub\Session;
use TutorHub\User;

$u = Session::getUser();
?>

<!doctype html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <title lang="en"><?=isset($title)?"$title - ":''?>TutorHub</title>

		<meta name="description" content="Una piattaforma che facilita la comunicazione tra tutor e studenti"/>
		<meta name="keywords" content="tutor, studente, studenti, scuola, università, corsi, esami, insegnamenti, lezioni"/>
		<meta name="author" content="Studenti corso di laurea Informatica, Università di Padova"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <!-- Da aggiungere la favicon -->
        <link rel="stylesheet" type="text/css" href="css/icons/ticons.css">
        <link rel="stylesheet" type="text/css" href="css/fullcalendar.css">
        <link rel="stylesheet" type="text/css" href="css/fonts/fonts.css">
        <link rel="stylesheet" type="text/css" href="css/animate.css">

        <link rel="stylesheet" type="text/css" href="css/main.css?<?=time()?>">

        
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body <?=($page=='home')?'class="dark"':''?>>
        <!--[if lte IE 9]>
            <p class="browserupgrade">Stai utilizzando una versione di browser <strong>obsoleta</strong> e non compatibile. Aggiorna il tuo browser per migliorare la tua esperienza e sicurezza online.</p>
        <![endif]-->

        <!-- need roles and symantic things after -->

            
            
        <header id="header">
            <div class="wrapper">
                <a href="index.php?page=<?=Session::isLogged()?'dashboard':'home'?>" class="logo" title="TutorHub <?=Session::isLogged()?'Dashboard':'Home'?>" lang="en" tabindex="1">tutor<b>Hub</b>
                    <small class="usertype"><?php if(Session::isLogged()) echo (Session::getUser()['type'] == User::TYPE_STUDENT)?'studente':'tutor'; ?></small>
                </a>
                <a href="#link-to-content" class="not-visible" title="salta il menù" tabindex="2">salta il menù</a>
                <nav class="main-menu">
                    <ul>
                        <?php if(!Session::isLogged()) { ?>
                        <li><?=($page=='home'?'<span class="selected" lang="en">Home</span>': '<a href="index.php" title="Home" lang="en" tabindex="3">Home</a>')?></li>
                        <li><?=($page=='signup'?'<span class="selected">Iscriviti</span>':'<a href="index.php?page=signup" title="Iscriviti" tabindex="4">Iscriviti</a>')?></li>
                        <li><?=($page=='login'?'<span class="selected">Accedi</span>':'<a href="index.php?page=login" title="Accedi" tabindex="5">Accedi</a>')?></li>
                        <li><?=($page=='contact-us'?'<span class="selected">Contattaci</span>': '<a href="index.php?page=contact-us" title="Contattaci" tabindex="6">Contattaci</a>')?></li>

                        <?php } else { ?>


                            <!-- STUDENTE -->
                            <?php if(Session::getUser()['type'] == User::TYPE_STUDENT) { ?>
                            <li> <?=($page=='dashboard'?'<span class="selected" lang="en">Dashboard</span>':'<a href="index.php?page=dashboard" title="Home" tabindex="3">Dashboard</a>')?></li>
                            <li><?=($page=='search'?'<span class="selected">Cerca Tutor</span>':'<a href="index.php?page=search" title="Cerca Tutor" tabindex="4">Cerca Tutor</a>')?></li>
                            <li><?=($page=='appointments'?'<span class="selected">Prenotazioni</span>':'<a href="index.php?page=appointments" title="Visualizza i tuoi appuntamenti" tabindex="5">Prenotazioni</a> ')?></li>
                            <?php } else if(Session::getUser()['type'] == User::TYPE_TUTOR) { ?>
                            <!-- TUTOR -->
                            <li><?=($page=='dashboard'?'<span class="selected" lang="en">Dashboard</span>': '<a href="index.php?page=dashboard" title="Dashboard" lang="en" tabindex="3">Dashboard</a>')?></li>
                            <li><?=($page=='appointments'?'<span class="selected">Prenotazioni</span>': '<a href="index.php?page=appointments" title="Dashboard" tabindex="4">Prenotazioni</a>')?></li>
                            <li><?=($page=='availability'?'<span class="selected">Disponibilità</span>': '<a href="index.php?page=availability" title="Dashboard" tabindex="5">Disponibilità</a>')?></li>
                            <?php } ?>

                            <li class="dropdown"><a class="profile-pic dropdown-toggle" title="Menu profilo" href="" aria-haspopup="true" aria-expanded="false" tabindex="6"><img src="<?=empty($u['photoUrl'])?'img/profile.jpg':$u['photoUrl']?>" alt="utente"><i class="ti-angle-down" aria-hidden="true"></i></a>
                                <nav class="dropdown-menu visuallyhidden">
                                    <ul>
                                        <li class="user-box">
                                            <div class="user-pic"><img src="<?=empty($u['photoUrl'])?'img/profile.jpg':$u['photoUrl']?>" alt="la tua immagine di profilo"></div>
                                            <div class="user-text">
                                                <h4><?=$u['name']?> <?=$u['surname']?></h4>
                                                <p><?=$u['email']?></p><a href="index.php?page=view-profile" class="btn btn-rounded btn-danger btn-sm" tabindex="7">Visualizza Profilo</a>
                                            </div>
                                        </li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="index.php?page=modify-profile" class="<?=($page=='modify-profile'?'selected':'')?>" tabindex="8"><i class="ti-user" aria-hidden="true"></i> Modifica Profilo</a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="index.php?page=modify-account" class="<?=($page=='modify-account'?'selected':'')?>" tabindex="9"><i class="ti-settings" aria-hidden="true"></i> Modifica Account</a></li>
                                        <li role="separator" class="divider" aria-hidden="true"></li>
                                        <li><a href="index.php?page=logout" tabindex="10"><i class="ti-arrow-circle-left" aria-hidden="true"></i> Logout</a></li>
                                    </ul>
                                </nav>
                            </li>
                        <?php } ?>


                    </ul>
                </nav>
            </div>
        </header>
        
        <main id="main">
