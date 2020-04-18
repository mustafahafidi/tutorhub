<?php
use TutorHub\Session;
use TutorHub\User;

$u = Session::getUser();
if ($u === null || $u['type'] != User::TYPE_TUTOR) die();

$title='Disponibilità';
include("header.php");
?>
            <div id="link-to-content" class="availability container">
            	
                <h1 class="page-title">Aggiorna le tue disponibilità</h1>
              	<aside class="alert">
                	<p>Qui trovi le disponibiltà da te fornite. Il calendario è editabile a tuo piacimento! Trascina il mouse per selezionare una nuova disponibilità e clicca per eliminare una esistente.</p>
                </aside>
                <article class="calendar card">
	                <h4 class="card-title">Calendario disponibilità</h4>
                	<div id="calendar"></div>
                </article>
            </div>

          
<?php include("footer.php"); ?>