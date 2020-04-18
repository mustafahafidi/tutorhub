<?php

use TutorHub\Session;
use TutorHub\User;

// TODO fix this up

$validator=['invalid'=>false];
if (isset($_POST['firstname'],
        $_POST['lastname'],
        $_POST['email'],
        $_POST['password'],
        $_POST['confpassword'],
        $_POST['accounttype'])) {

    $user = new User(\TutorHub\getDb());
    $user['name'] = $_POST['firstname'];
    $user['surname'] = $_POST['lastname'];
    $user['email'] = $_POST['email'];
    $user['city'] = $_POST['city'];
    $user['password'] = $_POST['password'];
    $user['type'] = $_POST['accounttype'];

    $validator = $user->validateInputs();
    $validator['confpassword'] = ($_POST['password'] === $_POST['confpassword']);
    if (!$validator['confpassword'])
        $validator['invalid'] = true;
    if(!$validator['invalid']) {
        $user['password'] = User::hashPassword($_POST['password']);
        $user->commit();
        Session::setUser($user);
        header('Location: index.php?page=dashboard');
        die();
    }
}

$title='Iscriviti';
include("header.php");
?>
            <div id="link-to-content" class="container">
                <h1 class="page-title">Iscriviti</h1>
				<div class="signupform">
                    <?php if($validator['invalid']) { ?>
                    <div class="alert alert-danger" aria-hidden="true" role="alert">
                        <?=(!$validator['exists']?'Email già presente nella piattaforma':'Dati forniti invalidi, correggere gli errori e riprovare.')?>
                    </div>
                    <?php } ?>
                    <form action="index.php?page=signup" method="post" accept-charset="utf-8">
                        <fieldset>
                            <legend>Per iscriverti alla piattaforma inserisci le seguenti informazioni</legend>

                            <label for="firstname" class="visuallyhidden">Nome:</label>
                                <input type="text" placeholder="Inserisci nome" id="firstname" name="firstname" tabindex="7" value="<?=($user['name']??'')?>"
                                <?=($validator['invalid']&&!$validator['name']?'aria-invalid="true" class="danger" required><div class="form-error">Inserire un nome valido.</div>':' required>')?>

                            <label for="lastname" class="visuallyhidden">Cognome:</label>
                                <input type="text" placeholder="Inserisci cognome" id="lastname" name="lastname" tabindex="8"  value="<?=($user['surname']??'')?>" 
                                <?=($validator['invalid']&&!$validator['surname']?'aria-invalid="true" class="danger" required><div class="form-error">Inserire un cognome valido.</div>':' required>')?>

                            <label for="email" class="visuallyhidden">Email:</label>
                                <input type="email" placeholder="Inserisci la tua email" id="email" name="email" tabindex="9"  value="<?=($user['email']??'')?>" 
                                <?=($validator['invalid']&&!$validator['email']?'aria-invalid="true" class="danger" required><div class="form-error">Inserire un email valida.</div>':' required>')?>

                            <label for="city" class="visuallyhidden">Città:</label>
                                <input type="text" placeholder="Inserisci la città" id="city" name="city" tabindex="10"  value="<?=($user['city']??'')?>" 
                                <?=($validator['invalid']&&!$validator['city']?'aria-invalid="true" class="danger" required><div class="form-error">Inserire una città valida.</div>':' required>')?>

                            <label for="password" class="visuallyhidden">Password:</label>
                                <input type="password" id="password" name="password" placeholder="Inserisci la password" tabindex="11"  value="<?=($_POST['password']??'')?>" 
                                <?=($validator['invalid']&&!$validator['password']?'aria-invalid="true" class="danger" required>
                                <div class="form-error">Inserire una password di almeno 8 caratteri.</div>':' required>')?>

                            <label for="confpassword" class="visuallyhidden">Conferma password:</label>
                                <input type="password" id="confpassword" name="confpassword" placeholder="Conferma la password" tabindex="12"  value="<?=($_POST['confpassword']??'')?>"
                                <?=($validator['invalid']&&!$validator['confpassword']?'aria-invalid="true" class="danger" required>
                                <div class="form-error">Confermare la password inserita.</div>':' required>')?>

                        </fieldset>
                        <fieldset class="accounttype">
                            <legend>Tipologia Account</legend>
                            <label class="radiobutton">Tutor
                              <input type="radio" name="accounttype" value="1" <?=(isset($user['type'])?($user['type']=='1'?'checked':''):'')?> tabindex="13" required>
                              <span class="checkmark"></span>
                            </label>
                            <label class="radiobutton">Studente
                              <input type="radio" name="accounttype" value="0" <?=(isset($user['type'])?($user['type']=='0'?'checked':''):'')?> tabindex="14" required>
                              <span class="checkmark"></span>
                            </label>
                        </fieldset>
                        <button class="btn btn-3d btn-blue" tabindex="15">Iscriviti</button>
                    </form>
                </div>
            </div>
<?php include("footer.php"); ?>