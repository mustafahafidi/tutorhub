<?php
$title = 'Contattaci';
include("header.php");
?>
            <div id="link-to-content" class="container">

                <h1 class="page-title">Contattaci</h1>
                <div class="contactform">
                    <form action="" method="post" accept-charset="utf-8">
                        <fieldset>
                            <legend class="">Per inviarci un messaggio, inserisci i dati di contatto e il messaggio</legend>
                            <label for="name" class="visuallyhidden">Nome:</label>
                            <input type="text" placeholder="Inserisci il tuo nome" id="name" name="name" required tabindex="6">
                            <label for="email" class="visuallyhidden">Email:</label>
                            <input type="email" placeholder="Inserisci la tua email" id="email" name="email" required tabindex="7">
                            <label for="title" class="visuallyhidden">Titolo:</label>
                            <input type="text" placeholder="Inserisci un titolo per il messaggio" id="title" name="title" required tabindex="8">
                            <label for="message" class="visuallyhidden">Messaggio:</label>   
                            <textarea id="message" name="message" rows="5" placeholder="Inserisci il messaggio che vuoi inviarci" required="required" tabindex="9"></textarea>
                        </fieldset>
                        <button type="submit" class="btn btn-3d btn-dark" tabindex="10">Invia</button>
                    </form>
                </div>

            </div>
<?php include("footer.php"); ?>