<?php

use TutorHub\Session;
use TutorHub\User;

$invalid = false;
if (isset($_POST['email']) && isset($_POST['password'])) {
    $user = User::fromCredentials(\TutorHub\getDb(), $_POST['email'], $_POST['password']);
    if ($user !== null) {
        Session::setUser($user);
        header('Location: index.php?page=dashboard');
        die();
    }
    $invalid = true;
}

$title='Accedi';
include("header.php");
?>
        <div id="link-to-content" class="container">
            <h1 class="page-title">Accedi al tuo account</h1>
            <div class="loginform">
                <?php if($invalid) { ?>
                <div class="alert alert-danger" aria-hidden="true" role="alert">
                    Email o password incorretta, riprovare.
                </div>
                <?php } ?>
                <form action="index.php?page=login" method="post" accept-charset="utf-8">
                    <fieldset>
                        <legend>Per accedere al tuo account inserisci la tua email e password</legend>
                        <label for="email" class="visuallyhidden">Email:</label>
                            <input type="email" placeholder="Inserisci la tua email" id="email" name="email" <?=($invalid?'class="danger"':'')?> required tabindex="7">

                        <label for="password" class="visuallyhidden">Password:</label>
                            <input type="password" id="password" name="password" placeholder="Inserisci la password" <?=($invalid?'class="danger"':'')?> required tabindex="8">
                    </fieldset>
                    <button type="submit" class="btn btn-3d btn-blue" tabindex="9">Accedi</button>
                </form>
            </div>
        </div>
<?php include("footer.php"); ?>