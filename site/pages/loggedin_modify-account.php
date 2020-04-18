<?php
// TODO ratings
use TutorHub\Session;
use TutorHub\User;

if (!Session::isLogged()) die();

function handleType(User $user, $type) : bool
{
    switch ($type) {
        case 'student':
            if ($user['type'] != User::TYPE_STUDENT) {
                $user['type'] = User::TYPE_STUDENT;
                return true;
            }
            break;
        case 'tutor':
            if ($user['type'] != User::TYPE_TUTOR) {
                $user['type'] = User::TYPE_TUTOR;
                return true;
            }
            break;
    }
    return false;
}

function handlePassword(User $user, string $old, string $new, string $conf) : bool
{
    if (!$user->checkPassword($old)) {
        // TODO handle wrong password
        return false;
    }
    if ($new !== $conf) {
        // TODO handle mismatch
        return false;
    }
    $user['password'] = User::hashPassword($new);
    return true;
}

$u = Session::getUser();

$modified = false;
if (isset($_POST['accounttype']))
    $modified = handleType($u, $_POST['accounttype']) || $modified;
if (isset($_POST['oldpassword']) || isset($_POST['newpassword']) || isset($_POST['confpassword']))
    $modified = handlePassword($u, $_POST['oldpassword'], $_POST['newpassword'], $_POST['confpassword']) || $modified;

if ($modified) {
    $u->commit();
    // TODO success message
}

$rating = number_format($u['ratingAvg'] ?? 0, 1);

$title = 'Modifica account';
include("header.php");
?>
            <div id="link-to-content" class="profile container">

               <h1 class="page-title">Modifica il tuo Account</h1>
               <aside class="alert">
                	<p>Qui puoi modificare i dati di accesso al tuo account.</p>
                </aside>
               <section class="profile-info card">
               		<img src="<?=empty($u['photoUrl'])?'img/profile.jpg':$u['photoUrl']?>" alt="foto profilo"/>
                	<h2><?=$u['name']?> <?=$u['surname']?></h2>
                	<ul>
                		<li><span>Tipologia account: </span> <?=$u['type']==User::TYPE_TUTOR?'Tutor':'Studente'?></li>
                		<li><span>Valutazioni: </span> <?=$rating?>/5.0</li>
                		<li><span>Email: </span> <?=$u['email']?></li>
                        <?php if (!empty($u['phone'])) { ?>
                		<li><span>Numero di telefono: </span> <?=$u['phone']?></li>
                		<?php } ?>
                	</ul>
               </section>
               <section class="profile-modify card">
	                <h4 class="card-title">Modifica informazioni Account</h4>

	               	<div class="profileform">
	                    <form action="" method="post" accept-charset="utf-8">
	                    	<fieldset class="accounttype">
	                    		<legend>Tipologia Account</legend>
	                    		<label class="radiobutton" for="checkboxTutor">Tutor
								  <input type="radio"<?php if ($u['type']==User::TYPE_TUTOR) { ?> checked="checked"<?php } ?> name="accounttype" value="tutor" id="checkboxTutor" tabindex="11">
								  <span class="checkmark"></span>
								</label>
								<label class="radiobutton" for="checkboxStud">Studente
								  <input type="radio"<?php if ($u['type']==User::TYPE_STUDENT) { ?> checked="checked"<?php } ?> name="accounttype" value="student" tabindex="12" id="checkboxStud">
								  <span class="checkmark"></span>
								</label>
	                    	</fieldset>
	                        <fieldset>	
	                        	<legend>Aggiorna la tua password</legend>
	                        	<label for="oldpassword" class="visuallyhidden">Vecchia password:</label>
	                                <input type="password" placeholder="Vecchia Password" id="oldpassword" name="oldpassword" tabindex="13">
	                            <label for="newpassword" class="visuallyhidden">Nuova password:</label>
	                                <input type="password" placeholder="Nuova Password" id="newpassword" name="newpassword" tabindex="14">
	                            <label for="confpassword" class="visuallyhidden">Conferma Password:</label>
	                                <input type="password" placeholder="Conferma nuova Password" id="confpassword" name="confpassword" tabindex="15">
	                        </fieldset>
	                        <button class="btn btn-3d btn-blue" tabindex="16" >Aggiorna dati account</button>

	                    </form>
	                </div>
               </section>
               
            </div>
<?php include("footer.php"); ?>