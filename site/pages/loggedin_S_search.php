<?php
use TutorHub\Session;
use TutorHub\User;

$u = Session::getUser();
if ($u === null || $u['type'] != User::TYPE_STUDENT) die();

$title = 'Ricerca';
include("header.php");
?>
            <div id="link-to-content" class="search container">

                <h1 class="page-title">Cerca Tutor</h1>
                <aside class="alert">
                	<p>Qui puoi effettuare una ricerca per trovare il tutor più adatto a te!</p>
                </aside>
                <div class="searchform">
	                <form action="index.php?page=search-results" method="post" accept-charset="utf-8">
	                	<fieldset>
	                		<legend>Inserisci il nome di una materia o città</legend>
	                		<label for="search" class="visuallyhidden">Nome:</label>
	                           	<input type="text" placeholder="Inserisci materia o città" id="search" name="search" tabindex="11">
	                           	<button class="btn btn-blue" tabindex="12">Cerca</button>
	                	</fieldset>
	                </form>
	            </div>
            </div>

<?php include("footer.php"); ?>